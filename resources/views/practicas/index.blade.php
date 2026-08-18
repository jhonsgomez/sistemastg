
@php
    use Carbon\Carbon;

    $fechaActual = Carbon::now()->format('Y-m-d');

    $anio_inicio = 2025;
    $anio_actual = Carbon::now()->year;
    $mes_actual = Carbon::now()->month;
@endphp

<!---Este es el que trae el menu -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Practicas <span
                class="bg-uts-500 text-gray-800 font-bold me-2 px-2.5 py-0.5 rounded uppercase">Empresariales</span>
        </h2>
    </x-slot>

    @push('styles')
        <style>
            div.dataTables_processing {
                display: none !important;
            }
            .btn-action {
                width: 42px;
                height: 32px;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                position: relative;
            }

            /* Contenedor de botones de acciones */
            #practicasTable .btn-action-container {
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                width: 100% !important;
            }

            /* Cada botón individual */
            #practicasTable .btn-action {
                width: 42px;
                height: 32px;
                display: inline-flex !important;
                justify-content: center !important;
                align-items: center !important;
                text-align: center !important;
            }

            /* Celda completa de acciones */
            #practicasTable td:last-child {
                text-align: center !important;
                vertical-align: middle !important;
            }
            
            .btn-action .loading-spinner {
                align-items: center;
                transform: translate(-50%, -50%);
                width: 1rem;
                height: 1rem;
            }

            .btn-action i {
                font-size: 1rem;
            }

            .modal-overlay {
                position: fixed !important;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                transition: opacity 0.3s ease;
                z-index: 999;
            }

            .modal-close-btn-custom,
            .modal-close-btn-calendar {
                position: absolute !important;
                top: 10px !important;
                right: 28px !important;
                background: none !important;
                border: none !important;
                font-size: 30px !important;
                cursor: pointer !important;
                color: #6b7280 !important;
                transition: color 0.3s ease !important;
            }

            .modal-close-btn-custom:hover,
            .modal-close-btn-calendar:hover {
                color: #dc2626 !important;
            }

            @media screen and (max-width: 640px) {
                .buttons-container {
                    padding-top: 1rem !important;
                }
                .modal-content {
                    padding: 1.5rem !important;
                }
                .container-actions {
                    justify-content: flex-start !important;
                    align-items: center !important;
                }
                .container_check {
                    margin-top: 0 !important;
                }
            }

            /* ESTILOS PARA OCULTAR MODALES */
            #detailsModal,
            #createModal,
            #responderSolicitudPractica,
            #replySolicitudModal,
            #desactivarProyectoModal,
            #activarProyectoModal,
            #desactivarPracticaModal,
            #activarPracticaModal,
            #warningModal,
            #reporteModal {
                visibility: hidden !important;
                opacity: 0 !important;
                transform: translateY(-10px) !important;
                transition: visibility 0.3s ease, opacity 0.3s ease, transform 0.3s ease;
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
            #reporteModal.show {
                visibility: visible !important;
                opacity: 1 !important;
                transform: translateY(0) !important;
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
                        
                        #integrantes_list {
                position: absolute;
                z-index: 50;
                width: 100%;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 0.5rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                max-height: 240px;
                overflow-y: auto;
            }

            #integrantes_list div:hover {
                background-color: #f3f4f6;
            }

            #selected_integrante_2 {
                display: none;
            }
                    
            .modal-content {
                max-width: 850px !important;
                width: 100% !important;
                padding: 2rem 3rem !important;
                background-color: white !important;
                border-radius: 8px !important;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            }

            #reporteModal .modal-content {
                max-width: 500px !important;
            }

            .modal-close-btn {
                position: absolute !important;
                top: 10px !important;
                right: 10px !important;
                background-color: transparent !important;
                border: none !important;
                font-size: 1.5rem !important;
                color: #333 !important;
                cursor: pointer !important;
            }

            .radio-verde {
                cursor: pointer !important;
            }

            .radio-verde:checked {
                color: #C1D631 !important;
            }

            input[type="radio"]:focus {
                outline: 1px solid #C1D631;
                box-shadow: 0 0 0 1px #C1D631;
            }

            .modal-close-btn:hover {
                color: #e53e3e !important;
            }

            #check_integrante_2:checked,
            #check_integrante_3:checked {
                background-color: #C1D631 !important;
            }

            .container_check {
                margin-top: 1.5rem;
            }

            /* Estilos para el foco del selector de cantidad de registros (length menu) */
            #practicasTable_wrapper .dataTables_length select:focus,
            .dataTables_length select:focus,
            #practicasTable_length select:focus,
            select.dt-input:focus {
                border-color: #C1D631 !important;
                outline: none !important;
                box-shadow: 0 0 0 1.5px rgba(193, 214, 49, 1) !important;
            }

            /* Estilos para cualquier input o select dentro del wrapper de DataTables */
            .dataTables_wrapper input:focus,
            .dataTables_wrapper select:focus {
                border-color: #C1D631 !important;
                outline: none !important;
                box-shadow: 0 0 0 2px rgba(193, 214, 49, 0.5) !important;
            }
                        /* ESTILOS DE PAGINACIÓN */
            .dataTables_paginate .paginate_button {
                padding: 0.25rem 0.75rem !important;
                margin: 0 2px !important;
                border-radius: 0.375rem !important;
                border: 1px solid #d1d5db !important;
                background: white !important;
                color: #374151 !important;
                font-size: 0.875rem !important;
            }

            .dataTables_paginate .paginate_button.current {
                background-color: #C1D631 !important;
                border-color: #C1D631 !important;
                color: white !important;
            }

            .dataTables_paginate .paginate_button:hover {
                background-color: #e5e7eb !important;
                border-color: #9ca3af !important;
            }
            #practicasTable_wrapper select.dt-input,
            #practicasTable_length select.dt-input {
                border-radius: 0.375rem !important;
                border: 1px solid #d1d5db !important;
                padding: 0.5rem 2rem 0.5rem 0.75rem !important;
                background-color: white !important;
                cursor: pointer !important;
                appearance: none !important;
                background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%236b7280"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>') !important;
                background-repeat: no-repeat !important;
                background-position: right 0.75rem center !important;
                background-size: 1rem !important;
                line-height: 1.5 !important;
                font-size: 1rem !important;
            }

            /* Estilos tipo Select2 */
            .select2-container--default .select2-selection--single {
                border-color: #d1d5db;
                border-radius: 0.375rem;
            }
            
            .select2-container--default.select2-container--open .select2-selection--single {
                border-color: #C1D631;
            }
            
            .select2-container--default .select2-selection--single:focus {
                border-color: #C1D631;
                box-shadow: 0 0 0 2px rgba(193, 214, 49, 0.2);
            }
            
            /* Estilos para la lista de resultados */
            .integrante-resultado {
                padding: 8px 12px;
                cursor: pointer;
                border-bottom: 1px solid #e5e7eb;
                font-size: 0.875rem;
            }
            
            .integrante-resultado:last-child {
                border-bottom: none;
            }
            
            .integrante-resultado:hover {
                background-color: #f0fdf4;
                background-color: rgba(193, 214, 49, 0.1);
            }
            
            .integrante-resultado.highlight {
                background-color: #f0fdf4;
                background-color: rgba(193, 214, 49, 0.2);
            }
            
            /* Tag seleccionado */
            .selected-tag {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background-color: #f0fdf4;
                border: 1px solid #C1D631;
                color: #4a5b1a;
                padding: 4px 8px 4px 12px;
                border-radius: 9999px;
                font-size: 0.875rem;
            }
            
            .selected-tag .remove-btn {
                cursor: pointer;
                font-size: 1.125rem;
                font-weight: bold;
                color: #9ca3af;
                transition: color 0.2s;
            }
            
            .selected-tag .remove-btn:hover {
                color: #dc2626;
            }
            
            /* Mensaje de búsqueda */
            .search-message {
                padding: 8px 12px;
                text-align: center;
                color: #6b7280;
                font-size: 0.875rem;
            }
            
            /* Input con borde verde cuando tiene foco */
            #search_integrante_2:focus {
                border-color: #C1D631;
                outline: none;
                box-shadow: 0 0 0 2px rgba(193, 214, 49, 0.2);
            }

            /* Estilo del input cuando tiene focus - VERDE del programa */
            #search_integrante_2:focus {
                border-color: #C1D631 !important;
                outline: none !important;
                box-shadow: 0 0 0 2px rgba(193, 214, 49, 0.2) !important;
                ring-color: #C1D631 !important;
            }
            
            /* Estilos para la lista desplegable */
            #integrantes_list {
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 0.5rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                max-height: 240px;
                overflow-y: auto;
                z-index: 50;
            }
            
            /* Cada resultado */
            #integrantes_list > div {
                padding: 10px 12px;
                cursor: pointer;
                border-bottom: 1px solid #f3f4f6;
                transition: all 0.2s;
            }
            
            #integrantes_list > div:last-child {
                border-bottom: none;
            }
            
            /* Hover con color VERDE del programa */
            #integrantes_list > div:hover {
                background-color: rgba(193, 214, 49, 0.1);
            }
            
            /* Estilo del elemento seleccionado (tag) */
            #selected_integrante_2 {
                margin-top: 8px;
            }
            
            #selected_integrante_2 > div {
                background-color: #f0f9f0;
                border: 1px solid #C1D631;
                border-radius: 8px;
                padding: 8px 12px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            
            #selected_integrante_2 button {
                background: none;
                border: none;
                cursor: pointer;
                font-size: 18px;
                color: #9ca3af;
                transition: color 0.2s;
            }
            
            #selected_integrante_2 button:hover {
                color: #dc2626;
            }
            
            /* Mensaje de "No se encontraron estudiantes" */
            #integrantes_list .text-center {
                padding: 12px;
                color: #6b7280;
                text-align: center;
            }
            
        </style>

    @endpush

    <!--Dasboard-->
    <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-b">
        <h2 class="text-2xl text-center font-bold text-gray-800">
            Prácticas <span
                class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Empresariales</span>
        </h2>


        <!-- ESTE ES EL ICONO ROJO -->
        <div class="flex justify-center items-center space-x-2 buttons-container">
            <button type="button" id="warning" onclick="openWarningModal()"
                class="btn-action shadow bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded-lg relative mx-1">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <svg id="loadingSpinner-warningOpen" style="margin: 4px 1px"
                    class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

            <!-- Botón Calendario (Verde) -->
            <!--- AQUI SOLO VA LA VARIABLE $fechas--->

            @if (isset($fechas))
                @if ($fechaActual >= $fechas['fecha_inicio_proyectos'] && $fechaActual <= $fechas['fecha_fin_proyectos'])
                    <button type="button" id="calendar" onclick="openCalendarModal(this)"
                        class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
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
                    @if (auth()->user()->hasRole(['estudiante']))
                        @can('create_proyecto_grado')
                            <button onclick="openCreateModal()" id="openCreateModalButton"
                                class="bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg flex items-center shadow transition">
                                <i class="fas fa-plus mr-2"></i>
                                <svg id="loadingSpinner-crear" style="margin: 4px 10px 4px 0"
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
                                </svg> Nueva Práctica
                            </button>
                        @endcan
                    @endif
                @endif
            @endif


            @if (auth()->user()->hasRole(['super_admin', 'admin', 'coordinador']))
                <button type="button" id="reporte" onclick="openReporteModal()"
                    class="btn-action shadow bg-green-700 hover:bg-green-800 text-white px-3 py-1 rounded-lg relative">
                    <i class="fa-solid fa-download"></i>
                </button>
            @endif
        </div>
    </div>

    <!--Datatables-->

    <div class="p-4">
    @if (auth()->user()->hasRole(['super_admin', 'admin', 'coordinador']))
    <div class="mb-3">
        <select id="filtroRolesPracticas"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block focus:ring-uts-500 focus:border-uts-500">
            <option value="" selected>Seleccione un filtro</option>
            <option value="pendientes_comite">Pendientes Comité</option>
            <option value="pendientes_director">Pendientes Director</option>
            <option value="pendientes_evaluador">Pendientes Evaluador</option>
            <option value="propuestas_pendientes">Propuestas sin aprobar</option>
            <option value="informes_pendientes">Informes finales sin aprobar</option>
        </select>
    </div>
    @endif
    
    @can('view_practicas')
    <table id="practicasTable" class="w-full">
        <thead>
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
    @endcan
</div>

    <!--Details Fase 0-->

    <div id="detailsModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0"
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;"
            onclick="closeDetailsModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeDetailsModal()">&times;</button>
                    <div class="p-2 mt-2">
                        <p class="text-2xl font-bold mt-4 mb-6" id="detailsTitle"></p>
                        <div id="content-details"></div>
                        <div class="flex justify-end space-x-2 mt-4">
                            <button type="button" onclick="closeDetailsModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Modal de prácticas-->
<div id="createModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0"
        style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;"
        onclick="closeCreateModal()">
        <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                onclick="event.stopPropagation()">
                <button class="modal-close-btn-custom" onclick="closeCreateModal()">&times;</button>
                @if (auth()->user()->hasRole(['estudiante']))
                    <form id="practicasForm" class="p-6 mt-2" method="POST" enctype="multipart/form-data">
                        @csrf
                        <p class="text-2xl font-bold mb-6" id="formTitle"></p>
                        <p class="text-md mb-6">
                            En este formulario, el estudiante registrará la información necesaria para realizar la solicitud de prácticas empresariales.
                        </p>

                        <p class="text-md text-gray-700 mb-6">
                            <i class="fa-solid fa-circle-info mr-1 text-uts-500"></i>
                            Tener en cuenta que la solicitud de prácticas empresariales es individual.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-6">
                            
                            {{-- FILA 1: NOMBRE COMPLETO --}}
                            <div>
                                <div class="flex items-center gap-2">
                                    <label class="block font-medium text-sm text-gray-700">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                        <span class="text-red-600 mr-1 text-lg">*</span> Nombre completo
                                    </label>
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-nombre_completo"></i>
                                        <div id="tooltip-nombre_completo"
                                            class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-40">
                                            Nombre completo del estudiante.
                                        </div>
                                    </div>
                                </div>
                                <input type="text" name="nombre_completo" value="{{ auth()->user()->name }}"
                                    class="bg-gray-200 border-gray-300 rounded-md mt-1 block w-full cursor-default" readonly>
                            </div>

                            {{-- CORREO INSTITUCIONAL --}}
                            <div>
                                <div class="flex items-center gap-2">
                                    <label class="block font-medium text-sm text-gray-700">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                        <span class="text-red-600 mr-1 text-lg">*</span> Correo institucional
                                    </label>
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-correo"></i>
                                        <div id="tooltip-correo"
                                            class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-44">
                                            Correo institucional del estudiante.
                                        </div>
                                    </div>
                                </div>
                                <input type="text" name="correo" value="{{ auth()->user()->email }}"
                                    class="bg-gray-200 border-gray-300 rounded-md mt-1 block w-full cursor-default" readonly>
                            </div>

                            {{-- FILA 2: NIVEL ACADÉMICO --}}
                            <div>
                                <div class="flex items-center gap-2">
                                    <label class="block font-medium text-sm text-gray-700">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                        <span class="text-red-600 mr-1 text-lg">*</span> Nivel académico
                                    </label>
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-nivel"></i>
                                        <div id="tooltip-nivel"
                                            class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-44">
                                            Nivel académico del estudiante.
                                        </div>
                                    </div>
                                </div>
                                <input type="text" name="nivel" value="{{ auth()->user()->nivel->nombre ?? '' }}"
                                    class="bg-gray-200 border-gray-300 rounded-md mt-1 block w-full cursor-default" readonly>
                            </div>

                            {{-- NÚMERO DE DOCUMENTO --}}
                            <div>
                                <div class="flex items-center gap-2">
                                    <label class="block font-medium text-sm text-gray-700">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                        <span class="text-red-600 mr-1 text-lg">*</span> Número de documento
                                    </label>
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-documento"></i>
                                        <div id="tooltip-documento"
                                            class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-48">
                                            Número de documento de identidad.
                                        </div>
                                    </div>
                                </div>
                                <input type="number" name="documento" value="{{ auth()->user()->nro_documento ?? '' }}"
                                    class="bg-gray-200 border-gray-300 rounded-md mt-1 block w-full cursor-default" readonly>
                            </div>

                            {{-- FILA 3: NÚMERO DE CELULAR --}}
                            <div>
                                <div class="flex items-center gap-2">
                                    <label class="block font-medium text-sm text-gray-700">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                        <span class="text-red-600 mr-1 text-lg">*</span> Número de celular
                                    </label>
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-celular"></i>
                                        <div id="tooltip-celular"
                                            class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-44">
                                            Número de celular de contacto.
                                        </div>
                                    </div>
                                </div>
                                <input type="number" name="celular" value="{{ auth()->user()->nro_celular ?? '' }}"
                                    class="bg-gray-200 border-gray-300 rounded-md mt-1 block w-full cursor-default" readonly>
                            </div>

                            {{-- SEGUNDO INTEGRANTE (opcional) --}}
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <label class="block font-medium text-sm text-gray-700">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                        Segundo integrante (opcional) 
                                    </label>
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-integrante"></i>
                                        <div id="tooltip-integrante"
                                            class="hidden absolute z-10 max-w-[90vw] px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg left-1/2 -translate-x-1/2 bottom-full mb-2 sm:left-0 sm:translate-x-0 w-44">
                                            Seleccione el compañero con quien realizará la práctica empresarial.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="relative">
                                    <select id="id_integrante_2"
                                            name="id_integrante_2"
                                            class="w-full">
                                        <option value=""></option>
                                    </select>

                                    <span id="id_integrante_2Error" class="text-red-500 text-sm"></span>
                                </div>
                                
                                <!-- Contenedor para mostrar el seleccionado -->
                                <div id="selected_integrante_2" class="mt-2 hidden"></div>
                                
                                <span id="id_integrante_2Error" class="text-red-500 text-sm"></span>
                            </div>

                            {{-- ¿CUENTA CON EMPRESA? (ocupa ambas columnas) --}}
                            @php $campoTieneEmpresa = $campos->where('name', 'tiene_empresa')->first(); @endphp
                            @if($campoTieneEmpresa)
                            <div class="col-span-1 sm:col-span-2">
                                <div class="flex items-center gap-2">
                                    <label class="block font-medium text-sm text-gray-700">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                        <span class="text-red-600 mr-1 text-lg">*</span> {{ $campoTieneEmpresa->label }}
                                    </label>
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-tiene_empresa"></i>
                                        <div id="tooltip-tiene_empresa"
                                            class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-44">
                                            Seleccione la opción si ya cuenta con empresa donde realizar las prácticas empresariales.
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="flex items-center gap-4">
                                        <label class="flex items-center gap-1">
                                            <input type="radio" name="tiene_empresa" value="1" class="radio-verde" onchange="toggleHojaVida()"> Sí
                                        </label>
                                        <label class="flex items-center gap-1">
                                            <input type="radio" name="tiene_empresa" value="0" class="radio-verde" onchange="toggleHojaVida()"> No
                                        </label>
                                    </div>
                                </div>
                                <span id="tiene_empresaError" class="text-red-500 text-sm"></span>
                            </div>
                            @endif

                            {{-- HOJA DE VIDA (condicional, ocupa ambas columnas) --}}
                            @php $campoHojaVida = $campos->where('name', 'hoja_vida')->first(); @endphp
                            @if($campoHojaVida)
                            <div class="col-span-1 sm:col-span-2">
                                <div class="flex items-center gap-2" id="hojaVidaLabel" style="display:none;">
                                    <label class="block font-medium text-sm text-gray-700">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                        {{ $campoHojaVida->label }}
                                    </label>
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-hoja_vida"></i>
                                        <div id="tooltip-hoja_vida"
                                            class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-44">
                                            Suba su hoja de vida en formato PDF si no cuenta con empresa.
                                        </div>
                                    </div>
                                </div>
                                <div id="hojaVidaContainer" style="display:none;"
                                    class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border-2 border-gray-300 gap-3 grid border-dashed">
                                    <div class="grid gap-1">
                                        <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                        <h2 class="text-center text-gray-400 text-xs leading-4">
                                            Solo archivos de PDF de máximo {{ config('practicas.peso_maximo_hojavida') }} MB
                                        </h2>
                                    </div>
                                    <div class="grid gap-2">
                                        <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                        <div class="flex items-center justify-center">
                                            <input type="file" name="hoja_vida" id="hoja_vida"
                                                class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf" />
                                            <div class="flex w-28 h-9 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer">Cargar</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 mb-4">
                                    <span id="hoja_vidaError" class="block text-red-500 text-sm mb-2"></span>

                                    <div class="pl-5">
                                        <ul id="file-list-fase0" class="text-gray-600 text-sm list-disc"></ul>
                                        <span id="files-size-fase0" class="block text-gray-800 text-sm mt-2"></span>
                                    </div>
                                </div>
                          
                                {{-- HOJA DE VIDA SEGUNDO INTEGRANTE --}}
                                <div class="col-span-1 sm:col-span-2">

                                    <div class="flex items-center gap-2" id="hojaVidaLabel2" style="display:none;">
                                        <label class="block font-medium text-sm text-gray-700">
                                            <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                            Hoja de vida integrante 2:
                                        </label>

                                        <div class="relative inline-block">
                                            <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                                data-tooltip="tooltip-hoja_vida2"></i>

                                            <div id="tooltip-hoja_vida2"
                                                class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg w-44">
                                                Suba la hoja de vida del integrante 2 en formato PDF si no cuenta con empresa.
                                            </div>
                                        </div>
                                    </div>

                                    <div id="hojaVidaContainer2" style="display:none;"
                                        class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border-2 border-gray-300 gap-3 grid border-dashed">

                                        <div class="grid gap-1">
                                            <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>

                                            <h2 class="text-center text-gray-400 text-xs leading-4">
                                                Solo archivos de PDF de máximo {{ config('practicas.peso_maximo_hojavida') }} MB
                                            </h2>
                                        </div>

                                        <div class="grid gap-2">
                                            <h4 class="text-center text-gray-900 text-sm font-medium">
                                                Arrastra o carga archivos aquí
                                            </h4>

                                            <div class="flex items-center justify-center">
                                                <input type="file"
                                                    name="hoja_vida_2"
                                                    id="hoja_vida_2"
                                                    class="absolute inset-0 opacity-0 cursor-pointer"
                                                    accept=".pdf" />

                                                <div class="flex w-28 h-9 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer">
                                                    Cargar
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 mb-4">
                                        <span id="hoja_vida2Error" class="block text-red-500 text-sm mb-2"></span>

                                        <div class="pl-5">
                                            <ul id="file-list-fase0-2" class="text-gray-600 text-sm list-disc"></ul>
                                            <span id="files-size-fase0-2" class="block text-gray-800 text-sm mt-2"></span>
                                        </div>
                                    </div>
                          
            

                                </div>

                            </div>

                            @endif

                            {{-- CAMPO OCULTO PERIODO --}}
                            @php $campoPeriodo = $campos->where('name', 'periodo')->first(); @endphp
                            @if($campoPeriodo)
                                @php
                                    $anio_actual = date('Y');
                                    $mes_actual = date('n');
                                    $periodo_actual = ($mes_actual <= 6) ? "$anio_actual-1" : "$anio_actual-2";
                                @endphp
                                <input type="hidden" name="periodo" value="{{ $periodo_actual }}">
                            @endif

                        </div>

                        <p class="text-red-600 text-sm mb-6 text-justify">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                           Si selecciona un segundo integrante, el comité evaluará si la propuesta puede realizarse entre dos estudiantes. Luego, aprobará o rechazará la solicitud.
                        </p>
                        
                        <p class="text-sm mb-6 text-blue-500 text-justify"><i class="fa-solid fa-circle-info mr-1"></i> Para los estudiantes con pensum 2019-1, cada estudiante debe tener aprobado el <strong>90%</strong> de los créditos (Tecnología: 97 / Profesional: 65).
                        Para los estudiantes con Pensum 2026-1, El estudiante debe tener aprobado el <strong>90%</strong> de los créditos (Tecnología: {{ config('practicas.creditos_tecnologia') }} / Profesional: {{ config('practicas.creditos_profesional') }}).
                        </p>
                        <p class="text-sm mb-6"><strong>NOTA: </strong>Verifique si la empresa tiene convenio vigente en la pagina de la Oficina de Relaciones Interinstitucionales (ORI): <a href="https://oriapp.uts.edu.co/activities_guest" target="_blank" class="text-uts-500 underline hover:text-uts-800"> Consultar convenios aquí </a></p>

                        <div class="flex justify-end space-x-2">
                            <button type="button" onclick="closeCreateModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">Cancelar</button>
                            <button type="submit" id="guardarModalButton" class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-guardar" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                                </svg>
                                Enviar
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

    <!--Responder Solicitud Practicas Modal-->
    <div id="responderSolicitudPractica" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0"
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;"
            onclick="closeResponderSolicitudModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeResponderSolicitudModal()">&times;</button>
                    <form id="responderSolicitudForm" class="p-4 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="respuestaTitulo"></p>
                        <input type="hidden" name="solicitudPractica_id" id="solicitudPractica_id">
                        <div class="mb-4">
                            <label for="estado" class="block  font-medium text-sm text-gray-700"><i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>Estado de la
                                Solicitud</label>
                            <select name="estado" id="estado"
                            class="border-gray-300 rounded-md mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                            <option value="" selected disabled>Seleccione una opción</option>
                            <option value="Aprobada">Aprobada</option>
                            <option value="Aplazada">Aplazada</option>
                            <option value="Rechazada">Rechazada</option>
                        </select>
                            <span id="estadoError" class="text-red-500 text-sm"></span>
                        </div>
                        <div class="mb-4">
                            <label for="mensaje" class="block  font-medium text-sm text-gray-700"><i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Respuesta de la solicitud:
                            </label>
                            <div id="txt-editor" class="shadow txt-editor-quill"></div>
                            <textarea name="mensaje" id="mensaje" class="hidden"></textarea>
                            <span id="mensajeError" class="text-red-500 text-sm"></span>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" onclick="closeResponderSolicitudModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cerrar
                            </button>
                            <button type="submit" id="responderSolicitudButton"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-replyProyectos" style="margin: 4px 10px 4px 0"
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
                                Responder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--Deshabilitar modal practicas-->

    <div id="desactivarPracticaModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeDesactivarPracticaModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeDesactivarPracticaModal()">
                        &times;
                    </button>
                    <form id="desactivarPracticaForm" class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="desactivarPracticaTitle"></p>
                        <p class="font-medium text-sm text-gray-700 mb-6">En este formulario el comité de trabajos de grado del programa desactivará la práctica en curso.</p>
                        <input type="hidden" name="practica_id" id="practica_id">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-4 mt-4">
                            <div>
                                <label for="nro_acta_desactivar" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>
                                    Número de acta:
                                </label>
                                <input type="text" name="nro_acta_desactivar" id="nro_acta_desactivar"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el número de acta">
                                <span id="nro_acta_desactivarError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="fecha_acta_desactivar" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-calendar-days mr-1 text-gray-500"></i>
                                    Fecha del acta:
                                </label>
                                <input type="date" name="fecha_acta_desactivar" id="fecha_acta_desactivar"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                <span id="fecha_acta_desactivarError" class="text-red-500 text-sm"></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="descripcion_desactivar" class="block font-medium text-sm text-gray-700" style="margin-bottom: 5px;">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Descripción de la respuesta:
                            </label>
                            <div id="txt-editor-desactivar" class="shadow txt-editor-quill"></div>
                            <textarea name="descripcion_desactivar" id="descripcion_desactivar" class="hidden"></textarea>
                            <span id="descripcion_desactivarError" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" onclick="closeDesactivarPracticaModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cerrar
                            </button>
                            <button type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-desactivarPractica" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    </path>
                                </svg>
                                Deshabilitar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--Hbilitar modal prácticas-->

    <div id="activarPracticaModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeActivarPracticaModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeActivarPracticaModal()">
                        &times;
                    </button>
                    <form id="activarPracticaForm" class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="activarPracticaTitle"></p>
                        <p class="font-medium text-sm text-gray-700 mb-6">En este formulario el comité habilitará la práctica empresarial previamente deshabilitada.</p>
                        <input type="hidden" name="practica_id_activar" id="practica_id_activar">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-4 mt-4">
                            <div>
                                <label for="nro_acta_activar" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>
                                    Número de acta:
                                </label>
                                <input type="text" name="nro_acta_activar" id="nro_acta_activar"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el número de acta">
                                <span id="nro_acta_activarError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="fecha_acta_activar" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-regular fa-calendar-days mr-1 text-gray-500"></i>
                                    Fecha del acta:
                                </label>
                                <input type="date" name="fecha_acta_activar" id="fecha_acta_activar"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                <span id="fecha_acta_activarError" class="text-red-500 text-sm"></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="descripcion_activar" class="block font-medium text-sm text-gray-700" style="margin-bottom: 5px;">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Descripción / motivo:
                            </label>
                            <div id="txt-editor-activar" class="shadow" style="height: 200px;"></div>
                            <textarea name="descripcion_activar" id="descripcion_activar" class="hidden"></textarea>
                            <span id="descripcion_activarError" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" onclick="closeActivarPracticaModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cerrar
                            </button>
                            <button type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-activarPractica" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    </path>
                                </svg>
                                Habilitar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!--Reporte modal prácticas-->

    <div id="warningModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeWarningModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeWarningModal()">
                        &times;
                    </button>
                    <form id="warningForm" class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="warningTitle">Enviar Reporte <span class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow text-lg">PQRSD</span></p>
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
                                <svg id="loadingSpinner-warning" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
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

    @if (isset($fechas))
        <div id="calendarModal" class="fixed z-50 inset-0 overflow-y-auto">
            <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5);" onclick="closeCalendarModal()">
                <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" style="width: 100% !important; padding: 2rem 2rem !important;" onclick="event.stopPropagation()">
                        
                        <button class="modal-close-btn-custom absolute top-2 right-4 text-2xl text-gray-500 hover:text-red-500 mt-2"
                            onclick="closeCalendarModal()">&times;
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

    <div id="reporteModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeReporteModal()">
        <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                <button class="modal-close-btn-custom" onclick="closeReporteModal()">
                    &times;
                </button>
                <form action="{{ route('practicas.reporte') }}" method="POST" id="reporteForm" class="p-6 mt-2">
                    @csrf
                    <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="reporteTitle"></p>
                    <div class="mb-4">
                        <label for="periodo_reporte" class="block font-medium text-md text-gray-700 mb-4">
                            Periodo académico:
                        </label>
                        <select name="periodo_reporte" id="periodo_reporte"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500" required>
                            <option value="" selected disabled>Seleccione una opción</option>
                            @for ($anio = $anio_actual; $anio >= $anio_inicio; $anio--)
                            @if ($anio == $anio_actual)
                            @if ($mes_actual > 6)
                            <option value="{{ $anio }}-2">{{ $anio }}-2</option>
                            @endif
                            <option value="{{ $anio }}-1">{{ $anio }}-1</option>
                            @else
                            <option value="{{ $anio }}-2">{{ $anio }}-2</option>
                            <option value="{{ $anio }}-1">{{ $anio }}-1</option>
                            @endif
                            @endfor
                        </select>
                        <span id="periodo_reporteError" class="text-red-500 text-sm"></span>
                    </div>
                    <div class="flex justify-end space-x-2 mt-8">
                        <button
                            type="button"
                            onclick="closeReporteModal()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            id="reporteSubmitButton"
                            class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                            <svg id="loadingSpinner-reporte" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                <path
                                    d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                    stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path
                                    d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                    stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
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

    @push('scripts')
        <script src="{{ asset('js/fases/practicas/fase_0.js') }}"></script>

        <script>
            const MAX_FILE_SIZE_MB = {{ config('practicas.peso_maximo_archivos') }};
            const MAX_FILE_SIZE_HV = {{ config('practicas.peso_maximo_hojavida') }};
        </script>
        
        <!--Crear solicitud modal-->

        <script>
            function openCreateModal() {
                var añoActual = new Date().getFullYear();
                var mesActual = new Date().getMonth() + 1;
                var numero = mesActual <= 6 ? 1 : 2;
                var periodo_academico = añoActual + '-' + numero;

                $('#formTitle').html(`Iniciar nueva <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">práctica</span>`);

                $('#modalidad').val('');
                $('#id_integrante_2').val('').trigger('change');
                $('#periodo').val(periodo_academico);

                $('#nivelError').text('');
                $('#modalidadError').text('');
                $('#id_integrante_1Error').text('');
                $('#id_integrante_2Error').text('');
                $('#periodoError').text('');

                $('#createModal').addClass('show');
            }

            function closeCreateModal() {
    $('#practicasForm')[0].reset();

    $('#id_integrante_2').val(null).trigger('change');
    $('#selected_integrante_2').html('').addClass('hidden');

    $('#id_integrante_2Error').text('');
    $('#tiene_empresaError').text('');
    $('#hoja_vidaError').text('');
    $('#hoja_vida2Error').text('');

    $('#hoja_vida').val('');
    $('#hoja_vida_2').val('');
    $('#file-list-fase0').empty();
    $('#file-list-fase0-2').empty();
    $('#files-size-fase0').text('');
    $('#files-size-fase0-2').text('');

    $('#hojaVidaContainer').hide();
    $('#hojaVidaLabel').hide();
    $('#hojaVidaContainer2').hide();
    $('#hojaVidaLabel2').hide();

    $('#createModal').removeClass('show');
}
        </script>

        <script>
    $(document).ready(function() {
    $('#id_integrante_2').select2({
        dropdownParent: $('#createModal'),
        placeholder: 'Buscar por documento o nombre',
        allowClear: true,
        width: '100%',
        minimumInputLength: 5,
        ajax: {
            url: '{{ route("practicas.buscar_estudiantes") }}',
            dataType: 'json',
            delay: 500,
            data: function(params) {
                return {
                    search: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            id: item.id,
                            text: item.documento + ' | ' + item.nombre_completo
                        };
                    })
                };
            }
        }
    });
});
</script>

        <!--Responder solicitud modal-->
<script>
    function openResponderSolicitudModal(id) {
        if (!window.quill) {
            window.quill = new Quill('#txt-editor', {
                theme: 'snow',
                placeholder: 'Ingrese el mensaje de respuesta indicando detalles al destinatario.',
                modules: {
                    toolbar: {
                        container: [
                            [{ 'header': 1 }],
                            [{ 'header': 2 }],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            ['bold', 'italic', 'underline'],
                            [{ 'color': [] }],
                            [{ 'align': [] }],
                            ['image'],
                            ['clean']
                        ],
                        handlers: {
                            image: imageHandler
                        }
                    }
                }
            });
        } else {
            window.quill.root.innerHTML = '';
        }
        
        $('#respuestaTitulo').html(`Responder <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Solicitud</span>`);
        $('#solicitudPractica_id').val(id);
        $('#estado').val('');
        $('#mensaje').val('');
        $('#estadoError').text('');
        $('#mensajeError').text('');
        
        $('#responderSolicitudPractica').addClass('show');
    }

            function imageHandler() {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.click();

        input.onchange = function () {
            const file = input.files[0];

            if (!file) return;

            const formData = new FormData();
            formData.append('image', file);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route("practicas.quill.upload") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    const range = window.quill.getSelection();
                    window.quill.insertEmbed(range.index, 'image', response.url);
                },
                error: function() {
                    showToast('No se pudo cargar la imagen', 'error');
                }
            });
        };
    }

    // ========== CERRAR MODAL RESPONDER ==========
    function closeResponderSolicitudModal() {
        $('#responderSolicitudPractica').removeClass('show');
        if (window.quill) {
            window.quill.root.innerHTML = '';
        }
    }

    // ========== ENVÍO DEL FORMULARIO DE RESPUESTA ==========
$(document).ready(function() {
    $('#responderSolicitudForm').on('submit', function(e) {
        e.preventDefault();

        if (window.quill) {
            $('#mensaje').val(window.quill.root.innerHTML);
        }

        const estadoSeleccionado = $('#estado').val();
        if (!estadoSeleccionado) {
            $('#estadoError').text('Debe seleccionar un estado');
            return;
        }

        const mensaje = $('#mensaje').val();
        if (!mensaje || mensaje === '<p><br></p>') {
            $('#mensajeError').text('Debe ingresar un mensaje de respuesta');
            return;
        }

        let mensajeConfirmacion = "Esta acción no se puede deshacer";
        let tituloConfirmacion = "¿Está seguro?";

        if (estadoSeleccionado === 'Aprobada') {
            mensajeConfirmacion = "Esta acción no se podrá deshacer";
        } else if (estadoSeleccionado === 'Rechazada' || estadoSeleccionado === 'Aplazada') {
            mensajeConfirmacion = "Esta acción no se podrá deshacer";
        }

        Swal.fire({
            heightAuto: false,
            title: tituloConfirmacion,
            text: mensajeConfirmacion,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#C1D631',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, enviar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const button = $('#responderSolicitudButton');
                const loadingSpinner = $('#loadingSpinner-replyProyectos');
                button.prop('disabled', true);
                loadingSpinner.removeClass('hidden');

                $.ajax({
                    url: '{{ route("practicas.responder") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#practicasTable').DataTable().ajax.reload();
                        closeResponderSolicitudModal();
                        
                        showToast('Respuesta enviada exitosamente', 'success');
                    },
                    error: function(xhr) {
                        $('#estadoError').text('');
                        $('#mensajeError').text('');

                        if (xhr.status === 404) {
                            Swal.fire('Error', 'Ruta no encontrada. Limpia la caché de rutas.', 'error');
                            return;
                        }

                        const errors = xhr.responseJSON?.errors;
                        if (errors) {
                            if (errors.estado) $('#estadoError').text(errors.estado[0]);
                            if (errors.mensaje) $('#mensajeError').text(errors.mensaje[0]);
                        } else {
                            showToast(xhr.responseJSON?.message || 'Ocurrió un error inesperado', 'error');
                        }
                    },
                    complete: function() {
                        button.prop('disabled', false);
                        loadingSpinner.addClass('hidden');
                    }
                });
            }
        });
    });
});

// Toast
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

        <!--Practicas-->

        <script>
            $('#practicasForm').on('submit', function(e) {
                e.preventDefault();

                const button = document.getElementById('guardarModalButton');
                const loadingSpinner = document.getElementById('loadingSpinner-guardar');
                button.disabled = true;
                loadingSpinner.classList.remove('hidden');

                const url = "{{ route('practicas.store') }}";
                let formData = new FormData(this);

                Swal.fire({
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
                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                closeCreateModal();
                                showToast('Solicitud enviada correctamente');
                                $('#practicasTable').DataTable().ajax.reload();
                            },
                            error: function(xhr) {
                                const errors = xhr.responseJSON?.errors;
                                if (errors) {
                                    for (let campo in errors) {
                                        $('#' + campo + 'Error').text(errors[campo][0]);
                                    }
                                } else {
                                    $('#practicasTable').DataTable().ajax.reload();
                                    closeCreateModal();
                                    showToast('Solicitud enviada (recarga para ver los cambios)');
                                }
                            },
                            complete: function() {
                                button.disabled = false;
                                loadingSpinner.classList.add('hidden');
                            }
                        });
                    } else {
                        button.disabled = false;
                        loadingSpinner.classList.add('hidden');
                    }
                });
            });
        </script>

        <!--Hoja de vida-->

        <script>
    $(document).ready(function() {
        $('.tooltip-icon').on('mouseenter', function() {
            var tooltipId = $(this).data('tooltip');
            $('#' + tooltipId).removeClass('hidden');
        }).on('mouseleave', function() {
            var tooltipId = $(this).data('tooltip');
            $('#' + tooltipId).addClass('hidden');
        });
    });


function toggleHojaVida() {
    const tieneEmpresaNo = document.querySelector(
        'input[name="tiene_empresa"][value="0"]'
    );

    const integrante2 = document.getElementById('id_integrante_2');

    const container1 = document.getElementById('hojaVidaContainer');
    const label1 = document.getElementById('hojaVidaLabel');

    const container2 = document.getElementById('hojaVidaContainer2');
    const label2 = document.getElementById('hojaVidaLabel2');

    if (!container1) return;

    if (tieneEmpresaNo && tieneEmpresaNo.checked) {

        container1.style.display = 'block';

        if (label1) {
            label1.style.display = 'flex';
        }
        if (integrante2 && integrante2.value !== '') {

            container2.style.display = 'block';

            if (label2) {
                label2.style.display = 'flex';
            }

        } else {

            container2.style.display = 'none';

            if (label2) {
                label2.style.display = 'none';
            }
        }
    } else {

        container1.style.display = 'none';
        if (label1) {
            label1.style.display = 'none';
        }
        container2.style.display = 'none';
        if (label2) {
            label2.style.display = 'none';
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {

    toggleHojaVida();

    const integrante2 = document.getElementById('id_integrante_2');

    if (integrante2) {

        integrante2.addEventListener('change', function () {
            toggleHojaVida();
        });
    }
});
</script>

        <!--Datatables-->

        <script>
var table = $('#practicasTable').DataTable({

    order: [[0, 'desc']],

    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ $dataRoute ?? route('practicas.data') }}",
        data: function (d) {
            d.rol_especifico = "{{ $rol_especifico ?? '' }}";

            d.filter = $('#filtroRolesPracticas').val();

        }
    },
    columns: [
            { 
                data: 'formatted_id', 
                name: 'formatted_id', 
                className: 'text-center',
                orderable: true,
                searchable: true
            },
            { 
                data: 'descripcion', 
                name: 'descripcion', 
                className: 'text-center',
                orderable: false,
                sortable: false,
            },
            { 
                data: 'estado', 
                name: 'estado', 
                className: 'text-center',
                orderable: true,
                searchable: true
            },
            { 
                data: 'acciones', 
                name: 'acciones', 
                className: 'text-center',
                orderable: false,
                searchable: false 
            }
        ],
        responsive: true,
        language: {
            "sProcessing": "",
            "sLengthMenu": "Mostrar _MENU_ registros por página",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible",
            "sInfo": "Mostrando registros del _START_ de _END_",
            "sInfoEmpty": "Mostrando 0 registros",
            "sInfoFiltered": "(filtrado de _MAX_ registros totales)",
            "sSearch": "Buscar:",
            "sLoadingRecords": "",
            "oPaginate": {
                "sFirst": '<i class="fa-solid fa-angles-left" style="font-size: 10px;"></i>',
                "sLast": '<i class="fa-solid fa-angles-right" style="font-size: 10px;"></i>',
                "sNext": '<i class="fa-solid fa-angle-right" style="font-size: 10px;"></i>',
                "sPrevious": '<i class="fa-solid fa-angle-left" style="font-size: 10px;"></i>'
            }
        },
        pagingType: "full_numbers",
        lengthMenu: [[5, 10, 20], [5, 10, 20]],
        pageLength: 5,
        initComplete: function () {
    $('.dt-input').attr('placeholder', 'Ingrese una clave');
}
        });

            

        $('#filtroRolesPracticas').on('change', function() {
            table.ajax.reload();
        });

            // Funciones para habilitar/deshabilitar (ya existentes)
            function habilitarPractica(id) {
                $.post('{{ route('practicas.habilitar') }}', {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    })
                    .done(() => $('#practicasTable').DataTable().ajax.reload());
            }

            function deshabilitarPractica(id) {
                $.post('{{ route('practicas.deshabilitar') }}', {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    })
                    .done(() => $('#practicasTable').DataTable().ajax.reload());
            }
        </script>

        <!--Details modal functions-->

        <script>
            function openDetailsModal(btn, id) {
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }
    
    $('#detailsTitle').html(`Detalles de la <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Práctica</span>`);

    $.get('/tg/practicas/' + id + '/detalle', function(response) {
        let html = `
            <div class="mt-4">
                <!-- Título -->
                <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                    <p class="font-semibold text-gray-700 mb-2 sm:mb-0  w-1/3 min-w-[100px] ">Título de la práctica:</p>
                    <span class="text-gray-800 w-full sm:flex-1 sm:ml-2 ">${escapeHtml(response.titulo)}</span>
                </div>
                
                
                ${response.docentes_html}
                
                <!-- Nivel académico -->
                <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                <p class="font-semibold text-gray-700 mb-2 sm:mb-0  w-1/3 min-w-[100px] ">Nivel académico:</p>
                <span class="text-gray-800 w-full sm:flex-1 sm:ml-2 ">${escapeHtml(response.nivel)}</span>
                </div>
                
                <!-- Modalidad -->
                <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                <p class="font-semibold text-gray-700 mb-2 sm:mb-0  w-1/3 min-w-[100px] ">Modalidad:</p>
                <span class="text-gray-800 w-full sm:flex-1 sm:ml-2">${escapeHtml(response.modalidad)}</span>
                </div>
                
                <!-- Periodo académico -->
                <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                <p class="font-semibold text-gray-700 mb-2 sm:mb-0  w-1/3 min-w-[100px]">Periodo académico:</p>
                <span class="text-gray-800 w-full sm:flex-1 sm:ml-2">${escapeHtml(response.periodo)}</span>
                </div>

                ${response.integrantes_html}
                
        `;
        
        // Empresa / Hoja de vida
        if (response.tiene_empresa) {
            html += `
                <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                    <p class="font-semibold text-gray-700 mb-2 sm:mb-0  w-1/3 min-w-[100px]">¿Cuenta con empresa?</p>
                    <span class="text-gray-800 w-full sm:flex-1 sm:ml-2">Sí</span>
                </div>
            `;
        } else {
            //Hpja de vida integrante 1
            if (response.hoja_vida) {
                html += `
                    <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                        <p class="font-semibold text-gray-700 mb-2 sm:mb-0  w-1/3 min-w-[100px]">Hoja de vida integrante 1:</p>
                        <span class="text-gray-800 w-full sm:flex-1 sm:ml-2">
                            <a target="_blank"
                                class="text-red-600 text-sm underline"
                                href="/tg/storage/${response.hoja_vida}">

                                    <i class="fa-regular fa-file-pdf text-red-600 mr-1"></i>
                                    Documento 1
                                </a>
                        </span>
                    </div>
                `;
            }
            //Hoja de vida integrante 2
            if (response.hoja_vida_2) {

                html += `
                    <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                        <p class="font-semibold text-gray-700 mb-2 sm:mb-0  w-1/3 min-w-[100px]">
                           Hoja de vida integrante 2:
                        </p>
                        <span class="text-gray-800 w-full sm:flex-1 sm:ml-2">
                            <a target="_blank"
                                class="text-red-600 text-sm underline"
                                href="/tg/storage/${response.hoja_vida_2}">
                                    <i class="fa-regular fa-file-pdf text-red-600 mr-1"></i>
                                    Documento 2
                            </a>
                        </span>
                    </div>
                `;
            }


         }
        
        // En la función openDetailsModal, reemplaza la sección de fechas:

// Fechas
html += `
    <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
        <p class="font-semibold text-gray-700 mb-2 sm:mb-0 w-1/3 min-w-[100px]">Fechas propuesta:</p>
        <span class="text-gray-800 w-full sm:flex-1 sm:ml-2">
            <span><b>Envío de propuesta:</b> ${escapeHtml(response.fechas_propuesta.envio_estudiante)}</span><br>
            <span><b>Revisión director:</b> ${escapeHtml(response.fechas_propuesta.revision_director)}</span><br>
            <span><b>Revisión evaluador:</b> ${escapeHtml(response.fechas_propuesta.revision_evaluador)}</span>
        </span>
    </div>
    <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
        <p class="font-semibold text-gray-700 mb-2 sm:mb-0 w-1/3 min-w-[100px]">Fechas informe:</p>
        <span class="text-gray-800 w-full sm:flex-1 sm:ml-2">
            <span><b>Envío de informe:</b> ${escapeHtml(response.fechas_informe.envio_estudiante)}</span><br>
            <span><b>Revisión director:</b> ${escapeHtml(response.fechas_informe.revision_director)}</span><br>
            <span><b>Revisión evaluador:</b> ${escapeHtml(response.fechas_informe.revision_evaluador)}</span>
        </span>
    </div>
`;
        
        $('#content-details').html(html);
        $('#detailsModal').addClass('show');
        
        if (btn) {
            const icon = btn.querySelector('i');
            const spinner = btn.querySelector('.loading-spinner');
            if (icon) icon.classList.remove('hidden');
            if (spinner) spinner.classList.add('hidden');
            btn.disabled = false;
        }
    }).fail(function(xhr) {
        Swal.fire('Error', 'No se pudieron cargar los detalles.', 'error');
        if (btn) {
            const icon = btn.querySelector('i');
            const spinner = btn.querySelector('.loading-spinner');
            if (icon) icon.classList.remove('hidden');
            if (spinner) spinner.classList.add('hidden');
            btn.disabled = false;
        }
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

            function closeDetailsModal() {
                $('#detailsModal').removeClass('show');
            }

        </script>

        <!--modales alertas prácticas-->

        <script>

function deshabilitarPracticaConActa(id) {
    $('#desactivarPracticaTitle').html(`Desactivar <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Práctica</span>`);
    $('#practica_id').val(id);
    $('#nro_acta_desactivar').val('');
    $('#fecha_acta_desactivar').val('');
    
    if (!window.quillDesactivar) {
        window.quillDesactivar = new Quill('#txt-editor-desactivar', {
            theme: 'snow',
            placeholder: 'Describa la respuesta para el estudiante.',
            modules: {
                toolbar: [
                    [{ 'header': 1}],
                    [{ 'header': 2}],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'color': [] }],
                    ['bold', 'italic', 'underline'],
                    ['clean']
                ]
            }
        });
    } else {
        window.quillDesactivar.root.innerHTML = '';
    }
    
    $('#desactivarPracticaModal').addClass('show');
}

function closeDesactivarPracticaModal() {
    $('#desactivarPracticaModal').removeClass('show');
    $('#desactivarPracticaForm')[0].reset();
    if (window.quillDesactivar) window.quillDesactivar.root.innerHTML = '';
}

// ========== ACTIVAR PRÁCTICA ==========
function habilitarPracticaConActa(id) {
    $('#activarPracticaTitle').html(`Activar <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Práctica</span>`);
    $('#practica_id_activar').val(id);
    $('#nro_acta_activar').val('');
    $('#fecha_acta_activar').val('');
    
    // Inicializar Quill para activar
    if (!window.quillActivar) {
        window.quillActivar = new Quill('#txt-editor-activar', {
            theme: 'snow',
            placeholder: 'Describa la respuesta para el estudiante.',
            modules: {
                toolbar: [
                    [{ 'header': 1}],
                    [{ 'header': 2}],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'color': [] }],
                    ['bold', 'italic', 'underline'],
                    ['clean']
                ]
            }
        });
    } else {
        window.quillActivar.root.innerHTML = '';
    }
    
    $('#activarPracticaModal').addClass('show');
}

function closeActivarPracticaModal() {
    $('#activarPracticaModal').removeClass('show');
    $('#activarPracticaForm')[0].reset();
    if (window.quillActivar) window.quillActivar.root.innerHTML = '';
}

function openWarningModal() {
    if (!window.quillWarning) {
        window.quillWarning = new Quill('#txt-editor-warning', {
            theme: 'snow',
            placeholder: 'Describa su reporte o inconveniente y déjenos saber sus recomendaciones.',
            modules: {
                toolbar: [
                    [{ 'header': 1}],
                    [{ 'header': 2}],
                    ['bold', 'italic', 'underline'],
                    [{ 'color': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean']
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


$(document).ready(function() {
    $('#desactivarPracticaForm').on('submit', function(e) {
        e.preventDefault();
        
        if (window.quillDesactivar) {
            $('#descripcion_desactivar').val(window.quillDesactivar.root.innerHTML);
        }
        
        const mensaje = $('#descripcion_desactivar').val();
        if (!mensaje || mensaje === '<p><br></p>') {
            $('#descripcion_desactivarError').text('Debe ingresar una justificación para deshabilitar la práctica');
            return;
        }
        
        const nroActa = $('#nro_acta_desactivar').val();
        if (!nroActa) {
            $('#nro_acta_desactivarError').text('Debe ingresar el número de acta');
            return;
        }
        
        const fechaActa = $('#fecha_acta_desactivar').val();
        if (!fechaActa) {
            $('#fecha_acta_desactivarError').text('Debe seleccionar la fecha del acta');
            return;
        }
        
        Swal.fire({
            heightAuto: false,
            title: 'Desactivar práctica',
            text: 'No podrá deshacer esta acción',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#C1D631',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Deshabilitar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const button = $('#desactivarPracticaForm').find('button[type="submit"]');
                const spinner = $('#loadingSpinner-desactivar');
                
                button.prop('disabled', true);
                if (spinner.length) spinner.removeClass('hidden');
                
                $.ajax({
                    url: '{{ route("practicas.deshabilitar_con_acta") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#practicasTable').DataTable().ajax.reload();
                        closeDesactivarPracticaModal();
                        showToast(response.success || 'Práctica deshabilitada exitosamente');
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON?.errors;
                        if (errors) {
                            for (let key in errors) {
                                $('#' + key + 'Error').text(errors[key][0]);
                            }
                            setTimeout(() => {
                                for (let key in errors) {
                                    $('#' + key + 'Error').text('');
                                }
                            }, 5000);
                        } else {
                            showToast(xhr.responseJSON?.message || 'Error al deshabilitar la práctica', 'error');
                        }
                    },
                    complete: function() {
                        button.prop('disabled', false);
                        if (spinner.length) spinner.addClass('hidden');
                    }
                });
            }
        });
    });

    // ========== HABILITAR PRÁCTICA ==========
    $('#activarPracticaForm').on('submit', function(e) {
        e.preventDefault();
        
        // Obtener el contenido del editor Quill
        if (window.quillActivar) {
            $('#descripcion_activar').val(window.quillActivar.root.innerHTML);
        }
        
        // Validar que el mensaje no esté vacío
        const mensaje = $('#descripcion_activar').val();
        if (!mensaje || mensaje === '<p><br></p>') {
            $('#descripcion_activarError').text('Debe ingresar una justificación para habilitar la práctica');
            return;
        }
        
        // Validar número de acta
        const nroActa = $('#nro_acta_activar').val();
        if (!nroActa) {
            $('#nro_acta_activarError').text('Debe ingresar el número de acta');
            return;
        }
        
        // Validar fecha del acta
        const fechaActa = $('#fecha_acta_activar').val();
        if (!fechaActa) {
            $('#fecha_acta_activarError').text('Debe seleccionar la fecha del acta');
            return;
        }
        
        // Mostrar alerta de confirmación
        Swal.fire({
            heightAuto: false,
            title: 'Habilitar práctica',
            text: 'No podrá deshacer esta acción',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#C1D631',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Habilitar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const button = $('#activarPracticaForm').find('button[type="submit"]');
                const spinner = $('#loadingSpinner-activar');
                
                button.prop('disabled', true);
                if (spinner.length) spinner.removeClass('hidden');
                
                $.ajax({
                    url: '{{ route("practicas.habilitar_con_acta") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#practicasTable').DataTable().ajax.reload();
                        closeActivarPracticaModal();
                        showToast(response.success || 'Práctica habilitada exitosamente');
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON?.errors;
                        if (errors) {
                            for (let key in errors) {
                                $('#' + key + 'Error').text(errors[key][0]);
                            }
                            setTimeout(() => {
                                for (let key in errors) {
                                    $('#' + key + 'Error').text('');
                                }
                            }, 5000);
                        } else {
                            showToast(xhr.responseJSON?.message || 'Error al habilitar la práctica', 'error');
                        }
                    },
                    complete: function() {
                        button.prop('disabled', false);
                        if (spinner.length) spinner.addClass('hidden');
                    }
                });
            }
        });
    });

    // ========== REPORTAR PROBLEMA ==========
    $('#warningForm').on('submit', function(e) {
        e.preventDefault();
        
        // Obtener el contenido del editor Quill
        if (window.quillWarning) {
            $('#mensaje_warning').val(window.quillWarning.root.innerHTML);
        }
        
        // Validar que el mensaje no esté vacío
        const mensaje = $('#mensaje_warning').val();
        if (!mensaje || mensaje === '<p><br></p>') {
            $('#mensaje_warningError').text('Debe ingresar una descripción del problema');
            return;
        }
        
        // Mostrar alerta de confirmación
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
                const button = $('#warningForm').find('button[type="submit"]');
                const spinner = $('#loadingSpinner-warning');
                
                button.prop('disabled', true);
                if (spinner.length) spinner.removeClass('hidden');
                
                $.ajax({
                    url: '{{ route("practicas.reportar_problema") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        closeWarningModal();
                        showToast(response.success || 'Reporte enviado exitosamente');
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON?.errors;
                        if (errors && errors.mensaje_warning) {
                            $('#mensaje_warningError').text(errors.mensaje_warning[0]);
                            setTimeout(() => {
                                $('#mensaje_warningError').text('');
                            }, 5000);
                        } else {
                            showToast(xhr.responseJSON?.message || 'Error al enviar el reporte', 'error');
                        }
                    },
                    complete: function() {
                        button.prop('disabled', false);
                        if (spinner.length) spinner.addClass('hidden');
                    }
                });
            }
        });
        });
    });

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
                }
            }
    </script>

    <script>
        // Reportes - Prácticas
function openReporteModal() {
    $('#reporteTitle').html(`Generar <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Reporte</span>`);

    $('#periodo_reporte').val('');
    $('#periodo_reporteError').text('');
    $('#reporteModal').addClass('show');
}

function closeReporteModal() {
    $('#reporteModal').removeClass('show');
}

// Submit del formulario de reporte
$(document).ready(function() {
    $('#reporteForm').on('submit', function(e) {
        e.preventDefault();
        
        // Limpiar errores
        $('#periodo_reporteError').text('');
        
        const periodo = $('#periodo_reporte').val();
        if (!periodo) {
            $('#periodo_reporteError').text('Debe seleccionar un periodo académico');
            return;
        }
        
        const button = $('#reporteSubmitButton');
        const spinner = $('#loadingSpinner-reporte');
        
        button.prop('disabled', true);
        spinner.removeClass('hidden');
        
        // Crear un FormData para enviar el formulario
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhrFields: {
                responseType: 'blob' // Importante para manejar la descarga de archivos
            },
            success: function(response, status, xhr) {
                // Crear un enlace para descargar el archivo
                const blob = new Blob([response], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                
                // Obtener el nombre del archivo del header Content-Disposition
                const contentDisposition = xhr.getResponseHeader('Content-Disposition');
                let filename = 'reporte_practicas.xlsx';
                if (contentDisposition) {
                    const matches = contentDisposition.match(/filename="?([^"]+)"?/);
                    if (matches && matches[1]) {
                        filename = matches[1];
                    }
                }
                
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
                
                closeReporteModal();
                showToast('Reporte generado correctamente', 'success');
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.periodo_reporte) {
                        $('#periodo_reporteError').text(errors.periodo_reporte[0]);
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    showToast(xhr.responseJSON.message, 'error');
                } else {
                    showToast('Error al generar el reporte', 'error');
                }
            },
            complete: function() {
                button.prop('disabled', false);
                spinner.addClass('hidden');
            }
        });
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

    <script>

        function showRoadmapSpinner(form) {
            const button = form.querySelector('button');
            const icon = button.querySelector('i');
            const spinner = button.querySelector('.loading-spinner');
            
            if (icon) icon.classList.add('hidden');
            if (spinner) spinner.classList.remove('hidden');
            button.disabled = true;
            
            return true; // Permite que el formulario se envíe
        }

    </script>

        <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    @endpush


</x-app-layout>