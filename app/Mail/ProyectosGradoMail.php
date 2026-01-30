<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProyectosGradoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $asunto_correo;
    public $cuerpo_correo;
    public $tipo_correo;
    public $correos_destinatarios;
    public $comentarios;
    public $esRespuesta;

    /**
     * Create a new message instance.
     */
    public function __construct($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta)
    {
        $this->asunto_correo = $asunto_correo;
        $this->cuerpo_correo = $cuerpo_correo;
        $this->comentarios = $comentarios;
        $this->tipo_correo = $tipo_correo;
        $this->correos_destinatarios = $correos_destinatarios;
        $this->esRespuesta = $esRespuesta;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $correos_cc = User::role('admin')->pluck('email')->toArray();
        $correos_cc[] = config('mail.correo_sistemas');

        return new Envelope(
            subject: $this->asunto_correo,
            to: $this->correos_destinatarios,
            cc: $correos_cc,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $mensaje_adicional = "<p>Esto es un correo generado automáticamente por el sistema de trabajos de grado del programa, favor no responder al mismo.</p>";

        return new Content(
            view: 'emails.proyectos.template',
            with: [
                'tipo_correo' => $this->tipo_correo,
                'cuerpo_correo' => $this->cuerpo_correo,
                'comentarios' => $this->comentarios,
                'mensaje_adicional' => $mensaje_adicional,
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
        $adjuntos = [];

        if (isset($this->cuerpo_correo['adjunto']) && is_array($this->cuerpo_correo['adjunto'])) {
            foreach ($this->cuerpo_correo['adjunto'] as $archivo) {
                if (!empty($archivo)) {
                    $adjuntos[] = Attachment::fromPath($archivo);
                }
            }
        }

        return $adjuntos;
    }
}
