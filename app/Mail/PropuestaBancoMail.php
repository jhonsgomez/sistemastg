<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PropuestaBancoMail extends BaseMailable
{
    use Queueable, SerializesModels;

    public $cuerpo_correo;
    public $tipo_correo;
    public $correo_destinatario;
    public $comentarios;
    public $esRespuesta;

    /**
     * Create a new message instance.
     */
    public function __construct($cuerpo_correo, $comentarios, $tipo_correo, $correo_destinatario, $esRespuesta)
    {
        $this->cuerpo_correo = $cuerpo_correo;
        $this->comentarios = $comentarios;
        $this->tipo_correo = $tipo_correo;
        $this->correo_destinatario = $correo_destinatario;
        $this->esRespuesta = $esRespuesta;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = null;
        $correos_cc = null;

        $correos_lideres = User::role('lider_investigacion')->pluck('email')->toArray();
        $correos_admins = User::role('admin')->pluck('email')->toArray();

        $correos_cc = array_merge($correos_lideres, $correos_admins);
        $correos_cc[] = env('CORREO_SISTEMAS');

        if ($this->esRespuesta) {
            $subject = 'IDEA PARA BANCO ' . $this->cuerpo_correo['periodo_academico'] . ' ' . strtoupper($this->cuerpo_correo['estado_solicitud']);
        } else {
            $subject = 'IDEA PARA BANCO ' . $this->cuerpo_correo['periodo_academico'];
        }

        return new Envelope(
            subject: $subject,
            to: $this->correo_destinatario,
            cc: $correos_cc,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $mensaje_adicional = "<p><strong>NOTA: </strong>Este es un correo informativo del sistema. El líder de investigación será el encargado de realizar la respectiva revisión y dará respuesta a la solicitud realizada por el docente.</p>";

        return new Content(
            view: 'emails.banco.template',
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
        return [];
    }
}
