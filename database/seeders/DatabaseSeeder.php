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
        // ─── Administrateur ──────────────────────────────────────────────────
        User::create([
            'name'       => 'Administrateur SENSTOCK',
            'email'      => 'admin@senstock.sn',
            'password'   => Hash::make('Admin@2024!'),
            'role'       => 'administrateur',
            'department' => 'DSI',
            'is_active'  => true,
        ]);

        // ─── Responsable IT ──────────────────────────────────────────────────
        User::create([
            'name'       => 'Mamadou Diallo',
            'email'      => 'responsable-it@senstock.sn',
            'password'   => Hash::make('Senstock@2024!'),
            'role'       => 'responsable_it',
            'department' => 'DSI',
            'phone'      => '+221 77 000 00 01',
            'is_active'  => true,
        ]);

        // ─── Techniciens IT ──────────────────────────────────────────────────
        $techniciens = [
            ['name' => 'Ibrahima Sow',   'email' => 'i.sow@senstock.sn',   'phone' => '+221 77 000 00 02'],
            ['name' => 'Fatou Ndiaye',   'email' => 'f.ndiaye@senstock.sn', 'phone' => '+221 77 000 00 03'],
            ['name' => 'Ousmane Ba',     'email' => 'o.ba@senstock.sn',     'phone' => '+221 77 000 00 04'],
        ];

        foreach ($techniciens as $t) {
            User::create(array_merge($t, [
                'password'   => Hash::make('Senstock@2024!'),
                'role'       => 'technicien',
                'department' => 'DSI',
                'is_active'  => true,
            ]));
        }

        // ─── Utilisateurs ────────────────────────────────────────────────────
        $utilisateurs = [
            ['name' => 'Aissatou Mbaye',   'email' => 'a.mbaye@senstock.sn',   'department' => 'Comptabilité'],
            ['name' => 'Cheikh Guèye',     'email' => 'c.gueye@senstock.sn',   'department' => 'Logistique'],
            ['name' => 'Rokhaya Diop',     'email' => 'r.diop@senstock.sn',    'department' => 'RH'],
            ['name' => 'Modou Fall',       'email' => 'm.fall@senstock.sn',     'department' => 'Commercial'],
            ['name' => 'Ndéye Sarr',       'email' => 'n.sarr@senstock.sn',    'department' => 'Direction'],
        ];

        foreach ($utilisateurs as $u) {
            User::create(array_merge($u, [
                'password'  => Hash::make('Senstock@2024!'),
                'role'      => 'utilisateur',
                'is_active' => true,
            ]));
        }

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
    }
}
