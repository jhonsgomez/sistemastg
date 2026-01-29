<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Configuraciones <span class="bg-uts-500 text-gray-800 font-bold me-2 px-2.5 py-0.5 rounded uppercase">generales</span>
        </h2>
    </x-slot>

    @push('styles')
    <style>
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

        #settingFechasModal {
            visibility: hidden;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
        }

        #settingFechasModal.show {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        #settingBackupsModal {
            visibility: hidden;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
        }

        #settingBackupsModal.show {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5) !important;
            transition: background-color 0.3s ease;
        }

        .modal-content {
            max-width: 700px !important;
            width: 100% !important;
            padding: 2rem 3rem !important;
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
    </style>
    @endpush

    <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-b">
        <h2 class="text-2xl text-center font-bold text-gray-800">
            Configuración <span class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">general</span>
        </h2>
    </div>

    <div class="p-4">
        <p class="text-gray-600 mt-2 mb-8">Aquí podrás ajustar algunos modulos generales del sistema para modificar su comportamiento:</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <button
                type="button"
                id="setting-fechas"
                onclick="openSettingFechasModal()"
                class="border bg-white p-6 text-gray-600 rounded-lg shadow-lg hover:bg-uts-500 hover:text-white transition ease-in-out duration-200 flex flex-col items-center justify-center">
                <i class="fas fa-calendar-alt text-4xl mb-4"></i>
                <svg id="loadingSpinner-setting-fechas" class="hidden mb-4 w-10 h-10 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                    <path
                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round">
                    </path>
                    <path
                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                    </path>
                </svg>
                <span class="text-center font-bold text-lg">Fechas del periodo</span>
                <p class="text-center mt-2 text-sm">Ajusta las fechas del periodo acdémico actual.</p>
            </button>

            <button
                type="button"
                id="setting-backups"
                onclick="openSettingBackupsModal()"
                class="border bg-white p-6 text-gray-600 rounded-lg shadow-lg hover:bg-uts-500 hover:text-white transition ease-in-out duration-200 flex flex-col items-center justify-center">
                <i class="fas fa-database text-4xl mb-4"></i>
                <svg id="loadingSpinner-setting-backups" class="hidden mb-4 w-10 h-10 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                    <path
                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.80101 49.8132 6.66488 46.6163 5.20749 43.0978C3.75011 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round">
                    </path>
                    <path
                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                    </path>
                </svg>
                <span class="text-center font-bold text-lg">Copia de seguridad</span>
                <p class="text-center mt-2 text-sm">Descargar un backup del sistema para restauración.</p>
            </button>
        </div>
    </div>

    <div id="settingFechasModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeSettingFechasModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeSettingFechasModal()">
                        &times;
                    </button>
                    <form class="p-6 mt-2" id="settingFechasForm">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="settingFechasTitle"></p>

                        <p class="mt-10 mb-4 text-gray-700 font-medium"><i class="fa-solid fa-calendar-days mr-2 text-gray-700"></i> Banco de ideas</p>

                        <input type="hidden" name="periodo_academico" id="periodo_academico">

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center gap-4">
                            <label for="fecha_inicio_banco" class="font-medium text-sm text-gray-700 flex items-center">
                                Inicio de propuestas de banco:
                            </label>
                            <div class="flex-1">
                                <input
                                    type="date"
                                    name="fecha_inicio_banco"
                                    id="fecha_inicio_banco"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm flex-1 focus:ring-uts-500 focus:border-uts-500">
                                <span id="fecha_inicio_bancoError" class="text-red-500 text-sm"></span>
                            </div>
                        </div>
                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center gap-4">
                            <label for="fecha_fin_banco" class="font-medium text-sm text-gray-700 flex items-center">
                                Fin de propuestas de banco:
                            </label>
                            <div class="flex-1">
                                <input
                                    type="date"
                                    name="fecha_fin_banco"
                                    id="fecha_fin_banco"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm flex-1 focus:ring-uts-500 focus:border-uts-500">
                                <span id="fecha_fin_bancoError" class="text-red-500 text-sm"></span>
                            </div>
                        </div>

                        <p class="mt-6 mb-4 text-gray-700 font-medium"><i class="fa-solid fa-calendar-days mr-2 text-gray-700"></i> Proyectos de grado</p>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center gap-4">
                            <label for="fecha_inicio_proyectos" class="font-medium text-sm text-gray-700 flex items-center">
                                Inicio de nuevos proyectos de grado:
                            </label>
                            <div class="flex-1">
                                <input
                                    type="date"
                                    name="fecha_inicio_proyectos"
                                    id="fecha_inicio_proyectos"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm flex-1 focus:ring-uts-500 focus:border-uts-500">
                                <span id="fecha_inicio_proyectosError" class="text-red-500 text-sm"></span>
                            </div>
                        </div>
                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center gap-4">
                            <label for="fecha_fin_proyectos" class="font-medium text-sm text-gray-700 flex items-center">
                                Fin de nuevos proyectos de grado:
                            </label>
                            <div class="flex-1">
                                <input
                                    type="date"
                                    name="fecha_fin_proyectos"
                                    id="fecha_fin_proyectos"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm flex-1 focus:ring-uts-500 focus:border-uts-500">
                                <span id="fecha_fin_proyectosError" class="text-red-500 text-sm"></span>
                            </div>
                        </div>

                        <p class="mt-6 mb-4 text-gray-700 font-medium"><i class="fa-solid fa-calendar-days mr-2 text-gray-700"></i> Procedimientos de grado</p>

                        <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
                            <label for="fecha_aprobacion_propuesta" class="font-medium text-sm text-gray-700 flex items-center">
                                Fin de aprobación de propuesta:
                            </label>
                            <div class="flex-1">
                                <input
                                    type="date"
                                    name="fecha_aprobacion_propuesta"
                                    id="fecha_aprobacion_propuesta"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm flex-1 focus:ring-uts-500 focus:border-uts-500">
                                <span id="fecha_aprobacion_propuestaError" class="text-red-500 text-sm"></span>
                            </div>
                        </div>

                        <div class="mt-10 flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeSettingFechasModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button
                                id="settingFechasButton"
                                type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-settingFechas" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

    <div id="settingBackupsModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeSettingBackupsModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeSettingBackupsModal()">
                        &times;
                    </button>
                    <form class="p-6 mt-2" id="settingBackupsForm" action="{{ route('ajustes.backups') }}" method="POST">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="settingBackupsTitle">Copia de seguridad</p>

                        <p class="mt-10 mb-4 text-gray-700 font-medium"><i class="fa-solid fa-database mr-2 text-gray-700"></i> Descarga de backup</p>

                        <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
                            <label for="backup_type" class="font-medium text-sm text-gray-700 flex items-center">
                                Tipo de backup:
                            </label>
                            <div class="flex-1">
                                <select name="backup_type" id="backup_type" required
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm flex-1 focus:ring-uts-500 focus:border-uts-500">
                                    <option value="" disabled selected>Seleccione un tipo de backup</option>
                                    <option value="db">Base de datos</option>
                                </select>
                                <span id="backup_typeError" class="text-red-500 text-sm"></span>
                            </div>
                        </div>

                        <div class="mt-10 flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeSettingBackupsModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button
                                id="settingBackupsButton"
                                type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-settingBackups" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.80101 49.8132 6.66488 46.6163 5.20749 43.0978C3.75011 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white"></path>
                                </svg>
                                Descargar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function getPeriodoActual() {
            const añoActual = new Date().getFullYear();
            const mesActual = new Date().getMonth() + 1;
            const numero = mesActual <= 6 ? 1 : 2;
            const periodo_academico = añoActual + '-' + numero;

            return periodo_academico
        }

        async function openSettingFechasModal() {
            let periodo_academico = getPeriodoActual();
            let route = "{{ route('fechas.info', ['periodo' => 'PERIODO_ACADEMICO']) }}";
            route = route.replace('PERIODO_ACADEMICO', periodo_academico);

            const button = document.getElementById(`setting-fechas`);
            const loadingSpinner = document.getElementById(`loadingSpinner-setting-fechas`);

            button.querySelector('i').classList.add('hidden');
            loadingSpinner.classList.remove('hidden');

            $('#settingFechasTitle').html(`Fechas del periodo <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">${periodo_academico}</span>`);

            $.get(route, async function(data) {
                $('#periodo_academico').val(periodo_academico);
                $('#fecha_inicio_banco').val(data?.fecha_inicio_banco || '');
                $('#fecha_fin_banco').val(data?.fecha_fin_banco || '') || '';
                $('#fecha_inicio_proyectos').val(data?.fecha_inicio_proyectos || '');
                $('#fecha_fin_proyectos').val(data?.fecha_fin_proyectos || '');
                $('#fecha_aprobacion_propuesta').val(data?.fecha_aprobacion_propuesta || '');

                $('#fecha_inicio_bancoError').text('');
                $('#fecha_fin_bancoError').text('');
                $('#fecha_inicio_proyectosError').text('');
                $('#fecha_fin_proyectosError').text('');
                $('#fecha_aprobacion_propuestaError').text('');

                $('#settingFechasModal').addClass('show');

                loadingSpinner.classList.add('hidden');
                button.querySelector('i').classList.remove('hidden');
            });
        }

        $('#settingFechasForm').on('submit', function(e) {
            e.preventDefault();

            const button = document.getElementById(`settingFechasButton`);
            const loadingSpinner = document.getElementById(`loadingSpinner-settingFechas`);

            const url = "{{ route('ajustes.fechas') }}";
            const method = 'POST';

            Swal.fire({
                heightAuto: false,
                title: '¿Está seguro?',
                text: "Esta accion puede revertirse más adelante",
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
                            closeSettingFechasModal();
                            showToast('Fechas actualizadas correctamente');
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON.errors;
                            $('#fecha_inicio_bancoError').text(errors?.fecha_inicio_banco?.[0] || '');
                            $('#fecha_fin_bancoError').text(errors?.fecha_fin_banco?.[0] || '');
                            $('#fecha_inicio_proyectosError').text(errors?.fecha_inicio_proyectos?.[0] || '');
                            $('#fecha_fin_proyectosError').text(errors?.fecha_fin_proyectos?.[0] || '');
                            $('#fecha_aprobacion_propuestaError').text(errors?.fecha_aprobacion_propuesta?.[0] || '');
                        },
                        complete: function() {
                            loaderGeneral.classList.replace('flex', 'hidden');
                            loadingSpinner.classList.add('hidden');
                        }
                    });
                }
            });
        });

        function closeSettingFechasModal() {
            $('#settingFechasModal').removeClass('show');
        }

        async function openSettingBackupsModal() {
            const button = document.getElementById(`setting-backups`);
            const loadingSpinner = document.getElementById(`loadingSpinner-setting-backups`);

            button.querySelector('i').classList.add('hidden');
            loadingSpinner.classList.remove('hidden');

            $('#settingBackupsTitle').html(`Copia de <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">seguridad</span>`);
            $('#backup_type').val('');

            $('#backup_typeError').text('');

            $('#settingBackupsModal').addClass('show');

            loadingSpinner.classList.add('hidden');
            button.querySelector('i').classList.remove('hidden');
        }

        function closeSettingBackupsModal() {
            $('#settingBackupsModal').removeClass('show');
        }
    </script>
    @endpush
</x-app-layout>