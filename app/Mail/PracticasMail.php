<?php

namespace App\Mail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PracticasMail extends Mailable
{
    use Queueable, SerializesModels;

    public $asunto_correo;
    public $cuerpo_correo;
    public $comentarios;
    public $tipo_correo;
    public $correos_destinatarios;
    public $esRespuesta;

    public function __construct($asunto_correo, $cuerpo_correo, $comentarios, $tipo_correo, $correos_destinatarios, $esRespuesta)
    {
       /* $correos_cc = User::role('admin')->pluck('email')->toArray();
        $correos_cc[] = config('mail.correo_sistemas');*/

        $this->asunto_correo = $asunto_correo;
        $this->cuerpo_correo = $cuerpo_correo;
        $this->comentarios = $comentarios;
        $this->tipo_correo = $tipo_correo;
        $this->correos_destinatarios = $correos_destinatarios;
        $this->esRespuesta = $esRespuesta;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asunto_correo,
            to: $this->correos_destinatarios,
        );
    }

    public function content(): Content
    {
         $mensaje_adicional = "<p>Esto es un correo generado automáticamente por el sistema de trabajos de grado del programa, favor no responder al mismo.</p>";

        return new Content(
            view: 'emails.practicas.template',
            with: [
                'tipo_correo' => $this->tipo_correo,
                'cuerpo_correo' => $this->cuerpo_correo,
                'comentarios' => $this->comentarios,
                'mensaje_adicional' => $mensaje_adicional,
            ],
        );
    }

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

