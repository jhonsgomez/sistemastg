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
            #fase5EstudianteModal,
            #fase5DetailsModal {
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
            #fase5EstudianteModal.show,
            #fase5DetailsModal.show {
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
                width: 1.25rem;
                height: 1.25rem;
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

        </style>
    @endPush

    <div>

        <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-b">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center">
                Seguimiento de <span
                    class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase">Prácticas</span>
            </h2>

            <!-- Botones de acción en el header -->
            <div class="flex justify-center items-center space-x-2 buttons-container">
                <!-- Botón Alertas (Rojo) -->
                <button type="button" id="warning" onclick="openWarningModal()"
                    class="btn-action shadow bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded-lg relative">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <svg id="loadingSpinner-warningOpen" style="margin: 4px 1px"
                        class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

                <!-- Botón Calendario (Verde) -->
                <!--- AQUI SOLO VA LA VARIABLE $fechas--->
            <button type="button" id="calendar" onclick="openCalendarModal(this)"
    class="btn-action shadow bg-uts-500 hover:bg-uts-800 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
    <i class="fa-regular fa-calendar"></i>
    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none"
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

                <!-- Botón Configuración (Gris) -->
                <button onclick="openConfiguracionModal()"
                    class="btn-action bg-gray-500 hover:bg-gray-700 text-white rounded-lg transition flex items-center">
                    <i class="fa-solid fa-gear"></i>
                </button>

                <!-- Botón Volver (Verde) -->
                <a href="{{ route('practicas.index') }}"
                    class=" bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg transition flex items-center">
                    <i class="fa-solid fa-arrow-rotate-left mr-2"></i>
                    <span>Volver</span>
                </a>
            </div>
        </div>
        
        
        <!-- Nota informativa -->
        
        <!-- Container donde aparecen las fases -->
        <div id="px-6">
            <p class="text-gray-700 mt-6">
                Aquí podrás llevar el seguimiento de tus prácticas empresariales en curso.
            </p>
            <div class="mt-6 grid w-full grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">

                <!-- FASE 1 -->
<div id="fase-1"
    class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $fase_actual >= 1 ? 'card-activated' : '' }} {{ $fase_actual == 1 ? 'card-activated-animated' : '' }}">

    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-10">
        <path fill-rule="evenodd"
            d="M5.625 1.5H9a3.75 3.75 0 0 1 3.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 0 1 3.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 0 1-1.875-1.875V3.375c0-1.036.84-1.875 1.875-1.875Zm6.905 9.97a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.72-1.72V18a.75.75 0 0 0 1.5 0v-4.19l1.72 1.72a.75.75 0 1 0 1.06-1.06l-3-3Z"
            clip-rule="evenodd" />
        <path
            d="M14.25 5.25a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963A5.23 5.23 0 0 0 16.5 7.5h-1.875a.375.375 0 0 1-.375-.375V5.25Z" />
    </svg>

    <span class="text-center font-bold text-lg">Fase 1: F-DC-126</span>
    <p class="text-center mt-2 text-xs mx-4">El estudiante envía el formato de solicitud de practicantes.</p>

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
                <button type="button" onclick="openFase1EstudianteModal()"
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
                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                    </svg>
                </button>

                <!-- Botón Responder con spinner -->
                @if ($yaEnvio)
                    <button type="button" onclick="openFase1AdminModal(this)"
                        class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center">
                        <i class="fa-solid fa-share"></i>
                        <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                        </svg>
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
                <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                </svg>
            </button>
        </div>
    @endif
</div>

                <!-- FASE 2: Pago de modalidad -->
<div id="fase-2"
    class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $fase_actual >= 2 ? 'card-activated' : '' }} {{ $fase_actual == 2 ? 'card-activated-animated' : '' }}">

    <i class="fa-solid fa-circle-check" style="font-size: 32px; margin-bottom: 10px;"></i>

    <span class="text-center font-bold text-lg">Fase 2: Pago</span>
    <p class="text-center mt-2 text-xs mx-4">El estudiante sube la liquidación y soporte de pago.</p>

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
                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                    </svg>
                </button>

                <!-- Botón Responder con spinner (solo si el estudiante ya envió) -->
                @if ($yaEnvio)
                    <button type="button" onclick="openFase2AdminModal(this)"
                        class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center">
                        <i class="fa-solid fa-share"></i>
                        <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                </svg>
            </button>
        </div>
    @endif
</div>

                <!-- FASE 3: PROPUESTA I -->
<div id="fase-3"
    class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $fase_actual >= 3 ? 'card-activated' : '' }} {{ $fase_actual == 3 ? 'card-activated-animated' : '' }}">

    <i class="fa-solid fa-hourglass-half" style="font-size: 32px; margin-bottom: 10px;"></i>

    <span class="text-center font-bold text-lg">Fase 3: Propuesta I</span>

    <p class="text-center mt-2 text-xs mx-4">
        El estudiante envía la propuesta al director.
    </p>

    @if ($fase_actual == 3)

        @php
            $user = auth()->user();
            
            $esEstudiante = $user->hasRole('estudiante');
            $esDirector = $user->hasRole(['super_admin', 'admin', 'coordinador', 'director_practica']);
            $esEvaluador = $user->hasRole(['evaluador_practica']);
            $esComite = $user->hasRole(['super_admin', 'admin', 'coordinador']);
            
            // Obtener valores desde la práctica
            $submited_fase3_valor = $practica->valoresCampos->where('campo.name', 'submited_fase3')->first();
            $estado_director_valor = $practica->valoresCampos->where('campo.name', 'estado_director_fase3')->first();
            
            $estudianteYaEnvio = $submited_fase3_valor && $submited_fase3_valor->valor == 'true';
            $directorYaRespondio = $estado_director_valor && ($estado_director_valor->valor == 'Aprobada' || $estado_director_valor->valor == 'Rechazada');
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
                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                    </svg>
                </button>
                
                <button type="button"
                    onclick="openFase3EstudianteModal(this)"
                    class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
                    <i class="fa-solid fa-user-pen"></i>
                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 24 24">
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
                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
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
                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center w-10 h-10">
                    <i class="fa-solid fa-eye"></i>
                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                    </svg>
                </button>

                <button type="button"
                    onclick="openFase3DirModal(this)"
                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center w-10 h-10">
                    <i class="fa-solid fa-reply"></i>
                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
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
                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
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
                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
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
                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
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
                <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                </svg>
            </button>
        </div>
    @endif
</div>

                <!-- FASE 4: PROPUESTA II -->
<div id="fase-4" class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $fase_actual >= 4 ? 'card-activated' : '' }} {{ $fase_actual == 4 ? 'card-activated-animated' : '' }}">
    <i class="fa-solid fa-paper-plane" style="font-size: 32px; margin-bottom: 10px;"></i>
    <span class="text-center font-bold text-lg">Fase 4: Propuesta II</span>
    <p class="text-center mt-2 text-xs mx-4">El evaluador revisa la propuesta del director.</p>
            
    @if ($fase_actual == 4)
        @php
            $user = auth()->user();
            
            $esDirector = $user->hasRole(['super_admin', 'admin', 'coordinador', 'director_practica']);
            $esEvaluador = $user->hasRole(['evaluador_practica']);
            $esComite = $user->hasRole(['super_admin', 'admin', 'coordinador']);
            $esEstudiante = $user->hasRole('estudiante');
            
            // Obtener el estado del director en Fase 3
            $estado_director_fase3_valor = $practica->valoresCampos->where('campo.name', 'estado_director_fase3')->first();
            $estado_evaluador_fase4_valor = $practica->valoresCampos->where('campo.name', 'estado_evaluador_fase4')->first();
            
            // El director aprobó en Fase 3 (eso permite que el evaluador vea y responda en Fase 4)
            $directorAproboFase3 = $estado_director_fase3_valor && $estado_director_fase3_valor->valor == 'Aprobada';
            
            $evaluadorYaRespondio = $estado_evaluador_fase4_valor && ($estado_evaluador_fase4_valor->valor == 'Aprobada' || $estado_evaluador_fase4_valor->valor == 'Rechazada');
            $evaluadorAprobo = $estado_evaluador_fase4_valor && $estado_evaluador_fase4_valor->valor == 'Aprobada';
        @endphp

        
        {{-- ================= EVALUADOR (puede ver y responder) ================= --}}
        @if ($esEvaluador && $directorAproboFase3 && !$evaluadorYaRespondio)
        <div class="flex justify-center items-center mt-3 gap-2">
            <button type="button"
            onclick="openFase3DetailsModal(this)"
            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center w-10 h-10">
            <i class="fa-solid fa-eye"></i>
            <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                    </svg>
                </button>
                
                <button type="button"
                onclick="openFase4EvaluadorModal(this)"
                class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center w-10 h-10">
                <i class="fa-solid fa-reply"></i>
                <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
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
            <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
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
                            <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                            </svg>
                        </button>
                    </div>
                
        {{-- ================= COMITÉ FASE 4 ================= --}}
        @elseif ($esComite && $evaluadorAprobo)
            <div class="flex justify-center items-center mt-3 gap-2">

                {{-- VER --}}
                 <div class="flex justify-center items-center mt-3">
            <button type="button"
            onclick="openFase3DetailsModal(this)"
            class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg relative inline-flex items-center justify-center">
            <i class="fa-solid fa-eye"></i>
            <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
            </svg>
                    </button>
                </div>

                {{-- EDITAR / RESPONDER --}}
                <button type="button"
                    onclick="openFase4ComiteModal(this)"
                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg relative inline-flex items-center justify-center w-10 h-10">
                    <i class="fa-solid fa-reply"></i>
                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
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
                <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                </svg>
            </button>
        </div>
    @endif
    
    
</div>



                <!-- FASE 5. INFORME I -->
                <div
                    class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $fase_actual >= 5 ? 'card-activated' : '' }} {{ $fase_actual == 5 ? 'card-activated-animated' : '' }}">
                    <i class="fa-solid fa-hourglass-half" style="font-size: 32px; margin-bottom: 10px;"></i>
                    <span class="text-center font-bold text-lg">Fase 5: Informe I</span>
                    <p class="text-center mt-2 text-xs mx-4">El estudiante envía el informe al director.</p>

                        @if ($fase_actual == 5)

                        @php
                            $user = auth()->user();
                            $esEstudiante = $user->hasRole('estudiante');
                            $esDirector = $user->hasRole('director_practica');
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
                                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute"
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

                            </button>

                        </div>

                    @endif
                </div>

                <!-- FASE 6: INFORME II -->
                <div id="fase-6"
                    class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase
                    {{ $fase_actual >= 6 ? 'card-activated' : '' }}
                    {{ $fase_actual == 6 ? 'card-activated-animated' : '' }}">

                    <i class="fa-solid fa-paper-plane" style="font-size: 32px; margin-bottom: 10px;"></i>

                    <span class="text-center font-bold text-lg">Fase 6: Informe II</span>

                    <p class="text-center mt-2 text-xs mx-4">
                        El director envía el informe al evaluador y comité.
                    </p>

                    @if ($fase_actual == 6)

                        @php
                            $user = auth()->user();

                            $esDirector = $user->hasRole(['super_admin', 'admin', 'coordinador', 'director_practica']);
                            $esEvaluador = $user->hasRole(['evaluador_practica']);
                            $esComite = $user->hasRole(['super_admin', 'admin', 'coordinador']);
                            $esEstudiante = $user->hasRole('estudiante');

                            $estado_director_fase6_valor = $practica->valoresCampos
                                ->where('campo.name', 'estado_director_fase5')
                                ->first();

                            $estado_evaluador_fase6_valor = $practica->valoresCampos
                                ->where('campo.name', 'estado_evaluador_fase6')
                                ->first();

                            $directorAproboFase6 = $estado_director_fase6_valor &&
                                $estado_director_fase6_valor->valor == 'Aprobada';

                            $evaluadorYaRespondio = $estado_evaluador_fase6_valor &&
                                in_array($estado_evaluador_fase6_valor->valor, ['Aprobada', 'Rechazada']);

                            $evaluadorAprobo = $estado_evaluador_fase6_valor &&
                                $estado_evaluador_fase6_valor->valor == 'Aprobada';
                        @endphp

                        {{-- ================= DIRECTOR ================= --}}
                        @if ($esDirector)

                            <div class="flex justify-center items-center mt-3 gap-2">

                                <button type="button"
                                    onclick="openFase5DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg w-10 h-10 flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                            </div>


                        {{-- ================= EVALUADOR ================= --}}
               
                        @elseif ($esEvaluador && $directorAproboFase6)

                            <div class="flex justify-center items-center mt-3 gap-2">

                                <button type="button"
                                    onclick="openFase6DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg w-10 h-10 flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                                @if (!$evaluadorYaRespondio)
                                    <button type="button"
                                        onclick="openFase6EvaluadorModal(this)"
                                        class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg w-10 h-10 flex items-center justify-center">
                                        <i class="fa-solid fa-reply"></i>
                                    </button>
                                @endif

                            </div>


                        {{-- ================= COMITÉ ================= --}}
                        @elseif ($esComite && $evaluadorAprobo)

                            <div class="flex justify-center items-center mt-3 gap-2">

                                <button type="button"
                                    onclick="openFase6DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg w-10 h-10 flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                                <button type="button"
                                    onclick="openFase6ComiteModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg w-10 h-10 flex items-center justify-center">
                                    <i class="fa-solid fa-reply"></i>
                                </button>

                            </div>


                        {{-- ================= ESTUDIANTE ================= --}}
                        @elseif ($esEstudiante)

                            <div class="flex justify-center items-center mt-3">

                                <button type="button"
                                    onclick="openFase6DetailsModal(this)"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg w-10 h-10 flex items-center justify-center">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                            </div>

                        @endif


                    @elseif ($fase_actual > 6)

                        <div class="flex justify-center items-center mt-3">

                            <button type="button"
                                onclick="openFase6DetailsModal(this)"
                                class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white rounded-lg w-10 h-10 flex items-center justify-center">
                                <i class="fa-solid fa-eye"></i>
                            </button>

                        </div>

                    @endif

                </div>

                <!-- FASE 7 -->
                <div
                    class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $fase_actual >= 7 ? 'card-activated' : '' }} {{ $fase_actual == 7 ? 'card-activated-animated' : '' }}">
                    <i class="fa-solid fa-user-graduate" style="font-size: 32px; margin-bottom: 10px;"></i>
                    <span class="text-center font-bold text-lg">Fase Final</span>
                    <p class="text-center mt-2 text-xs mx-4">Estudiantes, director y evaluador programan sustentación.
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- ==================== Modal FASE 1 ==================== -->

    <!-- Modal FASE 1 - Estudiante (Enviar documentos) -->
    <div id="fase1EstudianteModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0"
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;"
            onclick="closeFase1EstudianteModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">
                    <button
                        class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500"
                        onclick="closeFase1EstudianteModal()">&times;</button>
                    <form class="p-6 mt-2" id="fase1EstudianteForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="practica_id" value="{{ $practica->id }}">
                        <p class="text-2xl font-bold mb-4 d-flex">Prácticas <span
                                class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Fase 1</span></p>
                        <p class="font-medium text-sm text-gray-700 mb-6">En este formulario el estudiante enviará el
                            formato de solicitud de practicantes. Dicho formato podrá descargarlo desde el siguiente enlace. <a href="https://www.dropbox.com/scl/fo/pudgcaq639agy7t06ahjs/AF-dMByc71OWsxcfY2dLe3A?rlkey=6s0b9ajweteyx2ang7ywvk6xm&e=1&dl=0" target="_blank" class="text-uts-500 underline hover:text-uts-800">Consultar Base Documental.</a></p>

                        <div class="grid grid-cols-1 gap-6 mb-6">
                            <!-- Checkbox Práctica institucional -->
                            <div>
                                <div class="flex items-center gap-1 mb-2">
                                    <label class="flex items-center gap-1">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                        <span class="text-red-500">*</span></label>
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
                                    <label class="block font-medium text-sm text-gray-700">Nombre de la empresa</label>
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
                                    <span class="text-red-500">*</span></label>
                                    <label class="block font-medium text-sm text-gray-700">Formato F-DC-126 
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-fdc126"></i>
                                        <div id="tooltip-fdc126"
                                            class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                            Suba el formato F-DC-126 diligenciado (Word, máximo 5MB).
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border-2 border-gray-300 gap-3 grid border-dashed">
                                    <div class="grid gap-1">
                                        <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                        <h2 class="text-center text-gray-400 text-xs leading-4">Solo archivos Word de
                                            máximo 5MB</h2>
                                    </div>
                                    <div class="grid gap-2">
                                        <h4 class="text-center text-gray-900 text-sm font-medium">Arrastra o carga tu
                                            archivo aquí</h4>
                                        <div class="flex items-center justify-center">
                                            <input type="file" name="doc_fdc126" id="doc_fdc126"
                                                class="absolute inset-0 opacity-0 cursor-pointer"
                                                accept=".doc,.docx" />
                                            <div
                                                class="flex w-28 h-9 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer">
                                                Cargar</div>
                                        </div>
                                    </div>
                                </div>
                                <span id="doc_fdc126Error" class="text-red-500 text-sm"></span>
                                <ul id="file-list-fase1" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                                <br>

                                <p class="text-red-600 text-sm mb-2">
                                    <i class="fa-solid fa-circle-info mr-1"></i>
                                    <strong>NOTA:</strong> Si selecciona la opción de práctica institucional tenga en cuenta de que previamente eebe estar aprobado por la coordinación.
                                </p>

                                <p class="text-sm mb-6"><strong>NOTA:</strong> El formato F-DC-126 debe estar
                                    debidamente diligenciado y firmado.</p>

                                
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2 mt-4">
                            <button type="button" onclick="closeFase1EstudianteModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cancelar</button>
                            <button type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-fase1" style="margin: 4px 10px 4px 0"
                                    class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64"
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
    

    <!-- Moda FASE 1 - Detalles (Ver información enviada) -->
<div id="fase1DetailsModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" onclick="closeFase1DetailsModal()">
        <div class="flex items-center justify-center min-h-screen p-4 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                onclick="event.stopPropagation()">
                <button
                    class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500"
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

    <!-- Moda FASE 1 - Administrador (Responder solicitud) -->
    <div id="fase1AdminModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0"  style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase1AdminModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">
                    <button
                        class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500"
                        onclick="closeFase1AdminModal()">&times;</button>
                    <form class="p-6 mt-2" id="fase1AdminForm">
                        @csrf
                        <input type="hidden" name="practica_id" value="{{ $practica->id }}">
                        <p class="text-2xl font-bold mb-4">Responder <span
                                class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Fase 1</span></p>
                        <p class="text-sm text-gray-600 mb-4">Apruebe o rechace la solicitud de práctica empresarial.
                        </p>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700"> 
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Estado de la práctica:
                            </label>
                            <select name="estado" id="estado_fase1"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <option value="">Seleccione un estado</option>
                                <option value="Aprobada">Aprobar</option>
                                <option value="Rechazada">Rechazar</option>
                            </select>
                            <span id="estado_fase1Error" class="text-red-500 text-sm"></span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>    
                                    Número de acta:
                                </label>
                                <input type="text" name="nro_acta" id="nro_acta_fase1"
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
                            <label class="block font-medium text-sm text-gray-700">
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
                                    class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64"
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

    <!-- Moda FASE 2 - Estudiante (Enviar documentos de pago) -->
<div id="fase2EstudianteModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase2EstudianteModal()">
        <div class="flex items-center justify-center min-h-screen p-4 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full modal-content relative"
                onclick="event.stopPropagation()" style="max-width: 900px !important; width: 100%;">
                
                <button class="modal-close-btn-custom absolute top-3 right-4 text-gray-400 hover:text-red-500 text-2xl z-10"
                    onclick="closeFase2EstudianteModal()">&times;</button>

                <form class="p-8" id="fase2EstudianteForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="practica_id" value="{{ $practica->id }}">

                    <!-- Título -->
                    <div class="mb-4 pr-4">
                        <p class="text-2xl font-bold" id="fase2EstudianteTitle"> 
                        <p class="text-gray-950 mt-6 text-sm">En este formulario el estudiante deberá subir la
                            liquidación de pago y el soporte correspondiente.</p>
                    </div>

                    <p class="text-sm text-gray-950"><strong>NOTA: </strong>Por cada integrante del proyecto se
                        debe cargar la liquidación y los respectivos soportes de pago.</p>

                    <!-- Documentos y enlaces informativos -->
                    <div class="flex items-center my-5">
                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                        <p class="font-semibold text-gray-700 flex items-center gap-2">
                            Documentos (Liquidaciones y soportes)
                        </p>
                    </div>
                    <ul class="space-y-2 text-sm mb-4">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                        <!-- Campo Liquidación de pago -->
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i><span
                                        class="text-red-500">*</span> Liquidación de pago
                                </label>
                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-liquidacion-fase2"></i>

                                    <div id="tooltip-liquidacion-fase2"
                                        class="tooltip-content hidden absolute left-6 top-0 z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                                        Suba la liquidación generada del pago de modalidad. El documento debe incluir la
                                        marca de agua correspondiente.
                                    </div>
                                </div>
                            </div>
                            <div
                                class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-1 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs">Solo archivos PDF de máximo 5MB</h2>
                                </div>
                                <div class="text-center">
                                    <input type="file" name="liquidacion_pago" id="liquidacion_pago"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full" accept=".pdf" />
                                    <div
                                        class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar</div>
                                </div>
                            </div>
                            <span id="liquidacion_pagoError" class="text-red-500 text-xs"></span>
                            <ul id="file-list-liquidacion" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                            
                        </div>

                        <!-- Campo Soporte de pago -->
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i><span
                                        class="text-red-500">*</span> Soporte de pago
                                </label>
                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-soporte-fase2"></i>

                                    <div id="tooltip-soporte-fase2"
                                        class="tooltip-content hidden absolute left-6 top-0 z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                                        Suba el soporte de pago correspondiente a la liquidación.
                                    </div>
                                </div>
                            </div>
                            <div
                                class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                                <div class="grid gap-1 text-center">
                                    <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs">Solo archivos PDF de máximo 5MB</h2>
                                </div>
                                <div class="text-center">
                                    <input type="file" name="soporte_pago" id="soporte_pago"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full" accept=".pdf" />
                                    <div
                                        class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                        Cargar</div>
                                </div>
                            </div>
                            <span id="soporte_pagoError" class="text-red-500 text-xs"></span>
                            <ul id="file-list-soporte" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                            
                        </div>

                    </div>

                    <div class="mt-4">
                        <p class="text-xs">
                            <i class="fa-solid fa-circle-question text-sm text-uts-500"></i>
                            <strong>NOTA:</strong> En caso de que el estudiante desee sugerir un director de trabajo de
                            grado, deberá adjuntar una página adicional en el archivo PDF correspondiente a los pagos de
                            la modalidad, indicando de manera formal el nombre del docente que desea sugerir como
                            director de trabajo de grado. El comité evaluará la sugerencia y responderá al estudiante.
                        </p>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                        <button type="button" onclick="closeFase2EstudianteModal()"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-lg transition">Cancelar</button>
                        <button type="submit"
                            class="flex bg-uts-500 hover:bg-uts-800 text-white px-5 py-2 rounded-lg transition items-center gap-2">
                            <svg id="loadingSpinner-fase2" class="hidden w-4 h-4 text-white animate-spin"
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

    <!-- Moda FASE 2 - Detalles (Ver información enviada) -->
<div id="fase2DetailsModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" onclick="closeFase2DetailsModal()">
        <div class="flex items-center justify-center min-h-screen p-4 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                onclick="event.stopPropagation()">
                <button
                    class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500"
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

    <!-- Moda FASE 2 - Administrador (Responder solicitud + Asignar docentes) -->
<div id="fase2AdminModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;"  onclick="closeFase2AdminModal()">
        <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                onclick="event.stopPropagation()">
                <button
                    class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500"
                    onclick="closeFase2AdminModal()">&times;</button>
                <form class="p-6 mt-2" id="fase2AdminForm">
                    @csrf
                    <input type="hidden" name="practica_id" value="{{ $practica->id }}">
                    <p class="text-2xl font-bold mb-4">Responder <span
                            class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Fase 2</span></p>
                    <p class="text-sm text-gray-600 mb-4">Apruebe o rechace el pago de modalidad. Si aprueba,
                        deberá asignar director y evaluador.</p>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700"> 
                            <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                            Estado de la práctica:
                        </label>
                        <select name="estado" id="estado_fase2"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                            <option value="">Seleccione un estado</option>
                            <option value="Aprobada">Aprobar</option>
                            <option value="Rechazada">Rechazar</option>
                        </select>
                        <span id="estado_fase2Error" class="text-red-500 text-sm"></span>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>    
                                Número de acta:
                            </label>
                            <input type="text" name="nro_acta" id="nro_acta_fase2"
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
        readonly
        value="{{ $codigo_modalidad_generado ?? '' }}"
        placeholder="Se generará automáticamente">
    <span id="codigo_modalidad_fase2Error" class="text-red-500 text-sm"></span>
</div>
                    
                    <!-- Contenedor para asignación de docentes (se muestra solo si selecciona Aprobada) -->
                    <div id="container_docentes_fase2" class="hidden mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="font-bold text-sm text-gray-700 mb-3">
                            <i class="fa-solid fa-chalkboard-user mr-2 text-gray-500"></i>
                            Asignación de docentes:
                        </p>

                        <div class="mb-3">
                            <label class="block font-medium text-sm text-gray-700">Director <span class="text-red-500">*</span></label>
                            <select name="director_id" id="director_id_fase2"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <option value="">Seleccione un director</option>
                                @foreach ($docentes as $docente)
                                    <option value="{{ $docente->id }}">{{ $docente->name }}</option>
                                @endforeach
                            </select>
                            <span id="director_id_fase2Error" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="mb-3">
                            <label class="block font-medium text-sm text-gray-700">Evaluador <span class="text-red-500">*</span></label>
                            <select name="evaluador_id" id="evaluador_id_fase2"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <option value="">Seleccione un evaluador</option>
                                @foreach ($docentes as $docente)
                                    <option value="{{ $docente->id }}">{{ $docente->name }}</option>
                                @endforeach
                            </select>
                            <span id="evaluador_id_fase2Error" class="text-red-500 text-sm"></span>
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700">Codirector (opcional)</label>
                            <select name="codirector_id" id="codirector_id_fase2"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <option value="">Seleccione un codirector (opcional)</option>
                                @foreach ($docentes as $docente)
                                    <option value="{{ $docente->id }}">{{ $docente->name }}</option>
                                @endforeach
                            </select>
                            <span id="codirector_id_fase2Error" class="text-red-500 text-sm"></span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">
                            <i class="fa-solid fa-message mr-2 text-gray-500"></i>
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
                                class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64"
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


  <!-- Modal FASE 3 - Estudiante (Enviar documentos de trabajo de grado) -->
<div id="fase3EstudianteModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto; background-color: rgba(0, 0, 0, 0.5);" onclick="closeFase3EstudianteModal()">
        <div class="flex items-center justify-center min-h-screen p-4 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full modal-content relative"
                onclick="event.stopPropagation()" style="max-width: 900px !important; width: 100%;">
                
                <button class="modal-close-btn-custom absolute top-3 right-4 text-gray-400 hover:text-red-500 text-2xl z-10"
                    onclick="closeFase3EstudianteModal()">&times;</button>

                <form class="p-8" id="fase3EstudianteForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="practica_id" value="{{ $practica->id }}">

                    <!-- Título -->
                    <div class="mb-4 pr-4">
                        <p class="text-2xl font-bold" id="fase3EstudianteTitle">Prácticas <span
                                class="bg-uts-500 text-white px-3 py-1 rounded uppercase shadow-md text-xl">Fase 3</span></p>
                        <p class="text-gray-950 mt-6 text-sm">En este formulario el estudiante podrá cargar los documentos requeridos para formalizar su propuesta de prácticas empresariales. Los siguientes archivos: </p>
                        <ul class="text-gray-950 mt-6 text-sm list-disc pl-5">
                            <li>ARL (.PDF)</li>
                            <li>Formato propuesta de prácticas (F-DC-127)</li>
                            <li>Acta de inicio de prácticas (F-DC-195)</li>
                        </ul>
                    </div>

                    <p class="text-sm text-gray-950"><strong>NOTA: </strong>El formato F-DC-127 y F-DC-195 debe estar debidamente diligenciado, firmado y no debe superar los 5MB en formato Word.</p>

                    <!-- Campo ARL -->
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i><span class="text-red-500">*</span> ARL
                            </label>
                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-arl-fase3"></i>
                            </div>
                        </div>
                        <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                            <div class="grid gap-1 text-center">
                                <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-center text-gray-400 text-xs">Arrastra o selecciona el archivo ARL (PDF, máx. 5MB)</h2>
                            </div>
                            <div class="text-center">
                                <input type="file" name="arl" id="arl"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full" accept=".pdf" />
                                <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>
                            </div>
                        </div>
                        <span id="arlError" class="text-red-500 text-xs"></span>
                        <ul id="file-list-arl" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                        <div id="tooltip-arl-fase3"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el certificado de afiliación de la Administradora de Riesgos Laborales (ARL). El documento debe incluir la información completa y legible.
                        </div>
                    </div>

                    <!-- Campo F-DC-127 -->
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i><span class="text-red-500">*</span> F-DC-127
                            </label>
                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-fdc127-fase3"></i>
                            </div>
                        </div>
                        <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                            <div class="grid gap-1 text-center">
                                <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-center text-gray-400 text-xs">Arrastra o selecciona el archivo F-DC-127 (.doc, .docx, máx. 5MB)</h2>
                            </div>
                            <div class="text-center">
                                <input type="file" name="doc_fdc127" id="doc_fdc127"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full" accept=".doc,.docx" />
                                <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>
                            </div>
                        </div>
                        <span id="doc_fdc127Error" class="text-red-500 text-xs"></span>
                        <ul id="file-list-fdc127" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                        <div id="tooltip-fdc127-fase3"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el documento F-DC-127 en word
                        </div>
                    </div>

                    <!-- Campo F-DC-195 -->
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i><span class="text-red-500">*</span> F-DC-195
                            </label>
                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-fdc195-fase3"></i>
                            </div>
                        </div>
                        <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                            <div class="grid gap-1 text-center">
                                <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-center text-gray-400 text-xs">Arrastra o selecciona el archivo F-DC-195 (.doc, .docx, máx. 5MB)</h2>
                            </div>
                            <div class="text-center">
                                <input type="file" name="doc_fdc195" id="doc_fdc195"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full" accept=".doc,.docx" />
                                <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>
                            </div>
                        </div>
                        <span id="doc_fdc195Error" class="text-red-500 text-xs"></span>
                        <ul id="file-list-fdc195" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                        <div id="tooltip-fdc195-fase3"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el documento F-DC-195 en PDF
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                        <button type="button" onclick="closeFase3EstudianteModal()"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-lg transition">Cancelar</button>
                        <button type="submit"
                            class="flex bg-uts-500 hover:bg-uts-800 text-white px-5 py-2 rounded-lg transition items-center gap-2">
                            <svg id="loadingSpinner-fase3" class="hidden w-4 h-4 text-white animate-spin" viewBox="0 0 64 64" fill="none">
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


    <!-- Moda FASE 3 - Detalles (Ver información enviada) -->
<div id="fase3DetailsModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" onclick="closeFase3DetailsModal()">
        <div class="flex items-center justify-center min-h-screen p-4 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                onclick="event.stopPropagation()">
                <button
                    class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500"
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

<!-- Modal Fase 3 Responder Director -->
<div id="fase3DirModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase3DirModal()">
        <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                onclick="event.stopPropagation()">
                <button class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500"
                    onclick="closeFase3DirModal()">&times;</button>
                <form class="p-6 mt-2" id="fase3DirForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="practica_id" value="{{ $practica->id }}">

                    <p class="text-2xl font-bold mb-4">
                        Responder
                        <span class="bg-uts-500 text-white px-2 py-0.5 rounded uppercase shadow">Fase 3</span>
                    </p>

                    <p class="text-sm text-gray-600 mb-4">Apruebe o rechace la propuesta de prácticas empresariales.</p>

                    <!-- ESTADO -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">
                            <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                            Estado de la práctica:
                        </label>
                        <select name="estado" id="estado_fase3_dir"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                            <option value="">Seleccione un estado</option>
                            <option value="Aprobada">Aprobar</option>
                            <option value="Rechazada">Rechazar</option>
                        </select>
                        <span id="estado_fase3_dirError" class="text-red-500 text-sm"></span>
                    </div>

                    <!-- F-DC-127 -->
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                <span class="text-red-500">*</span></label>
                                Propuesta (F-DC-127)
                            </label>
                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-fdc127-dir-fase3"></i>
                            </div>
                        </div>
                        <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                            <div class="grid gap-1 text-center">
                                <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-center text-gray-400 text-xs">
                                    Arrastra o selecciona el archivo F-DC-127 con comentarios o firmado
                                    (.doc, .docx, máx. 5MB)
                                </h2>
                            </div>
                            <div class="text-center">
                                <input type="file" name="fdc127" id="fdc127_fase3"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full"
                                    accept=".doc,.docx" />
                                <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>
                            </div>
                        </div>
                        <span id="fdc127_fase3Error" class="text-red-500 text-xs"></span>
                        <ul id="file-list-fdc127-fase3" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                        <div id="tooltip-fdc127-dir-fase3"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el documento F-DC-127 firmado o con comentarios del director.
                        </div>
                    </div>

                    <!-- F-DC-195 -->
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                <span class="text-red-500">*</span></label>
                                Acta de Inicio (F-DC-195)
                            </label>
                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-fdc195-dir-fase3"></i>
                            </div>
                        </div>
                        <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                            <div class="grid gap-1 text-center">
                                <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-center text-gray-400 text-xs">
                                    Arrastra o selecciona el archivo F-DC-195 con comentarios o firmado
                                    (.doc, .docx, máx. 5MB)
                                </h2>
                            </div>
                            <div class="text-center">
                                <input type="file" name="fdc195" id="fdc195_fase3"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full"
                                    accept=".doc,.docx" />
                                <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>
                            </div>
                        </div>
                        <span id="fdc195_fase3Error" class="text-red-500 text-xs"></span>
                        <ul id="file-list-fdc195-fase3" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                        <div id="tooltip-fdc195-dir-fase3"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el acta de inicio F-DC-195 firmada o con comentarios.
                        </div>
                    </div>

                    <!-- TURNITIN -->
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                <span class="text-red-500">*</span></label>
                                Informe de plagio (Turnitin)
                            </label>
                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-turnitin-dir-fase3"></i>
                            </div>
                        </div>
                        <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                            <div class="grid gap-1 text-center">
                                <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-center text-gray-400 text-xs">
                                    Arrastra o selecciona el informe Turnitin (.pdf, máx. 5MB)
                                </h2>
                            </div>
                            <div class="text-center">
                                <input type="file" name="turnitin" id="turnitin_fase3"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full"
                                    accept=".pdf" />
                                <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>
                            </div>
                        </div>
                        <span id="turnitin_fase3Error" class="text-red-500 text-xs"></span>
                        <ul id="file-list-turnitin-fase3" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                        <div id="tooltip-turnitin-dir-fase3"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el informe de similitud generado por Turnitin en formato PDF.
                        </div>
                    </div>

                    <!-- RESPUESTA CON QUILL -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">
                            <i class="fa-solid fa-message mr-2 text-gray-500"></i>
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
                                class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none">
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



<!-- Modal Responder Evaluador Fase 4 -->
<div id="fase4EvaluadorModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase4EvaluadorModal()">
        <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                onclick="event.stopPropagation()">

                <button class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500"
                    onclick="closeFase4EvaluadorModal()">&times;</button>

                <form class="p-6 mt-2" id="fase4EvaluadorForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="practica_id" id="practica_id_fase4" value="{{ $practica->id }}">

                    <p class="text-2xl font-bold mb-4">
                        Responder
                        <span class="bg-uts-500 text-white px-2 py-0.5 rounded uppercase shadow">Fase 4</span>
                    </p>

                    <p class="text-sm text-gray-600 mb-4">Apruebe o rechace la propuesta de prácticas empresariales.</p>

                    <!-- ESTADO -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">
                            <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                            Estado de la práctica:
                        </label>
                        <select name="estado" id="estado_fase4"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                            <option value="">Seleccione un estado</option>
                            <option value="Aprobada">Aprobar</option>
                            <option value="Rechazada">Rechazar</option>
                        </select>
                        <span id="estado_fase4Error" class="text-red-500 text-sm"></span>
                    </div>

                    <!-- F-DC-127 -->
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                <span class="text-red-500">*</span></label>
                                Propuesta (F-DC-127)
                            </label>
                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-fdc127-evaluador-fase4"></i>
                            </div>
                        </div>
                        <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                            <div class="grid gap-1 text-center">
                                <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-center text-gray-400 text-xs">
                                    Arrastra o selecciona el archivo F-DC-127 con comentarios o firmado
                                    (.doc, .docx, máx. 5MB)
                                </h2>
                            </div>
                            <div class="text-center">
                                <input type="file" name="fdc127" id="fdc127_fase4"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full"
                                    accept=".doc,.docx" />
                                <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>
                            </div>
                        </div>
                        <span id="fdc127_fase4Error" class="text-red-500 text-xs"></span>
                        <ul id="file-list-fdc127-fase4" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                        <div id="tooltip-fdc127-evaluador-fase4"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el documento F-DC-127 firmado o con comentarios del evaluador.
                        </div>
                    </div>

                    <!-- F-DC-195 -->
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                <span class="text-red-500">*</span></label>
                                Acta de Inicio (F-DC-195)
                            </label>
                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-fdc195-evaluador-fase4"></i>
                            </div>
                        </div>
                        <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                            <div class="grid gap-1 text-center">
                                <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-center text-gray-400 text-xs">
                                    Arrastra o selecciona el archivo F-DC-195 con comentarios o firmado
                                    (.doc, .docx, máx. 5MB)
                                </h2>
                            </div>
                            <div class="text-center">
                                <input type="file" name="fdc195" id="fdc195_fase4"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full"
                                    accept=".doc,.docx" />
                                <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>
                            </div>
                        </div>
                        <span id="fdc195_fase4Error" class="text-red-500 text-xs"></span>
                        <ul id="file-list-fdc195-fase4" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                        <div id="tooltip-fdc195-evaluador-fase4"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el acta de inicio F-DC-195 firmada o con comentarios del evaluador.
                        </div>
                    </div>

                    <!-- RESPUESTA CON QUILL -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">
                            <i class="fa-solid fa-message mr-2 text-gray-500"></i>
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
                                class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none">
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

    <!-- Modal Responder Comité Fase 4 -->
<div id="fase4ComiteModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase4ComiteModal()">
        <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                onclick="event.stopPropagation()">

                <button class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500"
                    onclick="closeFase4ComiteModal()">&times;</button>

                <form class="p-6 mt-2" id="fase4ComiteForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="practica_id" id="practica_id_fase4_comite" value="{{ $practica->id }}">

                    <p class="text-2xl font-bold mb-4">
                        Responder
                        <span class="bg-uts-500 text-white px-2 py-0.5 rounded uppercase shadow">Fase 4 - Comité</span>
                    </p>

                    <p class="text-sm text-gray-600 mb-4">El comité revisa la propuesta y asigna el título oficial.</p>

              
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">
                            <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                            Estado de la práctica:
                        </label>
                        <select name="estado" id="estado_fase4_comite"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                            <option value="">Seleccione un estado</option>
                            <option value="Aprobada">Aprobar</option>
                            <option value="Rechazada">Rechazar</option>
                        </select>
                        <span id="estado_fase4_comiteError" class="text-red-500 text-sm"></span>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">
                            <i class="fa-solid fa-heading mr-2 text-gray-500"></i>
                            Título de la propuesta:
                        </label>
                        <input type="text" name="titulo_propuesta" id="titulo_propuesta_fase4_comite"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500"
                            placeholder="Ingrese el título oficial de la propuesta">
                        <span id="titulo_propuesta_fase4_comiteError" class="text-red-500 text-sm"></span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>
                                Número de acta:
                            </label>
                            <input type="text" name="nro_acta" id="nro_acta_fase4_comite"
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

                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Propuesta (F-DC-127)
                            </label>
                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-fdc127-comite-fase4"></i>
                            </div>
                        </div>
                        <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                            <div class="grid gap-1 text-center">
                                <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-center text-gray-400 text-xs">
                                    Arrastra o selecciona el archivo F-DC-127 con comentarios o firmado
                                    (.doc, .docx, máx. 5MB)
                                </h2>
                            </div>
                            <div class="text-center">
                                <input type="file" name="fdc127" id="fdc127_fase4_comite"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full"
                                    accept=".doc,.docx" />
                                <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>
                            </div>
                        </div>
                        <span id="fdc127_fase4_comiteError" class="text-red-500 text-xs"></span>
                        <ul id="file-list-fdc127-fase4-comite" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                        <div id="tooltip-fdc127-comite-fase4"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el documento F-DC-127 firmado o con comentarios del comité.
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Acta de Inicio (F-DC-195)
                            </label>
                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-fdc195-comite-fase4"></i>
                            </div>
                        </div>
                        <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                            <div class="grid gap-1 text-center">
                                <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-center text-gray-400 text-xs">
                                    Arrastra o selecciona el archivo F-DC-195 con comentarios o firmado
                                    (.doc, .docx, máx. 5MB)
                                </h2>
                            </div>
                            <div class="text-center">
                                <input type="file" name="fdc195" id="fdc195_fase4_comite"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full"
                                    accept=".doc,.docx" />
                                <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>
                            </div>
                        </div>
                        <span id="fdc195_fase4_comiteError" class="text-red-500 text-xs"></span>
                        <ul id="file-list-fdc195-fase4-comite" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                        <div id="tooltip-fdc195-comite-fase4"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el acta de inicio F-DC-195 firmada o con comentarios del comité.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">
                            <i class="fa-solid fa-message mr-2 text-gray-500"></i>
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
                                class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none">
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

<!-- Modal FASE 5 - Estudiante (Enviar documentos F-DC-128, 129, 196) -->
<div id="fase5EstudianteModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0"
        style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto; background-color: rgba(0, 0, 0, 0.5);"
        onclick="closeFase5EstudianteModal()">

        <div class="flex items-center justify-center min-h-screen p-4 text-center relative">

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full modal-content relative"
                onclick="event.stopPropagation()" style="max-width: 900px !important; width: 100%;">

                <button
                    class="modal-close-btn-custom absolute top-3 right-4 text-gray-400 hover:text-red-500 text-2xl z-10"
                    onclick="closeFase5EstudianteModal()">&times;</button>

                <form class="p-8" id="fase5EstudianteForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="practica_id" value="{{ $practica->id }}">

                    <!-- Título -->
                    <div class="mb-4 pr-4">
                        <p class="text-2xl font-bold">
                            Prácticas
                            <span class="bg-uts-500 text-white px-3 py-1 rounded uppercase shadow-md text-xl">
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
                            Informe </a>,{!! '
                            <a href="https://www.dropbox.com/scl/fo/pudgcaq639agy7t06ahjs/AN084HnuyHffgYL5i--v_Ks/DOCUMENTOS%20DE%20GRADO?dl=0&preview=F-DC-196+Acta+de+Terminaci%C3%B3n+y+Recibo+a+Satisfacci%C3%B3n+de+Pr%C3%A1cticas+V2.doc&rlkey=6s0b9ajweteyx2ang7ywvk6xm&subfolder_nav_tracking=1"
                            target="_blank"
                            class="text-blue-600 underline">
                            Acta de Terminacion
                            </a>

                            y la

                            <a href="https://www.dropbox.com/scl/fo/pudgcaq639agy7t06ahjs/AN084HnuyHffgYL5i--v_Ks/DOCUMENTOS%20DE%20GRADO?dl=0&preview=F-DC-129+Rejilla+de+evaluaci%C3%B3n+informe+final+de+trabajo+de+grado+V2.docx&rlkey=6s0b9ajweteyx2ang7ywvk6xm&subfolder_nav_tracking=1"
                            target="_blank"
                            class="text-blue-600 underline">
                            Rejilla
                            </a>
                            ' !!} en formato de Word. Tenga en cuenta el tamaño máximo del archivo que puede cargar en cada campo, se le recomienda reducir o comprimir el peso del archivo antes de cargarlo (Puede usar herramientas online para ello o en su defecto la opción "Comprimir imágenes" del Word).
                    </p>
                        <br>

                    <!-- F-DC-128 -->
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                <span class="text-red-500">*</span> Informe Final F-DC-128
                            </label>
                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-fdc128-fase5"></i>
                            </div>
                        </div>

                        <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                            <div class="grid gap-1 text-center">
                                <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-gray-400 text-xs">Subir F-DC-128 (.doc, .docx, máx. 5MB)</h2>
                            </div>

                            <div class="text-center">
                                <input type="file" name="doc_fdc128" id="doc_fdc128"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full"
                                    accept=".doc,.docx" />

                                <div
                                    class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>
                            </div>
                        </div>
                        
                        <span id="doc_fdc128Error" class="text-red-500 text-xs"></span>
                        <ul id="file-list-fdc128" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                        <div id="tooltip-fdc128-fase5"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el Informe Final F-DC-128. El documento debe incluir la información completa y legible.
                        </div>
                    
                    </div>

                    <!-- F-DC-129 -->
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                <span class="text-red-500">*</span>Regilla de Evaluacion F-DC-129
                            </label>
                              <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-fdc129-fase5"></i>
                            </div>
                        </div>

                        <div
                            class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">

                            <div class="grid gap-1 text-center">
                                <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-gray-400 text-xs">Subir F-DC-129 (.doc, .docx, máx. 5MB)</h2>
                            </div>

                            <div class="text-center">
                                <input type="file" name="doc_fdc129" id="doc_fdc129"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full"
                                    accept=".doc,.docx" />

                                <div
                                    class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>
                            </div>
                        </div>

                        <span id="doc_fdc129Error" class="text-red-500 text-xs"></span>
                        <ul id="file-list-fdc129" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                            <div id="tooltip-fdc129-fase5"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el documento F-DC-129 en word
                        </div>
                    </div>

                    <!-- F-DC-196 -->
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <label class="block font-medium text-sm text-gray-700 mb-2">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                <span class="text-red-500">*</span> Acta de Terminacion F-DC-196
                            </label>
                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-fdc196-fase5"></i>
                            </div>
                        </div>

                        <div
                            class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">

                            <div class="grid gap-1 text-center">
                                <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-gray-400 text-xs">Subir F-DC-196 (.doc, .docx, máx. 5MB)</h2>
                            </div>

                            <div class="text-center">
                                <input type="file" name="doc_fdc196" id="doc_fdc196"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full"
                                    accept=".doc,.docx" />

                                <div
                                    class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>
                            </div>
                        </div>

                        <span id="doc_fdc196Error" class="text-red-500 text-xs"></span>
                        <ul id="file-list-fdc196" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                        <div id="tooltip-fdc196-fase5"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el documento F-DC-196 en word
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                        <button type="button" onclick="closeFase5EstudianteModal()"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-lg transition">
                            Cancelar
                        </button>

                        <button type="submit"
                            class="flex bg-uts-500 hover:bg-uts-800 text-white px-5 py-2 rounded-lg transition items-center gap-2">

                            <svg id="loadingSpinner-fase5" class="hidden w-4 h-4 text-white animate-spin"
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

        <div class="flex items-center justify-center min-h-screen p-4 text-center relative">

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full modal-content relative"
                onclick="event.stopPropagation()">

                <!-- BOTÓN CERRAR -->
                <button
                    class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500"
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

<!-- Modal Fase 5 Responder Director -->
<div id="fase5DirModal" class="fixed z-50 inset-0 overflow-y-auto hidden">
    <div class="modal-overlay absolute inset-0"
        style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;"
        onclick="closeFase5DirModal()">

        <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                onclick="event.stopPropagation()">

                <!-- BOTÓN CERRAR -->
                <button
                    class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500"
                    onclick="closeFase5DirModal()">
                    &times;
                </button>

                <form class="p-6 mt-2" id="fase5DirForm" enctype="multipart/form-data">

                    @csrf

                    <input type="hidden" name="practica_id" value="{{ $practica->id }}">

                    <!-- TÍTULO -->
                    <p class="text-2xl font-bold mb-4">
                        Responder
                        <span
                            class="bg-uts-500 text-white px-2 py-0.5 rounded uppercase shadow">
                            Fase 5
                        </span>
                    </p>

                    <p class="text-sm text-gray-600 mb-4">
                        Apruebe o rechace la entrega final de prácticas empresariales.
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
                            <option value="Aprobada">Aprobar</option>
                            <option value="Rechazada">Rechazar</option>

                        </select>

                        <span id="estado_fase5_dirError"
                            class="text-red-500 text-sm"></span>
                    </div>

                    <!-- F-DC-128 -->
                    <div class="mb-4">

                        <div class="flex items-center gap-2 mb-2">

                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                <span class="text-red-500">*</span>
                                Informe Final (F-DC-128)
                            </label>

                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-fdc128-dir-fase5"></i>
                            </div>

                        </div>

                        <div
                            class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">

                            <div class="grid gap-1 text-center">

                                <i
                                    class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>

                                <h2 class="text-center text-gray-400 text-xs">
                                    Arrastra o selecciona el archivo F-DC-128
                                    (.doc, .docx, máx. 5MB)
                                </h2>

                            </div>

                            <div class="text-center">

                                <input
                                    type="file"
                                    name="fdc128"
                                    id="fdc128_fase5"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full"
                                    accept=".doc,.docx" />

                                <div
                                    class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>

                            </div>

                        </div>

                        <span id="fdc128_fase5Error"
                            class="text-red-500 text-xs"></span>

                        <ul id="file-list-fdc128-fase5"
                            class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>

                        <div id="tooltip-fdc128-dir-fase5"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el documento F-DC-128 firmado o con comentarios.
                        </div>

                    </div>

                    <!-- F-DC-129 -->
                    <div class="mb-4">

                        <div class="flex items-center gap-2 mb-2">

                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                <span class="text-red-500">*</span>
                                Evaluación Final (F-DC-129)
                            </label>

                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-fdc129-dir-fase5"></i>
                            </div>

                        </div>

                        <div
                            class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">

                            <div class="grid gap-1 text-center">

                                <i
                                    class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>

                                <h2 class="text-center text-gray-400 text-xs">
                                    Arrastra o selecciona el archivo F-DC-129
                                    (.doc, .docx, máx. 5MB)
                                </h2>

                            </div>

                            <div class="text-center">

                                <input
                                    type="file"
                                    name="fdc129"
                                    id="fdc129_fase5"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full"
                                    accept=".doc,.docx" />

                                <div
                                    class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>

                            </div>

                        </div>

                        <span id="fdc129_fase5Error"
                            class="text-red-500 text-xs"></span>

                        <ul id="file-list-fdc129-fase5"
                            class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>

                        <div id="tooltip-fdc129-dir-fase5"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el documento F-DC-129 firmado o con comentarios.
                        </div>

                    </div>

                    <!-- F-DC-196 -->
                    <div class="mb-4">

                        <div class="flex items-center gap-2 mb-2">

                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                <span class="text-red-500">*</span>
                                Acta Final (F-DC-196)
                            </label>

                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-fdc196-dir-fase5"></i>
                            </div>

                        </div>

                        <div
                            class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">

                            <div class="grid gap-1 text-center">

                                <i
                                    class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>

                                <h2 class="text-center text-gray-400 text-xs">
                                    Arrastra o selecciona el archivo F-DC-196
                                    (.doc, .docx, máx. 5MB)
                                </h2>

                            </div>

                            <div class="text-center">

                                <input
                                    type="file"
                                    name="fdc196"
                                    id="fdc196_fase5"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full"
                                    accept=".doc,.docx" />

                                <div
                                    class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>

                            </div>

                        </div>

                        <span id="fdc196_fase5Error"
                            class="text-red-500 text-xs"></span>

                        <ul id="file-list-fdc196-fase5"
                            class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>

                        <div id="tooltip-fdc196-dir-fase5"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el documento F-DC-196 firmado o con comentarios.
                        </div>

                    </div>

                    <!-- TURNITIN -->
                    <div class="mb-4">

                        <div class="flex items-center gap-2 mb-2">

                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                <span class="text-red-500">*</span>
                                Informe de plagio (Turnitin)
                            </label>

                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                    data-tooltip="tooltip-turnitin-dir-fase5"></i>
                            </div>

                        </div>

                        <div
                            class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">

                            <div class="grid gap-1 text-center">

                                <i
                                    class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>

                                <h2 class="text-center text-gray-400 text-xs">
                                    Arrastra o selecciona el informe Turnitin
                                    (.pdf, máx. 5MB)
                                </h2>

                            </div>

                            <div class="text-center">

                                <input
                                    type="file"
                                    name="turnitin"
                                    id="turnitin_fase5"
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full"
                                    accept=".pdf" />

                                <div
                                    class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">
                                    Cargar
                                </div>

                            </div>

                        </div>

                        <span id="turnitin_fase5Error"
                            class="text-red-500 text-xs"></span>

                        <ul id="file-list-turnitin-fase5"
                            class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>

                        <div id="tooltip-turnitin-dir-fase5"
                            class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el informe de similitud generado por Turnitin en PDF.
                        </div>

                    </div>

                    <!-- RESPUESTA -->
                    <div class="mb-4">

                        <label class="block font-medium text-sm text-gray-700">
                            <i class="fa-solid fa-message mr-2 text-gray-500"></i>
                            Comentarios de la respuesta:
                        </label>

                        <div id="txt-editor-fase5-dir"
                            class="shadow txt-editor-quill"
                            style="height: 200px; background: white;">
                        </div>

                        <textarea
                            name="respuesta"
                            id="respuesta_fase5_dir"
                            class="hidden"></textarea>

                        <span id="respuesta_fase5_dirError"
                            class="text-red-500 text-sm"></span>

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
                                class="hidden w-4 h-4 text-gray-300 animate-spin"
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

<!-- Modal Fase 6 Responder Evaluador -->


<!-- Modal Fase 6 Detalles --> 

<!-- Modal Fase 6 Responder Comite -->

<!-- Modal Fase 7 Detalles  Todos los documentos se muestran-->


    <!-- Calendario Modal -->
    @if (isset($fechas))
        <div id="calendarModal" class="fixed z-50 inset-0 overflow-y-auto">
            <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5);" onclick="closeCalendarModal()">
                <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" style="width: 100% !important; padding: 2rem 2rem !important;" onclick="event.stopPropagation()">
                        
                        <button class="modal-close-btn-custom" onclick="closeCalendarModal()" style="position: absolute !important; top: 10px !important; right: 28px !important; background: none !important; border: none !important; cursor: pointer !important; color: #6b7280 !important;">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        
                        <div class="p-6 mt-2">
                            <p class="text-2xl font-bold text-gray-800 mb-4">Calendario de la <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Práctica</span></p>
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
                                        <tr class="bg-white">
                                            <td class="px-4 py-3 border border-gray-300">Propuestas en banco de ideas</td>
                                            <td class="px-4 py-3 border border-gray-300">
                                                Desde <span class="font-semibold" style="font-size: 0.9rem;">{{ $fechas['fecha_inicio_banco'] ?? 'No definida' }}</span> 
                                                hasta <span class="font-semibold" style="font-size: 0.9rem;">{{ $fechas['fecha_fin_banco'] ?? 'No definida' }}</span>
                                            </td>
                                        </tr>
                                        <tr class="bg-gray-50">
                                            <td class="px-4 py-3 border border-gray-300">Propuesta de proyectos de grado</td>
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
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeWarningModal()">
                        &times;
                    </button>
                    <form id="warningForm" class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="warningTitle">Enviar
                            Reporte <span
                                class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">PQRSD</span>
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
                                    class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64"
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
            };
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
        </script>

        <script>
    function openCalendarModal(btn) {
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
        console.log('Modal abierto');
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

function closeCalendarModal() {
    const modal = document.getElementById('calendarModal');
    if (modal) {
        modal.classList.remove('show');
        console.log('Modal cerrado');
    }
}
</script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <script src="{{ asset('js/fases/practicas/fase_1.js') }}"></script>
        <script src="{{ asset('js/fases/practicas/fase_2.js') }}"></script>
        <script src="{{ asset('js/fases/practicas/fase_3.js') }}"></script>
        <script src="{{ asset('js/fases/practicas/fase_4.js') }}"></script>
        <script src="{{ asset('js/fases/practicas/fase_5.js') }}"></script>
    @endpush
</x-app-layout>
