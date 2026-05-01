<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Commentaires sur les tickets
        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->boolean('is_internal')->default(false); // Note interne visible uniquement par l'équipe IT
            $table->timestamps();
        });

        // Pièces jointes
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('filename');           // Nom stocké sur le serveur
            $table->string('original_filename');  // Nom original du fichier
            $table->string('mime_type', 100);
            $table->bigInteger('file_size');      // Taille en octets
            $table->string('path');               // Chemin relatif storage/
            $table->timestamps();
        });

        // Journal d'audit (immuable — pas de updated_at, pas de softDelete)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');                            // Ex: auth.login, ticket.created
            $table->string('model_type')->nullable();            // Ex: App\Models\Ticket
            $table->unsignedBigInteger('model_id')->nullable();  // ID de l'entité concernée
            $table->json('old_values')->nullable();              // Valeurs avant modification
            $table->json('new_values')->nullable();              // Valeurs après modification
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at');                     // Pas de updated_at (immuable)

            // Index pour les recherches dans l'audit
            $table->index(['model_type', 'model_id']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });

        // Notifications Laravel (système natif)
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('ticket_comments');
    }
};
