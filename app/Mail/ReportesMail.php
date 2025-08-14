<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $asunto_correo;
    public $cuerpo_correo;

    /**
     * Create a new message instance.
     */
    public function __construct($asunto_correo, $cuerpo_correo)
    {
        $this->asunto_correo = $asunto_correo;
        $this->cuerpo_correo = $cuerpo_correo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $correo_destinatario = $this->cuerpo_correo['email'];

        $correos_cc = User::role('admin')->pluck('email')->toArray();
        $correos_cc[] = env('CORREO_SISTEMAS');

        return new Envelope(
            subject: $this->asunto_correo,
            to: $correo_destinatario,
            cc: $correos_cc,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reportes.template',
            with: [
                'cuerpo_correo' => $this->cuerpo_correo,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
