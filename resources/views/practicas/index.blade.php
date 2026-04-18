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
            .btn-action {
                padding: 6px 12px;
                margin: 0 2px;
                transition: all 0.3s ease;
                border-radius: 0.375rem;
            }

            .modal-overlay {
                position: static !important;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                transition: opacity 0.3s ease;
                z-index: 999;
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

            .transition {
                transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
            }

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
        </style>
    @endpush

    <!--Dasboard-->
    <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-b">
        <h2 class="text-2xl text-center font-bold text-gray-800">
            Mis Prácticas <span
                class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Empresariales</span>
        </h2>


        <!-- ESTE ES EL ICONO ROJO -->
        <div class="flex justify-center items-center space-x-2 buttons-container">
            <button type="button" id="warning" onclick="openWarningModal()"
                class="btn-action shadow bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded-lg relative">
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
            <!--- AQUI SOLO VA LA VARIABLE $fechas--->
            <button type="button" id="calendar" onclick="openCalendarModal()"
                class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative"
                style="margin-right: 0.3rem !important">
                <i class="fa-regular fa-calendar"></i>
                <svg id="loadingSpinner-calendarOpen" style="margin: 4px 1px"
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
                        </svg> Solicitar Practicas
                    </button>
                @endcan
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
        @can('list_proyectos_grado')
            {{-- Puedes usar el mismo permiso o crear uno nuevo --}}
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

    <div id="detailsModal" class="fixed z-50 inset-0 overflow-y-auto hidden">
        <div class="modal-overlay absolute inset-0"
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;"
            onclick="closeDetailsModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeDetailsModal()">&times;</button>
                    <div class="p-6 mt-2">
                        <p class="text-2xl font-bold" id="detailsTitle"></p>
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

    </div>
    <div id="createModal" class="hidden fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0"
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;"
            onclick="closeCreateModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative"
                    onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeCreateModal()">
                        &times;
                    </button>
                    @if (auth()->user()->hasRole(['estudiante']))
                        <form id="practicasForm" class="p-6 mt-2" method="POST" enctype="multipart/form-data">
                            @csrf
                            <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="formTitle"></p>
                            <p class="text-sm mb-2">En este formulario el estudiante registrará la información
                                necesaria para la solicitud de prácticas empresariales.</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-6 mb-6 ">
                                @foreach ($campos as $campo)
                                    <div class="{{ $campo->name == 'hoja_vida' ? 'col-span-1 sm:col-span-2' : '' }}">

                                        @if ($campo->label != null && $campo->type != 'hidden')
                                            <div class="flex items-center gap-2">
                                                <label for="{{ $campo->name }}"
                                                    class="block font-medium text-sm text-gray-700">

                                                    <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>

                                                    @if ($campo->required)
                                                        <span class="text-red-600 mr-1 text-lg">*</span>
                                                    @endif

                                                    {{ $campo->label }}
                                                </label>

                                                @if (!empty($campo->instructions))
                                                    <div class="relative inline-block">
                                                        <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                                            data-tooltip="tooltip-{{ $campo->name }}"></i>

                                                        <div id="tooltip-{{ $campo->name }}"
                                                            class="hidden absolute z-10 px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg">
                                                            {!! $campo->instructions !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        @switch($campo->type)

                                            {{-- ================= TEXT ================= --}}
                                            @case('text')
                                                @switch($campo->name)
                                                    @case('nombre_completo')
                                                        <input type="text" name="{{ $campo->name }}"
                                                            value="{{ auth()->user()->name }}"
                                                            class="bg-gray-200 border-gray-300 rounded-md mt-1 block w-full cursor-default"
                                                            readonly>
                                                    @break

                                                    @case('correo')
                                                        <input type="text" name="{{ $campo->name }}"
                                                            value="{{ auth()->user()->email }}"
                                                            class="bg-gray-200 border-gray-300 rounded-md mt-1 block w-full cursor-default"
                                                            readonly>
                                                    @break

                                                    @case('nivel')
                                                        <input type="text" name="{{ $campo->name }}"
                                                            value="{{ auth()->user()->nivel->nombre ?? '' }}"
                                                            class="bg-gray-200 border-gray-300 rounded-md mt-1 block w-full cursor-default"
                                                            readonly>
                                                    @break

                                                    @default
                                                        <input type="text" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                                            placeholder="{{ $campo->placeholder ?? '' }}"
                                                            class="border-gray-300 rounded-md mt-1 block w-full">
                                                    @break
                                                @endswitch
                                            @break

                                            {{-- ================= TEXTAREA ================= --}}
                                            @case('textarea')
                                                <textarea name="{{ $campo->name }}" id="{{ $campo->name }}" class="border-gray-300 rounded-md mt-1 block w-full">{{ old($campo->name) }}</textarea>
                                            @break

                                            {{-- ================= NUMBER ================= --}}
                                            @case('number')
                                                @switch($campo->name)
                                                    @case('documento')
                                                        <input type="number" name="{{ $campo->name }}"
                                                            value="{{ auth()->user()->nro_documento ?? '' }}"
                                                            class="bg-gray-200 border-gray-300 rounded-md mt-1 block w-full cursor-default"
                                                            readonly>
                                                    @break

                                                    @case('celular')
                                                        <input type="number" name="{{ $campo->name }}"
                                                            value="{{ auth()->user()->nro_celular ?? '' }}"
                                                            class="bg-gray-200 border-gray-300 rounded-md mt-1 block w-full cursor-default"
                                                            readonly>
                                                    @break
                                                @endswitch
                                            @break

                                            {{-- ================= DATE ================= --}}
                                            @case('date')
                                                <input type="date" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                                    class="border-gray-300 rounded-md mt-1 block w-full">
                                            @break

                                            {{-- ================= FILE ================= --}}
                                            @case('file')
                                                @if ($campo->name == 'hoja_vida')
                                                    <div class="col-span-1 sm:col-span-2">

                                                        <div id="hojaVidaContainer" style="display:none;"
                                                            class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border-2 border-gray-300 gap-3 grid border-dashed">
                                                            <div class="grid gap-1">
                                                                <i
                                                                    class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                                                <h2 class="text-center text-gray-400 text-xs leading-4">
                                                                    Solo archivos de PDF de máximo
                                                                    {{ config('custom.peso_maximo_propuesta') }}MB
                                                                </h2>
                                                            </div>

                                                            <div class="grid gap-2">
                                                                <h4 class="text-center text-gray-900 text-sm font-medium">
                                                                    Arrastra o carga tus archivos aquí
                                                                </h4>

                                                                <div class="flex items-center justify-center">
                                                                    <input type="file" name="{{ $campo->name }}"
                                                                        id="{{ $campo->name }}"
                                                                        class="absolute inset-0 opacity-0 cursor-pointer"
                                                                        accept=".pdf" />

                                                                    <div
                                                                        class="flex w-28 h-9 bg-uts-500 rounded-full shadow text-white text-sm font-semibold items-center justify-center cursor-pointer">
                                                                        Cargar
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <span id="{{ $campo->name }}Error"
                                                            class="text-red-500 text-sm"></span>
                                                        <ul id="file-list-fase0"
                                                            class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                                                        <span id="files-size-fase0" class="text-gray-800 text-sm"></span>

                                                    </div>
                                                @else
                                                    <input type="file" name="{{ $campo->name }}"
                                                        id="{{ $campo->name }}"
                                                        class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf" />
                                                @endif
                                            @break

                                            {{-- ================= CHECKBOX ================= --}}
                                            @case('checkbox')
                                                <div class="mt-2">

                                                    <div class="flex items-center gap-4">
                                                        <label class="flex items-center gap-1">
                                                            <input type="radio" name="{{ $campo->name }}" value="1"
                                                                class="radio-verde" @checked(($data['tiene_empresa'] ?? null) == 1)
                                                                onchange="toggleHojaVida()">
                                                            Sí
                                                        </label>

                                                        <label class="flex items-center gap-1">
                                                            <input type="radio" name="{{ $campo->name }}" value="0"
                                                                class="radio-verde" @checked(($data['tiene_empresa'] ?? null) == 0)
                                                                onchange="toggleHojaVida()">
                                                            No
                                                        </label>

                                                    </div>
                                                </div>
                                            @break

                                            {{-- ================= HIDDEN ================= --}}
                                            @case('hidden')
                                                <input type="hidden" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                                    value="{{ $campo->placeholder ?? '' }}">
                                            @break

                                            {{-- ================= DEFAULT ================= --}}

                                            @default
                                                <input type="text" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                                    placeholder="{{ $campo->placeholder ?? '' }}"
                                                    class="border-gray-300 rounded-md mt-1 block w-full">
                                        @endswitch


                                        <span id="{{ $campo->name }}Error" class="text-red-500 text-sm"></span>
                                    </div>
                                @endforeach
                            </div>


                            <p class="text-sm mb-6"><strong>NOTA: </strong>El estudiante debe tener aprobado el 90% de
                                los créditos (Tecnología: 97 / Profesional: 65).</p>
                            <p class="text-sm mb-6"><strong>Convenios: </strong>Verifique si la empresa tiene convenio
                                vigente en la pagina de la ORI :<a href="https://oriapp.uts.edu.co/activities_guest"
                                    target="_blank" class="text-uts-500 underline hover:text-uts-800"> Consultar
                                    convenios aquí </a></p>

                            <div class="flex justify-end space-x-2">
                                <button type="button" onclick="closeCreateModal()"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                    Cancelar
                                </button>
                                <button type="submit" id="guardarModalButton"
                                    class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                    <svg id="loadingSpinner-guardar" style="margin: 4px 10px 4px 0"
                                        class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64"
                                        fill="none" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24">
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
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/fases/practicas/fase_0.js') }}"></script>
        <script>
            function openCreateModal() {
                var añoActual = new Date().getFullYear();
                var mesActual = new Date().getMonth() + 1;
                var numero = mesActual <= 6 ? 1 : 2;
                var periodo_academico = añoActual + '-' + numero;

                $('#formTitle').html(
                    `Solicitud de <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Prácticas</span>`
                );

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

                $('#createModal').removeClass('hidden');
            }
        </script>
        <script>
            $('#practicasForm').on('submit', function(e) {
                e.preventDefault();

                const button = document.getElementById('guardarModalButton');
                const loadingSpinner = document.getElementById('loadingSpinner-guardar');

                const url = "{{ route('practicas.store') }}";

                let formData = new FormData(this); //  para archivos

                Swal.fire({
                    title: '¿Está seguro?',
                    text: "No podrá editar la información una vez se envíe",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, enviar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {

                        loadingSpinner.classList.remove('hidden');

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: formData,
                            processData: false, // IMPORTANTE
                            contentType: false, // IMPORTANTE

                            success: function(response) {

                                closeCreateModal();
                                showToast('Solicitud de Práctica enviada correctamente');
                            },

                            error: function(xhr) {

                                const errors = xhr.responseJSON?.errors;

                                // limpiar TODOS los errores primero
                                $('[id$="Error"]').text('');

                                // recorrer dinámicamente
                                for (let campo in errors) {
                                    $('#' + campo + 'Error').text(errors[campo][0]);
                                }
                            },

                            complete: function() {
                                loadingSpinner.classList.add('hidden');
                            }
                        });
                    }
                });
            });
        </script>

        <script>
            function closeDetailsModal() {
                $('#detailsModal').removeClass('show');
            }
        </script>
        <script>
            function closeCreateModal() {
                // TooltipManager.closeTooltips();
                $('#createModal').addClass('hidden');
            }
        </script>

        <script>
            function toggleHojaVida() {
                const tieneEmpresaSi = document.querySelector('input[name="tiene_empresa"][value="1"]');
                const tieneEmpresaNo = document.querySelector('input[name="tiene_empresa"][value="0"]');
                const container = document.getElementById('hojaVidaContainer');

                if (tieneEmpresaNo && tieneEmpresaNo.checked) {
                    // NO tiene empresa → mostrar hoja de vida
                    container.style.display = 'block';
                } else {
                    // SÍ tiene empresa → ocultar
                    container.style.display = 'none';
                }
            }

            // cuando carga el modal
            document.addEventListener('DOMContentLoaded', function() {
                toggleHojaVida();
            });
        </script>

        <!--Datatables-->

        <script>
            $('#practicasTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('practicas.data') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'descripcion',
                        name: 'descripcion'
                    },
                    {
                        data: 'estado',
                        name: 'estado'
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
                    "sInfoEmpty": "Mostrando 0 registros",
                    "sInfoFiltered": "(filtrado de _MAX_ registros totales)",
                    "sSearch": "Buscar:",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": "<<",
                        "sLast": ">>",
                        "sNext": ">",
                        "sPrevious": "<"
                    }
                },
                pagingType: "full_numbers"
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

        <script>
            function openDetailsModal(id) {

                // 1. Mostrar modal con loading
                $('#detailsTitle').html(
                    'Detalles de la <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded shadow">Solicitud</span>'
                    );
                $('#content-details').html('<div class="text-center">Cargando...</div>');
                $('#detailsModal').removeClass('hidden').addClass('show');

                // 2. Petición al backend
                $.get('{{ url('practicas') }}/' + id + '/detalle', function(response) {

                    let user = response.user;
                    let data = response.data;

                    // 3. Construir HTML
                    let html = `<br>
                    <div class="space-y-2">

                        <div class="p-2 bg-gray-50 rounded-lg shadow-sm flex items-center gap-2">
                            <p class="font-semibold text-gray-700 w-56">Nombre completo:</p>
                            <span class="text-gray-800">${user.name || 'N/A'}</span>
                        </div>

                        <div class="p-2 bg-gray-50 rounded-lg shadow-sm flex items-center gap-2">
                            <p class="font-semibold text-gray-700 w-56">Correo institucional:</p>
                            <span class="text-gray-800">${user.email || 'N/A'}</span>
                        </div>

                        <div class="p-2 bg-gray-50 rounded-lg shadow-sm flex items-center gap-2">
                            <p class="font-semibold text-gray-700 w-56">Nivel académico</p>
                            <span class="text-gray-800">${user.nivel.nombre || 'N/A'}</span>
                        </div>

                        <div class="p-2 bg-gray-50 rounded-lg shadow-sm flex items-center gap-2">
                            <p class="font-semibold text-gray-700 w-56">Documento:</p>
                            <span class="text-gray-800">${user.nro_documento || 'N/A'}</span>
                        </div>

                        <div class="p-2 bg-gray-50 rounded-lg shadow-sm flex items-center gap-2">
                            <p class="font-semibold text-gray-700 w-56">Celular:</p>
                            <span class="text-gray-800">${user.nro_celular || 'N/A'}</span>
                        </div>

                        <div class="p-2 bg-gray-50 rounded-lg shadow-sm flex items-center gap-2">
                            <p class="font-semibold text-gray-700 w-56">¿Tiene empresa?:</p>
                            <span class="text-gray-800">${data.tiene_empresa ? 'Sí' : 'No'}</span>
                        </div>

                        ${
                            data.hoja_vida 
                            ? `
                                                                                            <div class="p-2 bg-gray-50 rounded-lg shadow-sm flex items-center gap-2">
                                                                                                <p class="font-semibold text-gray-700 w-56">Hoja de vida:</p>
                                                                                                <a href="/storage/${data.hoja_vida}" target="_blank" class="text-uts-500 underline hover:text-uts-600">
                                                                                                    Ver archivo
                                                                                                </a>
                                                                                            </div>
                                                                                            ` 
                            : ''
                        }

                        <div class="p-2 bg-gray-50 rounded-lg shadow-sm flex items-center gap-2">
                            <p class="font-semibold text-gray-700 w-56">Estado:</p>
                            <span class="text-gray-800">${response.estado}</span>
                        </div>

                        <div class="p-2 bg-gray-50 rounded-lg shadow-sm flex items-center gap-2">
                            <p class="font-semibold text-gray-700 w-56">Fecha solicitud:</p>
                            <span class="text-gray-800">${response.fecha_solicitud}</span>
                        </div>

                    </div>

                        `;

                    // 4. Mostrar en modal
                    $('#content-details').html(html);

                }).fail(function() {

                    $('#content-details').html(
                        '<div class="text-red-500 text-center">Error al cargar los detalles.</div>'
                    );

                });
            }

            function closeDetailsModal() {
                $('#detailsModal').removeClass('show').addClass('hidden');
            }
        </script>
    @endpush


</x-app-layout>
