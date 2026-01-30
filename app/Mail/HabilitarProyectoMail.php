<?php

namespace App\Mail;

use App\Models\Modalidad;
use App\Models\Nivel;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HabilitarProyectoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $asunto;
    public $proyecto;
    public $acta;
    public $comentarios;
    public $campos;

    public $estudiantes;
    public $director;
    public $evaluador;
    public $comite;

    public $tipo_destinatario;
    public $destinatarios;

    public $titulo;
    public $nivel;
    public $modalidad;

    public function __construct($proyecto_id, $acta, $comentarios, $tipo_destinatario)
    {
        $this->asunto = 'PROYECTO DE GRADO HABILITADO';
        $this->proyecto = Solicitud::query()->where('id', '=', $proyecto_id)->first();
        $this->acta = $acta;
        $this->comentarios = $comentarios;
        $this->campos = $this->proyecto->camposConValores();

        $this->estudiantes = [];
        $this->director = null;
        $this->evaluador = null;
        $this->comite = null;

        $this->tipo_destinatario = $tipo_destinatario;
        $this->destinatarios = [];

        $this->titulo = null;
        $this->nivel = null;
        $this->modalidad = null;

        $this->setBody();
    }

    public function setBody()
    {
        $integrante_1 = User::query()->where('id', $this->findCampoByName($this->campos, 'id_integrante_1'))->first();
        $integrante_2 = User::query()->where('id', $this->findCampoByName($this->campos, 'id_integrante_2'))->first();
        $integrante_3 = User::query()->where('id', $this->findCampoByName($this->campos, 'id_integrante_3'))->first();

        if (isset($integrante_1)) $this->estudiantes[] = $integrante_1;
        if (isset($integrante_2)) $this->estudiantes[] = $integrante_2;
        if (isset($integrante_3)) $this->estudiantes[] = $integrante_3;

        $this->comite = User::role('admin')->pluck('email')->toArray();
        $this->comite[] = config('mail.correo_sistemas');

        $director_id = $this->findCampoByName($this->campos, 'director_id');
        $evaluador_id = $this->findCampoByName($this->campos, 'evaluador_id');

        switch ($this->tipo_destinatario) {
            case 'estudiantes':
                foreach ($this->estudiantes as $estudiante) {
                    $this->destinatarios[] = $estudiante->email;
                }
                break;
            case 'director':
                if (isset($evaluador_id) && $evaluador_id) {
                    $this->director = User::query()->where('id', $director_id)->first();
                    $this->destinatarios[] = $this->director->email;
                }
                break;
            case 'evaluador':
                if (isset($evaluador_id) && $evaluador_id) {
                    $this->evaluador = User::query()->where('id', $evaluador_id)->first();
                    $this->destinatarios[] = $this->evaluador->email;
                }
                break;
        }

        $this->destinatarios = array_unique($this->destinatarios);

        $check_idea_banco = $this->findCampoByName($this->campos, 'check_idea_banco');

        if ($check_idea_banco == 'true') {
            $idea_banco = Solicitud::query()->where('id', $this->findCampoByName($this->campos, 'idea_banco'))->first();
            $campos_idea = $idea_banco->camposConValores();
            $this->titulo = $this->findCampoByName($campos_idea, 'titulo');
        } else {
            $this->titulo = $this->findCampoByName($this->campos, 'titulo');
        }

        $this->nivel = Nivel::findOrFail(self::findCampoByName($this->campos, 'nivel'))->nombre;
        $this->modalidad = Modalidad::findOrFail(self::findCampoByName($this->campos, 'modalidad'))->nombre;
    }

    public function findCampoByName($campos, $name)
    {
        foreach ($campos as $item) {
            if (isset($item['campo']['name']) && $item['campo']['name'] === $name) {
                return $item['valor'] ? $item['valor'] : null;
            }
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asunto,
            to: $this->destinatarios,
            cc: $this->comite,
        );
    }

    public function content(): Content
    {
        $with = [
            'acta' => $this->acta,
            'comentarios' => $this->comentarios,
            'titulo' => $this->titulo,
            'nivel' => $this->nivel,
            'modalidad' => $this->modalidad,
        ];

        return new Content(
            view: 'emails.habilitar.template',
            with: $with,
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
