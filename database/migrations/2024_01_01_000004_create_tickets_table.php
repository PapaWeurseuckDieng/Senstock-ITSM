<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            // Référence unique
            $table->string('ticket_number', 20)->unique();

            // Relations
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');     // Demandeur
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // Technicien

            // Informations du ticket
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['panne', 'incident', 'demande', 'changement'])->default('incident');
            $table->enum('priority', ['critique', 'haute', 'normale', 'faible'])->default('normale');
            $table->enum('status', ['ouvert', 'en_cours', 'en_attente', 'resolu', 'ferme', 'annule'])->default('ouvert');
            $table->string('category')->nullable();

            // Horodatages T0 → T6
            $table->timestamp('t0_created_at')->nullable();   // Création
            $table->timestamp('t1_assigned_at')->nullable();  // Affectation
            $table->timestamp('t2_started_at')->nullable();   // Prise en charge
            $table->timestamp('t3_pending_at')->nullable();   // Mise en attente
            $table->timestamp('t4_resolved_at')->nullable();  // Résolution technicien
            $table->timestamp('t5_validated_at')->nullable(); // Validation utilisateur
            $table->timestamp('t6_closed_at')->nullable();    // Clôture

            // SLA
            $table->unsignedInteger('sla_duration_hours')->default(24);
            $table->timestamp('sla_deadline')->nullable();
            $table->boolean('sla_breached')->default(false);

            // Résolution
            $table->text('resolution_note')->nullable();
            $table->boolean('user_validated')->default(false);
            $table->unsignedTinyInteger('satisfaction_score')->nullable(); // 1 à 5
            $table->text('satisfaction_comment')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index pour les requêtes fréquentes
            $table->index('status');
            $table->index('priority');
            $table->index('type');
            $table->index('sla_deadline');
            $table->index('sla_breached');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
