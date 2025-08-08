<?php

namespace App\Mail;

use App\Models\Fecha;
use App\Models\Modalidad;
use App\Models\Nivel;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RevisionProyectoMail extends BaseMailable
{
    use Queueable, SerializesModels;

    public $asunto;
    public $perfil;

    public $proyecto;
    public $campos;

    public $integrante_1;
    public $integrante_2;
    public $integrante_3;

    public $director;
    public $evaluador;

    public $destinatarios;
    public $comite;

    public $titulo;
    public $codigo_modalidad;
    public $nivel;
    public $modalidad;

    public $periodo;
    public $fecha_maxima;

    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct($proyecto_id, $perfil)
    {
        $this->asunto = 'RECORDATORIO DE REVISIÓN DEL PROYECTO - DOCENTE ' . strtoupper($perfil);
        $this->perfil = $perfil;

        $this->proyecto = Solicitud::findOrFail($proyecto_id);
        $this->campos = $this->proyecto->camposConValores();

        $this->integrante_1 = null;
        $this->integrante_2 = null;
        $this->integrante_3 = null;

        $this->director = null;
        $this->evaluador = null;

        $this->destinatarios = [];
        $this->comite = null;

        $this->titulo = null;
        $this->codigo_modalidad = null;
        $this->nivel = null;
        $this->modalidad = null;

        $this->periodo = null;
        $this->fecha_maxima = null;

        $this->type = null;

        $this->getData();
    }


    public function getData()
    { 
        $this->integrante_1 = User::query()->where('id', $this->findCampoByName($this->campos, 'id_integrante_1'))->first();
        $this->integrante_2 = User::query()->where('id', $this->findCampoByName($this->campos, 'id_integrante_2'))->first();
        $this->integrante_3 = User::query()->where('id', $this->findCampoByName($this->campos, 'id_integrante_3'))->first();

        $this->director = User::query()->where('id', $this->findCampoByName($this->campos, 'director_id'))->first();
        $this->evaluador = User::query()->where('id', $this->findCampoByName($this->campos, 'evaluador_id'))->first();

        if ($this->perfil === 'director') {
            $this->destinatarios[] = $this->director->email;
        } else if ($this->perfil === 'evaluador') {
            $this->destinatarios[] = $this->evaluador->email;
        }

        $this->comite = User::role('admin')->pluck('email')->toArray();
        $this->comite[] = env('CORREO_SISTEMAS');

        $check_idea_banco = $this->findCampoByName($this->campos, 'check_idea_banco');

        if ($check_idea_banco == 'true') {
            $idea_banco = Solicitud::query()->where('id', $this->findCampoByName($this->campos, 'idea_banco'))->first();
            $campos_idea = $idea_banco->camposConValores();
            $this->titulo = $this->findCampoByName($campos_idea, 'titulo');
        } else {
            $this->titulo = $this->findCampoByName($this->campos, 'titulo');
        }

        $this->codigo_modalidad = $this->findCampoByName($this->campos, 'codigo_modalidad');
        $this->nivel = Nivel::findOrFail(self::findCampoByName($this->campos, 'nivel'))->nombre;
        $this->modalidad = Modalidad::findOrFail(self::findCampoByName($this->campos, 'modalidad'))->nombre;

        $this->periodo = self::findCampoByName($this->campos, 'periodo');

        $estado = $this->proyecto->estado;
        $fase = null;

        if (str_contains($estado, 'Fase')) {
            $estado_arrays = explode(' ', $estado);
            $fase = $estado_arrays[1];

            if (is_numeric($fase)) {
                $fase = (int) $fase;

                if ($fase < 4) {
                    $this->type = 'F-DC-124';
                    $this->fecha_maxima = self::getFechasByPeriodo($this->periodo)['fecha_aprobacion_propuesta'];
                } else {
                    $this->type = 'F-DC-125';
                    $this->fecha_maxima = self::findCampoByName($this->campos, 'fecha_maxima_informe');
                }
            }
        }
    }

    public function findCampoByName($campos, $name)
    {
        foreach ($campos as $item) {
            if (isset($item['campo']['name']) && $item['campo']['name'] === $name) {
                return $item['valor'] ? $item['valor'] : null;
            }
        }
    }

    public function getFechasByPeriodo($periodo)
    {
        $fechas = Fecha::where('periodo', '=', $periodo)->first();
        return $fechas ? $fechas->fechas : null;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asunto,
            to: $this->destinatarios,
            cc: $this->comite,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.recordatorios.revision.template',
            with: [
                'perfil' => $this->perfil,
                'type' => $this->type,
                'integrante_1' => $this->integrante_1,
                'integrante_2' => $this->integrante_2,
                'integrante_3' => $this->integrante_3,
                'director' => $this->director,
                'evaluador' => $this->evaluador,
                'titulo' => $this->titulo,
                'codigo_modalidad' => $this->codigo_modalidad,
                'nivel' => $this->nivel,
                'modalidad' => $this->modalidad,
                'fecha_maxima' => $this->fecha_maxima,
                'periodo' => $this->periodo,
                'proyecto' => $this->proyecto,
            ],
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
