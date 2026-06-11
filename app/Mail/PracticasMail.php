<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PracticasMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $data
    ) {}

    public function envelope(): Envelope
    {
        $configCorreo = config(
            'practicas.correos.' .
            $this->data['tipo_correo']
        );

        return new Envelope(
            subject: $configCorreo['subject'],
            cc: [
                config('mail.correo_sistemas')
            ],
        );
    }

    public function content(): Content
    {
        $configCorreo = config(
            'practicas.correos.' .
            $this->data['tipo_correo']
        );

        return new Content(
            view: $configCorreo['view'],

            with: [
                'data' => $this->data,
            ],
        );
    }



    public function attachments(): array
    {
        $attachments = [];

        Log::info('INICIO attachments PracticasMail', [
            'tipo_correo' => $this->data['tipo_correo'] ?? null,
            'adjuntos' => $this->data['adjuntos'] ?? [],
        ]);

        if (!empty($this->data['adjuntos'])) {
            foreach ($this->data['adjuntos'] as $archivo) {

                Log::info('Revisando adjunto PracticasMail', [
                    'archivo' => $archivo,
                    'exists' => Storage::disk('public')->exists($archivo),
                    'size' => Storage::disk('public')->exists($archivo) ? Storage::disk('public')->size($archivo) : null,
                ]);

                if (empty($archivo)) {
                    continue;
                }

                if (!Storage::disk('public')->exists($archivo)) {
                    Log::error('Adjunto no encontrado en PracticasMail', [
                        'archivo' => $archivo,
                    ]);
                    continue;
                }

                $attachments[] = Attachment::fromStorageDisk('public', $archivo)
                    ->as(basename($archivo))
                    ->withMime('application/pdf');
            }
        }

        Log::info('FIN attachments PracticasMail', [
            'total_adjuntos' => count($attachments),
        ]);

        return $attachments;
    }

    


}