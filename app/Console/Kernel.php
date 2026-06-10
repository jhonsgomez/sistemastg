<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ================= PROYECTOS =================
        $schedule->command('app:recordatorio-revision')->dailyAt('08:00');

        $schedule->command('app:recordatorio-propuesta')->dailyAt('08:00');
        $schedule->command('app:vencimiento-propuesta')->dailyAt('00:00');

        $schedule->command('app:recordatorio-informe')->dailyAt('08:00');
        $schedule->command('app:vencimiento-informe')->dailyAt('00:00');

        // ================= PRÁCTICAS =================
        $schedule->command('app:recordatorio-practica')->dailyAt('08:00');

        $schedule->command('app:enviar-recordatorio-prorroga-practica')->dailyAt('08:05');

        $schedule->command('app:enviar-recordatorio-revision-director-practica')->dailyAt('08:10');

        $schedule->command('app:vencimiento-practica')->dailyAt('00:05');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
