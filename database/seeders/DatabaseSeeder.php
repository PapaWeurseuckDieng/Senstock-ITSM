<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        

     
        // ─── Tickets de démonstration ─────────────────────────────────────
        $users       = User::where('role', 'utilisateur')->get();
        $technicien  = User::where('role', 'technicien')->first();

        $sampleTickets = [
            [
                'title'       => 'Ordinateur portable ne démarre plus',
                'description' => 'Mon laptop HP ne s\'allume plus depuis ce matin. J\'entends un bip et l\'écran reste noir.',
                'type'        => 'panne',
                'priority'    => 'haute',
                'category'    => 'materiel',
                'status'      => 'en_cours',
                'assigned_to' => $technicien->id,
            ],
            [
                'title'       => 'Impossible d\'accéder à la messagerie Outlook',
                'description' => 'Depuis la mise à jour d\'hier, Outlook affiche une erreur de connexion.',
                'type'        => 'incident',
                'priority'    => 'haute',
                'category'    => 'logiciel',
                'status'      => 'ouvert',
            ],
            [
                'title'       => 'Demande d\'installation de Microsoft Excel',
                'description' => 'J\'ai besoin de Microsoft Excel pour mon travail quotidien. Merci de procéder à l\'installation.',
                'type'        => 'demande',
                'priority'    => 'normale',
                'category'    => 'logiciel',
                'status'      => 'resolu',
                'assigned_to' => $technicien->id,
            ],
            [
                'title'       => 'Panne réseau au bureau 3ème étage',
                'description' => 'Aucune connexion internet dans tout le couloir du 3ème étage. Cela bloque le travail de 8 personnes.',
                'type'        => 'panne',
                'priority'    => 'critique',
                'category'    => 'reseau',
                'status'      => 'en_cours',
                'assigned_to' => $technicien->id,
            ],
        ];

        foreach ($sampleTickets as $idx => $ticketData) {
            $user = $users[$idx % $users->count()];
            Ticket::create(array_merge($ticketData, ['user_id' => $user->id]));
        }

        // --- Parc informatique
        $this->call(EquipementSeeder::class);
    }
}
