<x-app-layout>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Seguimiento de <span
                class="bg-uts-500 text-gray-800 font-bold me-2 px-2.5 py-0.5 rounded uppercase">Prácticas
                Empresariales</span>
        </h2>
    </x-slot>

    @push('styles')

        <style>

            /* ========== ESTILOS UNIFICADOS PARA MODALES ========== */

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
#calendarModal,
#reporteModal,
#fase1EstudianteModal,
#fase1DetailsModal,
#fase1AdminModal,
#fase2AdminModal,
#fase2DetailsModal,
#fase2EstudianteModal {
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
#calendarModal.show,
#reporteModal.show,
#fase1EstudianteModal.show,
#fase1DetailsModal.show,
#fase1AdminModal.show,
#fase2AdminModal.show,
#fase2DetailsModal.show,
#fase2EstudianteModal.show {
    visibility: visible !important;
    opacity: 1 !important;
    transform: translateY(0) scale(1) !important;
    pointer-events: auto !important;
}

/* Estilos para los botones de acción uniformes */
.btn-action {
    min-width: 42px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative;
    transition: all 0.3s ease;
    border-radius: 0.5rem;
}

.btn-action i {
    font-size: 1.125rem;
}

.btn-action .loading-spinner {
    align-items: center;
    transform: translate(-50%, -50%);
    width: 1.25rem;
    height: 1.25rem;
}

        </style>
    
    @endPush
    
    <div class="p-4">
        <div class="mb-4">
            <a href="{{ route('practicas.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                ← Volver al listado
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-2">Práctica #{{ $codigo_practica }}</h3>
            <p class="text-gray-600 mb-4">Estado actual: <strong>{{ $practica->estado }}</strong></p>
            <p class="text-gray-600">Aquí podrás llevar el seguimiento del progreso de tu práctica empresarial.</p>
        </div>
    </div>

    <!-- Container donde aparecen las fases -->
    <div class="p-4" id="container-fases-main">
        <div class="mt-8 grid w-full grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            
            <!-- FASE 1 -->
<div id="fase-1"
    class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $fase_actual >= 1 ? 'card-activated' : '' }} {{ $fase_actual == 1 ? 'card-activated-animated' : '' }}">
    
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-12">
        <path fill-rule="evenodd" d="M5.625 1.5H9a3.75 3.75 0 0 1 3.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 0 1 3.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 0 1-1.875-1.875V3.375c0-1.036.84-1.875 1.875-1.875Zm6.905 9.97a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.72-1.72V18a.75.75 0 0 0 1.5 0v-4.19l1.72 1.72a.75.75 0 1 0 1.06-1.06l-3-3Z" clip-rule="evenodd" />
        <path d="M14.25 5.25a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963A5.23 5.23 0 0 0 16.5 7.5h-1.875a.375.375 0 0 1-.375-.375V5.25Z" />
    </svg>

    <span class="text-center font-bold text-lg">Fase 1: Formato F-DC-126</span>
    <p class="text-center mt-2 text-xs mx-4">El estudiante envía el formato de solicitud de practicantes de la empresa.</p>
    
    @if ($fase_actual == 1)
        @php
            $user = auth()->user();
            $esEstudiante = $user->hasRole('estudiante');
            $esComite = $user->hasRole(['super_admin', 'admin', 'coordinador']);
            $yaEnvio = ($submited_fase1 == 'true');
        @endphp
        
        @if ($esEstudiante && !$yaEnvio)
            <!-- Estudiante: Mostrar botón para enviar SOLO si no ha enviado o fue rechazado -->
            <div class="flex justify-center items-center mt-3">
                <button type="button" onclick="openFase1EstudianteModal()"
                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                    <i class="fa-solid fa-user-pen"></i>
                </button>
            </div>
        @elseif ($esEstudiante && $yaEnvio)
            <!-- Estudiante: Después de enviar, SOLO puede ver detalles (no puede reenviar hasta que el comité rechace) -->
            <div class="flex justify-center items-center mt-3">
                <button type="button" onclick="openFase1DetailsModal()"
                    class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg">
                    <i class="fa-regular fa-eye"></i>
                </button>
            </div>
        @elseif ($esComite)
    <!-- Comité: Botones con estilos unificados -->
    <div class="flex justify-center items-center mt-3 gap-2">
        <!-- Botón Ver (gris) -->
        <!-- Botón Ver con spinner -->
        <button type="button" onclick="openFase1DetailsModal(this)"
            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white w-10 h-10 rounded-lg relative inline-flex items-center justify-center">
            <i class="fa-regular fa-eye"></i>
            <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
            </svg>
        </button>
        
        <!-- Botón Responder (gris también, mismo estilo que el ojito) -->
        @if ($yaEnvio)
            <button type="button" onclick="openFase1AdminModal()"
                class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white w-10 h-10 rounded-lg inline-flex items-center justify-center">
                <i class="fa-solid fa-reply"></i>
            </button>
        @endif
    </div>
@endif
    @elseif ($fase_actual > 1)
        <!-- Fase ya aprobada: Solo mostrar botón de ver detalles -->
        <div class="flex justify-center items-center mt-3">
            <button type="button" onclick="openFase1DetailsModal()"
                class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg">
                <i class="fa-regular fa-eye"></i>
            </button>
        </div>
    @endif
</div>

            <!-- FASE 2: Pago de modalidad -->
<div id="fase-2"
    class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $fase_actual >= 2 ? 'card-activated' : '' }} {{ $fase_actual == 2 ? 'card-activated-animated' : '' }}">
    
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-12">
        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" clip-rule="evenodd" />
    </svg>

    <span class="text-center font-bold text-lg">Fase 2: Pago de modalidad</span>
    <p class="text-center mt-2 text-xs mx-4">El estudiante sube la liquidación y soporte de pago.</p>
    
    @if ($fase_actual == 2)
        @php
            $user = auth()->user();
            $esEstudiante = $user->hasRole('estudiante');
            $esComite = $user->hasRole(['super_admin', 'admin', 'coordinador']);
            $yaEnvio = ($submited_fase2 == 'true');
        @endphp
        
        @if ($esEstudiante && !$yaEnvio)
            <!-- Estudiante: Mostrar botón para enviar -->
            <div class="flex justify-center items-center mt-3">
                <button type="button" onclick="openFase2EstudianteModal()"
                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                    <i class="fa-solid fa-user-pen"></i>
                </button>
            </div>
        @elseif ($esEstudiante && $yaEnvio)
            <!-- Estudiante: Después de enviar, solo puede ver detalles -->
            <div class="flex justify-center items-center mt-3">
                <button type="button" onclick="openFase2DetailsModal()"
                    class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg">
                    <i class="fa-regular fa-eye"></i>
                </button>
            </div>
        @elseif ($esComite)
            <!-- Comité: Botones Ver y Responder -->
            <div class="flex justify-center items-center mt-3 gap-2">
                <!-- Botón Ver -->
                <button type="button" onclick="openFase2DetailsModal(this)"
                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white w-10 h-10 rounded-lg relative inline-flex items-center justify-center">
                    <i class="fa-regular fa-eye"></i>
                    <svg class="loading-spinner hidden w-4 h-4 text-white animate-spin absolute" viewBox="0 0 64 64" fill="none">
                        <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                        <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                    </svg>
                </button>
                
                <!-- Botón Responder (solo si el estudiante ya envió) -->
                @if ($yaEnvio)
                    <button type="button" onclick="openFase2AdminModal()"
                        class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white w-10 h-10 rounded-lg inline-flex items-center justify-center">
                        <i class="fa-solid fa-reply"></i>
                    </button>
                @endif
            </div>
        @endif
    @elseif ($fase_actual > 2)
        <!-- Fase ya aprobada: Solo mostrar botón de ver detalles -->
        <div class="flex justify-center items-center mt-3">
            <button type="button" onclick="openFase2DetailsModal()"
                class="btn-action shadow bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg">
                <i class="fa-regular fa-eye"></i>
            </button>
        </div>
    @endif
</div>

            <!-- FASE 3 -->
            <div class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border opacity-50">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-12">
                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" clip-rule="evenodd" />
                </svg>
                <span class="text-center font-bold text-lg">Fase 3: Propuesta</span>
                <p class="text-center mt-2 text-xs mx-4">Próximamente...</p>
            </div>

            <!-- FASE 4 -->
            <div class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border opacity-50">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-12">
                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" clip-rule="evenodd" />
                </svg>
                <span class="text-center font-bold text-lg">Fase 4: Documentos</span>
                <p class="text-center mt-2 text-xs mx-4">Próximamente...</p>
            </div>

            <!-- FASE 5 -->
            <div class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border opacity-50">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-12">
                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" clip-rule="evenodd" />
                </svg>
                <span class="text-center font-bold text-lg">Fase 5: Finalización</span>
                <p class="text-center mt-2 text-xs mx-4">Próximamente...</p>
            </div>
        </div>
    </div>

    <!-- ==================== MODALES FASE 1 ==================== -->

    <!-- Modal FASE 1 - Estudiante (Enviar documentos) -->
    <div id="fase1EstudianteModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0"  style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase1EstudianteModal()">
        <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                <button class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500" onclick="closeFase1EstudianteModal()">&times;</button>
                    <form class="p-6 mt-2" id="fase1EstudianteForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="practica_id" value="{{ $practica->id }}">
                        <p class="text-2xl font-bold mb-4 d-flex">Fase 1: Envío de <span class="bg-uts-500 text-white px-2 py-0.5 rounded uppercase shadow">F-DC-126</span></p>
                        <p class="font-medium text-sm text-gray-700 mb-6">En este formulario el estudiante enviará el formato de solicitud de practicantes.</p>
                        
                        <div class="grid grid-cols-1 gap-6 mb-6">
                            <!-- Checkbox Práctica institucional -->
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="es_institucional" id="es_institucional" class="rounded border-gray-300 text-uts-500 focus:ring-uts-500" onchange="toggleNombreEmpresa()">
                                        <span class="text-sm text-gray-700">¿Es práctica institucional?</span>
                                    </label>
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon" data-tooltip="tooltip-institucional"></i>
                                        <div id="tooltip-institucional" class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                            Marque si la práctica se realizará en la UTS con previa autorización del coordinador.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Campo Nombre de la empresa (condicional) -->
                            <div id="nombre_empresa_container" style="display: none;">
                                <div class="flex items-center gap-2 mb-2">
                                    <label class="block font-medium text-sm text-gray-700">Nombre de la empresa</label>
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon" data-tooltip="tooltip-empresa"></i>
                                        
                                        <div id="tooltip-empresa" class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                           Ingrese el nombre de la empresa donde realizará la práctica.
                                        </div>
                                    </div>
                                </div>
                                <input type="text" name="nombre_empresa" id="nombre_empresa" placeholder="Escribe el nombre de la empresa" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                            </div>

                            <!-- Campo para subir F-DC-126 -->
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <label class="block font-medium text-sm text-gray-700">Formato F-DC-126 <span class="text-red-500">*</span></label>
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon" data-tooltip="tooltip-fdc126"></i>
                                        <div id="tooltip-fdc126" class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-64">
                                            Suba el formato F-DC-126 diligenciado (Word, máximo 5MB).
                                        </div>
                                    </div>
                                </div>
                              
                                <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border-2 border-gray-300 gap-3 grid border-dashed">
                                    <div class="grid gap-1">
                                        <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                        <h2 class="text-center text-gray-400 text-xs leading-4">Solo archivos Word de máximo 5MB</h2>
                                    </div>
                                    <div class="grid gap-2">
                                        <h4 class="text-center text-gray-900 text-sm font-medium">Arrastra o carga tu archivo aquí</h4>
                                        <div class="flex items-center justify-center">
                                            <input type="file" name="doc_fdc126" id="doc_fdc126" class="absolute inset-0 opacity-0 cursor-pointer" accept=".doc,.docx" />
                                            <div class="flex w-28 h-9 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer">Cargar</div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <p class="text-sm mb-6"><strong>Nota:</strong> El formato F-DC-126 debe estar debidamente diligenciado y no debe superar los 5MB en formato Word. </p>
                                
                                <span id="doc_fdc126Error" class="text-red-500 text-sm"></span>
                                <ul id="file-list-fase1" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2 mt-4">
                            <button type="button" onclick="closeFase1EstudianteModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cancelar</button>
                            <button type="submit" class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-fase1" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
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

    <!-- Modal FASE 1 - Detalles (Ver información enviada) -->
    <div id="fase1DetailsModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" onclick="closeFase1DetailsModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500" onclick="closeFase1DetailsModal()">&times;</button>
                    <div class="p-6 mt-2">
                        <p class="text-2xl font-bold mb-4">Detalles de la <span class="bg-uts-500 text-white px-2 py-0.5 rounded uppercase shadow">Fase 1</span></p>
                        <div id="fase1DetailsContent" class="space-y-3">
                            <div class="text-center py-4">
                                <svg class="inline w-8 h-8 text-gray-400 animate-spin" viewBox="0 0 64 64" fill="none">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                </svg>
                                <p class="mt-2 text-gray-500">Cargando detalles...</p>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button type="button" onclick="closeFase1DetailsModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal FASE 1 - Administrador (Responder solicitud) -->
   <div id="fase1AdminModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" onclick="closeFase1AdminModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500" onclick="closeFase1AdminModal()">&times;</button>
                    <form class="p-6 mt-2" id="fase1AdminForm">
                        @csrf
                        <input type="hidden" name="practica_id" value="{{ $practica->id }}">
                        <p class="text-2xl font-bold mb-4">Responder <span class="bg-uts-500 text-white px-2 py-0.5 rounded uppercase shadow">Fase 1</span></p>
                        <p class="text-sm text-gray-600 mb-4">Apruebe o rechace la solicitud de práctica empresarial.</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Número de acta <span class="text-red-500">*</span></label>
                                <input type="text" name="nro_acta" id="nro_acta_fase1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <span id="nro_acta_fase1Error" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Fecha del acta <span class="text-red-500">*</span></label>
                                <input type="date" name="fecha_acta" id="fecha_acta_fase1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <span id="fecha_acta_fase1Error" class="text-red-500 text-sm"></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Estado <span class="text-red-500">*</span></label>
                            <select name="estado" id="estado_fase1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <option value="">Seleccione un estado</option>
                                <option value="Aprobada">Aprobar</option>
                                <option value="Rechazada">Rechazar</option>
                            </select>
                            <span id="estado_fase1Error" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Respuesta / Comentarios <span class="text-red-500">*</span></label>
                            <textarea name="respuesta" id="respuesta_fase1" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500" placeholder="Describa los motivos de su decisión..."></textarea>
                            <span id="respuesta_fase1Error" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="flex justify-end space-x-2 mt-4">
                            <button type="button" onclick="closeFase1AdminModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cancelar</button>
                            <button type="submit" class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-fase1-admin" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                                </svg>
                                Enviar Respuesta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODALES FASE 2 ==================== -->

<!-- Modal FASE 2 - Estudiante (Enviar documentos de pago) -->
<div id="fase2EstudianteModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
    <!-- Overlay DETRÁS del modal (sin onclick para que no cierre) -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
    
    <!-- Contenedor del modal - centrado -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl" style="max-width: 900px !important;" onclick="event.stopPropagation()">
            
            <button class="absolute top-3 right-4 text-gray-400 hover:text-red-500 text-2xl z-10" onclick="closeFase2EstudianteModal()">&times;</button>
            
            <form class="p-16" id="fase2EstudianteForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="practica_id" value="{{ $practica->id }}">
                
                <!-- Título -->
                <div class="mb-4 pr-4">
                    <p class="text-2xl font-bold">Prácticas empresariales: <span class="bg-uts-500 text-white px-3 py-1 rounded uppercase shadow-md text-xl">Fase 2</span></p>
                    <p class="text-gray-950 mt-6 text-sm">En este formulario el estudiante deberá subir la liquidación de pago y el soporte correspondiente.</p>
                </div>

                <p class="text-sm text-gray-950"><strong>NOTA: </strong>Por cada integrante del proyecto se debe cargar la liquidación y los respectivos soportes de pago.</p>
                
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
                            <a href="{{ asset('ejemplos/fase_1-1.pdf') }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline flex items-center gap-1"> ABRIR ARCHIVO</a>
                        </li>
                        <li class="flex items-center gap-2 flex-wrap">
                            <span class="text-gray-600">Liquidación con marca de agua (Ejemplo):</span>
                            <a href="{{ asset('ejemplos/fase_1-2.pdf') }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline flex items-center gap-1"> ABRIR ARCHIVO</a>
                        </li>
                                    
                </ul>

                <!-- Campos en GRID para desktop (2 columnas) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    
                    <!-- Campo Liquidación de pago -->
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i><span class="text-red-500">*</span> Liquidación de pago 
                            </label>
                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon" data-tooltip="tooltip-liquidacion"></i>
                            </div>
                        </div>
                        <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                            <div class="grid gap-1 text-center">
                                <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-center text-gray-400 text-xs">Solo archivos PDF de máximo 5MB</h2>
                            </div>
                            <div class="text-center">
                                <input type="file" name="liquidacion_pago" id="liquidacion_pago" class="absolute inset-0 opacity-0 cursor-pointer w-full" accept=".pdf" />
                                <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">Cargar</div>
                            </div>
                        </div>
                        <span id="liquidacion_pagoError" class="text-red-500 text-xs"></span>
                        <ul id="file-list-liquidacion" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                        <div id="tooltip-liquidacion" class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba la liquidación generada del pago de modalidad. El documento debe incluir la marca de agua correspondiente.
                        </div>
                    </div>

                    <!-- Campo Soporte de pago -->
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <label class="block font-medium text-sm text-gray-700">
                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i><span class="text-red-500">*</span> Soporte de pago
                            </label>
                            <div class="relative inline-block">
                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon" data-tooltip="tooltip-soporte"></i>
                            </div>
                        </div>
                        <div class="w-full mt-1 relative py-8 bg-gray-50 rounded-xl border-2 border-gray-300 gap-3 grid border-dashed">
                            <div class="grid gap-1 text-center">
                                <i class="mx-auto text-3xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                <h2 class="text-center text-gray-400 text-xs">Solo archivos PDF de máximo 5MB</h2>
                            </div>
                            <div class="text-center">
                                <input type="file" name="soporte_pago" id="soporte_pago" class="absolute inset-0 opacity-0 cursor-pointer w-full" accept=".pdf" />
                                <div class="inline-flex w-28 h-8 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer hover:bg-uts-600 transition">Cargar</div>
                            </div>
                        </div>
                        <span id="soporte_pagoError" class="text-red-500 text-xs"></span>
                        <ul id="file-list-soporte" class="mt-2 text-gray-600 text-xs list-disc pl-5"></ul>
                        <div id="tooltip-soporte" class="tooltip-content hidden absolute z-10 px-4 py-3 bg-gray-700 text-white text-xs rounded-lg shadow-lg w-56">
                            Suba el soporte de pago correspondiente a la liquidación.
                        </div>
                    </div>
                    
                </div>
                
                <div class="mt-4">
                    <p class="text-xs">
                        <div class="relative inline-block">
                            <i class="fa-solid fa-circle-question text-sm text-uts-500 cursor-pointer tooltip-icon" data-tooltip="tooltip-liquidacion"></i>
                        </div>
                        <strong>NOTA:</strong> En caso de que el estudiante desee sugerir un director de trabajo de grado, deberá adjuntar una página adicional en el archivo PDF correspondiente a los pagos de la modalidad, indicando de manera formal el nombre del docente que desea sugerir como director de trabajo de grado. El comité evaluará la sugerencia y responderá al estudiante.
                    </p>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                    <button type="button" onclick="closeFase2EstudianteModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-lg transition">Cancelar</button>
                    <button type="submit" class="flex bg-uts-500 hover:bg-uts-600 text-white px-5 py-2 rounded-lg transition items-center gap-2">
                        <svg id="loadingSpinner-fase2" class="hidden w-4 h-4 text-white animate-spin" viewBox="0 0 64 64" fill="none">
                            <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                            <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                        </svg>
                        <i class="fa-regular fa-paper-plane"></i>
                        Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Modal FASE 2 - Detalles (Ver información enviada) -->
<div id="fase2DetailsModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" onclick="closeFase2DetailsModal()">
        <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                <button class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500" onclick="closeFase2DetailsModal()">&times;</button>
                <div class="p-6 mt-2">
                    <p class="text-2xl font-bold mb-4">Detalles de la <span class="bg-uts-500 text-white px-2 py-0.5 rounded uppercase shadow">Fase 2</span></p>
                    <div id="fase2DetailsContent" class="space-y-3">
                        <div class="text-center py-4">
                            <svg class="inline w-8 h-8 text-gray-400 animate-spin" viewBox="0 0 64 64" fill="none">
                                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                            </svg>
                            <p class="mt-2 text-gray-500">Cargando detalles...</p>
                        </div>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="button" onclick="closeFase2DetailsModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Modal FASE 2 - Administrador (Responder solicitud + Asignar docentes) -->
<div id="fase2AdminModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" onclick="closeFase2AdminModal()">
        <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                <button class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500" onclick="closeFase2AdminModal()">&times;</button>
                <form class="p-6 mt-2" id="fase2AdminForm">
                    @csrf
                    <input type="hidden" name="practica_id" value="{{ $practica->id }}">
                    <p class="text-2xl font-bold mb-4">Responder <span class="bg-uts-500 text-white px-2 py-0.5 rounded uppercase shadow">Fase 2</span></p>
                    <p class="text-sm text-gray-600 mb-4">Apruebe o rechace el pago de modalidad. Si aprueba, deberá asignar director y evaluador.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Número de acta <span class="text-red-500">*</span></label>
                            <input type="text" name="nro_acta" id="nro_acta_fase2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                            <span id="nro_acta_fase2Error" class="text-red-500 text-sm"></span>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Fecha del acta <span class="text-red-500">*</span></label>
                            <input type="date" name="fecha_acta" id="fecha_acta_fase2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                            <span id="fecha_acta_fase2Error" class="text-red-500 text-sm"></span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Estado <span class="text-red-500">*</span></label>
                        <select name="estado" id="estado_fase2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                            <option value="">Seleccione un estado</option>
                            <option value="Aprobada">Aprobar</option>
                            <option value="Rechazada">Rechazar</option>
                        </select>
                        <span id="estado_fase2Error" class="text-red-500 text-sm"></span>
                    </div>

                    <!-- Contenedor para asignación de docentes (se muestra solo si selecciona Aprobada) -->
                    <div id="container_docentes_fase2" class="hidden mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="font-bold text-sm text-gray-700 mb-3">Asignación de docentes:</p>
                        
                        <div class="mb-3">
                            <label class="block font-medium text-sm text-gray-700">Director <span class="text-red-500">*</span></label>
                            <select name="director_id" id="director_id_fase2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <option value="">Seleccione un director</option>
                                @foreach($docentes as $docente)
                                    <option value="{{ $docente->id }}">{{ $docente->name }}</option>
                                @endforeach
                            </select>
                            <span id="director_id_fase2Error" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="mb-3">
                            <label class="block font-medium text-sm text-gray-700">Evaluador <span class="text-red-500">*</span></label>
                            <select name="evaluador_id" id="evaluador_id_fase2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <option value="">Seleccione un evaluador</option>
                                @foreach($docentes as $docente)
                                    <option value="{{ $docente->id }}">{{ $docente->name }}</option>
                                @endforeach
                            </select>
                            <span id="evaluador_id_fase2Error" class="text-red-500 text-sm"></span>
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700">Codirector (opcional)</label>
                            <select name="codirector_id" id="codirector_id_fase2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500">
                                <option value="">Seleccione un codirector (opcional)</option>
                                @foreach($docentes as $docente)
                                    <option value="{{ $docente->id }}">{{ $docente->name }}</option>
                                @endforeach
                            </select>
                            <span id="codirector_id_fase2Error" class="text-red-500 text-sm"></span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Respuesta / Comentarios <span class="text-red-500">*</span></label>
                        <textarea name="respuesta" id="respuesta_fase2" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uts-500 focus:border-uts-500" placeholder="Describa los motivos de su decisión..."></textarea>
                        <span id="respuesta_fase2Error" class="text-red-500 text-sm"></span>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" onclick="closeFase2AdminModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cancelar</button>
                        <button type="submit" class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                            <svg id="loadingSpinner-fase2-admin" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none">
                                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
                                <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" class="text-white"></path>
                            </svg>
                            Enviar Respuesta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    
    @push('styles')
    <style>
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
            0% { box-shadow: 0 0 0 0 rgba(193, 214, 49, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(193, 214, 49, 0); }
            100% { box-shadow: 0 0 0 0 rgba(193, 214, 49, 0); }
        }
        .btn-action {
            padding: 6px 12px;
            margin: 0 4px;
            transition: all 0.3s ease;
            border-radius: 0.375rem;
        }
        .modal-overlay {
            transition: background-color 0.3s ease;
        }
        .modal-content {
            max-width: 650px !important;
            width: 100% !important;
        }
        .tooltip-icon {
            cursor: pointer;
        }
        .hidden {
            display: none;
        }
        .show {
            display: block !important;
        }
        #fase1EstudianteModal.show, #fase1DetailsModal.show, #fase1AdminModal.show {
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
        fase1_store: '{{ route("practicas.fase1.store") }}',
        fase1_details: '{{ route("practicas.fase1.details") }}',
        fase1_reply: '{{ route("practicas.fase1.reply") }}',
        fase2_store: '{{ route("practicas.fase2.store") }}',
        fase2_details: '{{ route("practicas.fase2.details") }}',
        fase2_reply: '{{ route("practicas.fase2.reply") }}'
    };
</script>

    <script src="{{ asset('js/fases/practicas/fase_2.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/fases/practicas/fase_1.js') }}"></script>
    @endpush
</x-app-layout>