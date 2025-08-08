<?php

namespace App\Mail;

use App\Models\Modalidad;
use App\Models\Nivel;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudEstimuloIcfesMail extends BaseMailable
{
    use Queueable, SerializesModels;

    public $asunto;
    public $adjunto;
    public $proyecto;
    public $tipo_destinatario;

    public $campos;
    public $estudiante;
    public $director;
    public $evaluador;
    public $integrantes;

    public $estado_solicitud;
    public $comentarios_comite;

    public $comite;
    public $destinatarios;

    public $titulo;
    public $nivel;
    public $modalidad;
    public $acta;

    /**
     * Create a new message instance.
     */
    public function __construct($proyecto_id, $estudiante_id, $adjunto = null, $tipo_destinatario, $estado_solicitud = null, $comentarios_comite = null, $acta = null)
    {
        $this->asunto = $tipo_destinatario === 'solicitud_estudiante' ? 'SOLICITUD DE ESTIMULO ICFES SABER TYT/PRO' : 'ESTIMULO ICFES PARA ESTUDIANTE';
        $this->adjunto = $adjunto;

        $this->tipo_destinatario = $tipo_destinatario;
        $this->estado_solicitud = $estado_solicitud;
        $this->comentarios_comite = $comentarios_comite;

        $this->proyecto = Solicitud::query()->where('id', '=', $proyecto_id)->first();
        $this->campos = $this->proyecto->camposConValores();
        $this->estudiante = User::query()->where('id', '=', $estudiante_id)->first();

        $this->destinatarios = [];
        $this->integrantes = [];

        $this->nivel = null;
        $this->modalidad = null;
        $this->acta = $acta;
        $this->director = null;
        $this->evaluador = null;
        $this->comite = [];

        $this->getData();
    }

    public function getData()
    {
        $integrante_1 = User::query()->where('id', $this->findCampoByName($this->campos, 'id_integrante_1'))->first();
        $integrante_2 = User::query()->where('id', $this->findCampoByName($this->campos, 'id_integrante_2'))->first();
        $integrante_3 = User::query()->where('id', $this->findCampoByName($this->campos, 'id_integrante_3'))->first();
        
        $this->comite = User::role('admin')->pluck('email')->toArray();
        $this->comite[] = env('CORREO_SISTEMAS');

        if (isset($this->estado_solicitud) && $this->estado_solicitud === 'Aprobado') {
            $this->director = User::findOrFail($this->findCampoByName($this->campos, 'director_id'));
            $this->evaluador = User::findOrFail($this->findCampoByName($this->campos, 'evaluador_id'));
        }

        switch ($this->tipo_destinatario) {
            case 'solicitud_estudiante':
                $this->destinatarios[] = $this->estudiante->email;
                break;
            case 'respuesta_estudiante':
                if (isset($this->estado_solicitud) && $this->estado_solicitud === 'Aprobado') {
                    if (isset($integrante_1)) $this->destinatarios[] = $integrante_1->email;
                    if (isset($integrante_2)) $this->destinatarios[] = $integrante_2->email;
                    if (isset($integrante_3)) $this->destinatarios[] = $integrante_3->email;
                } else {
                    $this->destinatarios[] = $this->estudiante->email;
                }
                break;
            case 'respuesta_docentes':
                $this->destinatarios[] = $this->director->email;
                $this->destinatarios[] = $this->evaluador->email;
                break;
            case 'finalizacion_estudiantes':
                if (isset($integrante_1)) $this->destinatarios[] = $integrante_1->email;
                if (isset($integrante_2)) $this->destinatarios[] = $integrante_2->email;
                if (isset($integrante_3)) $this->destinatarios[] = $integrante_3->email;
                break;
            case 'finalizacion_docentes':
                if (isset($this->director)) $this->destinatarios[] = $this->director->email;
                if (isset($this->evaluador)) $this->destinatarios[] = $this->evaluador->email;
                break;
        }

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
        
        if (isset($integrante_1)) $this->integrantes[] = $integrante_1;
        if (isset($integrante_2)) $this->integrantes[] = $integrante_2;
        if (isset($integrante_3)) $this->integrantes[] = $integrante_3;
    }

    public function findCampoByName($campos, $name)
    {
        foreach ($campos as $item) {
            if (isset($item['campo']['name']) && $item['campo']['name'] === $name) {
                return $item['valor'] ? $item['valor'] : null;
            }
        }
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
        $with = [
            'titulo' => $this->titulo,
            'nivel' => $this->nivel,
            'modalidad' => $this->modalidad,
            'estudiante' => $this->estudiante,
            'tipo_destinatario' => $this->tipo_destinatario,
            'nro_acta' => $this->acta->numero ?? '',
            'fecha_acta' => $this->acta->fecha ?? '',
        ];

        if (isset($this->estado_solicitud) && $this->estado_solicitud === 'Aprobado') {
            $with['director'] = $this->director;
            $with['evaluador'] = $this->evaluador;
            $with['tipo_destinatario'] = $this->tipo_destinatario;
            $with['estado_solicitud'] = $this->estado_solicitud;
            $with['comentarios_comite'] = $this->comentarios_comite;
            $with['integrantes'] = $this->integrantes;
        }

        return new Content(
            view: 'emails.estimulos.icfes.template',
            with: $with,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (isset($this->adjunto)) {
            return [
                Attachment::fromPath($this->adjunto)
            ];
        } else {
            return [];
        }
    }
}
