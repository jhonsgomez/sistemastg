@php
use Carbon\Carbon;

$fechaActual = Carbon::now()->format('Y-m-d');

$anio_inicio = 2025;
$anio_actual = Carbon::now()->year;
$mes_actual = Carbon::now()->month;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Proyectos de <span class="bg-uts-500 text-gray-800 font-bold me-2 px-2.5 py-0.5 rounded uppercase">Grado</span>
        </h2>
    </x-slot>

    @push('styles')
    <style>
        .btn-action {
            padding: 6px 12px;
            margin: 0 4px;
            transition: all 0.3s ease;
            border-radius: 0.375rem;
        }

        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            transition: opacity 0.3s ease;
        }

        .modal-close-btn-custom {
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

        .modal-close-btn-custom:hover {
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

        #createModal,
        #detailsModal,
        #replySolicitudModal,
        #desactivarProyectoModal,
        #activarProyectoModal,
        #calendarModal,
        #warningModal,
        #reporteModal {
            visibility: hidden;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
        }

        #createModal.show,
        #detailsModal.show,
        #replySolicitudModal.show,
        #desactivarProyectoModal.show,
        #activarProyectoModal.show,
        #calendarModal.show,
        #warningModal.show,
        #reporteModal.show {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5) !important;
            transition: background-color 0.3s ease;
        }

        .modal-content {
            max-width: 850px !important;
            width: 100% !important;
            padding: 2rem 3rem;
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
    </style>
    @endpush

    <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-b">
        <h2 class="text-2xl text-center font-bold text-gray-800">
            Proyectos de <span class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Grado</span>
        </h2>

        <div class="flex justify-center items-center space-x-2 buttons-container">
            <button type="button" id="warning" onclick="openWarningModal()"
                class="btn-action shadow bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded-lg relative">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <svg id="loadingSpinner-warningOpen" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                    <path
                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path
                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                    </path>
                </svg>
            </button>
            @if ($fechas)
            @if ($fechaActual >= $fechas['fecha_inicio_proyectos'] && $fechaActual <= $fechas['fecha_fin_proyectos'])
                <button type="button" id="calendar" onclick="openCalendarModal()"
                class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative"
                style="margin-right: 0.3rem !important">
                <i class="fa-regular fa-calendar"></i>
                <svg id="loadingSpinner-calendarOpen" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                    <path
                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path
                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                    </path>
                </svg>
                </button>
                @if (auth()->user()->hasRole(['estudiante']))
                    @can('create_proyecto_grado')
                    <button
                        onclick="openCreateModal()"
                        id="openCreateModalButton"
                        class="bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg flex items-center shadow">
                        <i class="fas fa-plus mr-2"></i>
                        <svg id="loadingSpinner-crear" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                            <path
                                d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                                d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                            </path>
                        </svg> Nuevo proyecto
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

    <div class="p-4">
        @if (auth()->user()->hasRole(['super_admin', 'admin', 'coordinador']))
        <div class="mb-3">
            <select id="filtroRoles"
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block focus:ring-uts-500 focus:border-uts-500">
                <option value="" selected>Seleccione un filtro</option>
                <option value="comite_filter">Pendientes Comité</option>
                <option value="director_filter">Pendientes Director</option>
                <option value="evaluador_filter">Pendientes Evaluador</option>
                <option value="propuestas_pendientes">Propuestas sin aprobar</option>
                <option value="informes_pendientes">Informes finales sin aprobar</option>
            </select>
        </div>
        @endif
        @can("list_proyectos_grado")
        <table id="proyectosTable" class="w-full">
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

    <div id="createModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeCreateModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeCreateModal()">
                        &times;
                    </button>
                    @if (auth()->user()->hasRole(['estudiante']))
                    <form id="proyectosForm" class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="formTitle"></p>
                        <p class="text-sm mb-2">En este formulario el estudiante describirá la información de los integrantes del nuevo proyecto y la modalidad.</p>
                        <p class="text-sm mb-6"><strong>NOTA: </strong>Si selecciona tres integrantes, el comite validará la información para poder dar paso a las siguientes fases.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-6 mb-6">
                            @foreach($campos as $campo)
                            <div>
                                @if ($campo->label != null && $campo->type != 'hidden' && $campo->type != 'checkbox')
                                <div class="flex items-center gap-2">
                                    <label for="{{ $campo->name }}" class="block font-medium text-sm text-gray-700">
                                        <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                        @if($campo->required)<span class="text-red-600 mr-1 text-lg">*</span>@endif{{ $campo->label }}
                                    </label>
                                    <div class="relative inline-block">
                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                            data-tooltip="tooltip-{{ $campo->name }}Edit"></i>
                                        <div id="tooltip-{{ $campo->name }}Edit"
                                            style="width: 10rem"
                                            class="hidden absolute z-10 max-w-[90vw] px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg left-1/2 -translate-x-1/2 bottom-full mb-2 sm:left-0 sm:translate-x-0">
                                            <p class="uppercase font-bold mb-2">Instrucciones:</p>
                                            {!! $campo->instructions ?? '' !!}
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @switch($campo->type)
                                @case('text')
                                @switch($campo->name)
                                @case('nivel')
                                <input type="text" name="{{ $campo->name }}" id="{{ $campo->name }}" value="{{ auth()->user()->nivel->nombre }}" class="bg-gray-200 border-gray-300 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500 cursor-default" readonly>
                                @break
                                @case('id_integrante_1')
                                <input type="text" name="{{ $campo->name }}" id="{{ $campo->name }}" value="{{ auth()->user()->name }}" class="bg-gray-200 border-gray-300 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500 cursor-default" readonly>
                                @break
                                @default
                                <input type="text" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                    placeholder="{{ $campo->placeholder ?? '' }}"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                @break
                                @endswitch
                                @break

                                @case('textarea')
                                <textarea name="{{ $campo->name }}" id="{{ $campo->name }}"
                                    placeholder="{{ $campo->placeholder ?? '' }}"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                    </textarea>
                                @break

                                @case('number')
                                <input type="number" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                    placeholder="{{ $campo->placeholder ?? '' }}"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                @break

                                @case('date')
                                <input type="date" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                    placeholder="{{ $campo->placeholder ?? '' }}"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                @break

                                @case('select')
                                <select name="{{ $campo->name }}" id="{{ $campo->name }}" lang="es"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                    <option value="" selected disabled>Selecciona una opción</option>
                                    @if ($campo->name == 'modalidad')
                                    @foreach ($modalidades as $modalidad)
                                    <option value="{{ $modalidad->id }}">{{ $modalidad->nombre }}</option>
                                    @endforeach
                                    @elseif ($campo->name == 'id_integrante_2' || $campo->name == 'id_integrante_3')
                                    @foreach ($estudiantes as $estudiante)
                                    <option value="{{ $estudiante->id }}">{{ $estudiante->nro_documento }} | {{ $estudiante->name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                @break

                                @case('file')
                                <input type="file" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                @break

                                @case('checkbox')
                                <input type="checkbox" name="{{ $campo->name }}" id="{{ $campo->name }}" class="border-gray-300 rounded-md shadow-sm mt-1 focus:ring-uts-500 focus:border-uts-500">
                                @break

                                @case('hidden')
                                <input type="hidden" name="{{ $campo->name }}" id="{{ $campo->name }}" value="{{ $campo->placeholder ?? '' }}">
                                @break

                                @default
                                <input type="text" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                    placeholder="{{ $campo->placeholder ?? '' }}"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                @endswitch

                                <span id="{{ $campo->name }}Error" class="text-red-500 text-sm"></span>
                            </div>
                            @endforeach
                            <div class="col-span-1 sm:col-span-2 lg:col-span-2 xl:col-span-2">
                                <p class="text-red-600 text-sm">
                                    <i class="fa-solid fa-circle-info mr-1"></i>
                                    Si selecciona un tercer integrante, el comité tendrá que evaluar la complejidad de la propuesta de los estudiantes y posteriormente aprobará o rechazará la solicitud.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeCreateModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                id="guardarModalButton"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-guardar" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="detailsModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeDetailsModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeDetailsModal()">
                        &times;
                    </button>
                    <form class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="detailsTitle"></p>
                        <div id="content-details"></div>
                        <div class="flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeDetailsModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cerrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="replySolicitudModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeReplySolicitudModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeReplySolicitudModal()">
                        &times;
                    </button>
                    <form id="replySolicitudForm" class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="replyTitle"></p>
                        <input type="hidden" name="solicitud_id" id="solicitud_id">
                        <div class="mb-4">
                            <label for="estado" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Estado de la solicitud:
                            </label>
                            <select name="estado" id="estado"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                <option value="" selected disabled>Selecciona una opción</option>
                                <option value="Aprobada">Aprobada</option>
                                <option value="Rechazada">Rechazada</option>
                            </select>
                            <span id="estadoError" class="text-red-500 text-sm"></span>
                        </div>
                        <div class="mb-4">
                            <label for="mensaje" class="block font-medium text-sm text-gray-700" style="margin-bottom: 5px;">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Respuesta de la solicitud:
                            </label>
                            <div id="txt-editor" class="shadow txt-editor-quill"></div>
                            <textarea name="mensaje" id="mensaje" class="hidden"></textarea>
                            <span id="mensajeError" class="text-red-500 text-sm"></span>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeReplySolicitudModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cerrar
                            </button>
                            <button
                                type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-replyProyectos" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
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

    <div id="desactivarProyectoModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeDesactivarProyectoModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeDesactivarProyectoModal()">
                        &times;
                    </button>
                    <form id="desactivarProyectoForm" class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="desactivarProyectoTitle"></p>
                        <p class="font-medium text-sm text-gray-700 mb-6">En este formulario el comité de trabajos de grado del programa desactivará el proyecto en curso.</p>
                        <input type="hidden" name="proyecto_id" id="proyecto_id">
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
                            <button
                                type="button"
                                onclick="closeDesactivarProyectoModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cerrar
                            </button>
                            <button
                                type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-desactivarProyecto" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

    <div id="activarProyectoModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeActivarProyectoModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeActivarProyectoModal()">
                        &times;
                    </button>
                    <form id="activarProyectoForm" class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="activarProyectoTitle"></p>
                        <p class="font-medium text-sm text-gray-700 mb-6">En este formulario el comité de trabajos de grado del programa podrá habilitar el proyecto deshabilitado.</p>
                        <input type="hidden" name="proyecto_id_activar" id="proyecto_id_activar">
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
                                Descripción de la respuesta:
                            </label>
                            <div id="txt-editor-activar" class="shadow txt-editor-quill"></div>
                            <textarea name="descripcion_activar" id="descripcion_activar" class="hidden"></textarea>
                            <span id="descripcion_activarError" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeActivarProyectoModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cerrar
                            </button>
                            <button
                                type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-activarProyecto" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

    <div id="warningModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeWarningModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeWarningModal()">
                        &times;
                    </button>
                    <form id="warningForm" class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="warningTitle"></p>
                        <div class="mb-4">
                            <label for="mensaje_warning" class="block font-medium text-md text-gray-700 mb-4">
                                En caso de presentar problemas con el sistema, por favor describa su reporte y envíelo:
                            </label>
                            <div id="txt-editor-warning" class="shadow txt-editor-quill"></div>
                            <textarea name="mensaje_warning" id="mensaje_warning" class="hidden"></textarea>
                            <span id="mensaje_warningError" class="text-red-500 text-sm"></span>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeWarningModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                id="warningModalButton"
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
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeCalendarModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeCalendarModal()">
                        &times;
                    </button>
                    <div class="p-6 mt-2">
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="calendarTitle"></p>
                        <p class="font-medium text-md text-gray-700 mb-2">Aquí podrá visualizar algunas fechas importantes del periodo en curso.</p>

                        <div class="overflow-x-auto mb-8">
                            <table class="min-w-full border-collapse border border-gray-300 bg-gray-100 shadow-md rounded-lg">
                                <thead>
                                    <tr class="bg-gray-200 text-gray-700">
                                        <th class="px-4 py-3 text-left font-semibold border border-gray-300 uppercase">Descripción</th>
                                        <th class="px-4 py-3 text-left font-semibold border border-gray-300 uppercase">Fechas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white border border-gray-300">
                                        <td class="px-4 py-3 border border-gray-300">Propuestas en banco de ideas</td>
                                        <td class="px-4 py-3 border border-gray-300">Desde el <span class="font-semibold" style="font-size: 0.9rem;">{{ $fechas['fecha_inicio_banco'] }}</span> hasta el <span class="font-semibold" style="font-size: 0.9rem;">{{ $fechas['fecha_fin_banco'] }}</span></td>
                                    </tr>
                                    <tr class="bg-gray-50 border border-gray-300">
                                        <td class="px-4 py-3 border border-gray-300">Propuesta de proyectos de grado</td>
                                        <td class="px-4 py-3 border border-gray-300">Desde el <span class="font-semibold" style="font-size: 0.9rem;">{{ $fechas['fecha_inicio_proyectos'] }}</span> hasta el <span class="font-semibold" style="font-size: 0.9rem;">{{ $fechas['fecha_fin_proyectos'] }}</span></td>
                                    </tr>
                                    <tr class="bg-white border border-gray-300">
                                        <td class="px-4 py-3 border border-gray-300">Fecha máxima para aprobación de propuestas</td>
                                        <td class="px-4 py-3 border border-gray-300">Hasta el <span class="font-semibold" style="font-size: 0.9rem;">{{ $fechas['fecha_aprobacion_propuesta'] }}</span></td>
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
                    <form action="{{ route('proyectos.reporte') }}" method="POST" id="reporteForm" class="p-6 mt-2">
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
    <script src="{{ asset('js/options/warning.js') }}"></script>
    <script src="{{ asset('js/options/calendar.js') }}"></script>
    <script src="{{ asset('js/options/reporte.js') }}"></script>
    <script>
        $(document).ready(function() {
            initializeDataTable('#proyectosTable', '{{ route("proyectos.data") }}',
                [{
                        data: 'formatted_id',
                        name: 'formatted_id',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'descripcion',
                        name: 'descripcion',
                        orderable: false,
                        sortable: false,
                    },
                    {
                        data: 'estado',
                        name: 'estado',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            );

            $('#id_integrante_2').select2({
                placeholder: 'Escribe el documento del integrante',
                allowClear: true,
                width: '100%',
                minimumInputLength: 5
            });

            $('#id_integrante_3').select2({
                placeholder: 'Escribe el documento del integrante',
                allowClear: true,
                width: '100%',
                minimumInputLength: 5
            });
        });

        function openCreateModal() {
            var añoActual = new Date().getFullYear();
            var mesActual = new Date().getMonth() + 1;
            var numero = mesActual <= 6 ? 1 : 2;
            var periodo_academico = añoActual + '-' + numero;

            $('#formTitle').html(`Iniciar nuevo <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Proyecto</span>`);

            $('#modalidad').val('');
            $('#id_integrante_2').val('').trigger('change');
            $('#id_integrante_3').val('').trigger('change');
            $('#periodo').val(periodo_academico);

            $('#nivelError').text('');
            $('#modalidadError').text('');
            $('#id_integrante_1Error').text('');
            $('#id_integrante_2Error').text('');
            $('#id_integrante_3Error').text('');
            $('#periodoError').text('');

            $('#createModal').addClass('show');
        }

        $('#proyectosForm').on('submit', function(e) {
            e.preventDefault();

            const button = document.getElementById(`guardarModalButton`);
            const loadingSpinner = document.getElementById(`loadingSpinner-guardar`);

            const url = "{{  route('proyectos.store') }}";
            const method = 'POST';

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
                    loaderGeneral.classList.replace('hidden', 'flex');
                    loadingSpinner.classList.remove('hidden');

                    $.ajax({
                        url: url,
                        method: method,
                        data: $(this).serialize(),
                        success: function(response) {
                            $('#proyectosTable').DataTable().ajax.reload();
                            closeCreateModal();
                            showToast('Solicitud enviada, tendrá respuesta en los próximos 5 días habiles');
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON.errors;

                            $('#modalidadError').text(errors?.modalidad?.[0] || '');
                            $('#nivelError').text(errors?.nivel?.[0] || '');
                            $('#id_integrante_1Error').text(errors?.id_integrante_1?.[0] || '');
                            $('#id_integrante_2Error').text(errors?.id_integrante_2?.[0] || '');
                            $('#id_integrante_3Error').text(errors?.id_integrante_3?.[0] || '');
                            $('#periodoError').text(errors?.periodo?.[0] || '');
                        },
                        complete: function() {
                            loaderGeneral.classList.replace('flex', 'hidden');
                            loadingSpinner.classList.add('hidden');
                        }
                    });
                }
            });
        });

        function openRoadMap(event, id, action) {
            event.preventDefault();

            document.querySelectorAll('.dtr-hidden [id]').forEach(el => el.id += '-hidden');

            const form = document.getElementById("roadmapForm-" + id);
            const button = document.getElementById("roadMapButton-" + id);
            const loadingSpinner = document.getElementById("loadingSpinnerRoadMap-" + id);

            button.querySelector("i").classList.add("hidden");
            loadingSpinner.classList.remove("hidden");

            form.action = action;

            setTimeout(() => {
                loadingSpinner.classList.add("hidden");
                button.querySelector("i").classList.remove("hidden");
                document.querySelectorAll('.dtr-hidden [id]').forEach(el => el.id = el.id.replace('-hidden', ''));
                form.submit();
            }, 1800);
        }

        function closeCreateModal() {
            TooltipManager.closeTooltips();
            $('#createModal').removeClass('show');
        }

        async function openDetailsModal(id) {
            document.querySelectorAll('.dtr-hidden [id]').forEach(el => el.id += '-hidden');

            const button = document.getElementById(`openModalButton-${id}`);
            const loadingSpinner = document.getElementById(`loadingSpinner-${id}`);

            button.querySelector('i').classList.add('hidden');
            loadingSpinner.classList.remove('hidden');

            $('#detailsTitle').html(`Detalles de la <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Solicitud</span>`);

            let detailsHtml = ``;
            let nivel, modalidad, periodo, integrante_1, integrante_2, integrante_3;

            let titulo = await getTitulo(id);
            let director = await getDocentes(id, 'director');
            let codirector = await getDocentes(id, 'codirector');
            let evaluador = await getDocentes(id, 'evaluador');

            let fechas_propuesta = await getFechasEnvioPropuesta(id);
            let fechas_informe = await getFechasEnvioInforme(id);

            const route = "{{ route('proyectos.campos', ['id' => ':id']) }}".replace(':id', id);

            $.get(route, async function(data) {
                for (let item of data.campos) {
                    switch (item.campo.name) {
                        case "nivel":
                            nivel = await getNivel(item.valor);
                            break;
                        case "modalidad":
                            modalidad = await getModalidad(item.valor);
                            break;
                        case "periodo":
                            periodo = item.valor;
                            break;
                        case "id_integrante_1":
                            integrante_1 = await getUser(item.valor);
                            break;
                        case "id_integrante_2":
                            integrante_2 = await getUser(item.valor);
                            break;
                        case "id_integrante_3":
                            integrante_3 = await getUser(item.valor);
                            break;
                        default:

                    }
                }

                detailsHtml += `
                    <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                        <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Título del proyecto:</p>
                        <span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${titulo}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                        <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Docentes:</p>
                        <span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">
                            <span><b>Director:</b> ${(director && Object.keys(director).length > 0) ? capitalizeName(director.name) : 'No asignado'}</span><br>
                            @if(auth()->user()->hasRole(['super_admin', 'admin', 'coordinador', 'director', 'evaluador']))
                                <span><b>Evaluador:</b> ${(evaluador && Object.keys(evaluador).length > 0) ? capitalizeName(evaluador.name) : 'No asignado'}</span><br>
                            @endif
                            <span><b>Codirector:</b> ${(codirector && Object.keys(codirector).length > 0) ? capitalizeName(codirector.name) : 'No asignado'}</span>
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                        <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Nivel académico:</p>
                        <span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${nivel.nombre}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                        <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Modalidad:</p>
                        <span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${modalidad.nombre}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                        <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Periodo académico:</p>
                        <span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${periodo}</span>
                    </div>
                `;

                if (integrante_1) {
                    detailsHtml += `
                        <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                            <p class="font-semibold text-gray-700 w-1/3 min-w-[100px]">Integrante:</p>
                            <span class="text-gray-800 flex-1 xl:ml-2 lg:ml-2 sm:ml-0">
                                <span>${integrante_1.name.toUpperCase()}</span><br>
                                <span>${integrante_1.tipo_documento.tag} ${integrante_1.nro_documento}</span><br>
                                <span class="underline text-blue-600">${integrante_1.email.toLowerCase()}</span><br>
                                <span>${integrante_1.nro_celular}</span><br>
                            </span>
                        </div>
                    `;
                }

                if (integrante_2) {
                    detailsHtml += `
                        <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                            <p class="font-semibold text-gray-700 w-1/3 min-w-[100px]">Integrante:</p>
                            <span class="text-gray-800 flex-1 xl:ml-2 lg:ml-2 sm:ml-0">
                                <span>${integrante_2.name.toUpperCase()}</span><br>
                                <span>${integrante_2.tipo_documento.tag} ${integrante_2.nro_documento}</span><br>
                                <span class="underline text-blue-600">${integrante_2.email.toLowerCase()}</span><br>
                                <span>${integrante_2.nro_celular}</span><br>
                            </span>
                        </div>
                    `;
                }

                if (integrante_3) {
                    detailsHtml += `
                        <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                            <p class="font-semibold text-gray-700 w-1/3 min-w-[100px]">Integrante:</p>
                            <span class="text-gray-800 flex-1 xl:ml-2 lg:ml-2 sm:ml-0">
                                <span>${integrante_3.name.toUpperCase()}</span><br>
                                <span>${integrante_3.tipo_documento.tag} ${integrante_3.nro_documento}</span><br>
                                <span class="underline text-blue-600">${integrante_3.email.toLowerCase()}</span><br>
                                <span>${integrante_3.nro_celular}</span><br>
                            </span>
                        </div>
                    `;
                }

                if (fechas_propuesta) {
                    detailsHtml += `
                        <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                            <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Fechas propuesta:</p>
                            <span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">
                                <span><b>Envío de propuesta:</b> ${(fechas_propuesta.envio_estudiante && fechas_propuesta.envio_estudiante !== '') ? formatDate(fechas_propuesta.envio_estudiante) : 'No disponible'}</span><br>
                                <span><b>Revisión director:</b> ${(fechas_propuesta.revision_director && fechas_propuesta.revision_director !== '') ? formatDate(fechas_propuesta.revision_director) : 'No disponible'}</span><br>
                                <span><b>Revisión evaluador:</b> ${(fechas_propuesta.revision_evaluador && fechas_propuesta.revision_evaluador !== '') ? formatDate(fechas_propuesta.revision_evaluador) : 'No disponible'}</span>
                            </span>
                        </div>
                    `;
                }

                if (fechas_informe) {
                    detailsHtml += `
                        <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                            <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Fechas informe:</p>
                            <span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">
                                <span><b>Envío de informe:</b> ${(fechas_informe.envio_estudiante && fechas_informe.envio_estudiante !== '') ? formatDate(fechas_informe.envio_estudiante) : 'No disponible'}</span><br>
                                <span><b>Revisión director:</b> ${(fechas_informe.revision_director && fechas_informe.revision_director !== '') ? formatDate(fechas_informe.revision_director) : 'No disponible'}</span><br>
                                <span><b>Revisión evaluador:</b> ${(fechas_informe.revision_evaluador && fechas_informe.revision_evaluador !== '') ? formatDate(fechas_informe.revision_evaluador) : 'No disponible'}</span>
                            </span>
                        </div>
                    `;
                }

                $('#content-details').html(detailsHtml);
                $('#detailsModal').addClass('show');

                loadingSpinner.classList.add('hidden');
                button.querySelector('i').classList.remove('hidden');
            });

            document.querySelectorAll('.dtr-hidden [id]').forEach(el => el.id = el.id.replace('-hidden', ''));
        }

        function capitalizeName(name) {
            return name
                .toLowerCase()
                .split(/\s+/)
                .map(word => 
                    word.charAt(0).toLocaleUpperCase() + word.slice(1)
                )
                .join(' ');
        }

        function formatDate(date) {
            return new Date(date).toLocaleString('es-ES', {
                timeZone: 'America/Bogota',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
        }

        function getFechasEnvioPropuesta(id) {
            const route = "{{ route('proyectos.fechas-propuesta', ['id' => ':id']) }}".replace(':id', id);

            return new Promise((resolve, reject) => {
                $.get(route, function(data) {
                    if (data) {
                        resolve(data);
                    } else {
                        reject("No se encontraron las fechas");
                    }
                });
            });
        }

        function getFechasEnvioInforme(id) {
            const route = "{{ route('proyectos.fechas-informe', ['id' => ':id']) }}".replace(':id', id);

            return new Promise((resolve, reject) => {
                $.get(route, function(data) {
                    if (data) {
                        resolve(data);
                    } else {
                        reject("No se encontraron las fechas");
                    }
                });
            });
        }

        function getUser(id) {
            const route = "{{ route('users.edit', ['id' => ':id']) }}".replace(':id', id);

            return new Promise((resolve, reject) => {
                $.get(route, function(data) {
                    if (data) {
                        resolve(data);
                    } else {
                        reject("No se encontró el usuario");
                    }
                });
            });
        }

        function getTitulo(id) {
            const route = "{{ route('proyectos.titulo', ['id' => ':id']) }}".replace(':id', id);

            return new Promise((resolve, reject) => {
                $.get(route, function(data) {
                    if (data) {
                        resolve(data);
                    } else {
                        reject("No se encontró el título");
                    }
                });
            });
        }

        function getDocentes(id, tipo) {
            return new Promise((resolve, reject) => {
                if (tipo === 'director') {
                    const route = "{{ route('proyectos.director', ['id' => ':id']) }}".replace(':id', id);

                    $.get(route, function(data) {
                        if (data) {
                            resolve(data);
                        } else {
                            reject("No se encontró el director");
                        }
                    });
                } else if (tipo === 'codirector') {
                    const route = "{{ route('proyectos.codirector', ['id' => ':id']) }}".replace(':id', id);

                    $.get(route, function(data) {
                        if (data) {
                            resolve(data);
                        } else {
                            reject("No se encontró el codirector");
                        }
                    });
                } else if (tipo === 'evaluador') {
                    const route = "{{ route('proyectos.evaluador', ['id' => ':id']) }}".replace(':id', id);

                    $.get(route, function(data) {
                        if (data) {
                            resolve(data);
                        } else {
                            reject("No se encontró el evaluador");
                        }
                    });
                } else {
                    reject("Tipo de docente no válido");
                }
            });
        }

        function getModalidad(id) {
            const route = "{{ route('modalidades.info', ['id' => ':id']) }}".replace(':id', id);

            return new Promise((resolve, reject) => {
                $.get(route, function(data) {
                    if (data) {
                        resolve(data);
                    } else {
                        reject("No se encontró la modalidad");
                    }
                });
            });
        }

        function getNivel(id) {
            const route = "{{ route('niveles.info', ['id' => ':id']) }}".replace(':id', id);

            return new Promise((resolve, reject) => {
                $.get(route, function(data) {
                    if (data) {
                        resolve(data);
                    } else {
                        reject("No se encontró el nivel");
                    }
                });
            });
        }

        function closeDetailsModal() {
            $('#detailsModal').removeClass('show');
        }

        function openReplySolicitudModal(id) {
            initQuillEditor(undefined, "Ingrese el mensaje de respuesta indicado detalles al destinatario.", 'txt-editor', 'mensaje');

            $('#replyTitle').html(`Responder <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Solicitud</span>`);

            $('#solicitud_id').val(id);
            $('#estado').val('');
            $('#mensaje').val('');

            $('#estadoError').text('');
            $('#mensajeError').text('');

            $('#replySolicitudModal').addClass('show');
        }

        $('#replySolicitudForm').on('submit', function(e) {
            e.preventDefault();

            const loadingSpinner = document.getElementById(`loadingSpinner-replyProyectos`);

            const url = "{{  route('proyectos.responder') }}";
            const method = 'POST';

            Swal.fire({
                heightAuto: false,
                title: '¿Está seguro?',
                text: "Esta acción no se podrá deshacer",
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
                        data: $(this).serialize(),
                        success: function(response) {
                            $('#proyectosTable').DataTable().ajax.reload();
                            closeReplySolicitudModal();
                            showToast('Respuesta enviada exitosamente');
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON.errors;
                            $('#estadoError').text(errors?.estado?.[0] || '');
                            $('#mensajeError').text(errors?.mensaje?.[0] || '');
                        },
                        complete: function() {
                            campo.value = '';
                            quill.root.innerHTML = '';
                            loaderGeneral.classList.replace('flex', 'hidden');
                            loadingSpinner.classList.add('hidden');
                        }
                    });
                }
            });
        });

        function closeReplySolicitudModal() {
            $('#replySolicitudModal').removeClass('show');
        }

        function openDesactivarProyectoModal(id) {
            initQuillEditor(undefined, "Describa la respuesta para el estudiante", 'txt-editor-desactivar', 'descripcion_desactivar');

            $('#desactivarProyectoTitle').html(`Desactivar <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Proyecto</span>`);

            $('#proyecto_id').val(id);
            $('#nro_acta_desactivar').val('');
            $('#fecha_acta_desactivar').val('');
            $('#descripcion_desactivar').val('');

            $('#nro_acta_desactivarError').text('');
            $('#fecha_acta_desactivarError').text('');
            $('#descripcion_desactivarError').text('');
        
            $('#desactivarProyectoModal').addClass('show');
        }

        $('#desactivarProyectoForm').on('submit', function(e) {
            event.preventDefault();

            const loadingSpinner = document.getElementById(`loadingSpinner-desactivarProyecto`);

            const url = "{{  route('proyectos.deshabilitar') }}";
            const method = 'POST';

            const formData = new FormData(this);

            Swal.fire({
                heightAuto: false,
                title: 'Desactivar proyecto',
                text: "No podrá deshacer esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#C1D631',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Deshabilitar',
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
                        success: function(response) {
                            $('#proyectosTable').DataTable().ajax.reload();
                            closeDesactivarProyectoModal();
                            showToast('Proyecto deshabilitado correctamente.');
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON?.errors;

                            $('#proyecto_idError').text(errors?.proyecto_id?.[0] || '');
                            $('#nro_acta_desactivarError').text(errors?.nro_acta_desactivar?.[0] || '');
                            $('#fecha_acta_desactivarError').text(errors?.fecha_acta_desactivar?.[0] || '');
                            $('#descripcion_desactivarError').text(errors?.descripcion_desactivar?.[0] || '');
                        },
                        complete: function() {
                            campo.value = '';
                            quill.root.innerHTML = '';
                            loaderGeneral.classList.replace('flex', 'hidden');
                            loadingSpinner.classList.add('hidden');
                        }
                    });
                }
            });
        });

        function closeDesactivarProyectoModal() {
            $('#desactivarProyectoModal').removeClass('show');
        }

        function openActivarProyectoModal(id) {
            initQuillEditor(undefined, "Describa la respuesta para el estudiante", 'txt-editor-activar', 'descripcion_activar');
            
            $('#activarProyectoTitle').html(`Habilitar <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Proyecto</span>`);

            $('#proyecto_id_activar').val(id);
            $('#nro_acta_activar').val('');
            $('#fecha_acta_activar').val('');
            $('#descripcion_activar').val('');

            $('#proyecto_id_activarError').text('');
            $('#nro_acta_activarError').text('');
            $('#fecha_acta_activarError').text('');
            $('#descripcion_activarError').text('');

            $('#activarProyectoModal').addClass('show');
        }

        $('#activarProyectoForm').on('submit', function(e) {
            event.preventDefault();

            const loadingSpinner = document.getElementById(`loadingSpinner-activarProyecto`);

            const url = "{{  route('proyectos.habilitar') }}";
            const method = 'POST';

            const formData = new FormData(this);

            Swal.fire({
                heightAuto: false,
                title: 'Habilitar proyecto',
                text: "No podrá deshacer esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#C1D631',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Habilitar',
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
                        success: function(response) {
                            $('#proyectosTable').DataTable().ajax.reload();
                            closeActivarProyectoModal();
                            showToast('Proyecto habilitado correctamente.');
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON?.errors;

                            $('#proyecto_id_activarError').text(errors?.proyecto_id_activar?.[0] || '');
                            $('#nro_acta_activarError').text(errors?.nro_acta_activar?.[0] || '');
                            $('#fecha_acta_activarError').text(errors?.fecha_acta_activar?.[0] || '');
                            $('#descripcion_activarError').text(errors?.descripcion_activar?.[0] || '');
                        },
                        complete: function() {
                            campo.value = '';
                            quill.root.innerHTML = '';
                            loaderGeneral.classList.replace('flex', 'hidden');
                            loadingSpinner.classList.add('hidden');
                        }
                    });
                }
            });
        });

        function closeActivarProyectoModal() {
            $('#activarProyectoModal').removeClass('show');
        }
    </script>
    @endpush
</x-app-layout>