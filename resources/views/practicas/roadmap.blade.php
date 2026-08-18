<!-- Vista Roadmap - seguimiento de prácticas -->
<x-app-layout>
    <x-slot name="header">
    </x-slot>

    @push('styles')
        <style>
            /* Overlay para todos los modales */
            .modal-overlay {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                background-color: rgba(0, 0, 0, 0.5) !important;
                z-index: 999 !important;
                height: 100%;
            }

            /* El modal debe estar por encima del overlay */
            .fixed.z-50 {
                z-index: 1000 !important;
            }

            /* Contenido del modal */
            .modal-content {
                max-width: 850px !important;
                width: 100% !important;
                padding: 2rem 3rem !important;
                background-color: white !important;
                border-radius: 8px !important;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            }

            /* Botón de cerrar unificado */
            .modal-close-btn-custom {
                position: absolute !important;
                top: 10px !important;
                right: 28px !important;
                background: none !important;
                border: none !important;
                cursor: pointer !important;
                color: #6b7280 !important;
                transition: color 0.3s ease !important;
                z-index: 10;
            }

            .modal-close-btn-custom:hover {
                color: #dc2626 !important;
            }

            /* ========== ANIMACIONES UNIFICADAS PARA MODALES ========== */
            #detailsModal,
            #createModal,
            #responderSolicitudPractica,
            #replySolicitudModal,
            #desactivarProyectoModal,
            #activarProyectoModal,
            #desactivarPracticaModal,
            #activarPracticaModal,
            #warningModal,
            #reporteModal,
            #fase1EstudianteModal,
            #fase1DetailsModal,
            #fase1AdminModal,
            #fase2AdminModal,
            #fase2DetailsModal,
            #fase2EstudianteModal,
            #fase3EstudianteModal,
            #fase3DetailsModal,
            #fase3DirModal,
            #fase4EvaluadorModal,
            #fase4ComiteModal,
            #icfesEstudianteModal,
            #icfesAdminModal,
            #configAdminModal,
            #configModal,
            #fase5EstudianteModal,
            #fase5DetailsModal,
            #fase5DirModal,
            #fase6EvaluadorModal,
            #fase6DetailsModal,
            #fase6ComiteModal,
            #fase7DetailsModal
            {
                visibility: hidden !important;
                opacity: 0 !important;
                transform: translateY(-20px) !important;
                transition: visibility 0.3s ease, opacity 0.3s ease, transform 0.3s ease !important;
                pointer-events: none !important;
            }

            #detailsModal.show,
            #createModal.show,
            #responderSolicitudPractica.show,
            #replySolicitudModal.show,
            #desactivarProyectoModal.show,
            #activarProyectoModal.show,
            #desactivarPracticaModal.show,
            #activarPracticaModal.show,
            #warningModal.show,
            #reporteModal.show,
            #fase1EstudianteModal.show,
            #fase1DetailsModal.show,
            #fase1AdminModal.show,
            #fase2AdminModal.show,
            #fase2DetailsModal.show,
            #fase2EstudianteModal.show,
            #fase3EstudianteModal.show,
            #fase3DetailsModal.show,
            #fase3DirModal.show,
            #fase4EvaluadorModal.show,
            #fase4ComiteModal.show,
            #icfesEstudianteModal.show,
            #icfesAdminModal.show,
            #configAdminModal.show,
            #configModal.show,
            #fase5EstudianteModal.show,
            #fase5DetailsModal.show,
            #fase5DirModal.show,
            #fase6EvaluadorModal.show,
            #fase6DetailsModal.show,
            #fase6ComiteModal.show,
            #fase7DetailsModal.show {
                visibility: visible !important;
                opacity: 1 !important;
                transform: translateY(0) scale(1) !important;
                pointer-events: auto !important;
            }

            /* Estilos para los botones de acción uniformes */
            .btn-action {
                min-width: 40px;
                height: 32px;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                position: relative;
                transition: all 0.3s ease;
                border-radius: 0.5rem;
            }

            .btn-action i {
                font-size: 1rem;
            }

            .btn-action .loading-spinner {
                align-items: center;
                transform: translate(-50%, -50%);
                width: 1rem;
                height: 1rem;
            }

            /* Estilos específicos para calendarModal */
            #calendarModal {
                visibility: hidden !important;
                opacity: 0 !important;
                transform: translateY(-20px) !important;
                transition: visibility 0.3s ease, opacity 0.3s ease, transform 0.3s ease !important;
                pointer-events: none !important;
            }

            #calendarModal.show {
                visibility: visible !important;
                opacity: 1 !important;
                transform: translateY(0) !important;
                pointer-events: auto !important;
            }

            .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
         }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 36px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px !important;
            }

        [type=checkbox]:checked, [type=radio]:checked {
            background-color: #C1D631 !important;
        }
        </style>
    @endPush
    <!-- ==================== ROADMAP LAS FASES ==================== -->
    <div>
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 p-4 border-b">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center">Seguimiento de <span class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase">Prácticas</span></h2>
            <!-- Botones de acción en el header -->
            <div class="flex justify-center items-center space-x-3 buttons-container">
                <!-- Botón Alertas (Rojo) -->
                <button type="button" id="warning" onclick="openWarningModal()"
                    class="btn-action shadow bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded-lg relative">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <svg id="loadingSpinner-warningOpen" style="margin: 4px 1px"
                        class="hidden text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                        <path
                            d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                            stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                        <path
                            d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                            stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"
                            class="text-white">
                        </path>
                    </svg>
                </button>

                @if ($fase_actual >= 5 && $fase_actual <= 6)
                {{-- Botón para ESTUDIANTE (solo si NO ha enviado) --}}
                @if (auth()->user()->hasRole(['estudiante']) && !$yaEnvio && !$esBeneficiario)
                    <button type="button" id="icfes-estudiante-button" onclick="openIcfesEstudianteModal()"
                        class="btn-action shadow bg-green-700 hover:bg-green-800 text-white px-3 py-1 rounded-lg relative">
                        <i class="fa-solid fa-flag-checkered"></i>
                        <svg id="loadingSpinner-icfes-estudiante" style="margin: 4px 1px" class="hidden text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                            <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                        </svg>
                    </button>
                @endif

                    {{-- Botón para ADMIN/COMITÉ (si HAY al menos un estudiante que envió y NO ha sido respondido) --}}
                    @if (auth()->user()->hasRole(['super_admin', 'admin']))
                        <button type="button" id="icfes-admin-button" onclick="openIcfesAdminModal()"
                            class="btn-action shadow bg-green-700 hover:bg-green-800 text-white px-3 py-1 rounded-lg relative">
                            <i class="fa-solid fa-flag-checkered"></i>
                            <svg id="loadingSpinner-icfes-admin" style="margin: 4px 1px" class="hidden text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                            </svg>
                        </button>
                    @endif
                @endif
                
                <!-- Botón Calendario (Verde) -->
                <!--- AQUI SOLO VA LA VARIABLE $fechas--->
                <button type="button" id="calendar" onclick="openCalendarModal(this)"
                    class="btn-action shadow bg-uts-500 hover:bg-uts-800 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                    <i class="fa-regular fa-calendar"></i>
                    <svg class="loading-spinner hidden w-3 h-3 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                        <path
                            d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                            stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path
                            d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                            stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"
                            class="text-white">
                        </path>
                    </svg>
                </button>

                {{-- Botón Configuraciones --}}
                @php
                    $fase_numerica = $fase_actual;
                    $puedeSolicitarRetiro = $fase_numerica >= 1 && $fase_numerica <= 6;
                    $puedeSolicitarCambioDocente = $fase_numerica >= 3 && $fase_numerica <= 6;
                    $puedeSolicitarProrroga = $fase_numerica >= 5 && $fase_numerica <= 6;
                    $tieneSolicitudPendiente = $tiene_solicitud_pendiente ?? false;
                @endphp

                @if (($puedeSolicitarRetiro || $puedeSolicitarCambioDocente || $puedeSolicitarProrroga) && !$tieneSolicitudPendiente)
                    @if (auth()->user()->hasRole(['super_admin', 'admin', 'coordinador']))
                        <button type="button" onclick="openConfigAdminModal()"
                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                            <i class="fa-solid fa-gear"></i>
                        </button>
                    @elseif (auth()->user()->hasRole('estudiante'))
                        <button type="button" onclick="openConfigModal({{ $fase_numerica }})"
                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                            <i class="fa-solid fa-gear"></i>
                        </button>
                    @endif
                @endif

                @if ($tieneSolicitudPendiente && auth()->user()->hasRole('estudiante'))
                    <span class="text-yellow-600 bg-yellow-100 px-2 py-1 rounded text-xs ml-2">
                        ⏳ Solicitud pendiente
                    </span>
                @endif

                <!-- Botón Volver (Verde) -->
@php
    $rutaVolver = route('practicas.index'); // Ruta por defecto
    
    if (auth()->user()->hasRole('director_practica')) {
        $rutaVolver = route('director.practicas.index');
    } elseif (auth()->user()->hasRole('evaluador_practica')) {
        $rutaVolver = route('evaluador.practicas.index');
    }
@endphp

<a href="{{ $rutaVolver }}"
    class="bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg transition flex items-center">
    <i class="fa-solid fa-arrow-rotate-left mr-2"></i>
    <span>Volver</span>
</a>
            </div>
        </div>
        
        <!-- Nota informativa -->
        
        <!-- Roadmap de las Fases de Practicas Empresariales-->
        <div class="px-6">
            <p class="text-gray-700 mt-6">Aquí podrás llevar el seguimiento de tus prácticas empresariales en curso.</p>
            <div class="mt-6 grid w-full grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">

                <!-- ROADMAP FASE 1 F-DC-126 -->
                <div id="fase-1" class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $fase_actual >= 1 ? 'card-activated' : '' }} {{ $fase_actual == 1 ? 'card-activated-animated' : '' }}">
                    <i class="fa-solid fa-file-word" style="font-size: 36px; margin-bottom: 20px;"></i>
                    <span class="text-center font-bold text-lg">Fase 1: F-DC-126</span>
                    <p class="text-center mt-2 text-sm mx-4">El estudiante envía el formato de solicitud de practicantes.</p>

                    @php
                    $tieneEmpresa = false;

                    foreach ($practica->camposConValores() as $campo) {

                        if (($campo['campo'] ?? null) === 'tiene_empresa') {

                            $tieneEmpresa = filter_var(
                                $campo['valor'],
                                FILTER_VALIDATE_BOOLEAN
                            );

                            break;
                        }
                    }
                    @endphp
                    
                    @if ($fase_actual == 1)
                        @php
                            $user = auth()->user();
                            $esEstudiante = $user->hasRole('estudiante');
                            $esComite = $user->hasRole(['super_admin', 'admin', 'coordinador']);
                            $yaEnvio = $submited_fase1 == 'true';
                        @endphp

                        @if ($esEstudiante && !$yaEnvio)
                            <!-- Estudiante: Mostrar botón para enviar SOLO si no ha enviado o fue rechazado -->
                            <div class="flex justify-center items-center mt-3">
                                <button type="button" onclick="openFase1EstudianteModal(this)"
                                    data-tiene-empresa="{{ $tieneEmpresa ? 1 : 0 }}"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-user-pen"></i>
                                </button>
                            </div>
                        @elseif ($esEstudiante && $yaEnvio)
                            <!-- Estudiante: Después de enviar, SOLO puede ver detalles (no puede reenviar hasta que el comité rechace) -->
                            <div class="flex justify-center items-center mt-3">
                                <button type="button" onclick="openFase1DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                                    </svg>
                                </button>
                            </div>
                        @elseif ($esComite)
                            <!-- Comité: Botones con estilos unificados -->
                            <div class="flex justify-center items-center mt-3 gap-2">
                                <!-- Botón Ver con spinner -->
                                <button type="button" onclick="openFase1DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                                    </svg>
                                </button>

                                <!-- Botón Responder con spinner -->
                                @if ($yaEnvio)
                                    <button type="button" onclick="openFase1AdminModal(this)"
                                        class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center">
                                        <i class="fa-solid fa-share"></i>
                                    </button>
                                @endif
                            </div>
                        @endif
                    @elseif ($fase_actual > 1)
                        <!-- Fase ya aprobada: Solo mostrar botón de ver detalles con spinner -->
                        <div class="flex justify-center items-center mt-3">
                            <button type="button" onclick="openFase1DetailsModal(this)"
                                class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                <i class="fa-solid fa-eye"></i>
                                <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- ROADMAP  FASE 2: PAGO  -->
                <div id="fase-2" class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $fase_actual >= 2 ? 'card-activated' : '' }} {{ $fase_actual == 2 ? 'card-activated-animated' : '' }}">
                    <i class="fa-solid fa-circle-check" style="font-size: 36px; margin-bottom: 20px;"></i>

                    <span class="text-center font-bold text-lg">Fase 2: Pago</span>
                    <p class="text-center mt-2 text-sm mx-4">El estudiante sube la liquidación y soporte de pago.</p>

                    @if ($fase_actual == 2)
                        @php
                            $user = auth()->user();
                            $esEstudiante = $user->hasRole('estudiante');
                            $esComite = $user->hasRole(['super_admin', 'admin', 'coordinador']);
                            $yaEnvio = $submited_fase2 == 'true';
                        @endphp

                        @if ($esEstudiante && !$yaEnvio)
                            <!-- Estudiante: Mostrar botón para enviar -->
                            <div class="flex justify-center items-center mt-3">
                                <button type="button" onclick="openFase2EstudianteModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-user-pen"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                                    </svg>
                                </button>
                            </div>
                        @elseif ($esEstudiante && $yaEnvio)
                            <!-- Estudiante: Después de enviar, solo puede ver detalles -->
                            <div class="flex justify-center items-center mt-3">
                                <button type="button" onclick="openFase2DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                                    </svg>
                                </button>
                            </div>
                        @elseif ($esComite)
                            <!-- Comité: Botones Ver y Responder -->
                            <div class="flex justify-center items-center mt-3 gap-2">
                                <!-- Botón Ver con spinner -->
                                <button type="button" onclick="openFase2DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                                    </svg>
                                </button>

                                <!-- Botón Responder con spinner (solo si el estudiante ya envió) -->
                                @if ($yaEnvio)
                                    <button type="button" onclick="openFase2AdminModal(this)"
                                        class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center">
                                        <i class="fa-solid fa-share"></i>
                                        <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        @endif
                    @elseif ($fase_actual > 2)
                        <!-- Fase ya aprobada: Solo mostrar botón de ver detalles con spinner -->
                        <div class="flex justify-center items-center mt-3">
                            <button type="button" onclick="openFase2DetailsModal(this)"
                                class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                <i class="fa-solid fa-eye"></i>
                                <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- FASE 3: PROPUESTA I -->
                <div id="fase-3" class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $fase_actual >= 3 ? 'card-activated' : '' }} {{ $fase_actual == 3 ? 'card-activated-animated' : '' }}">
                    <i class="fa-solid fa-hourglass-half" style="font-size: 36px; margin-bottom: 20px;"></i>
                    
                    <span class="text-center font-bold text-lg">Fase 3: Propuesta I</span>
                    <p class="text-center mt-2 text-sm mx-4">El estudiante envía la propuesta al director.</p>

                    @if ($fase_actual == 3)

                        @php
                            
                            $user = auth()->user();

                            $esComite = $user->hasAnyRole(['super_admin', 'admin', 'coordinador']);
                            $esEstudiante = $user->hasRole('estudiante');

                            $rolVista = $rol_especifico ?? null;

                            $soyDirectorAsignado = isset($director_actual) 
                                && (string) $director_actual === (string) $user->id;

                            $soyEvaluadorAsignado = isset($evaluador_actual) 
                                && (string) $evaluador_actual === (string) $user->id;

                            $esDirector = $rolVista === 'director_practica'
                                && $user->hasRole('director_practica')
                                && $soyDirectorAsignado;

                            $esEvaluador = $rolVista === 'evaluador_practica'
                                && $user->hasRole('evaluador_practica')
                                && $soyEvaluadorAsignado;
                            
                            // Obtener valores desde la práctica
                            $submited_fase3_valor = $practica->valoresCampos->where('campo.name', 'submited_fase3')->first();
                            $estado_director_valor = $practica->valoresCampos->where('campo.name', 'estado_director_fase3')->first();
                            
                            $estudianteYaEnvio = $submited_fase3_valor && $submited_fase3_valor->valor == 'true';
                            $directorYaRespondio = $estado_director_valor && ($estado_director_valor->valor == 'Aprobada' || $estado_director_valor->valor == 'Rechazada' || $estado_director_valor->valor == 'Aplazada');
                            $directorAprobo = $estado_director_valor && $estado_director_valor->valor == 'Aprobada';
                        @endphp

                        {{-- ESTUDIANTE --}}
                        @if ($esEstudiante && !$estudianteYaEnvio)
                            {{-- Estudiante: btn lápiz (puede enviar) --}}
                            <div class="flex justify-center items-center mt-3 gap-2">
                                <button type="button"
                                    onclick="openFase3DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                    </svg>
                                </button>
                                
                                <button type="button"
                                    onclick="openFase3EstudianteModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-user-pen"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </button>
                            </div>
                            
                        @elseif ($esEstudiante && $estudianteYaEnvio)
                            {{-- Estudiante: solo ojo (ya envió, esperando respuesta) --}}
                            <div class="flex justify-center items-center mt-3">
                                <button type="button"
                                    onclick="openFase3DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                    </svg>
                                </button>
                            </div>

                            {{-- DIRECTOR --}}
                        @elseif ($esDirector && $estudianteYaEnvio && !$directorYaRespondio)
                            {{-- Director: ojo + responder --}}
                            <div class="flex justify-center items-center mt-3 gap-2">
                                <button type="button"
                                    onclick="openFase3DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                    </svg>
                                </button>

                                <button type="button"
                                    onclick="openFase3DirModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-share"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                    </svg>
                                </button>
                            </div>

                        @elseif ($esDirector && $directorYaRespondio && !$directorAprobo)
                            {{-- Director después de RECHAZAR: solo ojo --}}
                            <div class="flex justify-center items-center mt-3">
                                <button type="button"
                                    onclick="openFase3DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                    </svg>
                                </button>
                            </div>

                        @elseif ($esDirector && $directorAprobo)
                            {{-- Director después de APROBAR: solo ojo --}}
                            <div class="flex justify-center items-center mt-3">
                                <button type="button"
                                    onclick="openFase3DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                    </svg>
                                </button>
                            </div>

                            {{-- EVALUADOR Y COMITÉ --}}
                        @elseif (($esEvaluador || $esComite) && $estudianteYaEnvio)
                            <div class="flex justify-center items-center mt-3">
                                <button type="button"
                                    onclick="openFase3DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif

                    @elseif ($fase_actual > 3)
                        <div class="flex justify-center items-center mt-3">
                            <button type="button"
                                onclick="openFase3DetailsModal(this)"
                                class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                <i class="fa-solid fa-eye"></i>
                                <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- FASE 4: PROPUESTA II -->
                <div id="fase-4" class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $fase_actual >= 4 ? 'card-activated' : '' }} {{ $fase_actual == 4 ? 'card-activated-animated' : '' }}">
                    <i class="fa-solid fa-paper-plane" style="font-size: 36px; margin-bottom: 20px;"></i>
                    <span class="text-center font-bold text-lg">Fase 4: Propuesta II</span>
                    <p class="text-center mt-2 text-sm mx-4">El evaluador revisa la propuesta del director.</p>
                            
                    @if ($fase_actual == 4)
                        @php
                            $user = auth()->user();

                            $esComite = $user->hasAnyRole(['super_admin', 'admin', 'coordinador']);
                            $esEstudiante = $user->hasRole('estudiante');

                            $rolVista = $rol_especifico ?? null;

                            $soyDirectorAsignado = isset($director_actual) 
                                && (string) $director_actual === (string) $user->id;

                            $soyEvaluadorAsignado = isset($evaluador_actual) 
                                && (string) $evaluador_actual === (string) $user->id;

                            $esDirector = $rolVista === 'director_practica'
                                && $user->hasRole('director_practica')
                                && $soyDirectorAsignado;

                            $esEvaluador = $rolVista === 'evaluador_practica'
                                && $user->hasRole('evaluador_practica')
                                && $soyEvaluadorAsignado;
                            
                            // Obtener el estado del director en Fase 3
                            $estado_director_fase3_valor = $practica->valoresCampos->where('campo.name', 'estado_director_fase3')->first();
                            $estado_evaluador_fase4_valor = $practica->valoresCampos->where('campo.name', 'estado_evaluador_fase4')->first();
                            
                            // El director aprobó en Fase 3 (eso permite que el evaluador vea y responda en Fase 4)
                            $directorAproboFase3 = $estado_director_fase3_valor && $estado_director_fase3_valor->valor == 'Aprobada';
                            
                            $evaluadorYaRespondio = $estado_evaluador_fase4_valor && ($estado_evaluador_fase4_valor->valor == 'Aprobada' || $estado_evaluador_fase4_valor->valor == 'Rechazada' || $estado_evaluador_fase4_valor->valor == 'Aplazada');
                            $evaluadorAprobo = $estado_evaluador_fase4_valor && $estado_evaluador_fase4_valor->valor == 'Aprobada';
                        @endphp

                        
                        {{-- ================= EVALUADOR (puede ver y responder) ================= --}}
                        @if ($esEvaluador && $directorAproboFase3 && !$evaluadorYaRespondio)
                        <div class="flex justify-center items-center mt-3 gap-2">
                            <button type="button"
                            onclick="openFase3DetailsModal(this)"
                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center">
                            <i class="fa-solid fa-eye"></i>
                            <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                    </svg>
                                </button>
                                
                                <button type="button"
                                onclick="openFase4EvaluadorModal(this)"
                                class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center">
                                <i class="fa-solid fa-share"></i>
                                <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                </svg>
                            </button>
                        </div>


                        {{-- ================= EVALUADOR (ya respondió, solo ver) ================= --}}
                        @elseif ($esEvaluador && $evaluadorYaRespondio)
                        <div class="flex justify-center items-center mt-3">
                            <button type="button"
                            onclick="openFase3DetailsModal(this)"
                            class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                            <i class="fa-solid fa-eye"></i>
                            <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                            </svg>
                                    </button>
                                </div>
                                
                                {{-- ================= DIRECTOR ================= --}}
                                @elseif ($esDirector && !$esComite)
                                    <div class="flex justify-center items-center mt-3">
                                        <button type="button"
                                            onclick="openFase3DetailsModal(this)"
                                            class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                            <i class="fa-solid fa-eye"></i>
                                            <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                                <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                            </svg>
                                        </button>
                                    </div>
                                
                        {{-- ================= COMITÉ FASE 4 ================= --}}
                        @elseif ($esComite && $evaluadorAprobo)
                            <div class="flex justify-center items-center mt-3 gap-2">

                                {{-- VER --}}
                                <div class="flex justify-center items-center">
                            <button type="button"
                            onclick="openFase3DetailsModal(this)"
                            class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                            <i class="fa-solid fa-eye"></i>
                            <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                            </svg>
                                    </button>
                                </div>

                                {{-- EDITAR / RESPONDER --}}
                            
                                <button type="button"
                                    onclick="openFase4ComiteModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg  relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-share"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                    </svg>
                                </button>

                                
                                

                            </div>

                        {{-- ================= ESTUDIANTE ================= --}}
                        @elseif ($esEstudiante && $evaluadorAprobo)
                            <div class="flex justify-center items-center mt-3">
                                <button type="button"
                                    onclick="openFase3DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        @endif
                        
                
                    @elseif ($fase_actual > 4)
                        <div class="flex justify-center items-center mt-3">
                            <button type="button"
                                onclick="openFase3DetailsModal(this)"
                                class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                <i class="fa-solid fa-eye"></i>
                                <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                    
                    
                </div>

                <!-- FASE 5. INFORME I -->
                <div class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $fase_actual >= 5 ? 'card-activated' : '' }} {{ $fase_actual == 5 ? 'card-activated-animated' : '' }}">
                    <i class="fa-solid fa-hourglass-half" style="font-size: 36px; margin-bottom: 20px;"></i>
                    <span class="text-center font-bold text-lg">Fase 5: Informe I</span>
                    <p class="text-center mt-2 text-sm mx-4">El estudiante envía el informe al director.</p>

                        @if ($fase_actual == 5)
                        @php
                            $user = auth()->user();

                            $esEstudiante = $user->hasRole('estudiante');
                            $esComite = $user->hasAnyRole(['super_admin', 'admin', 'coordinador']);

                            $rolVista = $rol_especifico ?? null;

                            $soyDirectorAsignado = isset($director_actual) 
                                && (string) $director_actual === (string) $user->id;

                            $esDirector = $rolVista === 'director_practica'
                                && $user->hasRole('director_practica')
                                && $soyDirectorAsignado;
                            $yaEnvio = $submited_fase5 == 'true';
                            $estadoDirector = $practica->valoresCampos
                                ->where('campo.name', 'estado_director_fase5')
                                ->first();

                            $directorRespondio = $estadoDirector && !empty($estadoDirector->valor);
                        @endphp

                        {{-- ESTUDIANTE --}}
                        @if ($esEstudiante && !$yaEnvio)
                            <div class="flex justify-center items-center mt-3">
                                <button type="button"
                                    onclick="openFase5EstudianteModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-user-pen"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute"
                                        viewBox="0 0 64 64" fill="none">
                                        <path d="M32 3..." />
                                    </svg>
                                </button>
                            </div>

                        @elseif ($esEstudiante && $yaEnvio)

                            <div class="flex justify-center items-center mt-3">
                                <button type="button"
                                    onclick="openFase5DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                    </svg>
                                </button>
                            </div>

                        {{-- DIRECTOR --}}
                        @elseif ($esDirector)

                            <div class="flex justify-center items-center mt-3 gap-2">
                                {{-- VER --}}
                                <button type="button"
                                    onclick="openFase5DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                    </svg>
                                </button>

                                {{-- RESPONDER --}}
                                @if ($yaEnvio && !$directorRespondio)

                                    <button type="button"
                                        onclick="openFase5DirModal(this)"
                                        class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center">
                                        <i class="fa-solid fa-share"></i>
                                    </button>
                                @endif
                            </div>
                        @endif

                    @elseif ($fase_actual > 5)

                        {{-- SOLO VER --}}
                        <div class="flex justify-center items-center mt-3">
                            <button type="button"
                                onclick="openFase5DetailsModal(this)"
                                class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                                <i class="fa-solid fa-eye"></i>
                                <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                </svg>
                            </button>

                        </div>

                    @endif
                </div>

        
                <!-- FASE 6: INFORME II -->
                <div id="fase-6" class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase
                    {{ $fase_actual >= 6 ? 'card-activated' : '' }}
                    {{ $fase_actual == 6 ? 'card-activated-animated' : '' }}">

                    <i class="fa-solid fa-paper-plane" style="font-size: 36px; margin-bottom: 20px;"></i>

                    <span class="text-center font-bold text-lg">Fase 6: Informe II</span>

                    <p class="text-center mt-2 text-sm mx-4">
                        El director envía el informe al evaluador y comité.
                    </p>

                    @if ($fase_actual == 6)

                        @php
                            $user = auth()->user();

                            $esComite = $user->hasAnyRole(['super_admin', 'admin', 'coordinador']);
                            $esEstudiante = $user->hasRole('estudiante');

                            $rolVista = $rol_especifico ?? null;

                            $soyDirectorAsignado = isset($director_actual) 
                                && (string) $director_actual === (string) $user->id;

                            $soyEvaluadorAsignado = isset($evaluador_actual) 
                                && (string) $evaluador_actual === (string) $user->id;

                            $esDirector = $rolVista === 'director_practica'
                                && $user->hasRole('director_practica')
                                && $soyDirectorAsignado;

                            $esEvaluador = $rolVista === 'evaluador_practica'
                                && $user->hasRole('evaluador_practica')
                                && $soyEvaluadorAsignado;

                            $estado_director_fase6_valor = $practica->valoresCampos
                                ->where('campo.name', 'estado_director_fase5')
                                ->first();

                            $estado_evaluador_fase6_valor = $practica->valoresCampos
                                ->where('campo.name', 'estado_evaluador_fase6')
                                ->first();

                            $directorAproboFase6 = $estado_director_fase6_valor &&
                                $estado_director_fase6_valor->valor == 'Aprobada';

                            $evaluadorYaRespondio = $estado_evaluador_fase6_valor &&
                                in_array($estado_evaluador_fase6_valor->valor, ['Aprobada', 'Rechazada', 'Aplazada']);

                            $evaluadorAprobo = $estado_evaluador_fase6_valor &&
                                $estado_evaluador_fase6_valor->valor == 'Aprobada';
                        @endphp


                        <div class="flex justify-center items-center mt-3 gap-2">

                            {{-- ================= EVALUADOR (PRIORIDAD ALTA) ================= --}}
                            @if ($esEvaluador && $directorAproboFase6)

                                <button type="button"
                                    onclick="openFase6DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                    </svg>
                                </button>

                                @if (!$evaluadorYaRespondio)
                                    <button type="button"
                                        onclick="openFase6EvaluadorModal(this)"
                                        class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-share"></i>
                                    </button>
                                @endif


                            {{-- ================= DIRECTOR ================= --}}
                            @elseif ($esDirector && !$esComite)

                                <button type="button"
                                    onclick="openFase5DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                    </svg>
                                </button>


                            {{-- ================= COMITÉ ================= --}}
                            @elseif ($esComite && $evaluadorAprobo)

                                <button type="button"
                                    onclick="openFase6DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                    </svg>
                                </button>

                                <button type="button"
                                    onclick="openFase6ComiteModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-share"></i>
                                </button>


                            {{-- ================= ESTUDIANTE ================= --}}
                            @elseif ($esEstudiante)

                                <button type="button"
                                    onclick="openFase6DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                    <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                    </svg>
                                </button>

                            @endif

                        </div>

                    @elseif ($fase_actual > 6)

                        <div class="flex justify-center items-center mt-3">

                            <button type="button"
                                onclick="openFase6DetailsModal(this)"
                                class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-eye"></i>
                                <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                </svg>
                            </button>

                        </div>

                    @endif

                </div>

                <!-- FASE FINAL -->
                <div class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $fase_actual >= 7 ? 'card-activated' : '' }} {{ $fase_actual == 7 ? 'card-activated-animated' : '' }}">

                    <i class="fa-solid fa-user-graduate" style="font-size: 36px; margin-bottom: 20px;"></i>
                    <span class="text-center font-bold text-lg">Fase Final</span>
                    <p class="text-center mt-2 text-sm mx-4">Estudiantes, director y evaluador programan sustentación.</p>

                    @if($fase_actual >= 7)

                        <div class="flex justify-center items-center mt-3 gap-2">
                            <button type="button"
                                onclick="openFase7DetailsModal(this)"
                                class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-eye"></i>
                                <svg class="loading-spinner hidden text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                </svg>
                            </button>
                        </div>

                    @endif

                </div>
            </div>
        </div>

    </div>

    <!-- ==================== MODALES DE LAS FASES ==================== -->

    <!-- MODAL FASE 1 - Estudiante (Enviar documentos) -->
    <div id="fase1EstudianteModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0"
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;"
            onclick="closeFase1EstudianteModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">
                    <button
                        class="modal-close-btn-custom absolute mt-2 top-4 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                        onclick="closeFase1EstudianteModal()">&times;</button>
                    <form class="p-6 mt-2" id="fase1EstudianteForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="practica_id" value="{{ $practica->id }}">
                        <p class="text-2xl font-bold mb-4 d-flex">Prácticas <span
                                class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Fase 1</span></p>
                        <p class="font-medium text-sm text-gray-700 mb-4">En este formulario el estudiante enviará el
                            formato de solicitud de practicantes. Dicho formato podrá descargarlo desde el siguiente enlace. <a href="https://www.dropbox.com/scl/fo/pudgcaq639agy7t06ahjs/AF-dMByc71OWsxcfY2dLe3A?rlkey=6s0b9ajweteyx2ang7ywvk6xm&e=1&dl=0" target="_blank" class="text-uts-500 underline hover:text-uts-800">Consultar Base Documental.</a></p>

                        <div class="grid grid-cols-1 gap-6 mb-4">
                            <!-- Checkbox Práctica institucional -->
                            <div id="contenedorPracticaInstitucional">
                                <div class="flex items-center gap-1">
                                    <label class="flex items-center gap-1">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                        <span class="text-red-500"></span></label>
                                        <span class="text-sm font-medium text-gray-700">¿Es práctica institucional?</span>
                                        <div class="relative inline-block mr-1">
                                            <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                                data-tooltip="tooltip-institucional"></i>
                                            <div id="tooltip-institucional"
                                                class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                                Marque si la práctica se realizará en la UTS con previa autorización del
                                                coordinador.
                                            </div>
                                        </div>
                                        <input type="checkbox" name="es_institucional" id="es_institucional"
                                            class="rounded border-gray-300 text-uts-500 focus:ring-uts-500"
                                            onchange="toggleNombreEmpresa()">
                                    </label>
                                </div>
                            </div>

                            <!-- Campo Nombre de la empresa (condicional) -->
                            <div id="nombre_empresa_container" style="display: none;">
                                <div class="flex items-center gap-1 mb-2">
                                    <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                    <span class="text-red-500">*</span></label>
                                    <label class="block font-medium text-sm text-gray-700">Nombre de la empresa: </label>
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-empresa"></i>

                                        <div id="tooltip-empresa"
                                            class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                            Ingrese el nombre de la empresa donde realizará la práctica.
                                        </div>
                                    </div>
                                </div>
                                <input type="text" name="nombre_empresa" id="nombre_empresa"
                                    placeholder="Escribe el nombre de la empresa"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                            </div>

                            <!-- Campo para subir F-DC-126 -->
                            <div>
                                <div class="flex items-center gap-1 mb-2">
                                    <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                    <span class="text-red-500">*</span>
                                    <label class="block font-medium text-sm text-gray-700">Formato F-DC-126: </label>
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-fdc126"></i>
                                            <div id="tooltip-fdc126"
                                                class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                                Suba el formato F-DC-126 diligenciado (.doc, .docx o .pdf, máximo {{ config('practicas.peso_maximo_archivos') }} MB).
                                            </div>
                                    </div>
                                </div>

                                <div
                                    class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border-2 border-gray-300 gap-3 grid border-dashed">
                                    <div class="grid gap-1">
                                        <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                        <h2 class="text-center text-gray-400 text-xs leading-4">
                                            Solo archivos de WORD o PDF de máximo {{ config('practicas.peso_maximo_archivos') }} MB
                                        </h2>
                                    </div>
                                    <div class="grid gap-2">
                                        <h4 class="text-center text-gray-900 text-sm font-medium">Arrastra o carga tu
                                            archivo aquí</h4>
                                        <div class="flex items-center justify-center">
                                            <input type="file" name="doc_fdc126" id="doc_fdc126"
                                                class="absolute inset-0 opacity-0 cursor-pointer"
                                                accept=".doc,.docx,.pdf" />
                                            <div
                                                class="flex w-28 h-9 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer">
                                                Cargar</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 mb-4">
                                    <span id="doc_fdc126Error" class="block text-red-500 text-sm mb-2"></span>

                                    <ul id="file-list-fase1" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                                </div>

                                <p class="text-red-600 text-sm mb-4">
                                    <i class="fa-solid fa-circle-info mr-1"></i> Si selecciona la opción de práctica institucional tenga en cuenta de que previamente debe estar aprobado por la coordinación.
                                </p>

                                <p class="text-sm mb-4 text-gray-600 text-center"><strong>NOTA:</strong> El formato F-DC-126 debe estar debidamente diligenciado y firmado.</p>
                                
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2 mt-4">
                            <button type="button" onclick="closeFase1EstudianteModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cancelar</button>
                            <button type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-fase1" style="margin: 4px 10px 4px 0"
                                    class="hidden text-gray-300 animate-spin w-4 h-4" viewBox="0 0 64 64"
                                    fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round"
                                        stroke-linejoin="round" class="text-white"></path>
                                </svg>
                                Enviar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MODAL FASE 1 - Detalles (Ver información enviada) -->
    <div id="fase1DetailsModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" onclick="closeFase1DetailsModal()">
            <div class="flex items-center justify-center min-h-screen text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">
                    <button
                        class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                        onclick="closeFase1DetailsModal()">&times;</button>
                    <div class="p-6 mt-2">
                        <p class="text-2xl font-bold mb-4">Detalles de la <span
                                class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Práctica</span></p>
                        <div id="fase1DetailsContent" class="space-y-3">
                            <div class="text-center py-4">
                                <svg class="inline w-8 h-8 text-gray-400 animate-spin" viewBox="0 0 64 64"
                                    fill="none">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button type="button" onclick="closeFase1DetailsModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FASE 1 - Administrador (Responder solicitud) -->
    <div id="fase1AdminModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0"  style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase1AdminModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">
                    <button
                        class="modal-close-btn-custom absolute top-4 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                        onclick="closeFase1AdminModal()">&times;</button>
                    <form class="p-6 mt-2" id="fase1AdminForm">
                        @csrf
                        <input type="hidden" name="practica_id" value="{{ $practica->id }}">
                        <p class="text-2xl font-bold mb-6">Prácticas <span
                                class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Fase 1</span></p>
                        </p>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700"> 
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Estado de la práctica:
                            </label>
                            <select name="estado" id="estado_fase1"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <option value="">Seleccione un estado</option>
                                <option value="Aprobada">Aprobada</option>
                                <option value="Aplazada">Aplazada</option>
                                <option value="Rechazada">Rechazada</option>
                            </select>
                            <span id="estado_fase1Error" class="text-red-500 text-sm"></span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>    
                                    Número de acta:
                                </label>
                                <input type="number" name="nro_acta" id="nro_acta_fase1"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500" placeholder="Ingrese el número de acta">
                                <span id="nro_acta_fase1Error" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-calendar-days mr-1 text-gray-500"></i>
                                    Fecha del acta:
                                </label>
                                <input type="date" name="fecha_acta" id="fecha_acta_fase1"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <span id="fecha_acta_fase1Error" class="text-red-500 text-sm"></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Comentarios de la respuesta:
                            </label>
                            <div id="txt-editor-fase1" class="shadow txt-editor-quill" style="height: 200px; background: white;"></div>
                            <textarea name="respuesta_fase1" id="respuesta_fase1" class="hidden"></textarea>
                            <span id="respuesta_fase1Error" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="flex justify-end space-x-2 mt-4">
                            <button type="button" onclick="closeFase1AdminModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cancelar</button>
                            <button type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-fase1-admin" style="margin: 4px 10px 4px 0"
                                    class="hidden text-gray-300 animate-spin w-4 h-4" viewBox="0 0 64 64"
                                    fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" class="text-white"></path>
                                </svg>
                                Responder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FASE 2 - Estudiante (Enviar documentos de pago) -->
    <div id="fase2EstudianteModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase2EstudianteModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full modal-content relative"
                    onclick="event.stopPropagation()" style="max-width: 900px !important; width: 100%;">
                    
                    <button class="modal-close-btn-custom absolute top-4 right-4 text-gray-400 hover:text-red-500 text-2xl z-10 mt-2"
                        onclick="closeFase2EstudianteModal()">&times;</button>

                    <form class="p-6 mt-2" id="fase2EstudianteForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="practica_id" value="{{ $practica->id }}">

                        <!-- Título -->
                        <div class="mb-4 pr-4">
                            <p class="text-2xl font-bold" id="fase2EstudianteTitle"> 
                            <p class="text-gray-950 mt-6 text-sm">En este formulario el estudiante deberá subir la
                                liquidación de pago y el soporte correspondiente.</p>
                        </div>

                        <p class="text-red-600 text-sm mb-4">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                           Por cada integrante del proyecto se
                            debe cargar la liquidación y los respectivos soportes de pago en un mismo documento respectivamente.
                        </p>

                        <!-- Documentos y enlaces informativos -->
                        <div class="flex items-center my-5">
                            <i class="fa-regular fa-bookmark mr-1 text-gray-500 text-sm"></i>
                            <p class="font-medium text-gray-700 flex items-center gap-2 text-sm">
                                <span class="text-red-500">*</span>
                                Documentos (Liquidaciones y soportes)
                            </p>
                        </div>
                        <ul class="space-y-2 text-sm mb-4 list-disc pt-4 text-gray-500">
                            <li class="flex items-center gap-2 flex-wrap">
                                <span class="text-gray-600">Instructivo para pagar la liquidación:</span>
                                <a href="{{ asset('ejemplos/fase_1-1.pdf') }}" target="_blank"
                                    class="text-blue-600 hover:text-blue-800 underline flex items-center gap-1">ABRIR ARCHIVO</a>
                            </li>
                            <li class="flex items-center gap-2 flex-wrap">
                                <span class="text-gray-600">Liquidación con marca de agua (Ejemplo):</span>
                                <a href="{{ asset('ejemplos/fase_1-2.pdf') }}" target="_blank"
                                    class="text-blue-600 hover:text-blue-800 underline flex items-center gap-1">ABRIR ARCHIVO</a>
                            </li>
                        </ul>

                        <!-- Campos en GRID para desktop (2 columnas) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Campo Liquidación de pago -->
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <label class="block font-medium text-sm text-gray-700">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                        <span class="text-red-500">*</span> Liquidación de pago: 
                                    </label>

                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-liquidacion-fase2"></i>

                                        <div id="tooltip-liquidacion-fase2"
                                            class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                            Suba la liquidación generada del pago de modalidad. El documento debe incluir la
                                            marca de agua correspondiente.
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                    
                                    <div class="grid gap-1">
                                        <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                        <h2 class="text-center text-gray-400 text-xs leading-4">
                                            Solo archivos de PDF de máximo {{ config('practicas.peso_maximo_archivos') }} MB
                                        </h2>
                                    </div>

                                    <div class="grid gap-2">
                                        <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>

                                        <div class="flex items-center justify-center">
                                            <input type="file" name="liquidacion_pago" id="liquidacion_pago"
                                                class="absolute inset-0 opacity-0 cursor-pointer w-full" accept=".pdf" />

                                            <div
                                                class="flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                                Cargar
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 mb-4">
                                    <span id="liquidacion_pagoError" class="block text-red-500 text-sm mb-2"></span>

                                    <ul id="file-list-liquidacion" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                                </div>

                            </div>

                            <!-- Campo Soporte de pago -->
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <label class="block font-medium text-sm text-gray-700">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i><span
                                            class="text-red-500">*</span> Soporte de pago: 
                                    </label>
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-soporte-fase2"></i>

                                        <div id="tooltip-soporte-fase2"
                                            class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                            Suba el soporte de pago correspondiente a la liquidación.
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                    
                                    <div class="grid gap-1">
                                        <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                        <h2 class="text-center text-gray-400 text-xs leading-4">
                                            Solo archivos de PDF de máximo {{ config('practicas.peso_maximo_archivos') }} MB
                                        </h2>
                                    </div>

                                    <div class="grid gap-2">
                                        <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>

                                        <div class="flex items-center justify-center">
                                            <input type="file" name="soporte_pago" id="soporte_pago"
                                                class="absolute inset-0 opacity-0 cursor-pointer w-full" accept=".pdf" />

                                            <div
                                                class="flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                                Cargar
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <span id="soporte_pagoError" class="block text-red-500 text-sm mb-2"></span>

                                    <ul id="file-list-soporte" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                                </div>
                                
                            </div>

                        </div>

                        <div class="flex items-start">
                            <p class="font-medium text-sm text-gray-700">
                            <span class="mr-1 mt-1 inline-block align-middle">
                                <i class="fa-solid fa-circle-info text-uts-500 text-xl"></i>
                            </span>    
                            <strong>NOTA:</strong>
                            <span class="text-gray-500">
                                    En caso de que el estudiante desee sugerir un director de trabajo de
                                    grado, deberá adjuntar una página adicional en el archivo PDF correspondiente a los pagos de
                                    la modalidad, indicando de manera formal el nombre del docente que desea sugerir como
                                    director de trabajo de grado. El comité evaluará la sugerencia y responderá al estudiante.
                            </span>
                            </p>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="closeFase2EstudianteModal()"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-lg transition">Cancelar</button>
                            <button type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-5 py-2 rounded-lg transition items-center gap-2">
                                <svg id="loadingSpinner-fase2" class="hidden text-white animate-spin w4 h-4"
                                    viewBox="0 0 64 64" fill="none">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" class="text-white"></path>
                                </svg>
                                Enviar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FASE 2 - Detalles (Ver información enviada) -->
    <div id="fase2DetailsModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" onclick="closeFase2DetailsModal()">
            <div class="flex items-center justify-center min-h-screen text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">
                    <button
                        class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                        onclick="closeFase2DetailsModal()">&times;</button>
                    <div class="p-6 mt-2">
                        <p class="text-2xl font-bold mb-4">Detalles de la <span
                                class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Práctica</span></p>
                        <div id="fase2DetailsContent" class="space-y-3">
                            <div class="text-center py-4">
                                <svg class="inline w-8 h-8 text-gray-400 animate-spin" viewBox="0 0 64 64"
                                    fill="none">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button type="button" onclick="closeFase2DetailsModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FASE 2 - Administrador (Responder solicitud + Asignar docentes) -->
    <div id="fase2AdminModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;"  onclick="closeFase2AdminModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">
                    <button
                        class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                        onclick="closeFase2AdminModal()">&times;</button>
                    <form class="p-6 mt-2" id="fase2AdminForm">
                        @csrf
                        <input type="hidden" name="practica_id" value="{{ $practica->id }}">
                        <p class="text-2xl font-bold mb-4">Prácticas <span
                                class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Fase 2</span></p>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700"> 
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Estado de la práctica:
                            </label>
                            <select name="estado" id="estado_fase2"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <option value="">Seleccione un estado</option>
                                <option value="Aprobada">Aprobada</option>
                                <option value="Aplazada">Aplazada</option>
                                <option value="Rechazada">Rechazada</option>
                            </select>
                            <span id="estado_fase2Error" class="text-red-500 text-sm"></span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>    
                                    Número de acta:
                                </label>
                                <input type="number" name="nro_acta" id="nro_acta_fase2"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500" placeholder="Ingrese el número de acta">
                                <span id="nro_acta_fase2Error" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-calendar-days mr-1 text-gray-500"></i>
                                    Fecha del acta:
                                </label>
                                <input type="date" name="fecha_acta" id="fecha_acta_fase2"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <span id="fecha_acta_fase2Error" class="text-red-500 text-sm"></span>
                            </div>
                        </div>

                        <!-- Código de modalidad (se muestra solo si aprueba) -->
                        <div class="mb-4 hidden" id="container_codigo_modalidad_fase2">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-hashtag mr-1 text-gray-500"></i>
                                Código de modalidad:
                            </label>
                            <input type="text" name="codigo_modalidad" id="codigo_modalidad_fase2"
                                class="bg-gray-100 border-gray-300 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500 cursor-default"
                                value="{{ $codigo_modalidad_generado ?? '' }}"
                                placeholder="Se generará automáticamente">
                            <span id="codigo_modalidad_fase2Error" class="text-red-500 text-sm"></span>
                        </div>
                        
                        <!-- Contenedor para asignación de docentes (se muestra solo si selecciona Aprobada) -->
                        <div id="container_docentes_fase2">

                            <div class="mb-4">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Asigne el director del proyecto:
                                </label>
                                <select name="director_id"
                                        id="director_id_fase2"
                                        class="w-full mt-1">
                                    <option value=""></option>

                                    @foreach ($docentes as $docente)
                                        <option value="{{ $docente->id }}">
                                            {{ $docente->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="director_id_fase2Error" class="text-red-500 text-sm"></span>
                            </div>

                            <div class="mb-4">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Asigne el evaluador del proyecto:
                                </label>
                                                                <select name="evaluador_id"
                                        id="evaluador_id_fase2"
                                        class="w-full mt-1">
                                    <option value=""></option>

                                    @foreach ($docentes as $docente)
                                        <option value="{{ $docente->id }}">
                                            {{ $docente->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="evaluador_id_fase2Error" class="text-red-500 text-sm"></span>
                            </div>

                            <div class="mb-4">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Asigne el codirector del proyecto (opcional):
                                </label>
                                <select name="codirector_id"
                                        id="codirector_id_fase2"
                                        class="w-full mt-1">
                                    <option value=""></option>

                                    @foreach ($docentes as $docente)
                                        <option value="{{ $docente->id }}">
                                            {{ $docente->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="codirector_id_fase2Error" class="text-red-500 text-sm"></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Comentarios de la respuesta:
                            </label>
                            <div id="txt-editor-fase2" class="shadow txt-editor-quill" style="height: 200px; background: white;"></div>
                            <textarea name="respuesta" id="respuesta_fase2" class="hidden"></textarea>
                            <span id="respuesta_fase2Error" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="flex justify-end space-x-2 mt-4">
                            <button type="button" onclick="closeFase2AdminModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cancelar</button>
                            <button type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-fase2-admin" style="margin: 4px 10px 4px 0"
                                    class="hidden text-gray-300 animate-spin w-4 h-4" viewBox="0 0 64 64"
                                    fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" class="text-white"></path>
                                </svg>
                                Responder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- MODAL FASE 3 - Estudiante (Enviar documentos de trabajo de grado) -->
    <div id="fase3EstudianteModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto; background-color: rgba(0, 0, 0, 0.5);" onclick="closeFase3EstudianteModal()">
            <div class="flex items-center justify-center min-h-screen text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full modal-content relative"
                    onclick="event.stopPropagation()" style="max-width: 900px !important; width: 100%;">
                    
                    <button class="modal-close-btn-custom absolute top-4 right-4 text-gray-400 hover:text-red-500 text-2xl z-10 mt-2"
                        onclick="closeFase3EstudianteModal()">&times;</button>

                    <form class="p-6 mt-2" id="fase3EstudianteForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="practica_id" value="{{ $practica->id }}">

                        <!-- Título -->
                        <div class="mb-4 pr-4">
                            <p class="text-2xl font-bold" id="fase3EstudianteTitle">Prácticas <span
                                    class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">
                                    Fase 3
                                </span>
                            </p>
                 
                            <p class="text-gray-950 mt-6 text-sm mb-3">
                                En este formulario el estudiante podrá cargar los documentos necesarios para continuar con la propuesta de prácticas empresariales.
                            </p>

                            <!-- Documentos requeridos para formalizar la propuesta -->
                            <div class="flex items-center my-5 mb-3">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500 text-sm"></i>
                                <p class="font-medium text-gray-700 flex items-center gap-2 text-sm">
                                    <span class="text-red-500">*</span>
                                    Documentos de la propuesta de prácticas:
                                </p>
                            </div>

                            <ul class="space-y-2 text-sm mb-4 list-disc text-gray-500">
                                <li class="flex items-center gap-2 flex-wrap">
                                    <span class="text-gray-600">Certificado ARL:</span>
                                    <span class="text-gray-500">Archivo en formato PDF.</span>
                                </li>

                                <li class="flex items-center gap-2 flex-wrap">
                                    <span class="text-gray-600">Formato propuesta de prácticas:</span>
                                    <span class="text-gray-500">F-DC-127.</span>
                                </li>

                                <li class="flex items-center gap-2 flex-wrap">
                                    <span class="text-gray-600">Acta de inicio de prácticas:</span>
                                    <span class="text-gray-500">F-DC-195.</span>
                                </li>
                            </ul>
                        </div>

                        <p class="text-sm text-gray-950"><strong>NOTA: </strong>El formato F-DC-127 y F-DC-195 debe estar debidamente diligenciado, firmado y no debe superar los {{ config('practicas.peso_maximo_propuesta') }} MB en formato Word.</p>

                        <!-- Campo ARL -->
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-2 mt-6">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                    <span class="text-red-500">*</span> Certificado ARL:
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-arl-fase3"></i>

                                    <div id="tooltip-arl-fase3"  class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el certificado de afiliación de la Administradora de Riesgos Laborales (ARL). 
                                        El documento debe incluir la información completa y legible.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-2 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de PDF de máximo {{ config('practicas.peso_maximo_propuesta') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                      <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>

                                    <input type="file" name="arl" id="arl"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".pdf" />

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="arlError" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-arl" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>


                        <!-- Campo F-DC-127 -->
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                    <span class="text-red-500">*</span> Formato F-DC-127: 
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-fdc127-fase3"></i>

                                    <div id="tooltip-fdc127-fase3"  class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el documento F-DC-127 en formato WORD.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-2 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de WORD de máximo {{ config('practicas.peso_maximo_propuesta') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                      <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>

                                    <input type="file" name="doc_fdc127" id="doc_fdc127"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".doc,.docx" />

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="doc_fdc127Error" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-fdc127" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>

                        <!-- Campo F-DC-195 -->
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                    <span class="text-red-500">*</span> Formato F-DC-195:
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-fdc195-fase3"></i>

                                    <div id="tooltip-fdc195-fase3"  class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el documento F-DC-195 en formato WORD o PDF.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-2 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de WORD y PDF de máximo {{ config('practicas.peso_maximo_propuesta') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                      <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>

                                    <input type="file" name="doc_fdc195" id="doc_fdc195"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".doc,.docx,.pdf" />

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="doc_fdc195Error" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-fdc195" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end space-x-3 mt-6 pt-4">
                            <button type="button" onclick="closeFase3EstudianteModal()"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-lg transition">Cancelar</button>
                            <button type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-5 py-2 rounded-lg transition items-center gap-2">
                                <svg id="loadingSpinner-fase3" class="hidden text-white animate-spin w-4 h-4" viewBox="0 0 64 64" fill="none">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                </svg>
                                Enviar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- MODAL FASE 3 - Detalles (Ver información enviada) -->
    <div id="fase3DetailsModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" onclick="closeFase3DetailsModal()">
            <div class="flex items-center justify-center min-h-screen text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">
                    <button
                        class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                        onclick="closeFase3DetailsModal()">&times;</button>
                    <div class="p-6 mt-2">
                        <p class="text-2xl font-bold mb-4">Detalles de la <span
                                class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Práctica</span></p>
                        <div id="fase3DetailsContent" class="space-y-3">
                            <div class="text-center py-4">
                                <svg class="inline w-8 h-8 text-gray-400 animate-spin" viewBox="0 0 64 64"
                                    fill="none">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button type="button" onclick="closeFase3DetailsModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FASE 3 Responder Director -->
    <div id="fase3DirModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase3DirModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                        onclick="closeFase3DirModal()">&times;</button>
                    <form class="p-6 mt-2" id="fase3DirForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="practica_id" value="{{ $practica->id }}">

                        <p class="text-2xl font-bold mb-4">
                            Prácticas
                            <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">
                                Fase 3
                            </span>
                        </p>

                        <!-- ESTADO -->
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Estado de la práctica:
                            </label>
                            <select name="estado" id="estado_fase3_dir"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <option value="">Seleccione un estado</option>
                                <option value="Aprobada">Aprobada</option>
                                <option value="Aplazada">Aplazada</option>
                                <option value="Rechazada">Rechazada</option>
                            </select>
                            <span id="estado_fase3_dirError" class="text-red-500 text-sm"></span>
                        </div>


                        <!-- F-DC-127 -->
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Formato F-DC-127:
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-fdc127-dir-fase3"></i>

                                    <div id="tooltip-fdc127-dir-fase3"
                                        class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el documento F-DC-127 firmado o con comentarios del director.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-1 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de WORD de máximo {{ config('practicas.peso_maximo_propuesta') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                        Arrastra o carga tus archivos aquí
                                    </h4>

                                    <input type="file" name="fdc127" id="fdc127_fase3"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".doc,.docx" />

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="fdc127_fase3Error" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-fdc127-fase3" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>

                        <!-- F-DC-195 -->
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Formato F-DC-195:
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-fdc195-dir-fase3"></i>

                                    <div id="tooltip-fdc195-dir-fase3"
                                        class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el acta de inicio F-DC-195 firmada o con comentarios.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-1 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de WORD y PDF de máximo {{ config('practicas.peso_maximo_propuesta') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                        Arrastra o carga tus archivos aquí
                                    </h4>

                                    <input type="file" name="fdc195" id="fdc195_fase3"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".pdf,.doc,.docx" />

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="fdc195_fase3Error" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-fdc195-fase3" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>

                        <!-- TURNITIN -->
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Informe de plagio (Turnitin):
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-turnitin-dir-fase3"></i>

                                    <div id="tooltip-turnitin-dir-fase3"
                                        class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el informe de similitud generado por Turnitin en formato PDF.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-1 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de PDF de máximo {{ config('practicas.peso_maximo_propuesta') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                        Arrastra o carga tus archivos aquí
                                    </h4>

                                    <input type="file" name="turnitin" id="turnitin_fase3"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".pdf" />

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="turnitin_fase3Error" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-turnitin-fase3" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>

                        <!-- RESPUESTA CON QUILL -->
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Comentarios de la respuesta:
                            </label>
                            <div id="txt-editor-fase3-dir" class="shadow txt-editor-quill" style="height: 200px; background: white;"></div>
                            <textarea name="respuesta" id="respuesta_fase3_dir" class="hidden"></textarea>
                            <span id="respuesta_fase3_dirError" class="text-red-500 text-sm"></span>
                        </div>

                        <!-- BOTONES -->
                        <div class="flex justify-end space-x-2 mt-4">
                            <button type="button" onclick="closeFase3DirModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cancelar</button>
                            <button type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-fase3-admin" style="margin: 4px 10px 4px 0"
                                    class="hidden text-gray-300 animate-spin w-4 h-4" viewBox="0 0 64 64" fill="none">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                </svg>
                                Responder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FASE 4 Responder Evaluador  -->
    <div id="fase4EvaluadorModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase4EvaluadorModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">

                    <button class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                        onclick="closeFase4EvaluadorModal()">&times;</button>

                    <form class="p-6 mt-2" id="fase4EvaluadorForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="practica_id" id="practica_id_fase4" value="{{ $practica->id }}">

                        <p class="text-2xl font-bold mb-4">
                            Prácticas
                            <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">
                                Fase 4
                            </span>
                        </p>

                        <!-- ESTADO -->
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Estado de la práctica:
                            </label>
                            <select name="estado" id="estado_fase4"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <option value="">Seleccione un estado</option>
                                <option value="Aprobada">Aprobada</option>
                                <option value="Aplazada">Aplazada</option>
                                <option value="Rechazada">Rechazada</option>
                                
                            </select>
                            <span id="estado_fase4Error" class="text-red-500 text-sm"></span>
                        </div>

                        <!-- F-DC-127 -->
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Formato F-DC-127:
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-fdc127-evaluador-fase4"></i>

                                    <div id="tooltip-fdc127-evaluador-fase4"
                                        class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el documento F-DC-127 firmado o con comentarios del evaluador.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-1 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de WORD de máximo {{ config('practicas.peso_maximo_propuesta') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                        Arrastra o carga tus archivos aquí
                                    </h4>

                                    <input type="file" name="fdc127" id="fdc127_fase4"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".doc,.docx" />

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="fdc127_fase4Error" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-fdc127-fase4" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>

                        <!-- RESPUESTA CON QUILL -->
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Comentarios de la respuesta:
                            </label>
                            <div id="txt-editor-fase4-evaluador" class="shadow txt-editor-quill" style="height: 200px; background: white;"></div>
                            <textarea name="respuesta" id="respuesta_fase4" class="hidden"></textarea>
                            <span id="respuesta_fase4Error" class="text-red-500 text-sm"></span>
                        </div>

                        <!-- BOTONES -->
                        <div class="flex justify-end space-x-2 mt-4">
                            <button type="button" onclick="closeFase4EvaluadorModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cancelar</button>
                            <button type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-fase4-admin" style="margin: 4px 10px 4px 0"
                                    class="hidden text-gray-300 animate-spin w-4 h-4" viewBox="0 0 64 64" fill="none">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                </svg>
                                Responder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FASE 4 Responder Comité -->
    <div id="fase4ComiteModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase4ComiteModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">

                    <button class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                        onclick="closeFase4ComiteModal()">&times;</button>

                    <form class="p-6 mt-2" id="fase4ComiteForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="practica_id" id="practica_id_fase4_comite" value="{{ $practica->id }}">

                        <p class="text-2xl font-bold mb-4">
                            Prácticas
                            <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Fase 4</span>
                        </p>
                
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Estado de la práctica:
                            </label>
                            <select name="estado" id="estado_fase4_comite"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <option value="">Seleccione un estado</option>
                                <option value="Aprobada">Aprobada</option>
                                <option value="Aplazada">Aplazada</option>
                                <option value="Rechazada">Rechazada</option>
                            </select>
                            <span id="estado_fase4_comiteError" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>
                                    Número de acta:
                                </label>
                                <input type="number" name="nro_acta" id="nro_acta_fase4_comite"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500"
                                placeholder="Ingrese el número de acta">
                                <span id="nro_acta_fase4_comiteError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-calendar-days mr-1 text-gray-500"></i>
                                    Fecha del acta:
                                </label>
                                <input type="date" name="fecha_acta" id="fecha_acta_fase4_comite"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <span id="fecha_acta_fase4_comiteError" class="text-red-500 text-sm"></span>
                            </div>
                        </div>

                        <div id="container_titulo_fase4_comite" class="mb-4 hidden">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-heading mr-2 text-gray-500"></i>
                                Título de la propuesta:
                            </label>

                            <input type="text"
                                name="titulo_propuesta"
                                id="titulo_propuesta_fase4_comite"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500"
                                placeholder="Ingrese el título oficial de la propuesta">

                            <span id="titulo_propuesta_fase4_comiteError"
                                class="text-red-500 text-sm"></span>
                        </div>

                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Formato F-DC-127:
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-fdc127-comite-fase4"></i>

                                    <div id="tooltip-fdc127-comite-fase4"
                                        class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el documento F-DC-127 firmado o con comentarios del comité.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-1 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de WORD de máximo {{ config('practicas.peso_maximo_propuesta') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                        Arrastra o carga tus archivos aquí
                                    </h4>

                                    <input type="file" name="fdc127" id="fdc127_fase4_comite"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".doc,.docx" />

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="fdc127_fase4_comiteError" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-fdc127-fase4-comite" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Comentarios de la respuesta:
                            </label>
                            <div id="txt-editor-fase4-comite" class="shadow txt-editor-quill" style="height: 200px; background: white;"></div>
                            <textarea name="respuesta" id="respuesta_fase4_comite" class="hidden"></textarea>
                            <span id="respuesta_fase4_comiteError" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="flex justify-end space-x-2 mt-4">
                            <button type="button" onclick="closeFase4ComiteModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cancelar</button>
                            <button type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-fase4-comite" style="margin: 4px 10px 4px 0"
                                    class="hidden text-gray-300 animate-spin w-4 h-4" viewBox="0 0 64 64" fill="none">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                </svg>
                                Responder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FASE 5 - Estudiante (Enviar documentos F-DC-128, 129, 196) -->
    <div id="fase5EstudianteModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0"
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto; background-color: rgba(0, 0, 0, 0.5);"
            onclick="closeFase5EstudianteModal()">

            <div class="flex items-center justify-center min-h-screen text-center relative">

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full modal-content relative"
                    onclick="event.stopPropagation()" style="max-width: 900px !important; width: 100%;">

                    <button
                        class="modal-close-btn-custom absolute top-4 right-4 text-gray-400 hover:text-red-500 text-2xl z-10 mt-2"
                        onclick="closeFase5EstudianteModal()">&times;</button>

                    <form class="p-6 mt-2" id="fase5EstudianteForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="practica_id" value="{{ $practica->id }}">

                        <!-- Título -->
                        <div class="mb-4 pr-4">
                            <p class="text-2xl font-bold">
                                Prácticas
                                <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">
                                    Fase 5
                                </span>
                            </p>

                            <p class="text-gray-950 mt-6 text-sm">
                                En este formulario el estudiante deberá cargar el informe final de trabajo de grado (F-DC-128) completo, el Acta de terminacion y recibo de satisfaccion de las practicas y la rejilla de evaluación (F-DC-129) diligenciada en el apartado de "Información general del proyecto" . Todos los documentos deben ir en formato de word.
                            </p>
                        </div>

                        <p class="text-sm text-gray-950">
                            <strong>NOTA:</strong> Se deberá cargar el  
                            <a href="https://www.dropbox.com/scl/fo/pudgcaq639agy7t06ahjs/AN084HnuyHffgYL5i--v_Ks/DOCUMENTOS%20DE%20GRADO?dl=0&preview=F-DC-128+Informe+final+de+trabajo+de+grado+en+modalidad+de+pr%C3%A1ctica+V2.docx"
                                target="_blank"
                                class="text-blue-600 underline">
                                INFORME </a>,{!! '
                                <a href="https://www.dropbox.com/scl/fo/pudgcaq639agy7t06ahjs/AN084HnuyHffgYL5i--v_Ks/DOCUMENTOS%20DE%20GRADO?dl=0&preview=F-DC-196+Acta+de+Terminaci%C3%B3n+y+Recibo+a+Satisfacci%C3%B3n+de+Pr%C3%A1cticas+V2.doc&rlkey=6s0b9ajweteyx2ang7ywvk6xm&subfolder_nav_tracking=1"
                                target="_blank"
                                class="text-blue-600 underline">
                                ACTA DE TERMINACIÓN
                                </a>

                                y la

                                <a href="https://www.dropbox.com/scl/fo/pudgcaq639agy7t06ahjs/AN084HnuyHffgYL5i--v_Ks/DOCUMENTOS%20DE%20GRADO?dl=0&preview=F-DC-129+Rejilla+de+evaluaci%C3%B3n+informe+final+de+trabajo+de+grado+V2.docx&rlkey=6s0b9ajweteyx2ang7ywvk6xm&subfolder_nav_tracking=1"
                                target="_blank"
                                class="text-blue-600 underline">
                                REJILLA
                                </a>
                                ' !!} en formato de Word. Tenga en cuenta el tamaño máximo del archivo que puede cargar en cada campo, se le recomienda reducir o comprimir el peso del archivo antes de cargarlo (Puede usar herramientas online para ello o en su defecto la opción "Comprimir imágenes" del Word).
                        </p>
                            <br>

                            <!-- F-DC-128 -->
                            <div class="mb-6">
                                <div class="flex items-center gap-2 mb-2">
                                    <label class="block font-medium text-sm text-gray-700 mb-2">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                        <span class="text-red-500">*</span> Formato F-DC-128:
                                    </label>

                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-fdc128-fase5"></i>

                                        <div id="tooltip-fdc128-fase5"
                                            class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                            Suba el Informe Final F-DC-128. El documento debe incluir la información completa y legible.
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                    <div class="grid gap-1 text-center">
                                        <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                        <h2 class="text-gray-400 text-xs">
                                            Solo archivos de WORD de máximo {{ config('practicas.peso_maximo_informe') }} MB
                                        </h2>
                                    </div>

                                    <div class="text-center">
                                        <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                            Arrastra o carga tus archivos aquí
                                        </h4>

                                        <input type="file" name="doc_fdc128" id="doc_fdc128"
                                            class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                            accept=".doc,.docx" />

                                        <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                            Cargar
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 mb-4">
                                    <span id="doc_fdc128Error" class="block text-red-500 text-sm mb-2"></span>
                                    <ul id="file-list-doc-fdc128" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                                </div>
                            </div>


                            <!-- F-DC-129 -->
                            <div class="mb-6">
                                <div class="flex items-center gap-2 mb-2">
                                    <label class="block font-medium text-sm text-gray-700 mb-2">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                        <span class="text-red-500">*</span> Formato F-DC-129:
                                    </label>

                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-fdc129-fase5"></i>

                                        <div id="tooltip-fdc129-fase5"
                                            class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                            Suba el documento F-DC-129 en formato WORD.
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                    <div class="grid gap-1 text-center">
                                        <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                        <h2 class="text-gray-400 text-xs">
                                            Solo archivos de WORD de máximo {{ config('practicas.peso_maximo_informe') }} MB
                                        </h2>
                                    </div>

                                    <div class="text-center">
                                        <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                            Arrastra o carga tus archivos aquí
                                        </h4>

                                        <input type="file" name="doc_fdc129" id="doc_fdc129"
                                            class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                            accept=".doc,.docx" />

                                        <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                            Cargar
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 mb-4">
                                    <span id="doc_fdc129Error" class="block text-red-500 text-sm mb-2"></span>
                                    <ul id="file-list-doc-fdc129" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                                </div>
                            </div>


                            <!-- F-DC-196 -->
                            <div class="mb-6">
                                <div class="flex items-center gap-2 mb-2">
                                    <label class="block font-medium text-sm text-gray-700 mb-2">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                        <span class="text-red-500">*</span> Formato F-DC-196:
                                    </label>

                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-fdc196-fase5"></i>

                                        <div id="tooltip-fdc196-fase5"
                                            class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                            Suba el documento F-DC-196 en formato WORD.
                                        </div>
                                    </div>
                                </div>

                                <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                    <div class="grid gap-1 text-center">
                                        <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                        <h2 class="text-gray-400 text-xs">
                                            Solo archivos de WORD y PDF de máximo {{ config('practicas.peso_maximo_informe') }} MB
                                        </h2>
                                    </div>

                                    <div class="text-center">
                                        <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                            Arrastra o carga tus archivos aquí
                                        </h4>

                                        <input type="file" name="doc_fdc196" id="doc_fdc196"
                                            class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                            accept=".pdf,.doc,.docx" />

                                        <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                            Cargar
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 mb-4">
                                    <span id="doc_fdc196Error" class="block text-red-500 text-sm mb-2"></span>
                                    <ul id="file-list-doc-fdc196" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                                </div>
                            </div>

                        <!-- Botones -->
                        <div class="flex justify-end space-x-3 mt-6 pt-4">
                            <button type="button" onclick="closeFase5EstudianteModal()"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-lg transition">
                                Cancelar
                            </button>

                            <button type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-5 py-2 rounded-lg transition items-center gap-2">

                                <svg id="loadingSpinner-fase5" class="hidden text-white animate-spin w-4 h-4"
                                    viewBox="0 0 64 64" fill="none">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" class="text-white"></path>
                                </svg>

                                Enviar
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FASE 5 - Detalles -->
    <div id="fase5DetailsModal" class="fixed z-50 inset-0 overflow-y-auto hidden" >

        <div class="modal-overlay absolute inset-0 bg-black bg-opacity-50"
            onclick="closeFase5DetailsModal()">

            <div class="flex items-center justify-center min-h-screen text-center relative">

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">

                    <!-- BOTÓN CERRAR -->
                    <button
                        class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                        onclick="closeFase5DetailsModal()">
                        &times;
                    </button>

                    <div class="p-6 mt-2">

                        <!-- TÍTULO -->
                        <p class="text-2xl font-bold mb-6">
                            Detalles de la
                            <span
                                class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">
                                PRACTICA
                            </span>
                        </p>

                        <!-- CONTENIDO -->
                        <div id="fase5DetailsContent" class="space-y-4">

                            <!-- LOADING -->
                            <div class="text-center py-8">

                                <svg class="inline w-8 h-8 text-gray-400 animate-spin"
                                    viewBox="0 0 64 64"
                                    fill="none">

                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor"
                                        stroke-width="5">
                                    </path>

                                </svg>

                                <p class="mt-2 text-gray-500">
                                    Cargando información...
                                </p>

                            </div>

                        </div>

                        <!-- FOOTER -->
                        <div class="flex justify-end mt-6">

                            <button type="button"
                                onclick="closeFase5DetailsModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">

                                Cerrar

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!--MODAL FASE 5 Responder Director -->
    <div id="fase5DirModal" class="fixed z-50 inset-0 overflow-y-auto hidden">
        <div class="modal-overlay absolute inset-0"
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;"
            onclick="closeFase5DirModal()">

            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">

                    <!-- BOTÓN CERRAR -->
                    <button
                        class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                        onclick="closeFase5DirModal()">
                        &times;
                    </button>

                    <form class="p-6 mt-2" id="fase5DirForm" enctype="multipart/form-data">

                        @csrf

                        <input type="hidden" name="practica_id" value="{{ $practica->id }}">

                        <!-- TÍTULO -->
                        <p class="text-2xl font-bold mb-4">
                            Prácticas
                            <span
                                class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">
                                Fase 5
                            </span>
                        </p>

                        <!-- ESTADO -->
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Estado de la práctica:
                            </label>

                            <select
                                name="estado"
                                id="estado_fase5_dir"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">

                                <option value="">Seleccione un estado</option>
                                <option value="Aprobada">Aprobada</option>
                                <option value="Aplazada">Aplazada</option>
                                <option value="Rechazada">Rechazada</option>

                            </select>

                            <span id="estado_fase5_dirError"
                                class="text-red-500 text-sm"></span>
                        </div>

                        <!-- F-DC-128 -->
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Formato F-DC-128:
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-fdc128-dir-fase5"></i>

                                    <div id="tooltip-fdc128-dir-fase5"
                                        class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el documento F-DC-128 firmado o con comentarios.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-1 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>

                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de WORD de máximo {{ config('practicas.peso_maximo_informe') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                        Arrastra o carga tus archivos aquí
                                    </h4>

                                    <input type="file" name="fdc128" id="fdc128_fase5"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".doc,.docx" />

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="fdc128_fase5Error" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-fdc128-fase5" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>

                        <!-- F-DC-129 -->
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                    <span class="text-red-500">*</span>
                                    Formato F-DC-129:
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-fdc129-dir-fase5"></i>

                                    <div id="tooltip-fdc129-dir-fase5"
                                        class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el documento F-DC-129 firmado o con comentarios.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-1 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>

                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de WORD de máximo {{ config('practicas.peso_maximo_informe') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                        Arrastra o carga tus archivos aquí
                                    </h4>

                                    <input type="file" name="fdc129" id="fdc129_fase5"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".doc,.docx" />

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="fdc129_fase5Error" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-fdc129-fase5" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>

                        <!-- F-DC-196 -->
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                    <span class="text-red-500">*</span>
                                    Formato F-DC-196:
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-fdc196-dir-fase5"></i>

                                    <div id="tooltip-fdc196-dir-fase5"
                                        class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el documento F-DC-196 firmado o con comentarios.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-1 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>

                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de WORD y PDF de máximo {{ config('practicas.peso_maximo_informe') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                        Arrastra o carga tus archivos aquí
                                    </h4>

                                    <input type="file" name="fdc196" id="fdc196_fase5"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".pdf,.doc,.docx" />

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="fdc196_fase5Error" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-fdc196-fase5" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>


                        <!-- TURNITIN -->
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Informe de plagio (Turnitin)
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-turnitin-dir-fase5"></i>

                                    <div id="tooltip-turnitin-dir-fase5"
                                        class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el informe de similitud generado por Turnitin en PDF.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-1 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>

                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de PDF de máximo {{ config('practicas.peso_maximo_informe') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                        Arrastra o carga tus archivos aquí
                                    </h4>

                                    <input type="file" name="turnitin" id="turnitin_fase5"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".pdf" />

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="turnitin_fase5Error" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-turnitin-fase5" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>


                        <!-- RESPUESTA -->
                        <div class="mb-4">

                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Comentarios de la respuesta:
                            </label>

                            <div id="txt-editor-fase5-dir" class="shadow txt-editor-quill"style="height: 200px; background: white;"></div>

                            <textarea name="respuesta"id="respuesta_fase5_dir"class="hidden"></textarea>

                            <span id="respuesta_fase5_dirError"class="text-red-500 text-sm"></span>
                        </div>
                        <!-- BOTONES -->
                        <div class="flex justify-end space-x-2 mt-4">

                            <button
                                type="button"
                                onclick="closeFase5DirModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">

                                <svg id="loadingSpinner-fase5-dir"
                                    style="margin: 4px 10px 4px 0"
                                    class="hidden text-gray-300 animate-spin w-4 h-4"
                                    viewBox="0 0 64 64"
                                    fill="none">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor"
                                        stroke-width="5"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor"
                                        stroke-width="5"
                                        class="text-white"></path>
                                </svg>
                                Responder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FASE 6 Responder Evaluador -->
    <div id="fase6EvaluadorModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0"
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;"
            onclick="closeFase6EvaluadorModal()">

            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">

                    <button class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                        onclick="closeFase6EvaluadorModal()">&times;</button>

                    <form class="p-6 mt-2" id="fase6EvaluadorForm" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="practica_id" id="practica_id_fase6"
                            value="{{ $practica->id }}">

                        <p class="text-2xl font-bold mb-4">
                            Prácticas
                            <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">
                                Fase 6
                            </span>
                        </p>

                        <!-- ESTADO -->
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Estado de la práctica:
                            </label>

                            <select name="estado" id="estado_fase6"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <option value="">Seleccione un estado</option>
                                <option value="Aprobada">Aprobada</option>
                                <option value="Aplazada">Aplazada</option>
                                <option value="Rechazada">Rechazada</option>
                            </select>

                            <span id="estado_fase6Error" class="text-red-500 text-sm"></span>
                        </div>

                        <!-- F-DC-128 -->
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Formato F-DC-128:
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-fdc128-evaluador-fase6"></i>

                                    <div id="tooltip-fdc128-evaluador-fase6"
                                        class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el documento F-DC-128 firmado o con comentarios del evaluador.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-1 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>

                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de WORD de máximo {{ config('practicas.peso_maximo_informe') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                        Arrastra o carga tus archivos aquí
                                    </h4>

                                    <input type="file" name="fdc128" id="fdc128_fase6"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".doc,.docx" />

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="fdc128_fase6Error" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-fdc128-fase6" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>


                        <!-- F-DC-129 -->
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Formato F-DC-129:
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-fdc129-evaluador-fase6"></i>

                                    <div id="tooltip-fdc129-evaluador-fase6"
                                        class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el documento F-DC-129 firmado o con comentarios del evaluador.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-1 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>

                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de WORD de máximo {{ config('practicas.peso_maximo_informe') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                        Arrastra o carga tus archivos aquí
                                    </h4>

                                    <input type="file" name="fdc129" id="fdc129_fase6"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".doc,.docx" />

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="fdc129_fase6Error" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-fdc129-fase6" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>
                        <!-- RESPUESTA -->
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Comentarios de la respuesta:
                            </label>

                            <div id="txt-editor-fase6-evaluador"
                                class="shadow txt-editor-quill"
                                style="height: 200px; background: white;">
                            </div>

                            <textarea name="respuesta" id="respuesta_fase6"
                                class="hidden"></textarea>

                            <span id="respuesta_fase6Error" class="text-red-500 text-sm"></span>
                        </div>

                        <!-- BOTONES -->
                        <div class="flex justify-end space-x-2 mt-4">

                            <button type="button"
                                onclick="closeFase6EvaluadorModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>

                            <button type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">

                                <svg id="loadingSpinner-fase6-admin"
                                    style="margin: 4px 10px 4px 0"
                                    class="hidden text-gray-300 animate-spin w-4 h-4"
                                    viewBox="0 0 64 64" fill="none">

                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5">
                                    </path>

                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5"
                                        class="text-white">
                                    </path>
                                </svg>

                                Responder
                            </button>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FASE 6 Detalles --> 
    <div id="fase6DetailsModal" class="fixed z-50 inset-0 overflow-y-auto hidden">

        <div class="modal-overlay absolute inset-0 bg-black bg-opacity-50"
            onclick="closeFase6DetailsModal()">

            <div class="flex items-center justify-center min-h-screen text-center relative">

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">

                    <!-- BOTÓN CERRAR -->
                    <button
                        class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                        onclick="closeFase6DetailsModal()">
                        &times;
                    </button>

                    <div class="p-6 mt-2">

                        <!-- TÍTULO -->
                        <p class="text-2xl font-bold mb-6">
                            Detalles de la
                            <span
                                class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">
                                PRÁCTICA
                            </span>
                        </p>

                        <!-- CONTENIDO -->
                        <div id="fase6DetailsContent" class="space-y-4">

                            <!-- LOADING -->
                            <div class="text-center py-8">

                                <svg class="inline w-8 h-8 text-gray-400 animate-spin"
                                    viewBox="0 0 64 64"
                                    fill="none">

                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor"
                                        stroke-width="5">
                                    </path>

                                </svg>

                                <p class="mt-2 text-gray-500">
                                    Cargando información...
                                </p>

                            </div>

                        </div>

                        <!-- FOOTER -->
                        <div class="flex justify-end mt-6">

                            <button type="button"
                                onclick="closeFase6DetailsModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">

                                Cerrar

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- MODAL FASE 6 Responder Comite -->
    <div id="fase6ComiteModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0"
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;"
            onclick="closeFase6ComiteModal()">

            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">

                    <button class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                        onclick="closeFase6ComiteModal()">&times;</button>

                    <form class="p-6 mt-2" id="fase6ComiteForm" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="practica_id"
                            id="practica_id_fase6_comite"
                            value="{{ $practica->id }}">

                        <p class="text-2xl font-bold mb-4">
                            Prácticas
                            <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">
                                Fase 6
                            </span>
                        </p>

                        <!-- ESTADO -->
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Estado de la práctica:
                            </label>

                            <select name="estado" id="estado_fase6_comite"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <option value="">Seleccione un estado</option>
                                <option value="Aprobada">Aprobada</option>
                                <option value="Aplazada">Aplazada</option>
                                <option value="Rechazada">Rechazada</option>
                            </select>

                            <span id="estado_fase6_comiteError" class="text-red-500 text-sm"></span>
                        </div>

                        <!-- ACTA -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">

                            <div>
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>
                                    Número de acta:
                                </label>

                                <input type="number"
                                    name="nro_acta"
                                    id="nro_acta_fase6_comite"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el número de acta">

                                <span id="nro_acta_fase6_comiteError"
                                    class="text-red-500 text-sm"></span>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-calendar-days mr-1 text-gray-500"></i>
                                    Fecha del acta:
                                </label>

                                <input type="date"
                                    name="fecha_acta"
                                    id="fecha_acta_fase6_comite"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">

                                <span id="fecha_acta_fase6_comiteError"
                                    class="text-red-500 text-sm"></span>
                            </div>

                        </div>

                        <!-- F-DC-128 -->
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Formato F-DC-128:
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-fdc128-comite-fase6"></i>

                                    <div id="tooltip-fdc128-comite-fase6"
                                        class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el documento F-DC-128 firmado o con comentarios del comité.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-1 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>

                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de WORD de máximo {{ config('practicas.peso_maximo_informe') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                        Arrastra o carga tus archivos aquí
                                    </h4>

                                    <input type="file"
                                        name="fdc128"
                                        id="fdc128_fase6_comite"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".doc,.docx">

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="fdc128_fase6_comiteError" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-fdc128-fase6-comite" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>


                        <!-- F-DC-129 -->
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Formato F-DC-129:
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-fdc129-comite-fase6"></i>

                                    <div id="tooltip-fdc129-comite-fase6"
                                        class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                        Suba el documento F-DC-129 firmado o con comentarios del comité.
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-1 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>

                                    <h2 class="text-center text-gray-400 text-xs">
                                        Solo archivos de WORD de máximo {{ config('practicas.peso_maximo_informe') }} MB
                                    </h2>
                                </div>

                                <div class="text-center">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">
                                        Arrastra o carga tus archivos aquí
                                    </h4>

                                    <input type="file"
                                        name="fdc129"
                                        id="fdc129_fase6_comite"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        accept=".doc,.docx">

                                    <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 mb-4">
                                <span id="fdc129_fase6_comiteError" class="block text-red-500 text-sm mb-2"></span>
                                <ul id="file-list-fdc129-fase6-comite" class="text-gray-600 text-sm list-disc pl-5 m-0"></ul>
                            </div>
                        </div>

                        <!-- RESPUESTA -->
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Comentarios de la respuesta:
                            </label>

                            <div id="txt-editor-fase6-comite"
                                class="shadow txt-editor-quill"
                                style="height: 200px; background: white;">
                            </div>

                            <textarea name="respuesta"
                                id="respuesta_fase6_comite"
                                class="hidden"></textarea>

                            <span id="respuesta_fase6_comiteError"
                                class="text-red-500 text-sm"></span>
                        </div>

                        <!-- BOTONES -->
                        <div class="flex justify-end space-x-2 mt-4">

                            <button type="button"
                                onclick="closeFase6ComiteModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>

                            <button type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-warning" style="margin: 4px 10px 4px 0"
                                    class="hidden text-gray-300 animate-spin w-4 h-4" viewBox="0 0 64 64"
                                    fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round"
                                        stroke-linejoin="round" class="text-white">
                                    </path>
                                </svg>
                                Responder
                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <!--MODAL FASE FINAL - Detalles  Todos los documentos se muestran-->
    <div id="fase7DetailsModal" class="fixed z-50 inset-0 overflow-y-auto hidden">

        <div class="modal-overlay absolute inset-0 bg-black bg-opacity-50"
            onclick="closeFase7DetailsModal()">

            <div class="flex items-center justify-center min-h-screen text-center relative">

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">

                    <!-- BOTÓN CERRAR -->
                    <button
                        class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                        onclick="closeFase7DetailsModal()">
                        &times;
                    </button>

                    <div class="p-6 mt-2">

                        <!-- TÍTULO -->
                        <p class="text-2xl font-bold mb-6">
                            Detalles de la
                            <span
                                class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">
                                PRÁCTICA
                            </span>
                        </p>

                        <!-- CONTENIDO -->
                        <div id="fase7DetailsContent" class="space-y-4">

                            <!-- LOADING -->
                            <div class="text-center py-8">

                                <svg class="inline w-8 h-8 text-gray-400 animate-spin"
                                    viewBox="0 0 64 64"
                                    fill="none">

                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor"
                                        stroke-width="5">
                                    </path>

                                </svg>

                                <p class="mt-2 text-gray-500">
                                    Cargando archivos...
                                </p>

                            </div>

                        </div>

                        <!-- FOOTER -->
                        <div class="flex justify-end mt-6">

                            <button type="button"
                                onclick="closeFase7DetailsModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">

                                Cerrar

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Calendario Modal -->
    @if (isset($fechas))
        <div id="calendarModal" class="fixed z-50 inset-0 overflow-y-auto">
            <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5);" onclick="closeCalendarModal()">
                <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" style="width: 100% !important; padding: 2rem 2rem !important;" onclick="event.stopPropagation()">

                        <button class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                            onclick="closeCalendarModal()">&times;
                        </button>

                        <div class="p-6 mt-2">
                            <p class="text-2xl font-bold text-gray-800 mb-4">Calendario de <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Prácticas</span></p>
                            <p class="font-medium text-md text-gray-700 mb-2">Aquí podrá visualizar algunas fechas importantes de la práctica en curso.</p>
                            
                            <div class="overflow-x-auto mb-4">
                                <table class="min-w-full border-collapse border border-gray-300 bg-gray-50 shadow-md rounded-lg">
                                    <thead>
                                        <tr class="bg-gray-200 text-gray-700">
                                            <th class="px-4 py-3 text-left font-semibold border border-gray-300 uppercase text-sm">Descripción</th>
                                            <th class="px-4 py-3 text-left font-semibold border border-gray-300 uppercase text-sm">Fechas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="bg-gray-50">
                                            <td class="px-4 py-3 border border-gray-300">Propuesta de prácticas</td>
                                            <td class="px-4 py-3 border border-gray-300">
                                                Desde <span class="font-semibold" style="font-size: 0.9rem;">{{ $fechas['fecha_inicio_proyectos'] ?? 'No definida' }}</span> 
                                                hasta <span class="font-semibold" style="font-size: 0.9rem;">{{ $fechas['fecha_fin_proyectos'] ?? 'No definida' }}</span>
                                            </td>
                                        </tr>
                                        <tr class="bg-white">
                                            <td class="px-4 py-3 border border-gray-300">Fecha máxima para aprobación de propuestas</td>
                                            <td class="px-4 py-3 border border-gray-300">
                                                Hasta <span class="font-semibold" style="font-size: 0.9rem;">{{ $fechas['fecha_aprobacion_propuesta'] ?? 'No definida' }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!--Reporte modal prácticas-->
    <div id="warningModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0"
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;"
            onclick="closeWarningModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative "
                    onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeWarningModal()">
                        &times;
                    </button>
                    <form id="warningForm" class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="warningTitle">Enviar
                            Reporte <span
                                class="bg-uts-500 text-white  text-lg font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">PQRSD</span>
                        </p>
                        <div class="mb-4">
                            <label for="mensaje_warning" class="block font-medium text-md text-gray-700 mb-4">
                                En caso de presentar problemas con el sistema, por favor describa su reporte y envíelo:
                            </label>
                            <div id="txt-editor-warning" class="shadow txt-editor-quill"></div>
                            <textarea name="mensaje_warning" id="mensaje_warning" class="hidden"></textarea>
                            <span id="mensaje_warningError" class="text-red-500 text-sm"></span>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" onclick="closeWarningModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-warning" style="margin: 4px 10px 4px 0"
                                    class="hidden text-gray-300 animate-spin w-4 h-4" viewBox="0 0 64 64"
                                    fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round"
                                        stroke-linejoin="round" class="text-white">
                                    </path>
                                </svg>
                                Enviar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ICFES MODAL - Para prácticas (Fases 5 y 6) -->
@if ($fase_actual >= 5 && $fase_actual <= 6)
    
    {{-- Modal Estudiante (solo visible cuando el estudiante no ha enviado) --}}
    
        <div id="icfesEstudianteModal" class="fixed z-50 inset-0 overflow-y-auto">
                    <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeIcfesEstudianteModal()">
                        <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                                <button class="modal-close-btn-custom" onclick="closeIcfesEstudianteModal()">&times;</button>
                                <form id="icfesEstudianteForm" class="p-6 mt-2">
                                    @csrf
                                    <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="icfesEstudianteTitle"></p>
                                    <p class="font-medium text-sm text-gray-700 text-justify">En este formulario el estudiante podrá cargar los resultados de su prueba saber TyT/Pro en dado caso de que pueda hacer parte de los beneficios establecidos en el <a href="https://www.uts.edu.co/sitio/estudiantes/" target="_blank" class="text-blue-500 underline uppercase">reglamento de estímulos</a>. Debe cargar un solo archivo en formato PDF, el comité de trabajos de grado revisará la validez del documento y dará respuesta al estudiante. Tenga en cuenta la siguiente información:</p>
                                    <p class="font-medium text-sm text-gray-700 my-4">Si en su práctica hay más de un integrante debe tener presente lo siguiente:</p>
                                    <ul class="list-disc">
                                        <li class="ml-4 font-medium text-sm text-gray-700 text-justify">Si <strong>UN</strong> solo integrante cumple con el puntaje requerido, únicamente ese integrante deberá cargar desde su cuenta los resultados en formato PDF. El otro u otros integrantes deberán continuar el desarrollo de la práctica de forma normal.</li>
                                        <li class="ml-4 font-medium text-sm text-gray-700 text-justify">Si <strong>TODOS</strong> los integrantes cumplen con el puntaje, cada uno deberá cargar los resultados desde su propia cuenta en formato PDF. El comité se encargará de finalizar toda la práctica.</li>
                                        <li class="ml-4 font-medium text-sm text-gray-700 text-justify">Si <strong>NINGUNO</strong> cumple con el puntaje, el comité no aprobará ningún beneficio a los estudiantes.</li>
                                    </ul>

                                        <div class="flex items-start my-4">
                                            <p class="font-medium text-sm text-red-700 text-justify">
                                                <i class="fa-solid fa-circle-info text-red-500 text-xl mr-2 mt-1"></i><strong>IMPORTANTE:</strong> Una vez aprobados los documentos de la propuesta de prácticas empresariales (ARL, FDC-127, FDC195) <strong>SOLO en esa etapa</strong>, el estudiante podrá aplicar al beneficio, y de acuerdo a la desición tomada por coordinación, el estudiante será eximido de la entrega de los documentos finales, <strong>Sin embargo, deberá terminar las 600 horas prácticas en la empresa. </strong> <br> Los requisitos para ser aprobado el Beneficio saber TYT/PRO son los siguientes:  <br> 1. Cuando se certifique por parte de una E.P.S. una enfermedad grave del estudiante que le impida continuar con su práctica. <br> 2. Cuando la empresa solicita el aplazamiento de la práctica por motivos ajenos al desarrollo de su actividad económica.
                                            </p>
                                        </div>

                                    <input type="hidden" name="practica_id" id="practica_id" value="{{ $practica->id }}">
                                    <input type="hidden" name="submited_icfes_practicas" id="submited_icfes_practicas" value="{{ auth()->user()->id }}_true">
                                    <div class="grid grid-cols-1 gap-6 mt-4">
                                        <div class="flex items-center gap-2">
                                            <label for="doc_icfes_practicas" class="block font-medium text-sm text-gray-700">
                                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                                <span class="text-red-600 mr-1 text-lg">*</span>Evidencias de resultados pruebas TyT/Pro
                                            </label>
                                            <div class="relative inline-block">
                                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                                    data-tooltip="tooltip-doc_icfes_practicas"></i>

                                                <div id="tooltip-doc_icfes_practicas"
                                                    class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                                    <p class="uppercase font-bold mb-2">Instrucciones:</p>
                                                    Solo se debe subir un archivo en formato PDF.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-full relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_doc_icfes_practicas">
                                            <div class="grid gap-1">
                                                <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                                <h2 class="text-center text-gray-400 text-xs leading-4">Solo archivos de PDF de máximo {{ config('practicas.peso_maximo_archivos') }} MB</h2>
                                            </div>
                                            <div class="grid gap-2">
                                                <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                                <div class="flex items-center justify-center">
                                                    <input type="file" name="doc_icfes_practicas[]" id="doc_icfes_practicas" class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf" />
                                                    <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="doc_icfes_practicasError" class="text-red-500 text-sm"></span>
                                    <ul id="doc_icfes_practicas-file-list" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                                    <span id="doc_icfes_practicas-files-size" class="text-gray-800 text-sm"></span>
                                    <div class="mt-8 flex justify-end space-x-2">
                                        <button type="button" onclick="closeIcfesEstudianteModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cancelar</button>
                                        <button id="icfesEstudianteButton" type="submit" class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                            <svg id="loadingSpinner-icfesEstudiante" style="margin: 4px 10px 4px 0" class="hidden text-gray-300 animate-spin w-4 h-4" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                                            </svg>
                                            Enviar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
    
    
    {{-- Modal Admin (SIEMPRE debe existir en el DOM) --}}
<div id="icfesAdminModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeIcfesAdminModal()">
        <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                <button class="modal-close-btn-custom" onclick="closeIcfesAdminModal()">&times;</button>
                <form id="icfesAdminForm" class="p-6 mt-2">
                    @csrf
                    <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="icfesAdminTitle"></p>

                    <input type="hidden" name="practica_id" value="{{ $practica->id }}">

                    {{-- Estado de la solicitud --}}
                    <div class="mb-4">
                        <label for="estado_icfes_practicas" class="block font-medium text-sm text-gray-700">
                            <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                            Estado de la solicitud:
                        </label>
                        <select name="estado_icfes_practicas" id="estado_icfes_practicas" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                            <option value="" selected disabled>Selecciona una opción</option>
                            <option value="Aprobado">Aprobado</option>
                            <option value="Rechazado">Rechazado</option>
                        </select>
                        <span id="estado_icfes_practicasError" class="text-red-500 text-sm"></span>
                    </div>

                    {{-- Integrante de la práctica (SOLO los que han enviado solicitud) --}}
                    <div class="mb-4">
                        <label for="estudiante_id_practicas" class="block font-medium text-sm text-gray-700">
                            <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                            Integrante de la práctica:
                        </label>
                        <select name="estudiante_id"
                                id="estudiante_id_practicas"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">

                            <option value="" selected disabled>
                                Selecciona un estudiante
                            </option>

                            @foreach ($solicitudesEnviadas as $estudiante)
                                <option value="{{ $estudiante->id }}">
                                    {{ $estudiante->name }}
                                </option>
                            @endforeach
                        </select>

                        <span id="estudiante_id_practicasError" class="text-red-500 text-sm"></span>
                    </div>

                    {{-- Acta --}}
                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="nro_acta_icfes_practicas" class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>
                                Número de acta:
                            </label>
                            <input type="number" name="nro_acta_icfes_practicas" id="nro_acta_icfes_practicas"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                placeholder="Ingrese el número de acta">
                            <span id="nro_acta_icfes_practicasError" class="text-red-500 text-sm"></span>
                        </div>
                        <div>
                            <label for="fecha_acta_icfes_practicas" class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-calendar-days mr-1 text-gray-500"></i>
                                Fecha del acta:
                            </label>
                            <input type="date" name="fecha_acta_icfes_practicas" id="fecha_acta_icfes_practicas"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                            <span id="fecha_acta_icfes_practicasError" class="text-red-500 text-sm"></span>
                        </div>
                    </div>

                    {{-- Comentarios --}}
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 mb-2">
                            <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                            Comentarios de la respuesta:
                        </label>
                        <div id="txt-editor-icfes-practicas" class="txt-editor-quill shadow"></div>
                        <textarea name="respuesta_icfes_practicas" id="respuesta_icfes_practicas" class="hidden"></textarea>
                        <span id="respuesta_icfes_practicasError" class="text-red-500 text-sm"></span>
                    </div>

                    {{-- Botones --}}
                    <div class="mt-2 flex justify-end space-x-2">
                        <button type="button" onclick="closeIcfesAdminModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cancelar</button>
                        <button id="icfesAprobarButton" type="submit" class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                            <svg id="loadingSpinner-icfesAdmin" style="margin: 4px 10px 4px 0" class="hidden text-gray-300 animate-spin w-4 h-4" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                            </svg>
                            Responder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    
@endif

<!-- CONFIGURACIONES DE LA PRÁCTICA - ESTUDIANTE -->
<div id="configModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeConfigModal()">
        <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative mt-2" onclick="event.stopPropagation()">
                <button class="modal-close-btn-custom" onclick="closeConfigModal()">&times;</button>
                <form class="p-6 mt-2" id="configModalForm" enctype="multipart/form-data">
                    @csrf
                    <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="configModalTitle"></p>
                    <p class="font-medium text-sm text-gray-700 mb-6 text-justify">En este formulario el estudiante podrá realizar algunas solicitudes dentro de su práctica, tales como cambio de director, cambio de evaluador, prórroga y retiro de la práctica.</p>
                    <p class="font-medium text-sm text-gray-700 mb-6 text-justify">- En caso de solicitar <strong>RETIRO</strong>, el estudiante que desee realizar esta solicitud deberá ingresar desde su <strong>PROPIA</strong> cuenta de usuario y adjuntar una carta solicitando el retiro de forma voluntaria.</p>

                    @if ($fase_actual >= 5)
                        <p class="font-medium text-sm text-gray-700 mb-6 text-justify">- En caso de solicitar <strong>PRÓRROGA</strong>, el estudiante debe tener en cuenta que esta solo se puede solicitar como máximo <strong>DOS</strong> veces en una práctica y deberá adjuntar una carta solicitando la prórroga junto con la liquidación y soporte de pago de la misma.</p>
                        <div class="flex items-start mb-6">
                            <p class="font-medium text-sm text-red-700 text-justify">
                                <i class="fa-solid fa-circle-info text-red-500 text-xl mr-2 mt-1"></i><strong>IMPORTANTE:</strong> El comité de prácticas <strong>únicamente aprobará la prórroga si todos los integrantes activos han realizado el pago correspondiente</strong>. Por lo tanto, <strong>TODOS</strong> los integrantes deberán pagar la <strong>PRÓRROGA</strong>, o en su defecto, solicitar el retiro de forma voluntaria para que los demás integrantes puedan continuar con la práctica. Para solicitar la prórroga <strong>ÚNICAMENTE</strong> deberá hacerlo un solo integrante del proyecto adjuntando todos los soportes de pago en un único archivo pdf junto con la carta solicitando la prórroga.
                            </p>
                        </div>
                    @endif

                    <input type="hidden" name="practica_id" value="{{ $practica->id }}">

                    <div class="mb-4">
                        <label for="tipo_solicitud" class="block font-medium text-sm text-gray-700">
                            <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                            Tipo de solicitud:
                        </label>
                        <select name="tipo_solicitud" id="tipo_solicitud" lang="es" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                            <option value="" selected disabled>Selecciona una opción</option>
                            <option value="retiro">Retiro de la práctica</option>
                            @if ($fase_actual >= 3)
                                <option value="cambio_director">Cambio de director</option>
                                <option value="cambio_evaluador">Cambio de evaluador</option>
                            @endif
                            @if ($fase_actual >= 5)
                                <option value="prorroga">Prórroga</option>
                            @endif
                        </select>
                        <span id="tipo_solicitudError" class="text-red-500 text-sm"></span>
                    </div>

                    <div id="container-doc_prorroga_config" class="mb-5 hidden">
                        <div class="mb-4">
                            <label for="carta_prorroga" class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Carta de solicitud de prórroga:
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_carta_prorroga">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs leading-4">Solo archivos de PDF de máximo {{ config('practicas.peso_maximo_archivos') }} MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input type="file" name="carta_prorroga[]" id="carta_prorroga" class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="carta_prorrogaError" class="text-red-500 text-sm"></span>
                            <ul id="file-list-prorroga-carta" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-prorroga-carta" class="text-gray-800 text-sm"></span>
                        </div>
                        <div class="mb-4">
                            <label for="liquidacion_prorroga" class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Liquidación de prórroga:
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_liquidacion_prorroga">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs leading-4">Solo archivos de PDF de máximo {{ config('practicas.peso_maximo_archivos') }} MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input type="file" name="liquidacion_prorroga[]" id="liquidacion_prorroga" class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="liquidacion_prorrogaError" class="text-red-500 text-sm"></span>
                            <ul id="file-list-liquidacion-prorroga" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-liquidacion-prorroga" class="text-gray-800 text-sm"></span>
                        </div>
                        <div>
                            <label for="soporte_prorroga" class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Soporte de pago de prórroga:
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_soporte_prorroga">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs leading-4">Solo archivos de PDF de máximo  {{ config('practicas.peso_maximo_archivos') }} MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input type="file" name="soporte_prorroga[]" id="soporte_prorroga" class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="soporte_prorrogaError" class="text-red-500 text-sm"></span>
                            <ul id="file-list-soporte-prorroga" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-soporte-prorroga" class="text-gray-800 text-sm"></span>
                        </div>
                    </div>

                    <div id="container-doc_retiro_config" class="mb-5 hidden">
                        <label for="carta_retiro" class="block font-medium text-sm text-gray-700">
                            <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                            <span class="text-red-600 mr-1 text-lg">*</span>
                            Carta de solicitud de retiro:
                        </label>
                        <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_carta_retiro">
                            <div class="grid gap-1">
                                <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-center text-gray-400 text-xs leading-4">Solo archivos de PDF de máximo {{ config('practicas.peso_maximo_archivos') }} MB</h2>
                            </div>
                            <div class="grid gap-2">
                                <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                <div class="flex items-center justify-center">
                                    <input type="file" name="carta_retiro[]" id="carta_retiro" class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf" />
                                    <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                </div>
                            </div>
                        </div>
                        <span id="carta_retiroError" class="text-red-500 text-sm"></span>
                        <ul id="file-list-retiro" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                        <span id="files-size-retiro" class="text-gray-800 text-sm"></span>
                    </div>

                    <div class="mb-4">
                        <label for="comentarios_config" class="block font-medium text-sm text-gray-700" style="margin-bottom: 5px;">
                            <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                            Comentarios de la solicitud:
                        </label>
                        <div id="txt-editor-config" class="txt-editor-quill shadow"></div>
                        <textarea name="comentarios_config" id="comentarios_config" class="hidden"></textarea>
                        <span id="comentarios_configError" class="text-red-500 text-sm"></span>
                    </div>

                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" onclick="closeConfigModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cancelar</button>
                        <button id="configModalButton" type="submit" class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                        <svg id="loadingSpinner-configModalResponse" style="margin: 4px 10px 4px 0" class="hidden text-gray-300 animate-spin w-4 h-4" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                            <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                        </svg>
                        Enviar
                    </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CONFIGURACIONES DE LA PRÁCTICA - ADMIN -->
<div id="configAdminModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeConfigAdminModal()">
        <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative mt-2" onclick="event.stopPropagation()">
                <button class="modal-close-btn-custom" onclick="closeConfigAdminModal()">&times;</button>
                <form class="p-6 mt-2" id="configAdminForm" enctype="multipart/form-data">
                    @csrf
                    <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="configAdminTitle"></p>
                    <input type="hidden" name="practica_id" value="{{ $practica->id }}">

                    @if ($fase_actual >= 3 && isset($director_actual) && isset($evaluador_actual))
                        <div class="mb-4">
                            <label for="director_id" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Director actual:
                            </label>
                            <select name="director_id" id="director_id-config" lang="es" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                <option value="" selected disabled>Selecciona una opción</option>
                                @foreach ($docentes as $docente)
                                    <option value="{{ $docente->id }}" @if (isset($director_actual) && $docente->id == $director_actual) selected @endif>{{ $docente->name }}</option>
                                @endforeach
                            </select>
                            <span id="director_idError" class="text-red-500 text-sm"></span>
                        </div>
                        <div class="mb-4">
                            <label for="evaluador_id" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Evaluador actual:
                            </label>
                            <select name="evaluador_id" id="evaluador_id-config" lang="es" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                <option value="" selected disabled>Selecciona una opción</option>
                                @foreach ($docentes as $docente)
                                    <option value="{{ $docente->id }}" @if (isset($evaluador_actual) && $docente->id == $evaluador_actual) selected @endif>{{ $docente->name }}</option>
                                @endforeach
                            </select>
                            <span id="evaluador_idError" class="text-red-500 text-sm"></span>
                        </div>
                    @endif

                    <!-- FECHAS DE LA PRÁCTICA (SOLO LECTURA) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block font-medium text-sm text-gray-700">
                            <i class="fa-regular fa-calendar mr-2 text-gray-500"></i>
                            Fecha de inicio de la práctica:
                        </label>
                        @php
                            $fechaInicio = $practica->valoresCampos->where('campo.name', 'fecha_inicio_practica')->first();
                            $fechaLimite = $practica->valoresCampos->where('campo.name', 'fecha_limite_practica')->first();
                        @endphp
                        <input type="text" 
                            value="{{ $fechaInicio ? \Carbon\Carbon::parse($fechaInicio->valor)->format('d/m/Y') : 'No definida' }}"
                            class="bg-gray-100 border-gray-300 rounded-md shadow-sm mt-1 block w-full cursor-default"
                            readonly>
                    </div>
                    <div>
                        <label class="block font-medium text-sm text-gray-700">
                            <i class="fa-regular fa-calendar-check mr-2 text-gray-500"></i>
                            Fecha límite actual:
                        </label>
                        <input type="text" 
                            value="{{ $fechaLimite ? \Carbon\Carbon::parse($fechaLimite->valor)->format('d/m/Y') : 'No definida' }}"
                            class="bg-gray-100 border-gray-300 rounded-md shadow-sm mt-1 block w-full cursor-default"
                            readonly>
                    </div>
                </div>

                        <!-- PRÓRROGA -->
                        @php
                            $prorrogas = (int) ($practica->valoresCampos->where('campo.name', 'solicitudes_prorroga')->first()?->valor ?? 0);
                        @endphp

                        @if($prorrogas < 2)
                            <div class="mb-4">
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="aprobar_prorroga_radio" id="aprobar_prorroga_si" class="mr-2" value="1">
                                        <span>Aprobar prórroga</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="aprobar_prorroga_radio" id="aprobar_prorroga_no" class="mr-2" value="0">
                                        <span>Rechazar prórroga</span>
                                    </label>
                                </div>
                                <input type="hidden" name="aprobar_prorroga" id="aprobar_prorroga" value="0">
                            </div>
                        @else
                            <div class="mb-4 p-3 border rounded-lg bg-gray-100">
                                <p class="text-sm text-gray-500 text-center">
                                    <i class="fa-solid fa-ban mr-2"></i>
                                    Límite de prórrogas alcanzado (2/2)
                                </p>
                            </div>
                        @endif
                    
                    <div class="mb-4">
                        <label for="retirar_estudiante" class="block font-medium text-sm text-gray-700">
                            <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                            Retirar estudiante:
                        </label>
                        <select name="retirar_estudiante" id="retirar_estudiante" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                            <option value="">Seleccione un estudiante</option>
                            @foreach ($lista_integrantes as $integrante)
                                <option value="{{ $integrante->id }}">{{ $integrante->name }}</option>
                            @endforeach
                        </select>
                        <span id="retirar_estudianteError" class="text-red-500 text-sm"></span>
                    </div>

                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="nro_acta_ajustes" class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>
                                Número de acta:
                            </label>
                            <input type="number" name="nro_acta_ajustes" id="nro_acta_ajustes" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500" placeholder="Ingrese el número de acta" required>
                            <span id="nro_acta_ajustesError" class="text-red-500 text-sm"></span>
                        </div>
                        <div>
                            <label for="fecha_acta_ajustes" class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-calendar-days mr-1 text-gray-500"></i>
                                Fecha del acta:
                            </label>
                            <input type="date" name="fecha_acta_ajustes" id="fecha_acta_ajustes" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                            <span id="fecha_acta_ajustesError" class="text-red-500 text-sm"></span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 mb-2">
                            <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                            Comentarios de la respuesta:
                        </label>
                        <div id="txt-editor-config-admin" class="txt-editor-quill shadow"></div>
                        <textarea name="comentarios_config_admin" id="comentarios_config_admin" class="hidden"></textarea>
                        <span id="comentarios_config_adminError" class="text-red-500 text-sm"></span>
                    </div>

                    <div class="mt-2 flex justify-end space-x-2">
                        <button type="button" onclick="closeConfigAdminModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cancelar</button>
                        <button id="configAdminButton" type="submit" class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                            <svg id="loadingSpinner-configAdminResponse" style="margin: 4px 10px 4px 0" class="hidden text-gray-300 animate-spin w-4 h-4" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                            </svg>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    
    @push('styles')
        <style>
            .hidden {
                display: none;
            }
            .card-fase {
                cursor: default;
                transition: all 0.3s ease;
            }

            .card-activated {
                background-color: #C1D631 !important;
                color: white !important;
            }

            .card-activated-animated {
                animation: pulse-animation 2s infinite;
            }

            @keyframes pulse-animation {
                0% {
                    box-shadow: 0 0 0 0 rgba(193, 214, 49, 0.4);
                }

                70% {
                    box-shadow: 0 0 0 15px rgba(193, 214, 49, 0);
                }

                100% {
                    box-shadow: 0 0 0 0 rgba(193, 214, 49, 0);
                }
            }

            .modal-overlay {
                transition: background-color 0.3s ease;
            }

            .modal-content {
                width: 100% !important;
            }

       
            /* Tooltips */
            [id^="tooltip-"] {
                background-color: #4b5563;
                color: white;
                padding: 8px 12px;
                border-radius: 6px;
                font-size: 0.75rem;
                line-height: 1.4;
                max-width: 200px;
                white-space: normal;
                word-wrap: break-word;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }

            .relative.inline-block [id^="tooltip-"] {
                position: absolute !important;
                bottom: calc(100% + 8px) !important;
                left: 50% !important;
                top: auto !important;
                transform: translateX(-50%) !important;
                z-index: 9999 !important;
            }

            [id^="tooltip-"]::before {
                content: '';
                position: absolute;
                bottom: -6px;
                left: 50%;
                transform: translateX(-50%);
                border-width: 6px 6px 0 6px;
                border-style: solid;
                border-color: #4b5563 transparent transparent transparent;
            }
            .show {
                display: block !important;
            }

            #fase1EstudianteModal.show,
            #fase1DetailsModal.show,
            #fase1AdminModal.show {
                display: flex !important;
            }

            .modal-close-btn-custom {
                z-index: 10;
            }

        </style>
    @endpush

    @push('scripts')

    <script>
        const MAX_FILE_SIZE_MB = {{ config('practicas.peso_maximo_archivos') }};
    </script>

    <script>
        const MAX_FILE_SIZE_PR = {{ config('practicas.peso_maximo_propuesta') }};
    </script>

    <script>
        const MAX_FILE_SIZE_IN = {{ config('practicas.peso_maximo_informe') }};
    </script>

        <script>
            // Variables globales con las rutas
            const ROUTES = {
                fase1_store: '{{ route('practicas.fase1.store') }}',
                fase1_details: '{{ route('practicas.fase1.details') }}',
                fase1_reply: '{{ route('practicas.fase1.reply') }}',
                fase2_store: '{{ route('practicas.fase2.store') }}',
                fase2_details: '{{ route('practicas.fase2.details') }}',
                fase2_reply: '{{ route('practicas.fase2.reply') }}',
                fase3_store: '{{ route('practicas.fase3.store') }}',
                fase3_details: '{{ route('practicas.fase3.details') }}',
                fase3_reply: '{{ route('practicas.fase3.reply') }}',
                fase4_reply: '{{ route('practicas.fase4.reply') }}',
                fase4_comite_reply: "{{ route('practicas.fase4.comite.reply') }}",
                fase5_store: '{{ route('practicas.fase5.store') }}',
                fase5_details: '{{ route('practicas.fase5.details') }}',
                fase5_reply: '{{ route('practicas.fase5.reply') }}',
                fase6_reply: '{{ route('practicas.fase6.reply') }}',
                fase6_comite_reply: "{{ route('practicas.fase6.comite.reply') }}",
                fase6_details: '{{ route('practicas.fase6.details') }}',
                fase7_details: '{{ route('practicas.fase7.details') }}'
            };
        </script>

        <script>
            window.quillUploadUrl = @json(route('practicas.quill.upload'));
            window.csrfToken = @json(csrf_token());
        </script>

        <script>

            // ========== REPORTE DE PROBLEMA ==========
            function openWarningModal() {
                // Inicializar Quill para warning
                if (!window.quillWarning) {
                    window.quillWarning = new Quill('#txt-editor-warning', {
                        theme: 'snow',
                        placeholder: 'Describa su reporte o inconveniente y déjenos saber sus recomendaciones.',
                        modules: {
                            toolbar: [
                                [{
                                    'header': 1
                                }], // H1
                                [{
                                    'header': 2
                                }], // H2
                                ['bold', 'italic', 'underline'], // Negrita, cursiva, subrayado
                                [{
                                    'list': 'ordered'
                                }, {
                                    'list': 'bullet'
                                }], // Numeración y viñetas
                                [{
                                    'color': []
                                }], // Selector de color
                                ['clean'] // Eliminar formato
                            ]
                        }
                    });
                } else {
                    window.quillWarning.root.innerHTML = '';
                }

                $('#warningModal').addClass('show');
            }

            function closeWarningModal() {
                $('#warningModal').removeClass('show');
                $('#warningForm')[0].reset();
                if (window.quillWarning) window.quillWarning.root.innerHTML = '';
            }

            $('#warningForm').on('submit', function (e) {
                e.preventDefault();

                const loadingSpinner = document.getElementById('loadingSpinner-warning');

                $('#mensaje_warning').val(window.quillWarning.root.innerHTML);

                const url = `${window.APP_URL}/reportes/enviar`;
                const method = 'POST';

                const formData = new FormData(this);

                formData.append('modulo', 'PRÁCTICAS EMPRESARIALES');

                Swal.fire({
                    heightAuto: false,
                    title: '¿Está seguro?',
                    text: "No podrá editar la información una vez se envíe",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#C1D631',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, enviar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        loadingSpinner.classList.remove('hidden');

                        $.ajax({
                            url: url,
                            method: method,
                            data: formData,
                            processData: false,
                            contentType: false,

                            success: function () {
                                closeWarningModal();
                                showToast('Reporte enviado correctamente');
                            },

                            error: function (xhr) {
                                const errors = xhr.responseJSON?.errors;
                                $('#mensaje_warningError').text(errors?.mensaje_warning?.[0] || '');
                            },

                            complete: function () {
                                loadingSpinner.classList.add('hidden');

                                if (window.quillWarning) {
                                    window.quillWarning.root.innerHTML = '';
                                }

                                $('#mensaje_warning').val('');
                            }
                        });
                    }
                });
            });
        </script>

        <script>
            function openCalendarModal(btn) 
            {
                // Mostrar spinner y ocultar icono en el botón
                if (btn) {
                    const icon = btn.querySelector('i');
                    const spinner = btn.querySelector('.loading-spinner');
                    if (icon) icon.classList.add('hidden');
                    if (spinner) spinner.classList.remove('hidden');
                    btn.disabled = true;
                }
        
                // Usar la clase 'show' en lugar de style.display
                const modal = document.getElementById('calendarModal');
                if (modal) {
                    modal.classList.add('show');
        
                } else {
                    console.error('Modal no encontrado');
                }
        
                // Restaurar el botón después de abrir
                if (btn) {
                    setTimeout(() => {
                        const icon = btn.querySelector('i');
                        const spinner = btn.querySelector('.loading-spinner');
                        if (icon) icon.classList.remove('hidden');
                        if (spinner) spinner.classList.add('hidden');
                        btn.disabled = false;
                    }, 200);
                }
            }

            function closeCalendarModal() 
            {
                const modal = document.getElementById('calendarModal');
                if (modal) {
                    modal.classList.remove('show');
      
                }
            }
        </script>

        <script>
    // ==================== ESTUDIANTE ====================

    $('#doc_icfes_practicas').on('change', function(e){

    const file = e.target.files[0];

    const fileList = $('#doc_icfes_practicas-file-list');

    fileList.empty();

    if (!file) return;

    const extension = file.name.split('.').pop().toLowerCase();

    if (extension !== 'pdf') {

        Swal.fire({
            icon: 'error',
            title: 'Formato inválido',
            text: 'Solo se permiten archivos PDF.',
            confirmButtonColor: '#C1D631'
        });

        $(this).val('');
        return;
    }

    const maxSizeBytes = MAX_FILE_SIZE_MB * 1024 * 1024;

    if (file.size > maxSizeBytes) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo demasiado grande',
            text: `El archivo no puede superar los ${MAX_FILE_SIZE_MB} MB.`,
            confirmButtonColor: '#C1D631'
        });

        $(this).val('');
        return;
    }

    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);

    fileList.html(`
        <li class="mb-2 mt-4">
            <div class="text-gray-600 text-sm mb-4">
                ${file.name}
            </div>
            <div class="text-sm text-gray-900">
                Tamaño total: ${fileSizeMB} MB de ${MAX_FILE_SIZE_MB} MB permitidos
            </div>
        </li>
    `);

});

    $(document).ready(function() {
        $('#icfesEstudianteForm').submit(function(e) {
    e.preventDefault();
    e.stopPropagation();


    const loadingSpinner = document.getElementById('loadingSpinner-icfesEstudiante');
    const url = '/practicas/icfes';

    const fileInput = document.getElementById('doc_icfes_practicas');

    // ================= VALIDAR EXISTENCIA =================
    if (!fileInput.files || fileInput.files.length === 0) {
        Swal.fire({
            title: 'Error',
            text: 'Debe seleccionar un archivo PDF',
            icon: 'error',
            confirmButtonColor: '#C1D631'
        });
        return false;
    }

    const file = fileInput.files[0];

    // ================= VALIDAR EXTENSIÓN =================
    const extension = file.name.split('.').pop().toLowerCase();

    if (extension !== 'pdf') {
        Swal.fire({
            title: 'Formato inválido',
            text: 'Solo se permiten archivos PDF.',
            icon: 'error',
            confirmButtonColor: '#C1D631'
        });

        fileInput.value = '';
        return false;
    }

    // ================= VALIDAR TAMAÑO =================
    const maxSizeBytes = MAX_FILE_SIZE_MB * 1024 * 1024;

    if (file.size > maxSizeBytes) {
        Swal.fire({
            title: 'Archivo demasiado grande',
            text: `El archivo no puede superar los ${MAX_FILE_SIZE_MB} MB.`,
            icon: 'error',
            confirmButtonColor: '#C1D631'
        });

        fileInput.value = '';
        return false;
    }

    // Crear FormData SOLO cuando todo esté validado
    const formData = new FormData(this);

    Swal.fire({
        heightAuto: false,
        title: '¿Está seguro?',
        text: 'No podrá editar la información una vez se envíe',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C1D631',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {

        if (result.isConfirmed) {

            if (loadingSpinner) {
                loadingSpinner.classList.remove('hidden');
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    closeIcfesEstudianteModal();

                    showToast(
                        response.success || 'Información enviada correctamente',
                        'success'
                    );

                    setTimeout(() => location.reload(), 3000);
                },
                error: function(xhr) {

                    if (xhr.responseJSON && xhr.responseJSON.errors) {

                        const errors = xhr.responseJSON.errors;

                        $('#doc_icfes_practicasError')
                            .text(errors.doc_icfes_practicas?.[0] || '');

                    } else {

                        Swal.fire({
                            title: 'Error',
                            text: xhr.responseJSON?.error || 'Ocurrió un error',
                            icon: 'error',
                            confirmButtonColor: '#C1D631'
                        });

                    }
                },
                complete: function() {

                    if (loadingSpinner) {
                        loadingSpinner.classList.add('hidden');
                    }

                }
            });
        }
    });

    return false;
});
        
        // ==================== ADMIN ====================
        $('#icfesAdminForm').on('submit', function(e) {
            e.preventDefault();

            const loadingSpinner = document.getElementById('loadingSpinner-icfesAdmin');
            const url = '/practicas/icfes/responder';
            const method = 'POST';
            const formData = new FormData(this);

            // Validar campos requeridos
            const estado = $('#estado_icfes_practicas').val();
            if (!estado) {
                $('#estado_icfes_practicasError').text('Debe seleccionar un estado');
                return;
            }
            
            const estudianteId = $('#estudiante_id_practicas').val();
            if (!estudianteId) {
                $('#estudiante_id_practicasError').text('Debe seleccionar un integrante');
                return;
            }

            Swal.fire({
                heightAuto: false,
                title: '¿Está seguro?',
                text: "No podrá editar la respuesta una vez se envíe",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#C1D631',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, enviar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    loaderGeneral.classList.replace('hidden', 'flex');
                    loadingSpinner.classList.remove('hidden');

                    $.ajax({
                        url: url,
                        method: method,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            closeIcfesAdminModal();
                            showToast('Respuesta enviada correctamente', 'success');
                            setTimeout(() => location.reload(), 1500);
                        },
                        error: function (xhr) {
                            const errors = xhr.responseJSON.errors;
                            $('#estado_icfes_practicasError').text(errors?.estado_icfes_practicas?.[0] || '');
                            $('#estudiante_id_practicasError').text(errors?.estudiante_id?.[0] || '');
                            $('#nro_acta_icfes_practicasError').text(errors?.nro_acta_icfes_practicas?.[0] || '');
                            $('#fecha_acta_icfes_practicasError').text(errors?.fecha_acta_icfes_practicas?.[0] || '');
                            $('#respuesta_icfes_practicasError').text(errors?.respuesta_icfes_practicas?.[0] || '');
                        },
                        complete: function () {
                            loaderGeneral.classList.replace('flex', 'hidden');
                            loadingSpinner.classList.add('hidden');
                        }
                    });
                }
            });
        });
    });

    // ==================== FUNCIONES ====================
    
    function openIcfesEstudianteModal() {
        $('#icfesEstudianteTitle').html(`Beneficio saber <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">TYT/PRO</span>`);
        $('#doc_icfes_practicas').val('');
        $('#doc_icfes_practicasError').text('');
        $('#icfesEstudianteModal').addClass('show');
    }

    function closeIcfesEstudianteModal() {
        $('#icfesEstudianteModal').removeClass('show');
    }

    function openIcfesAdminModal() {
        // Inicializar Quill como en Fase 5
        initQuillEditor(undefined, "Describa los detalles de la respuesta para el estudiante.", 'txt-editor-icfes-practicas', 'respuesta_icfes_practicas');

        $('#icfesAdminTitle').html(`Beneficio saber <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">TYT/PRO</span>`);

        // Inicializar Select2 una sola vez
        if (!$('#estudiante_id_practicas').hasClass("select2-hidden-accessible")) {
            $('#estudiante_id_practicas').select2({
                dropdownParent: $('#icfesAdminModal'),
                placeholder: 'Buscar estudiante',
                allowClear: true,
                width: '100%'
            });
        }
        
        // Limpiar campos
        $('#estado_icfes_practicas').val('').trigger('change');
        $('#estudiante_id_practicas').val(null).trigger('change');
        $('#nro_acta_icfes_practicas').val('');
        $('#fecha_acta_icfes_practicas').val('');
        $('#respuesta_icfes_practicas').val('');

        // Limpiar errores
        $('#estado_icfes_practicasError').text('');
        $('#estudiante_id_practicasError').text('');
        $('#nro_acta_icfes_practicasError').text('');
        $('#fecha_acta_icfes_practicasError').text('');
        $('#respuesta_icfes_practicasError').text('');

        $('#icfesAdminModal').addClass('show');
    }

    function closeIcfesAdminModal() {
        $('#icfesAdminModal').removeClass('show');
    }
</script>

<script>

    $(document).ready(function () {

    // ================= CARTA PRÓRROGA =================
    $('#carta_prorroga').on('change', function (e) {

        const file = e.target.files[0];
        const fileList = $('#file-list-prorroga-carta');

        fileList.empty();

        if (!file) return;

        const extension = file.name.split('.').pop().toLowerCase();

        if (extension !== 'pdf') {

            Swal.fire({
                icon: 'error',
                title: 'Formato inválido',
                text: 'Solo se permiten archivos PDF.',
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });

            $(this).val('');
            return;
        }

        const maxSizeBytes = MAX_FILE_SIZE_MB * 1024 * 1024;

        if (file.size > maxSizeBytes) {

            Swal.fire({
                icon: 'error',
                title: 'Archivo demasiado grande',
                text: `El archivo no puede superar los ${MAX_FILE_SIZE_MB} MB.`,
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });

            $(this).val('');
            return;
        }

        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);

        fileList.html(`
            <li class="mb-2 mt-4">
                <div class="text-gray-600 text-sm mb-4">
                    ${file.name}
                </div>
                <div class="text-sm text-gray-900">
                    Tamaño total: ${fileSizeMB} MB de ${MAX_FILE_SIZE_MB} MB permitidos
                </div>
            </li>
        `);

    });

    // ================= LIQUIDACIÓN PRÓRROGA =================
    $('#liquidacion_prorroga').on('change', function (e) {

        const file = e.target.files[0];
        const fileList = $('#file-list-liquidacion-prorroga');

        fileList.empty();

        if (!file) return;

        const extension = file.name.split('.').pop().toLowerCase();

        if (extension !== 'pdf') {

            Swal.fire({
                icon: 'error',
                title: 'Formato inválido',
                text: 'Solo se permiten archivos PDF.',
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });

            $(this).val('');
            return;
        }

        const maxSizeBytes = MAX_FILE_SIZE_MB * 1024 * 1024;

        if (file.size > maxSizeBytes) {

            Swal.fire({
                icon: 'error',
                title: 'Archivo demasiado grande',
                text: `El archivo no puede superar los ${MAX_FILE_SIZE_MB} MB.`,
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });

            $(this).val('');
            return;
        }

        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);

        fileList.html(`
            <li class="mb-2 mt-4">
                <div class="text-gray-600 text-sm mb-4">
                    ${file.name}
                </div>
                <div class="text-sm text-gray-900">
                    Tamaño total: ${fileSizeMB} MB de ${MAX_FILE_SIZE_MB} MB permitidos
                </div>
            </li>
        `);

    });

    // ================= SOPORTE PRÓRROGA =================
    $('#soporte_prorroga').on('change', function (e) {

        const file = e.target.files[0];
        const fileList = $('#file-list-soporte-prorroga');

        fileList.empty();

        if (!file) return;

        const extension = file.name.split('.').pop().toLowerCase();

        if (extension !== 'pdf') {

            Swal.fire({
                icon: 'error',
                title: 'Formato inválido',
                text: 'Solo se permiten archivos PDF.',
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });

            $(this).val('');
            return;
        }

        const maxSizeBytes = MAX_FILE_SIZE_MB * 1024 * 1024;

        if (file.size > maxSizeBytes) {

            Swal.fire({
                icon: 'error',
                title: 'Archivo demasiado grande',
                text: `El archivo no puede superar los ${MAX_FILE_SIZE_MB} MB.`,
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });

            $(this).val('');
            return;
        }

        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);

        fileList.html(`
            <li class="mb-2 mt-4">
                <div class="text-gray-600 text-sm mb-4">
                    ${file.name}
                </div>
                <div class="text-sm text-gray-900">
                    Tamaño total: ${fileSizeMB} MB de ${MAX_FILE_SIZE_MB} MB permitidos
                </div>
            </li>
        `);

    });

    // ================= CARTA RETIRO =================
    $('#carta_retiro').on('change', function (e) {

        const file = e.target.files[0];
        const fileList = $('#file-list-retiro');

        fileList.empty();

        if (!file) return;

        const extension = file.name.split('.').pop().toLowerCase();

        if (extension !== 'pdf') {

            Swal.fire({
                icon: 'error',
                title: 'Formato inválido',
                text: 'Solo se permiten archivos PDF.',
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });

            $(this).val('');
            return;
        }

        const maxSizeBytes = MAX_FILE_SIZE_MB * 1024 * 1024;
        
        if (file.size > maxSizeBytes) {

            Swal.fire({
                icon: 'error',
                title: 'Archivo demasiado grande',
                text: `El archivo no puede superar los ${MAX_FILE_SIZE_MB} MB.`,
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });

            $(this).val('');
            return;
        }

        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);

        fileList.html(`
            <li class="mb-2 mt-4">
                <div class="text-gray-600 text-sm mb-4">
                    ${file.name}
                </div>
                <div class="text-sm text-gray-900">
                    Tamaño total: ${fileSizeMB} MB de ${MAX_FILE_SIZE_MB} MB permitidos
                </div>
            </li>
        `);

    });

});

    function openConfigModal() {

    initQuillEditor(undefined, "Describa su solicitud detalladamente.", 'txt-editor-config', 'comentarios_config');

    $('#configModalTitle').html(`Ajustes de la <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Práctica</span>`);

    $('#tipo_solicitud').val('').trigger('change');
    $('#carta_prorroga').val('');
    $('#liquidacion_prorroga').val('');
    $('#soporte_prorroga').val('');
    $('#carta_retiro').val('');
    $('#comentarios_config').val('');

    $('#tipo_solicitudError').text('');
    $('#carta_prorrogaError').text('');
    $('#liquidacion_prorrogaError').text('');
    $('#soporte_prorrogaError').text('');
    $('#carta_retiroError').text('');
    $('#comentarios_configError').text('');

    $('#configModal').addClass('show');
}

function closeConfigModal() {
    $('#configModal').removeClass('show');
}

var directorOriginalGlobal = null;
var evaluadorOriginalGlobal = null;

    var prorrogaOriginalValue = false;

function openConfigAdminModal() {

    // ================= SELECT2 =================
    if (!$('#director_id-config').hasClass('select2-hidden-accessible')) {
        $('#director_id-config').select2({
            dropdownParent: $('#configAdminModal'),
            width: '100%',
            minimumInputLength: 5,
        });
    }

    if (!$('#evaluador_id-config').hasClass('select2-hidden-accessible')) {
        $('#evaluador_id-config').select2({
            dropdownParent: $('#configAdminModal'),
            width: '100%',
            minimumInputLength: 5,
        });
    }

    if (!$('#retirar_estudiante').hasClass('select2-hidden-accessible')) {
        $('#retirar_estudiante').select2({
            dropdownParent: $('#configAdminModal'),
            placeholder: 'Seleccione el estudiante para retirar',
            allowClear: true,
            width: '100%',
        });
    }

    initQuillEditor(undefined, "Describa la respuesta para el estudiante.", 'txt-editor-config-admin', 'comentarios_config_admin');

    $('#configAdminTitle').html(`Ajustes de la <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Práctica</span>`);

    // Guardar valores ORIGINALES (los que vienen de BD)
    directorOriginalGlobal = $('#director_id-config').val();
    evaluadorOriginalGlobal = $('#evaluador_id-config').val();

        prorrogaOriginalValue = $('input[name="aprobar_prorroga_radio"]:checked').val();

    // Limpiar otros campos
    $('#retirar_estudiante').val('').trigger('change');
    $('#nro_acta_ajustes').val('');
    $('#fecha_acta_ajustes').val('');
    $('#comentarios_config_admin').val('');

    $('#director_idError').text('');
    $('#evaluador_idError').text('');
    $('#retirar_estudianteError').text('');
    $('#nro_acta_ajustesError').text('');
    $('#fecha_acta_ajustesError').text('');
    $('#comentarios_config_adminError').text('');

    $('#configAdminModal').addClass('show');
}

function closeConfigAdminModal() {
    $('#configAdminModal').removeClass('show');
}

$('#configAdminForm').on('submit', function(e) {
    e.preventDefault();
    
    var directorActual = $('#director_id-config').val();
    var evaluadorActual = $('#evaluador_id-config').val();
    var estudianteRetiro = $('#retirar_estudiante').val();
    var nroActa = $('#nro_acta_ajustes').val();
    var fechaActa = $('#fecha_acta_ajustes').val();
    
    // ===== CAMBIO AQUÍ =====
    var prorrogaActual = $('input[name="aprobar_prorroga_radio"]:checked').val();
    // Detectar si hay cambio en la prórroga (aprobación o rechazo)
    var hayProrroga = (prorrogaActual !== null && prorrogaActual !== '' && prorrogaActual !== prorrogaOriginalValue);
    // ===== FIN CAMBIO =====

    // Obtener comentarios del Quill
    var quill = Quill.find(document.querySelector('#txt-editor-config-admin'));
    var comentarios = quill ? quill.root.innerHTML : '';
    $('#comentarios_config_admin').val(comentarios);
    
    // Validar campos requeridos
    if (!nroActa || nroActa.trim() === '') {
        Swal.fire('Error', 'Debe ingresar el número de acta', 'error');
        return;
    }
    if (!fechaActa) {
        Swal.fire('Error', 'Debe ingresar la fecha del acta', 'error');
        return;
    }
    
    // Comparar cambios con valores originales
    var hayCambioDirector = (directorActual !== directorOriginalGlobal);
    var hayCambioEvaluador = (evaluadorActual !== evaluadorOriginalGlobal);
    var hayRetiro = (estudianteRetiro !== null && estudianteRetiro !== '');

    if (!hayCambioDirector && !hayCambioEvaluador && !hayRetiro && !hayProrroga) {
        Swal.fire({
            icon: 'info',
            title: 'Sin cambios',
            text: 'Debe realizar al menos un cambio',
            heightAuto: false
        });
        return;
    }
    
    // Mostrar qué cambios se van a aplicar
    let mensajeCambios = '';
    if (hayCambioDirector) mensajeCambios += '- Cambio de director\n';
    if (hayCambioEvaluador) mensajeCambios += '- Cambio de evaluador\n';
    if (hayRetiro) mensajeCambios += '- Retiro de estudiante\n';
    if (hayProrroga) {
        var radioValor = $('input[name="aprobar_prorroga_radio"]:checked').val();
        if (radioValor === '1') {
            mensajeCambios += '- Aprobación de prórroga (+90 días)\n';
        } else {
            mensajeCambios += '- Rechazo de prórroga\n';
        }
    }
    
    Swal.fire({
        heightAuto: false,
        title: '¿Está seguro?',
        html: `Se aplicarán los siguientes cambios:<br><br>${mensajeCambios.replace(/\n/g, '<br>')}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C1D631',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const button = $('#configAdminButton');
            const spinner = $('#loadingSpinner-configAdminResponse');
            
            button.prop('disabled', true);
            spinner.removeClass('hidden');
            
            // ========== Sincronizar radio button con el campo hidden ==========
            var valorRadio = $('input[name="aprobar_prorroga_radio"]:checked').val();
            $('input[name="aprobar_prorroga"]').val(valorRadio || '0');
            // ========================================================================
            
            var formData = new FormData(this);
            formData.set('comentarios_config_admin', comentarios);

            console.log('Valor del campo hidden aprobar_prorroga:', $('input[name="aprobar_prorroga"]').val());
            console.log('Radio seleccionado:', $('input[name="aprobar_prorroga_radio"]:checked').val());

            $.ajax({
                url: '{{ route("practicas.configurar_admin") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(response) {
                    closeConfigAdminModal();
                    showToast(response.success, 'success');
                    setTimeout(() => location.reload(), 3000);
                },
                error: function(xhr) {
                    console.log(xhr.responseJSON);
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            $('#' + key + 'Error').text(value[0]);
                        });
                        Swal.fire('Error', 'Por favor complete los campos requeridos', 'error');
                    } else {
                        Swal.fire('Error', xhr.responseJSON.error || 'Ocurrió un error', 'error');
                    }
                },
                complete: function() {
                    button.prop('disabled', false);
                    spinner.addClass('hidden');
                }
            });
        }
    });
});

function closeConfigAdminModal() {
    $('#configAdminModal').removeClass('show');
}

        // Mostrar/ocultar campos según tipo de solicitud
        $(document).ready(function() {
            $('#tipo_solicitud').on('change', function() {
                var tipo = $(this).val();
                $('#container-doc_prorroga_config').addClass('hidden');
                $('#container-doc_retiro_config').addClass('hidden');
                if (tipo === 'prorroga') {
                    $('#container-doc_prorroga_config').removeClass('hidden');
                } else if (tipo === 'retiro') {
                    $('#container-doc_retiro_config').removeClass('hidden');
                }
            });
        });

    </script>

        <script>

        // Submit del formulario de estudiante
        $('#configModalForm').on('submit', function(e) {
            e.preventDefault();
    
    // Validar que se haya seleccionado un tipo de solicitud
    const tipoSolicitud = $('#tipo_solicitud').val();
    if (!tipoSolicitud) {
        Swal.fire('Error', 'Debe seleccionar un tipo de solicitud', 'error');
        return;
    }
    
    // Validar archivos según tipo de solicitud
    if (tipoSolicitud === 'prorroga') {
        const cartaProrroga = $('#carta_prorroga')[0].files[0];
        const liquidacion = $('#liquidacion_prorroga')[0].files[0];
        const soporte = $('#soporte_prorroga')[0].files[0];
        
        if (!cartaProrroga) {
            Swal.fire('Error', 'Debe adjuntar la carta de solicitud de prórroga', 'error');
            return;
        }
        if (!liquidacion) {
            Swal.fire('Error', 'Debe adjuntar la liquidación de prórroga', 'error');
            return;
        }
        if (!soporte) {
            Swal.fire('Error', 'Debe adjuntar el soporte de pago de prórroga', 'error');
            return;
        }
    }

    if (tipoSolicitud === 'retiro') {
        const cartaRetiro = $('#carta_retiro')[0].files[0];
        if (!cartaRetiro) {
            Swal.fire('Error', 'Debe adjuntar la carta de solicitud de retiro', 'error');
            return;
        }
    }
    
    Swal.fire({
        heightAuto: false,
        title: '¿Está seguro?',
        text: "No podrá editar la información una vez se envíe",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C1D631',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const button = $('#configModalButton');
            const spinner = $('#loadingSpinner-configModalResponse');
            
            button.prop('disabled', true);
            spinner.removeClass('hidden');
            
            var formData = new FormData(this);
            
            $.ajax({
                url: '{{ route("practicas.configurar_estudiante") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(response) {
                    closeConfigModal();
                    showToast(response.success, 'success');
                    setTimeout(() => location.reload(), 3000);
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            $('#' + key + 'Error').text(value[0]);
                        });
                        Swal.fire('Error', 'Por favor complete los campos requeridos', 'error');
                    } else {
                        Swal.fire('Error', xhr.responseJSON.error || 'Ocurrió un error', 'error');
                    }
                },
                complete: function() {
                    button.prop('disabled', false);
                    spinner.addClass('hidden');
                }
            });
        }
    });
});

        function showToast(message, type = 'success') {
    Swal.fire({
        title: type === 'success' ? '¡Éxito!' : 'Error',
        text: message,
        icon: type,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

   
}

        </script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('js/fases/practicas/fase_0.js') }}"></script>
        <script src="{{ asset('js/fases/practicas/fase_1.js') }}"></script>
        <script src="{{ asset('js/fases/practicas/fase_2.js') }}"></script>
        <script src="{{ asset('js/fases/practicas/fase_3.js') }}"></script>
        <script src="{{ asset('js/fases/practicas/fase_4.js') }}"></script>
        <script src="{{ asset('js/fases/practicas/fase_5.js') }}"></script>
        <script src="{{ asset('js/fases/practicas/fase_6.js') }}"></script>
        <script src="{{ asset('js/fases/practicas/fase_final.js') }}"></script>
    @endpush
</x-app-layout>
