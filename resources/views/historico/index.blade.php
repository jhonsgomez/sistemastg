@php
    use Carbon\Carbon;

    $anio_inicio = 2022;
    $anio_fin = 2024;   
    
    $anio_actual = Carbon::now()->year;
    $mes_actual = Carbon::now()->month;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Histórico de <span class="bg-uts-500 text-gray-800 font-bold me-2 px-2.5 py-0.5 rounded uppercase">Proyectos</span>
        </h2>
    </x-slot>

    @push('styles')
    <style>
        .btn-delete {
            background-color: #dc2626;
            color: white;
        }

        .btn-delete:hover {
            background-color: #b32020;
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

        #createProyectoModal,
        #createMasiveProyectoModal,
        #editProyectoModal,
        #reporteModal {
            visibility: hidden;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
        }

        #createProyectoModal.show,
        #createMasiveProyectoModal.show,
        #editProyectoModal.show,
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

        @media screen and (max-width: 640px) {
            .modal-content {
                padding: 1.5rem !important;
            }

            .container-actions {
                justify-content: flex-start !important;
            }

            .buttons-container {
                padding-top: 1rem !important;
            }
        }
    </style>
    @endpush

    <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-b">
        <h2 class="text-2xl text-center font-bold text-gray-800">
            Histórico de <span class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Proyectos</span>
        </h2>
        <div class="flex justify-center items-center space-x-2 buttons-container">
            @if (auth()->user()->hasRole(['super_admin', 'admin', 'coordinador']))
                <button type="button" id="reporte" onclick="openReporteModal()"
                    class="btn-action shadow bg-green-700 hover:bg-green-800 text-white px-3 py-1 rounded-lg relative">
                    <i class="fa-solid fa-download"></i>
                </button>
            @endif
            <button type="button" id="reporte" onclick="openCreateMasiveProyectoModal()"
                class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                <i class="fa-solid fa-cloud-arrow-up"></i>
            </button>
            <button 
                type="button" 
                id="createProyectoButton" onclick="openCreateProyectoModal()"
                class="bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg flex items-center shadow">
                <i class="fa-solid fa-plus"></i>&nbsp;Nuevo Proyecto
            </button>
        </div>
    </div>

    <div class="p-4">
        @can("list_historico")
        <table id="historicoTable" class="w-full">
            <thead>
                <tr>
                    <th>Periodo</th>
                    <th>Código TG</th>
                    <th>Nivel</th>
                    <th>Estudiante</th>
                    <th>Correo</th>
                    <th>Cédula</th>
                    <th>Celular</th>
                    <th>Modalidad</th>
                    <th>Título</th>
                    <th>Director</th>
                    <th>Evaluador</th>
                    <th>Autores</th>
                    <th>Inicio TG</th>
                    <th>Aprobación propuesta</th>
                    <th>Fin TG</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
        @endcan
    </div>

    <!-- MODAL PARA CREAR -->
    <div id="createProyectoModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeCreateProyectoModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeCreateProyectoModal()">
                        &times;
                    </button>
                    <form id="proyectosCreateForm" class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="createProyectoTitle"></p>
                        
                        <div class="grid grid-cols-2 gap-4 mt-10">
                            <div>
                                <label for="create_periodo_academico" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Periodo académico:
                                </label>
                                <input 
                                    type="text" 
                                    name="periodo_academico" 
                                    id="create_periodo_academico" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el periodo académico"
                                />
                                <span id="create_periodo_academicoError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="create_codigo_tg" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-code-branch mr-2 text-gray-500"></i>
                                    Código TG:
                                </label>
                                <input 
                                    type="text" 
                                    name="codigo_tg" 
                                    id="create_codigo_tg" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el código TG"
                                />
                                <span id="create_codigo_tgError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="create_nivel" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-graduation-cap mr-2 text-gray-500"></i>
                                    Nivel:
                                </label>
                                <input 
                                    type="text" 
                                    name="nivel" 
                                    id="create_nivel" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el nivel"
                                />
                                <span id="create_nivelError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="create_estudiante" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-user-graduate mr-2 text-gray-500"></i>
                                    Estudiante:
                                </label>
                                <input 
                                    type="text" 
                                    name="estudiante" 
                                    id="create_estudiante" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el nombre del estudiante"
                                />
                                <span id="create_estudianteError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="create_correo" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-envelope mr-2 text-gray-500"></i>
                                    Correo:
                                </label>
                                <input 
                                    type="email" 
                                    name="correo" 
                                    id="create_correo" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el correo"
                                />
                                <span id="create_correoError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="create_documento" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-id-card mr-2 text-gray-500"></i>
                                    Cédula:
                                </label>
                                <input 
                                    type="text" 
                                    name="documento" 
                                    id="create_documento" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese la cédula"
                                />
                                <span id="create_documentoError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="create_celular" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-mobile-alt mr-2 text-gray-500"></i>
                                    Celular:
                                </label>
                                <input 
                                    type="text" 
                                    name="celular" 
                                    id="create_celular" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el celular"
                                />
                                <span id="create_celularError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="create_modalidad" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-user-tie mr-2 text-gray-500"></i>
                                    Modalidad:
                                </label>
                                <input 
                                    type="text" 
                                    name="modalidad" 
                                    id="create_modalidad" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese la modalidad"
                                />
                                <span id="create_modalidadError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="create_titulo" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-book-open mr-2 text-gray-500"></i>
                                    Título:
                                </label>
                                <textarea 
                                    name="titulo" 
                                    id="create_titulo" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el título"
                                    rows="2"
                                ></textarea>
                                <span id="create_tituloError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="create_director" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-user-tie mr-2 text-gray-500"></i>
                                    Director:
                                </label>
                                <input 
                                    type="text" 
                                    name="director" 
                                    id="create_director" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el nombre del director"
                                />
                                <span id="create_directorError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="create_evaluador" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-user-tie mr-2 text-gray-500"></i>
                                    Evaluador:
                                </label>
                                <input 
                                    type="text" 
                                    name="evaluador" 
                                    id="create_evaluador" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el nombre del evaluador"
                                />
                                <span id="create_evaluadorError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="create_autores" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-users mr-2 text-gray-500"></i>
                                    Autores:
                                </label>
                                <textarea 
                                    name="autores" 
                                    id="create_autores" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese los autores"
                                    rows="2"
                                ></textarea>
                                <span id="create_autoresError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="create_inicio_tg" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-calendar-alt mr-2 text-gray-500"></i>
                                    Inicio TG:
                                </label>
                                <input 
                                    type="date" 
                                    name="inicio_tg" 
                                    id="create_inicio_tg" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                />
                                <span id="create_inicio_tgError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="create_aprobacion_propuesta" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-calendar-check mr-2 text-gray-500"></i>
                                    Aprobación propuesta:
                                </label>
                                <input 
                                    type="date" 
                                    name="aprobacion_propuesta" 
                                    id="create_aprobacion_propuesta" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                />
                                <span id="create_aprobacion_propuestaError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="create_final_tg" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-calendar-alt mr-2 text-gray-500"></i>
                                    Fin TG:
                                </label>
                                <input 
                                    type="date" 
                                    name="final_tg" 
                                    id="create_final_tg" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                />
                                <span id="create_final_tgError" class="text-red-500 text-sm"></span>
                            </div>
                        </div>
                    
                        <div class="flex justify-end space-x-2 mt-4">
                            <button
                                type="button"
                                onclick="closeCreateProyectoModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                id="crearProyectoModalButton"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-crearProyecto" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    </path>
                                </svg>
                                Crear
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PARA CREAR DE FORMA MASIVA -->
    <div id="createMasiveProyectoModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeCreateMasiveProyectoModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeCreateMasiveProyectoModal()">
                        &times;
                    </button>
                    <form id="proyectosMasiveCreateForm" class="p-6 mt-2" enctype="multipart/form-data">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="createMasiveProyectoTitle"></p>

                        <div class="mb-6">
                            <p class="text-gray-700 text-sm">
                                <i class="fa-solid fa-info-circle mr-2 text-gray-500"></i>
                                Para cargar proyectos de forma masiva, siga los siguientes pasos:
                            </p>
                            <ol class="list-decimal list-inside text-gray-700 text-sm mt-2">
                                <li>
                                    Descargue el archivo de formato haciendo clic 
                                    <a href="{{ asset('formatos/formato_historico.xlsx') }}" class="text-blue-500 hover:underline font-medium">
                                        aquí
                                    </a>.
                                </li>
                                <li>
                                    Diligencie el formato descargado con la información correspondiente.
                                </li>
                                <li>
                                    Cargue el archivo completado en el campo de carga que se encuentra a continuación.
                                </li>
                            </ol>
                        </div>

                        <div>
                            <label for="file_proyectos" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-file-excel mr-2 text-gray-500"></i>
                                Cargue el archivo aquí:
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_file_proyectos">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400   text-xs leading-4">Solo archivos de Excel de máximo 15MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input
                                            type="file" 
                                            name="file_proyectos" 
                                            id="file_proyectos" 
                                            class="absolute inset-0 opacity-0 cursor-pointer"
                                            accept=".xlsx" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="file_proyectosError" class="text-red-500 text-sm"></span>
                            <ul id="file-list-file-proyectos" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-file-proyectos" class="text-gray-800 text-sm"></span>
                        </div>
                    
                        <div class="flex justify-end space-x-2 mt-6">
                            <button
                                type="button"
                                onclick="closeCreateMasiveProyectoModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                id="crearMasiveProyectoModalButton"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-crearMasiveProyecto" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    </path>
                                </svg>
                                Cargar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PARA EDITAR -->
    <div id="editProyectoModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeEditProyectoModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeEditProyectoModal()">
                        &times;
                    </button>
                    <form id="proyectosEditForm" class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="editProyectoTitle"></p>
                        <input type="hidden" name="proyecto_id" id="proyecto_id">
                        
                        <div class="grid grid-cols-2 gap-4 mt-10">
                            <div>
                                <label for="periodo_academico" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Periodo académico:
                                </label>
                                <input 
                                    type="text" 
                                    name="periodo_academico" 
                                    id="periodo_academico" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el periodo académico"
                                />
                                <span id="periodo_academicoError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="codigo_tg" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-code-branch mr-2 text-gray-500"></i>
                                    Código TG:
                                </label>
                                <input 
                                    type="text" 
                                    name="codigo_tg" 
                                    id="codigo_tg" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el código TG"
                                />
                                <span id="codigo_tgError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="nivel" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-graduation-cap mr-2 text-gray-500"></i>
                                    Nivel:
                                </label>
                                <input 
                                    type="text" 
                                    name="nivel" 
                                    id="nivel" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el nivel"
                                />
                                <span id="nivelError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="estudiante" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-user-graduate mr-2 text-gray-500"></i>
                                    Estudiante:
                                </label>
                                <input 
                                    type="text" 
                                    name="estudiante" 
                                    id="estudiante" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el nombre del estudiante"
                                />
                                <span id="estudianteError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="correo" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-envelope mr-2 text-gray-500"></i>
                                    Correo:
                                </label>
                                <input 
                                    type="email" 
                                    name="correo" 
                                    id="correo" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el correo"
                                />
                                <span id="correoError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="documento" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-id-card mr-2 text-gray-500"></i>
                                    Cédula:
                                </label>
                                <input 
                                    type="text" 
                                    name="documento" 
                                    id="documento" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese la cédula"
                                />
                                <span id="documentoError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="celular" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-mobile-alt mr-2 text-gray-500"></i>
                                    Celular:
                                </label>
                                <input 
                                    type="text" 
                                    name="celular" 
                                    id="celular" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el celular"
                                />
                                <span id="celularError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="modalidad" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-user-tie mr-2 text-gray-500"></i>
                                    Modalidad:
                                </label>
                                <input 
                                    type="text" 
                                    name="modalidad" 
                                    id="modalidad" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese la modalidad"
                                />
                                <span id="modalidadError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="titulo" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-book-open mr-2 text-gray-500"></i>
                                    Título:
                                </label>
                                <textarea 
                                    name="titulo" 
                                    id="titulo" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el título"
                                    rows="2"
                                ></textarea>
                                <span id="tituloError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="director" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-user-tie mr-2 text-gray-500"></i>
                                    Director:
                                </label>
                                <input 
                                    type="text" 
                                    name="director" 
                                    id="director" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el nombre del director"
                                />
                                <span id="directorError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="evaluador" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-user-tie mr-2 text-gray-500"></i>
                                    Evaluador:
                                </label>
                                <input 
                                    type="text" 
                                    name="evaluador" 
                                    id="evaluador" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese el nombre del evaluador"
                                />
                                <span id="evaluadorError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="autores" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-users mr-2 text-gray-500"></i>
                                    Autores:
                                </label>
                                <textarea 
                                    name="autores" 
                                    id="autores" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                    placeholder="Ingrese los autores"
                                    rows="2"
                                ></textarea>
                                <span id="autoresError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="inicio_tg" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-calendar-alt mr-2 text-gray-500"></i>
                                    Inicio TG:
                                </label>
                                <input 
                                    type="date" 
                                    name="inicio_tg" 
                                    id="inicio_tg" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                />
                                <span id="inicio_tgError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="aprobacion_propuesta" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-calendar-check mr-2 text-gray-500"></i>
                                    Aprobación propuesta:
                                </label>
                                <input 
                                    type="date" 
                                    name="aprobacion_propuesta" 
                                    id="aprobacion_propuesta" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                />
                                <span id="aprobacion_propuestaError" class="text-red-500 text-sm"></span>
                            </div>
                            <div>
                                <label for="final_tg" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-calendar-alt mr-2 text-gray-500"></i>
                                    Fin TG:
                                </label>
                                <input 
                                    type="date" 
                                    name="final_tg" 
                                    id="final_tg" 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                />
                                <span id="final_tgError" class="text-red-500 text-sm"></span>
                            </div>
                        </div>
                    
                        <div class="flex justify-end space-x-2 mt-4">
                            <button
                                type="button"
                                onclick="closeEditProyectoModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                id="editarProyectoModalButton"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-editarProyecto" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    </path>
                                </svg>
                                Editar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- REPORTES DEL HISTORICO -->
    <div id="reporteModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeReporteModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeReporteModal()">
                        &times;
                    </button>
                    <form action="{{ route('historico.reporte') }}" method="POST" id="reporteForm" class="p-6 mt-2">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="reporteTitle"></p>
                        <div class="mb-4">
                            <label for="periodo_reporte" class="block font-medium text-md text-gray-700 mb-4">
                                Periodo académico:
                            </label>
                            <select name="periodo_reporte" id="periodo_reporte"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500" required>
                                <option value="" selected disabled>Seleccione una opción</option>
                                <option value="2025-1">2025-1</option>
                                @for ($anio = $anio_fin; $anio >= $anio_inicio; $anio--)
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
    <script src="{{ asset('js/options/reporte.js') }}"></script>
    <script src="{{ asset('js/options/historico.js') }}"></script>
    <script>
        const capitalize = (str, lower = false) =>
            (lower ? str.toLowerCase() : str).replace(/(?:^|\s|["'([{])+\S/g, match => match.toUpperCase());;

        function convertirInicialesMayusculas(texto) {
            return texto
                .split(' ')
                .map(palabra => {
                    return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
                })
                .join(' ');
        }

        $(document).ready(function() {
            initializeDataTable('#historicoTable', '{{ route("historico.data") }}', [
                {
                    data: 'periodo_academico',
                    name: 'periodo_academico',
                    orderable: true,
                    searchable: true,
                    sortable: true,
                    render: function(data, type, row) {
                        return data ? data : 'No especificado';
                    }
                },
                {
                    data: 'codigo_tg',
                    name: 'codigo_tg',
                    orderable: true,
                    searchable: true,
                    sortable: true,
                    render: function(data, type, row) {
                        return data ? data : 'No especificado';
                    }
                },
                {
                    data: 'nivel',
                    name: 'nivel',
                    orderable: true,
                    searchable: true,
                    sortable: true,
                    render: function(data, type, row) {
                        return data ? capitalize(data, true) : 'No especificado';
                    }
                },
                {
                    data: 'estudiante',
                    name: 'estudiante',
                    orderable: true,
                    searchable: true,
                    sortable: true,
                    render: function(data, type, row) {
                        return data ? convertirInicialesMayusculas(data) : 'No especificado';
                    }
                },
                {
                    data: 'correo',
                    name: 'correo',
                    orderable: true,
                    searchable: true,
                    sortable: true,
                    render: function(data, type, row) {
                        return data ? `<span class="text-blue-500 hover:underline">${data.toLowerCase()}</span>` : 'No especificado';
                    }
                },
                {
                    data: 'documento',
                    name: 'documento',
                    orderable: true,
                    searchable: true,
                    sortable: true,
                    render: function(data, type, row) {
                        return data ? data : 'No especificado';
                    }
                },
                {
                    data: 'celular',
                    name: 'celular',
                    orderable: true,
                    searchable: true,
                    sortable: true,
                    render: function(data, type, row) {
                        return data ? data : 'No especificado';
                    }
                },
                {
                    data: 'modalidad',
                    name: 'modalidad',
                    orderable: true,
                    searchable: true,
                    sortable: true,
                    render: function(data, type, row) {
                        return data ? capitalize(data, true) : 'No especificado';
                    }
                },
                {
                    data: 'titulo',
                    name: 'titulo',
                    orderable: true,
                    searchable: true,
                    sortable: true,
                    render: function(data, type, row) {
                        return data ? data.toUpperCase() : 'No especificado';
                    }
                },
                {
                    data: 'director',
                    name: 'director',
                    orderable: true,
                    searchable: true,
                    render: function(data, type, row) {
                        return data ? convertirInicialesMayusculas(data) : 'No especificado';
                    }
                },
                {
                    data: 'evaluador',
                    name: 'evaluador',
                    orderable: true,
                    searchable: true,
                    render: function(data, type, row) {
                        return data ? convertirInicialesMayusculas(data): 'No especificado';
                    }
                },
                {
                    data: 'autores',
                    name: 'autores',
                    orderable: false,
                    searchable: true,
                    render: function(data, type, row) {
                        return data ? convertirInicialesMayusculas(data) : 'No especificado';
                    }
                },
                {
                    data:'inicio_tg',
                    name:'inicio_tg',
                    orderable: true,
                    searchable: true,
                    sortable: true,
                    render: function(data, type, row) {
                        return data ? data : 'No especificado';
                    }
                },
                {
                    data:'aprobacion_propuesta',
                    name:'aprobacion_propuesta',
                    orderable: true,
                    searchable: true,
                    sortable: true,
                    render: function(data, type, row) {
                        return data ? data : 'No especificado';
                    }
                },
                {
                    data:'final_tg',
                    name:'final_tg',
                    orderable: true,
                    searchable: true,
                    sortable: true,
                    render: function(data, type, row) {
                        return data ? data : 'No especificado';
                    }
                },
                {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false,
                    searchable: false,
                    sortable: false
                }
            ]);
        });
    </script>
    @endpush
</x-app-layout>