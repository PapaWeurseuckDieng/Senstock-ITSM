<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Les commandes Artisan enregistrées.
     */
    protected $commands = [
        Commands\CheckSlaBreaches::class,
    ];

    /**
     * Définit la planification des tâches.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Vérification SLA toutes les heures
        $schedule->command('itsm:check-sla')
                 ->hourly()
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/sla-check.log'));

        // Nettoyage des jobs échoués de plus de 30 jours
        $schedule->command('queue:flush')
                 ->monthly();
    }

    /**
     * Enregistre les commandes Artisan de l'application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
