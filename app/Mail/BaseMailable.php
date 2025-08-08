<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

abstract class BaseMailable extends Mailable implements ShouldQueue
{
    public function queue($queue)
    {
        $batchKey = 'mail_batch_' . now()->format('YmdHi'); // Batch por minuto
        $batchSize = 6; // 6 correos por lote (1 por cada 10 segundos = 1 minuto)

        $position = Cache::increment($batchKey);

        // Calcular delay basado en lotes
        $batchNumber = ceil($position / $batchSize) - 1;
        $positionInBatch = (($position - 1) % $batchSize);

        $delayInSeconds = ($batchNumber * 60) + ($positionInBatch * 15);

        // Limpiar cache después de 1 hora
        Cache::put($batchKey, $position, now()->addHour());

        return Mail::later(
            now()->addSeconds($delayInSeconds),
            $this
        );
    }
}
