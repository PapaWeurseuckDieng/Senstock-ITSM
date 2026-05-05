<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipements', function (Blueprint $table) {
            $table->id();

            // Identification
            $table->string('code_inventaire')->unique();          // EQ-2024-0001
            $table->string('nom');                                 // Dell Latitude 5420
            $table->enum('categorie', [
                'ordinateur_bureau',
                'ordinateur_portable',
                'serveur',
                'imprimante',
                'switch',
                'routeur',
                'onduleur',
                'iot',
                'telephone_ip',
                'autre',
            ])->default('ordinateur_bureau');

            // Identification matérielle
            $table->string('marque')->nullable();                  // Dell, HP, Lenovo
            $table->string('modele')->nullable();                  // Latitude 5420
            $table->string('numero_serie')->nullable()->unique();
            $table->string('adresse_mac', 17)->nullable();
            $table->string('adresse_ip', 45)->nullable();

            // Affectation
            $table->foreignId('assigned_user_id')                 // Utilisateur affecté
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->string('localisation')->nullable();            // Bureau 2A, Salle serveurs
            $table->string('departement')->nullable();

            // Statut
            $table->enum('statut', [
                'actif',
                'en_maintenance',
                'hors_service',
                'en_stock',
                'retire',
            ])->default('actif');

            // Informations d'achat
            $table->date('date_achat')->nullable();
            $table->decimal('prix_achat', 12, 2)->nullable();
            $table->string('fournisseur')->nullable();
            $table->string('numero_bon_commande')->nullable();

            // Garantie
            $table->date('fin_garantie')->nullable();

            // Specs techniques (JSON flexible)
            $table->json('specifications')->nullable();            // {"ram":"16GB","cpu":"i7","stockage":"512GB SSD"}

            // Notes
            $table->text('notes')->nullable();

            // Gestion
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('categorie');
            $table->index('statut');
            $table->index('assigned_user_id');
            $table->index('fin_garantie');
        });

        // Historique des mouvements d'un équipement
        Schema::create('equipement_historique', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipement_id')->constrained('equipements')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type_mouvement', [
                'affectation',
                'desaffectation',
                'maintenance',
                'retour_stock',
                'mise_hors_service',
                'changement_localisation',
                'autre',
            ]);
            $table->string('ancienne_valeur')->nullable();
            $table->string('nouvelle_valeur')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamp('created_at');

            $table->index(['equipement_id', 'created_at']);
        });

        // Lien entre tickets et équipements
        Schema::create('ticket_equipement', function (Blueprint $table) {
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('equipement_id')->constrained('equipements')->onDelete('cascade');
            $table->primary(['ticket_id', 'equipement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_equipement');
        Schema::dropIfExists('equipement_historique');
        Schema::dropIfExists('equipements');
    }
};
