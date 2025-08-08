@php
use Carbon\Carbon;

$fechaActual = Carbon::now()->format('Y-m-d');
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Propuestas para <span class="bg-uts-500 text-gray-800 font-bold me-2 px-2.5 py-0.5 rounded uppercase">Banco</span>
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

        .btn-response {
            background-color: #6b7280;
            color: white;
        }

        .btn-response:hover {
            background-color: #545963;
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

            .container-actions {
                justify-content: flex-start !important;
            }
        }

        #createModal,
        #editModal,
        #detailsModal,
        #replySolicitudModal,
        #calendarModal,
        #warningModal {
            visibility: hidden;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
        }

        #createModal.show,
        #editModal.show,
        #detailsModal.show,
        #replySolicitudModal.show,
        #calendarModal.show,
        #warningModal.show {
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


        .form-group {
            margin-bottom: 15px;
        }

        .helper-button {
            margin-left: 5px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .helper-message {
            display: none;
            margin-top: 5px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.9em;
            color: #333;
        }

        .helper-message.visible {
            display: block;
        }
    </style>
    @endpush

    <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-b">
        <h2 class="text-2xl text-center font-bold text-gray-800">
            Propuestas <span class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Banco</span>
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
            @if (isset($fechas))
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
            @if ($fechaActual >= $fechas['fecha_inicio_banco'] && $fechaActual <= $fechas['fecha_fin_banco'])
                @can('create_propuesta_banco')
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
                </svg> Crear Propuesta
                </button>
                @endcan
                @endif
                @endif
        </div>
    </div>

    <div class="p-4">
        @can("list_propuestas_banco")
        <table id="propuestasTable" class="w-full">
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
                    <form id="propuestasForm" class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="formTitle"></p>
                        <p class="text-sm mb-6">En este formulario, el docente será encargado de describir su propuesta para el banco de ideas.</p>
                        @foreach($campos as $campo)
                        <div class="mb-4">
                            @if ($campo->label != null && $campo->type != 'hidden')
                            <div class="flex items-center gap-2">
                                <label for="{{ $campo->name }}" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    {{ $campo->label }}
                                </label>

                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-{{ $campo->name }}"></i>
                                    <div id="tooltip-{{ $campo->name }}"
                                        class="hidden absolute z-10 max-w-[90vw] px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg left-1/2 -translate-x-1/2 bottom-full mb-2 sm:left-0 sm:translate-x-0">
                                        <p class="uppercase font-bold mb-2">Instrucciones:</p>
                                        {!! $campo->instructions ?? '' !!}
                                    </div>
                                </div>
                            </div>
                            @endif

                            @switch($campo->type)
                            @case('text')
                            <input type="text" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                placeholder="{{ $campo->placeholder ?? '' }}"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                @if($campo->required)@endif>
                            @break

                            @case('textarea')
                            <textarea name="{{ $campo->name }}" id="{{ $campo->name }}"
                                placeholder="{{ $campo->placeholder ?? '' }}"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                @if($campo->required)@endif></textarea>
                            @break

                            @case('number')
                            <input type="number" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                placeholder="{{ $campo->placeholder ?? '' }}"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                @if($campo->required)@endif>
                            @break

                            @case('date')
                            <input type="date" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                placeholder="{{ $campo->placeholder ?? '' }}"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                @if($campo->required)@endif>
                            @break

                            @case('select')
                            <select name="{{ $campo->name }}" id="{{ $campo->name }}"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                @if($campo->required)@endif>
                                <option value="" selected disabled>Selecciona una opción</option>
                                @if ($campo->name == 'modalidad')
                                @foreach ($modalidades as $modalidad)
                                <option value="{{ $modalidad->id }}">{{ $modalidad->nombre }}</option>
                                @endforeach
                                @elseif ($campo->name == 'nivel')
                                @foreach ($niveles as $nivel)
                                <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                                @endforeach
                                @elseif ($campo->name == 'linea_investigacion')
                                @foreach ($lineas_investigacion as $linea)
                                <option value="{{ $linea->id }}">{{ $linea->nombre }}</option>
                                @endforeach
                                @endif
                            </select>
                            @break

                            @case('file')
                            <input type="file" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                @if($campo->required)@endif>
                            @break

                            @case('checkbox')
                            <div class="flex items-center">
                                <input type="checkbox" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                    class="form-check-input"
                                    @if($campo->required)@endif>
                            </div>
                            @break

                            @case('hidden')
                            <input type="hidden" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                value="{{ $campo->placeholder ?? '' }}">
                            @break

                            @default
                            <input type="text" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                placeholder="{{ $campo->placeholder ?? '' }}"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                @if($campo->required)@endif>
                            @endswitch

                            <span id="{{ $campo->name }}Error" class="text-red-500 text-sm"></span>
                        </div>
                        @endforeach

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
                </div>
            </div>
        </div>
    </div>

    <div id="editModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeEditModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeEditModal()">
                        &times;
                    </button>
                    <form id="propuestasEditForm" class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="editTitle"></p>
                        <p class="text-sm mb-6">En este formulario, el docente será encargado de describir su propuesta para el banco de ideas.</p>
                        <input type="hidden" name="solicitud_idEdit" id="solicitud_idEdit">
                        @foreach($campos as $campo)
                        <div class="mb-4">
                            @if ($campo->label != null && $campo->type != 'hidden')
                            <div class="flex items-center gap-2">
                                <label for="{{ $campo->name }}Edit" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    {{ $campo->label }}
                                </label>
                                <div class="relative inline-block">
                                    <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                        data-tooltip="tooltip-{{ $campo->name }}Edit"></i>
                                    <div id="tooltip-{{ $campo->name }}Edit"
                                        class="hidden absolute z-10 max-w-[90vw] px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg left-1/2 -translate-x-1/2 bottom-full mb-2 sm:left-0 sm:translate-x-0">
                                        <p class="uppercase font-bold mb-2">Instrucciones:</p>
                                        {!! $campo->instructions ?? '' !!}
                                    </div>
                                </div>
                            </div>
                            @endif

                            @switch($campo->type)
                            @case('text')
                            <input type="text" name="{{ $campo->name }}Edit" id="{{ $campo->name }}Edit"
                                placeholder="{{ $campo->placeholder ?? '' }}"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                @if($campo->required)@endif>
                            @break

                            @case('textarea')
                            <textarea name="{{ $campo->name }}Edit" id="{{ $campo->name }}Edit"
                                placeholder="{{ $campo->placeholder ?? '' }}"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                @if($campo->required)@endif></textarea>
                            @break

                            @case('number')
                            <input type="number" name="{{ $campo->name }}Edit" id="{{ $campo->name }}Edit"
                                placeholder="{{ $campo->placeholder ?? '' }}"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                @if($campo->required)@endif>
                            @break

                            @case('date')
                            <input type="date" name="{{ $campo->name }}Edit" id="{{ $campo->name }}Edit"
                                placeholder="{{ $campo->placeholder ?? '' }}"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                @if($campo->required)@endif>
                            @break

                            @case('select')
                            <select name="{{ $campo->name }}Edit" id="{{ $campo->name }}Edit"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                @if($campo->required)@endif>
                                <option value="" selected disabled>Selecciona una opción</option>
                                @if ($campo->name == 'modalidad')
                                @foreach ($modalidades as $modalidad)
                                <option value="{{ $modalidad->id }}">{{ $modalidad->nombre }}</option>
                                @endforeach
                                @elseif ($campo->name == 'nivel')
                                @foreach ($niveles as $nivel)
                                <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                                @endforeach
                                @elseif ($campo->name == 'linea_investigacion')
                                @foreach ($lineas_investigacion as $linea)
                                <option value="{{ $linea->id }}">{{ $linea->nombre }}</option>
                                @endforeach
                                @endif
                            </select>
                            @break

                            @case('file')
                            <input type="file" name="{{ $campo->name }}Edit" id="{{ $campo->name }}Edit"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                @if($campo->required)@endif>
                            @break

                            @case('checkbox')
                            <div class="flex items-center">
                                <input type="checkbox" name="{{ $campo->name }}Edit" id="{{ $campo->name }}Edit"
                                    class="form-check-input"
                                    @if($campo->required)@endif>
                            </div>
                            @break

                            @case('hidden')
                            <input type="hidden" name="{{ $campo->name }}Edit" id="{{ $campo->name }}Edit"
                                value="{{ $campo->placeholder ?? '' }}">
                            @break

                            @default
                            <input type="text" name="{{ $campo->name }}Edit" id="{{ $campo->name }}Edit"
                                placeholder="{{ $campo->placeholder ?? '' }}"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                @if($campo->required)@endif>
                            @endswitch

                            <span id="{{ $campo->name }}EditError" class="text-red-500 text-sm"></span>
                        </div>
                        @endforeach

                        <div class="flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeEditModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                id="editarModalButton"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-editar" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                                <svg id="loadingSpinner-reply" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

    @push('scripts')
    <script src="{{ asset('js/options/warning.js') }}"></script>
    <script src="{{ asset('js/options/calendar.js') }}"></script>
    <script>
        $(document).ready(function() {
            initializeDataTable('#propuestasTable', '{{ route("propuestas.data") }}',
                [{
                        data: 'formatted_id',
                        name: 'formatted_id',
                        orderable: true,
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
                        render: function(data) {
                            let estadoHtml = `<p style="text-align: center">`;
                            switch (data) {
                                case "Pendiente":
                                    estadoHtml += `<span class="shadow bg-yellow-100 text-yellow-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded border border-yellow-300" id="estado_solicitud">${data}</span>`;
                                    break;
                                case "Aprobada":
                                    estadoHtml += `<span class="shadow bg-green-100 text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded border border-green-300" id="estado_solicitud">${data}</span>`;
                                    break;
                                case "Rechazada":
                                    estadoHtml += `<span class="shadow bg-red-100 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded border border-red-300" id="estado_solicitud">${data}</span>`;
                                    break;
                                default:
                                    estadoHtml += `<span class="shadow bg-gray-100 text-gray-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded border border-gray-300" id="estado_solicitud">${data}</span>`;
                            }
                            estadoHtml += `</p>`;
                            return estadoHtml;
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ]
            );
        });

        function getPeriodoActual() {
            const añoActual = new Date().getFullYear();
            const mesActual = new Date().getMonth() + 1;
            const numero = mesActual <= 6 ? 1 : 2;
            const periodo_academico = añoActual + '-' + numero;

            return periodo_academico
        }

        async function openDetailsModal(solicitud) {
            document.querySelectorAll('.dtr-hidden [id]').forEach(el => el.id += '-hidden');

            const button = document.getElementById(`openModalButton-${solicitud.id}`);
            const loadingSpinner = document.getElementById(`loadingSpinner-${solicitud.id}`);

            button.querySelector('i').classList.add('hidden');
            loadingSpinner.classList.remove('hidden');

            $('#detailsTitle').html(`Detalles de la <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Propuesta</span>`);

            const docente = await getDocente(solicitud.user_id);

            $.get(`/propuestas-banco/${solicitud.id}/campos`, async function(data) {

                let detailsHtml = `
                <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                    <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Docente responsable:</p>
                    <span class="text-gray-800 flex-1 ml-2">${docente.name}</span>
                </div>`;

                for (let campo of data.campos) {
                    detailsHtml += `
                        <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                            <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">${campo.campo.label}:</p>`;

                    switch (campo.campo.name) {
                        case "modalidad":
                            let modalidad = await getModalidad(campo.valor);
                            detailsHtml += `<span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${modalidad.nombre}</span>`;
                            break;
                        case "nivel":
                            let nivel = await getNivel(campo.valor);
                            detailsHtml += `<span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${nivel.nombre}</span>`;
                            break;
                        case "linea_investigacion":
                            let linea_investigacion = await getLinea(campo.valor);
                            detailsHtml += `<span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${linea_investigacion.nombre}</span>`;
                            break;
                        case "disponible":
                            let render = campo.valor === "true" ? 'Disponible' : 'No disponible';
                            detailsHtml += `<span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${render}</span>`;
                            break;
                        case "nombre":
                            detailsHtml += `<span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${campo.valor.toUpperCase()}</span>`;
                            break
                        case "titulo":
                            detailsHtml += `<span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${campo.valor.toUpperCase()}</span>`;
                            break;
                        case "objetivo":
                            detailsHtml += `<span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${campo.valor.charAt(0).toUpperCase() + campo.valor.slice(1).toLowerCase()}</span>`;
                            break;
                        default:
                            detailsHtml += `<span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${campo.valor}</span>`;
                    }

                    detailsHtml += `</div>`;
                }

                $('#content-details').html(detailsHtml);
                $('#detailsModal').addClass('show');

                loadingSpinner.classList.add('hidden');
                button.querySelector('i').classList.remove('hidden');
            });

            document.querySelectorAll('.dtr-hidden [id]').forEach(el => el.id = el.id.replace('-hidden', ''));
        }

        async function openEditModal(solicitud) {
            document.querySelectorAll('.dtr-hidden [id]').forEach(el => el.id += '-hidden');

            const button = document.getElementById(`openEditModalButton-${solicitud.id}`);
            const loadingSpinner = document.getElementById(`loadingEditSpinner-${solicitud.id}`);

            button.querySelector('i').classList.add('hidden');
            loadingSpinner.classList.remove('hidden');

            $('#editTitle').html(`Editar la <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Propuesta</span>`);

            const docente = await getDocente(solicitud.user_id);

            let solicitud_id = solicitud.id;
            let titulo;
            let modalidad;
            let objetivo;
            let linea_investigacion;
            let nivel;
            let periodo_academico = await getPeriodoActual();

            $.get(`/propuestas-banco/${solicitud.id}/campos`, async function(data) {

                for (let item of data.campos) {
                    switch (item.campo.name) {
                        case "titulo":
                            titulo = item.valor;
                            break;
                        case "modalidad":
                            modalidad = item.valor;
                            break;
                        case "objetivo":
                            objetivo = item.valor;
                            break;
                        case "linea_investigacion":
                            linea_investigacion = item.valor;
                            break;
                        case "nivel":
                            nivel = item.valor;
                            break;
                    }
                }

                $('#solicitud_idEdit').val(solicitud_id);
                $('#tituloEdit').val(titulo);
                $('#modalidadEdit').val(modalidad);
                $('#objetivoEdit').val(objetivo);
                $('#linea_investigacionEdit').val(linea_investigacion);
                $('#nivelEdit').val(nivel);
                $('#periodoEdit').val(periodo_academico);
                $('#disponibleEdit').val('false');

                $('#tituloEditError').text('');
                $('#modalidadEditError').text('');
                $('#objetivoEditError').text('');
                $('#linea_investigacionEditError').text('');
                $('#nivelEditError').text('');
                $('#periodoEditError').text('');
                $('#disponibleEditError').text('');

                $('#editModal').addClass('show');

                loadingSpinner.classList.add('hidden');
                button.querySelector('i').classList.remove('hidden');
            });

            document.querySelectorAll('.dtr-hidden [id]').forEach(el => el.id = el.id.replace('-hidden', ''));
        }

        function closeEditModal() {
            TooltipManager.closeTooltips();
            $('#editModal').removeClass('show');
        }

        function getDocente(id) {
            return new Promise((resolve, reject) => {
                $.get(`/users/${id}`, function(data) {
                    if (data) {
                        resolve(data);
                    } else {
                        reject("No se encontró el docente");
                    }
                });
            });
        }

        function getModalidad(id) {
            return new Promise((resolve, reject) => {
                $.get(`/modalidades/${id}`, function(data) {
                    if (data) {
                        resolve(data);
                    } else {
                        reject("No se encontró la modalidad");
                    }
                });
            });
        }

        function getNivel(id) {
            return new Promise((resolve, reject) => {
                $.get(`/niveles/${id}`, function(data) {
                    if (data) {
                        resolve(data);
                    } else {
                        reject("No se encontró el nivel");
                    }
                });
            });
        }

        function getLinea(id) {
            return new Promise((resolve, reject) => {
                $.get(`/lineas-investigacion/${id}`, function(data) {
                    if (data) {
                        resolve(data);
                    } else {
                        reject("No se encontró la línea de investigación.");
                    }
                });
            });
        }

        function closeDetailsModal() {
            $('#detailsModal').removeClass('show');
        }

        async function openCreateModal() {
            const button = document.getElementById(`openCreateModalButton`);
            const loadingSpinner = document.getElementById(`loadingSpinner-crear`);

            button.querySelector('i').classList.add('hidden');
            loadingSpinner.classList.remove('hidden');

            let periodo_academico = await getPeriodoActual();

            $('#formTitle').html(`Crear nueva <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Propuesta</span>`);
            $('#nombre').val('');
            $('#titulo').val('');
            $('#modalidad').val('');
            $('#objetivo').val('');
            $('#linea_investigacion').val('');
            $('#nivel').val('');
            $('#periodo').val(periodo_academico);
            $('#disponible').val('false');
            $('#nombreError').text('');
            $('#tituloError').text('');
            $('#modalidadError').text('');
            $('#objetivoError').text('');
            $('#linea_investigacionError').text('');
            $('#nivelError').text('');
            $('#periodoError').text('');
            $('#disponibleError').text('');
            $('#createModal').addClass('show');

            loadingSpinner.classList.add('hidden');
            button.querySelector('i').classList.remove('hidden');
        }

        $('#propuestasForm').on('submit', function(e) {
            e.preventDefault();

            const button = document.getElementById(`guardarModalButton`);
            const loadingSpinner = document.getElementById(`loadingSpinner-guardar`);

            const url = `/propuestas-banco`;
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
                            $('#propuestasTable').DataTable().ajax.reload();
                            closeCreateModal();
                            showToast('Solicitud enviada, tendrá respuesta en los próximos 5 días habiles');
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON.errors;
                            $('#nombreError').text(errors?.nombre?.[0] || '');
                            $('#tituloError').text(errors?.titulo?.[0] || '');
                            $('#modalidadError').text(errors?.modalidad?.[0] || '');
                            $('#objetivoError').text(errors?.objetivo?.[0] || '');
                            $('#linea_investigacionError').text(errors?.linea_investigacion?.[0] || '');
                            $('#nivelError').text(errors?.nivel?.[0] || '');
                            $('#periodoError').text(errors?.periodo?.[0] || '');
                            $('#disponibleError').text(errors?.disponible?.[0] || '');
                        },
                        complete: function() {
                            loaderGeneral.classList.replace('flex', 'hidden');
                            loadingSpinner.classList.add('hidden');
                        }
                    });
                }
            });
        });

        $('#propuestasEditForm').on('submit', function(e) {
            e.preventDefault();

            const button = document.getElementById(`editarModalButton`);
            const loadingSpinner = document.getElementById(`loadingSpinner-editar`);

            const url = `/propuestas-banco`;
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
                            $('#propuestasTable').DataTable().ajax.reload();
                            closeEditModal();
                            showToast('Solicitud enviada, tendrá respuesta en los próximos 5 días habiles');
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON.errors;
                            $('#tituloEditError').text(errors?.tituloEdit?.[0] || '');
                            $('#modalidadEditError').text(errors?.modalidadEdit?.[0] || '');
                            $('#objetivoEditError').text(errors?.objetivoEdit?.[0] || '');
                            $('#linea_investigacionEditError').text(errors?.linea_investigacionEdit?.[0] || '');
                            $('#nivelEditError').text(errors?.nivelEdit?.[0] || '');
                            $('#periodoEditError').text(errors?.periodoEdit?.[0] || '');
                            $('#disponibleEditError').text(errors?.disponibleEdit?.[0] || '');
                        },
                        complete: function() {
                            loaderGeneral.classList.replace('flex', 'hidden');
                            loadingSpinner.classList.add('hidden');
                        }
                    });
                }
            });
        });

        function closeCreateModal() {
            TooltipManager.closeTooltips();
            $('#createModal').removeClass('show');
        }

        function openReplySolicitudModal(id) {
            initQuillEditor(undefined, "Ingrese el mensaje de respuesta indicado detalles al docente.", 'txt-editor', 'mensaje');

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

            const url = `/propuestas-banco/responder`;
            const method = 'POST';

            const loadingSpinner = document.getElementById(`loadingSpinner-reply`);

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
                            $('#propuestasTable').DataTable().ajax.reload();
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

        function repostSolicitud(event, solicitud_id) {
            event.preventDefault();

            document.querySelectorAll('.dtr-hidden [id]').forEach(el => el.id += '-hidden');

            const button = document.getElementById(`repostSolicitudButton-${solicitud_id}`);
            const loadingSpinner = document.getElementById(`loadingRepostSpinner-${solicitud_id}`);

            button.querySelector('i').classList.add('hidden');
            loadingSpinner.classList.remove('hidden');

            const form = document.getElementById(`repostSolicitudForm-${solicitud_id}`);
            const formData = new FormData(form);

            const periodo_actual = getPeriodoActual();

            const url = `/propuestas-banco/publicar`;
            const method = 'POST';

            Swal.fire({
                heightAuto: false,
                title: '¿Está seguro?',
                text: "Desea volver a proponer esta idea para el " + periodo_actual,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#C1D631',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, enviar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    loaderGeneral.classList.replace('hidden', 'flex');

                    $.ajax({
                        url: url,
                        method: method,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#propuestasTable').DataTable().ajax.reload();
                            showToast('Solicitud enviada, tendrá respuesta en los próximos 5 días habiles');
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON?.errors;
                        },
                        complete: function() {
                            loaderGeneral.classList.replace('flex', 'hidden');
                            loadingSpinner.classList.add('hidden');
                            button.querySelector('i').classList.remove('hidden');
                        }
                    });
                } else {
                    loaderGeneral.classList.replace('flex', 'hidden');
                    loadingSpinner.classList.add('hidden');
                    button.querySelector('i').classList.remove('hidden');
                }
            });

            document.querySelectorAll('.dtr-hidden [id]').forEach(el => el.id = el.id.replace('-hidden', ''));
        }
    </script>
    @endpush
</x-app-layout>