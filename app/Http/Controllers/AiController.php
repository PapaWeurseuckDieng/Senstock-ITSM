<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Equipement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class AiController extends Controller
{
    // ─── Pages ───────────────────────────────────────────────────────────────

    public function assistant()
    {
        return view('ai.assistant');
    }

    public function baseConnaissances()
    {
        return view('ai.base-connaissances');
    }

    public function faq()
    {
        return view('ai.faq');
    }

    // ─── API : Analyser un ticket (logique locale + recherche web) ────────────

    public function analyserTicket(Request $request)
    {
        $request->validate(['description' => 'required|string|min:10|max:2000']);

        $desc = strtolower($request->description);

        // Classification locale basée sur les mots-clés
        $type     = $this->detecterType($desc);
        $priorite = $this->detecterPriorite($desc);
        $categorie= $this->detecterCategorie($desc);
        $score    = $this->calculerUrgence($priorite, $type);

        // Recherche web pour la solution rapide
        $query    = $this->extraireMotsCles($request->description);
        $resultats= $this->searchWeb($query . ' solution IT');
        $solution = !empty($resultats)
            ? $resultats[0]['snippet']
            : 'Contactez l\'équipe IT avec les détails du problème.';

        // Générer un titre concis
        $mots  = array_filter(explode(' ', $request->description), fn($m) => strlen($m) > 3);
        $titre = ucfirst(implode(' ', array_slice($mots, 0, 8)));
        if (strlen($titre) > 80) $titre = substr($titre, 0, 77) . '...';

        return response()->json([
            'type'           => $type,
            'priorite'       => $priorite,
            'categorie'      => $categorie,
            'titre_suggere'  => $titre,
            'resume'         => ucfirst(substr($request->description, 0, 150)),
            'solution_rapide'=> $solution,
            'score_urgence'  => $score,
            'source'         => 'web',
        ]);
    }

    // ─── API : Suggérer une réponse au ticket ────────────────────────────────

    public function suggererReponse(Request $request)
    {
        $request->validate(['ticket_id' => 'required|exists:tickets,id']);

        $ticket   = Ticket::with(['user', 'assignee', 'comments'])->findOrFail($request->ticket_id);
        $query    = $ticket->title . ' ' . $ticket->type . ' solution IT';
        $resultats= $this->searchWeb($query);

        $etapes = [];
        $reponse = "Bonjour {$ticket->user?->name},\n\nNous avons bien pris en compte votre ticket #{$ticket->ticket_number} concernant : {$ticket->title}.\n\n";

        if (!empty($resultats)) {
            $reponse .= "Voici les étapes recommandées pour résoudre ce problème :\n";
            foreach (array_slice($resultats, 0, 3) as $i => $r) {
                $etapes[] = $r['title'] . ' : ' . $r['snippet'];
                $reponse .= ($i + 1) . '. ' . $r['snippet'] . "\n";
            }
            $reponse .= "\nSi le problème persiste, notre technicien interviendra directement.";
        } else {
            $etapes = [
                'Vérifier les connexions matérielles',
                'Redémarrer l\'équipement concerné',
                'Contacter le support IT si le problème persiste',
            ];
            $reponse .= "Notre équipe IT analyse votre demande et reviendra vers vous dans les meilleurs délais selon la priorité " . strtoupper($ticket->priority) . ".";
        }

        return response()->json([
            'reponse'               => $reponse,
            'etapes'                => $etapes,
            'temps_estime'          => $this->estimerTemps($ticket->priority),
            'necessite_intervention'=> in_array($ticket->type, ['panne', 'incident']),
            'source'                => 'web',
        ]);
    }

    // ─── API : Analyser les performances (100% local) ────────────────────────

    public function analyserPerformances(Request $request)
    {
        $total   = Ticket::count();
        $resolus = Ticket::whereIn('status', ['resolu', 'ferme'])->count();
        $ouverts = Ticket::where('status', 'ouvert')->count();
        $enCours = Ticket::where('status', 'en_cours')->count();
        $slaOk   = Ticket::whereIn('status', ['resolu', 'ferme'])->where('sla_breached', false)->count();
        $slaRate = $resolus > 0 ? round($slaOk / $resolus * 100, 1) : 0;

        $avgH = Ticket::whereNotNull('t4_resolved_at')
            ->whereNotNull('t0_created_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, t0_created_at, t4_resolved_at)) as avg_h'))
            ->value('avg_h');
        $avgH = round($avgH ?? 0, 1);

        // Note globale calculée localement
        $note = 5;
        if ($slaRate >= 90) $note += 2;
        elseif ($slaRate >= 75) $note += 1;
        elseif ($slaRate < 50) $note -= 2;

        if ($avgH > 0 && $avgH <= 8)  $note += 1;
        if ($avgH > 48) $note -= 1;
        if ($total > 0 && ($resolus / $total) > 0.8) $note += 1;
        $note = max(1, min(10, $note));

        $tendance = $slaRate >= 75 ? 'positive' : ($slaRate >= 50 ? 'stable' : 'negative');

        // Points forts et axes d'amélioration basés sur les données
        $pointsForts = [];
        $axes        = [];
        $alertes     = [];
        $recs        = [];

        if ($slaRate >= 80) {
            $pointsForts[] = "Taux SLA de {$slaRate}% — objectif atteint";
        } else {
            $axes[]   = "Taux SLA de {$slaRate}% — objectif 80% non atteint";
            $alertes[]= "SLA critique : {$slaRate}% de respect des délais";
            $recs[]   = [
                'titre'       => 'Améliorer le taux SLA',
                'description' => 'Analyser les tickets en retard et renforcer les ressources IT',
                'priorite'    => 'haute',
                'impact'      => 'Amélioration satisfaction utilisateurs',
            ];
        }

        if ($ouverts > 10) {
            $alertes[] = "{$ouverts} tickets ouverts non traités";
            $recs[]    = [
                'titre'       => 'Réduire le backlog',
                'description' => "Traiter en priorité les {$ouverts} tickets en attente",
                'priorite'    => 'haute',
                'impact'      => 'Réduction des délais de traitement',
            ];
        } else {
            $pointsForts[] = "Faible backlog : seulement {$ouverts} tickets ouverts";
        }

        if ($avgH > 0 && $avgH <= 24) {
            $pointsForts[] = "Délai moyen de résolution de {$avgH}h — performant";
        } elseif ($avgH > 48) {
            $axes[] = "Délai moyen de {$avgH}h — trop élevé";
        }

        if ($resolus > 0) {
            $pointsForts[] = "{$resolus} tickets résolus au total";
        }

        return response()->json([
            'note_globale'       => $note,
            'points_forts'       => $pointsForts ?: ['Équipe IT opérationnelle'],
            'points_amelioration'=> $axes ?: ['Maintenir les performances actuelles'],
            'alertes'            => $alertes,
            'recommandations'    => $recs,
            'tendance'           => $tendance,
            'resume_executif'    => "La plateforme ITSM SENSTOCK gère {$total} tickets avec un taux de résolution de " . ($total > 0 ? round($resolus/$total*100) : 0) . "%. Le taux de respect des SLA est de {$slaRate}% avec un délai moyen de {$avgH}h. " . ($tendance === 'positive' ? 'Les performances sont satisfaisantes.' : 'Des améliorations sont nécessaires sur les délais de traitement.'),
            'source'             => 'local',
        ]);
    }

    // ─── API : Base de connaissances — recherche web ──────────────────────────

    public function rechercherSolution(Request $request)
    {
        $request->validate(['question' => 'required|string|min:5|max:500']);

        // Chercher dans les tickets résolus en base
        $ticketsSimilaires = Ticket::whereIn('status', ['resolu', 'ferme'])
            ->whereNotNull('resolution_note')
            ->where(function ($q) use ($request) {
                foreach (array_slice(explode(' ', $request->question), 0, 3) as $mot) {
                    if (strlen($mot) > 3) {
                        $q->orWhere('title', 'like', "%{$mot}%")
                          ->orWhere('resolution_note', 'like', "%{$mot}%");
                    }
                }
            })
            ->select('ticket_number', 'title', 'resolution_note')
            ->limit(3)
            ->get();

        // Recherche web
        $query    = $request->question . ' solution Windows IT';
        $resultats= $this->searchWeb($query);

        // Construire les étapes à partir des résultats
        $etapes = [];
        if ($ticketsSimilaires->count()) {
            foreach ($ticketsSimilaires as $t) {
                $etapes[] = "[Ticket #{$t->ticket_number}] {$t->resolution_note}";
            }
        }
        foreach (array_slice($resultats, 0, 4) as $r) {
            $etapes[] = $r['snippet'];
        }
        if (empty($etapes)) {
            $etapes = [
                'Vérifier les connexions et redémarrer l\'équipement',
                'Consulter les logs d\'erreur Windows',
                'Créer un ticket si le problème persiste',
            ];
        }

        return response()->json([
            'solution'           => !empty($resultats) ? $resultats[0]['snippet'] : 'Recherche non concluante. Créez un ticket IT.',
            'etapes'             => $etapes,
            'outils_necessaires' => $this->outilsParCategorie($this->detecterCategorie(strtolower($request->question))),
            'temps_estime'       => '15-30 minutes',
            'difficulte'         => 'moyen',
            'prevenir_recurrence'=> 'Effectuer des mises à jour régulières et sauvegarder les configurations.',
            'sources'            => array_map(fn($r) => $r['url'], array_slice($resultats, 0, 3)),
            'source'             => 'web',
        ]);
    }

    // ─── API : FAQ intelligente ───────────────────────────────────────────────

    public function faqRepondre(Request $request)
    {
        $request->validate(['question' => 'required|string|min:3|max:300']);

        $q        = $request->question;
        $resultats= $this->searchWeb($q . ' IT ITSM guide');
        $urgence  = $this->detecterUrgenceFaq(strtolower($q));

        $reponse = !empty($resultats)
            ? $resultats[0]['snippet']
            : 'Pour cette question, nous vous recommandons de créer un ticket afin que notre équipe IT puisse vous assister personnellement.';

        $actions = [];
        foreach (array_slice($resultats, 1, 2) as $r) {
            $actions[] = $r['title'] . ' → ' . $r['url'];
        }
        if (empty($actions)) {
            $actions = ['Créer un ticket si le problème nécessite une intervention'];
        }

        return response()->json([
            'reponse'          => $reponse,
            'actions'          => $actions,
            'creer_ticket'     => $urgence !== 'faible',
            'urgence'          => $urgence,
            'categorie_ticket' => $this->detecterCategorie(strtolower($q)),
            'source'           => 'web',
        ]);
    }

    // ─── API : Chat libre (recherche web + réponses locales) ─────────────────

    public function chat(Request $request)
    {
        $request->validate([
            'message'    => 'required|string|min:2|max:1000',
            'historique' => 'nullable|array|max:10',
        ]);

        $msg      = $request->message;
        $msgLower = strtolower($msg);
        $user     = Auth::user();

        // Réponses locales pour les questions communes ITSM
        $reponseLocale = $this->reponseLocale($msgLower, $user);
        if ($reponseLocale) {
            return response()->json(['reponse' => $reponseLocale, 'source' => 'local']);
        }

        // Recherche web pour les autres questions
        $resultats = $this->searchWeb($msg . ' IT support');

        if (!empty($resultats)) {
            $reponse  = "D'après mes recherches concernant **\"{$msg}\"** :\n\n";
            $reponse .= $resultats[0]['snippet'] . "\n\n";
            if (count($resultats) > 1) {
                $reponse .= "Sources complémentaires :\n";
                foreach (array_slice($resultats, 1, 2) as $r) {
                    $reponse .= "• {$r['title']} : {$r['url']}\n";
                }
            }
        } else {
            $reponse = "Je n'ai pas trouvé de résultats précis pour votre question. Je vous recommande de créer un ticket ou de contacter directement l'équipe IT pour une assistance personnalisée.";
        }

        return response()->json(['reponse' => $reponse, 'source' => 'web']);
    }

    // ─── Moteur de recherche web (DuckDuckGo — gratuit, sans clé) ────────────

    private function searchWeb(string $query): array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; SENSTOCK-ITSM/1.0)',
            ])->timeout(8)->get('https://api.duckduckgo.com/', [
                'q'             => $query,
                'format'        => 'json',
                'no_html'       => '1',
                'skip_disambig' => '1',
            ]);

            if (!$response->successful()) return [];

            $data      = $response->json();
            $resultats = [];

            if (!empty($data['AbstractText'])) {
                $resultats[] = [
                    'title'   => $data['Heading'] ?? $query,
                    'snippet' => substr($data['AbstractText'], 0, 250),
                    'url'     => $data['AbstractURL'] ?? 'https://duckduckgo.com/?q=' . urlencode($query),
                ];
            }

            foreach (array_slice($data['RelatedTopics'] ?? [], 0, 6) as $topic) {
                if (!empty($topic['Text']) && !empty($topic['FirstURL'])) {
                    $resultats[] = [
                        'title'   => substr($topic['Text'], 0, 80),
                        'snippet' => substr($topic['Text'], 0, 200),
                        'url'     => $topic['FirstURL'],
                    ];
                }
                // Sous-topics
                if (!empty($topic['Topics'])) {
                    foreach (array_slice($topic['Topics'], 0, 2) as $sub) {
                        if (!empty($sub['Text']) && !empty($sub['FirstURL'])) {
                            $resultats[] = [
                                'title'   => substr($sub['Text'], 0, 80),
                                'snippet' => substr($sub['Text'], 0, 200),
                                'url'     => $sub['FirstURL'],
                            ];
                        }
                    }
                }
            }

            return array_slice($resultats, 0, 6);
        } catch (\Exception $e) {
            return [];
        }
    }

    // ─── Helpers de classification locale ────────────────────────────────────

    private function detecterType(string $text): string
    {
        if (preg_match('/pann|broken|crash|ne fonctionne|ne démarre|bloqué|mort|hs|hors service/', $text)) return 'panne';
        if (preg_match('/incident|anomalie|bug|erreur|problème|lent|lenteur/', $text)) return 'incident';
        if (preg_match('/install|configur|demand|besoin|souhaite|voudrais|nouveau/', $text)) return 'demande';
        return 'incident';
    }

    private function detecterPriorite(string $text): string
    {
        if (preg_match('/urgent|critique|bloquant|production|impossible travailler|tout est|serveur|réseau complet/', $text)) return 'critique';
        if (preg_match('/important|rapide|aujourd\'hui|plusieurs personnes|équipe|département/', $text)) return 'haute';
        if (preg_match('/quand possible|pas urgent|mineur|petit/', $text)) return 'faible';
        return 'normale';
    }

    private function detecterCategorie(string $text): string
    {
        if (preg_match('/réseau|wifi|internet|connexion|ethernet|vpn|ip|dns/', $text)) return 'reseau';
        if (preg_match('/imprimante|scanner|print|imprimer/', $text)) return 'impression';
        if (preg_match('/mot de passe|password|accès|droits|compte|login|identif/', $text)) return 'acces';
        if (preg_match('/logiciel|application|office|outlook|word|excel|installer/', $text)) return 'logiciel';
        if (preg_match('/virus|malware|sécurité|phishing|pirat/', $text)) return 'securite';
        if (preg_match('/ordinateur|pc|laptop|écran|clavier|souris|serveur|disque/', $text)) return 'materiel';
        return 'autre';
    }

    private function calculerUrgence(string $priorite, string $type): int
    {
        $score = 5;
        $score += match($priorite) { 'critique' => 4, 'haute' => 2, 'faible' => -2, default => 0 };
        $score += match($type) { 'panne' => 2, 'incident' => 1, default => 0 };
        return max(1, min(10, $score));
    }

    private function detecterUrgenceFaq(string $text): string
    {
        if (preg_match('/urgent|bloqué|impossible|critique|panne|ne fonctionne/', $text)) return 'haute';
        if (preg_match('/problème|erreur|lent|bug/', $text)) return 'normale';
        return 'faible';
    }

    private function estimerTemps(string $priorite): string
    {
        return match($priorite) {
            'critique' => 'Moins de 4 heures',
            'haute'    => 'Moins de 8 heures',
            'normale'  => 'Moins de 24 heures',
            default    => 'Moins de 72 heures',
        };
    }

    private function outilsParCategorie(string $categorie): array
    {
        return match($categorie) {
            'reseau'     => ['Ping / Tracert', 'ipconfig', 'Gestionnaire réseau Windows'],
            'logiciel'   => ['Panneau de configuration', 'Gestionnaire de tâches', 'Observateur d\'événements'],
            'acces'      => ['Active Directory', 'Gestionnaire d\'utilisateurs', 'Console admin'],
            'materiel'   => ['Gestionnaire de périphériques', 'BIOS', 'Diagnostic constructeur'],
            'impression' => ['Gestionnaire d\'impression Windows', 'Pilotes imprimante'],
            default      => ['Gestionnaire de tâches Windows', 'Observateur d\'événements'],
        };
    }

    private function extraireMotsCles(string $texte): string
    {
        $stopWords = ['le', 'la', 'les', 'de', 'du', 'des', 'un', 'une', 'et', 'ou', 'je', 'mon', 'ma', 'mes', 'ne', 'pas', 'plus', 'est', 'sont'];
        $mots      = explode(' ', preg_replace('/[^a-zA-Z0-9àâäéèêëïîôùûüç ]/u', ' ', $texte));
        $mots      = array_filter($mots, fn($m) => strlen($m) > 3 && !in_array(strtolower($m), $stopWords));
        return implode(' ', array_slice($mots, 0, 5));
    }

    private function reponseLocale(string $msg, $user): ?string
    {
        if (preg_match('/bonjour|salut|hello|bonsoir/', $msg)) {
            return "Bonjour {$user->name} ! Je suis SENIA, votre assistant ITSM. Je peux vous aider à créer des tickets, rechercher des solutions techniques ou analyser vos KPI. Comment puis-je vous aider ?";
        }
        if (preg_match('/sla|délai|temps/', $msg)) {
            return "Les délais SLA de SENSTOCK :\n• **Critique** : 4 heures\n• **Haute** : 8 heures\n• **Normale** : 24 heures\n• **Faible** : 72 heures\n\nCes délais courent dès la création du ticket.";
        }
        if (preg_match('/créer|nouveau|ouvrir.*ticket/', $msg)) {
            return "Pour créer un ticket, rendez-vous dans le menu **Tickets → Nouveau ticket**. Remplissez le titre, la description détaillée et choisissez la priorité. Plus la description est précise, plus l'équipe IT pourra intervenir rapidement.";
        }
        if (preg_match('/statut|avancement|mon ticket/', $msg)) {
            return "Pour suivre vos tickets, allez dans **Tickets → Mes tickets**. Vous verrez le statut en temps réel et vous recevez une notification email à chaque changement.";
        }
        if (preg_match('/merci|thank/', $msg)) {
            return "Avec plaisir {$user->name} ! N'hésitez pas si vous avez d'autres questions.";
        }
        return null;
    }
}