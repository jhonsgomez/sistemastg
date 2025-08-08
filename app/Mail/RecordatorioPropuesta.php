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

class RecordatorioPropuesta extends BaseMailable
{
    use Queueable, SerializesModels;

    public $asunto;
    public $destinatarios;
    public $admins;

    public $proyecto;
    public $paraEstudiantes;
    public $campos;

    public $integrante_1;
    public $integrante_2;
    public $integrante_3;

    public $director;
    public $evaluador;

    public $titulo;
    public $nivel;
    public $modalidad;

    public $periodo;
    public $fecha_aprobacion;

    /**
     * Create a new message instance.
     */
    public function __construct($asunto, $proyecto_id, $paraEstudiantes)
    {
        $this->asunto = $asunto;
        $this->destinatarios = null;
        $this->admins = null;

        $this->proyecto = Solicitud::findOrFail($proyecto_id);
        $this->paraEstudiantes = $paraEstudiantes;
        $this->campos = $this->proyecto->camposConValores();

        $this->integrante_1 = null;
        $this->integrante_2 = null;
        $this->integrante_3 = null;

        $this->director = null;
        $this->evaluador = null;

        $this->titulo = null;
        $this->nivel = null;
        $this->modalidad = null;

        $this->periodo = null;
        $this->fecha_aprobacion = null;

        $this->getData();
    }

    public function getData()
    {
        // llenar integrantes y correos
        $this->integrante_1 = User::query()->where('id', $this->findCampoByName($this->campos, 'id_integrante_1'))->first();
        $this->integrante_2 = User::query()->where('id', $this->findCampoByName($this->campos, 'id_integrante_2'))->first();
        $this->integrante_3 = User::query()->where('id', $this->findCampoByName($this->campos, 'id_integrante_3'))->first();

        $this->director = User::query()->where('id', $this->findCampoByName($this->campos, 'director_id'))->first();
        $this->evaluador = User::query()->where('id', $this->findCampoByName($this->campos, 'evaluador_id'))->first();

        $check_idea_banco = $this->findCampoByName($this->campos, 'check_idea_banco');

        if ($check_idea_banco == 'true') {
            $idea_banco = Solicitud::query()->where('id', $this->findCampoByName($this->campos, 'idea_banco'))->first();
            $campos_idea = $idea_banco->camposConValores();
            $this->titulo = $this->findCampoByName($campos_idea, 'titulo');
        } else {
            $this->titulo = $this->findCampoByName($this->campos, 'titulo');
        }

        if ($this->paraEstudiantes) {
            if (isset($this->integrante_1)) {
                $this->destinatarios[] = $this->integrante_1->email;
            }

            if (isset($this->integrante_2)) {
                $this->destinatarios[] = $this->integrante_2->email;
            }

            if (isset($this->integrante_3)) {
                $this->destinatarios[] = $this->integrante_3->email;
            }
        } else {
            if (isset($this->director) && isset($this->evaluador)) {
                $this->destinatarios[] = $this->director->email;
                $this->destinatarios[] = $this->evaluador->email;
            }
        }

        $this->nivel = Nivel::findOrFail(self::findCampoByName($this->campos, 'nivel'))->nombre;
        $this->modalidad = Modalidad::findOrFail(self::findCampoByName($this->campos, 'modalidad'))->nombre;

        $this->periodo = self::findCampoByName($this->campos, 'periodo');
        $this->fecha_aprobacion = self::getFechasByPeriodo($this->periodo)['fecha_aprobacion_propuesta'];

        $this->admins = User::role('admin')->pluck('email')->toArray();
        $this->admins[] = env('CORREO_SISTEMAS');
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
            cc: $this->admins,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.recordatorios.propuestas.template',
            with: [
                'integrante_1' => $this->integrante_1,
                'integrante_2' => $this->integrante_2,
                'integrante_3' => $this->integrante_3,
                'fecha_aprobacion' => $this->fecha_aprobacion,
                'proyecto' => $this->proyecto,
                'periodo' => $this->periodo,
                'titulo' => $this->titulo,
                'nivel' => $this->nivel,
                'modalidad' => $this->modalidad
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
