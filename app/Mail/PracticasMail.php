<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

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

        if (!empty($this->data['adjuntos'])) {

            foreach ($this->data['adjuntos'] as $archivo) {

                $attachments[] =
                    Attachment::fromStorageDisk(
                        'public',
                        $archivo
                    );
            }
        }

        return $attachments;
    }
}