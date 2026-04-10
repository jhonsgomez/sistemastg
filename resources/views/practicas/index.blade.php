
<!---Este es el que trae el menu -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Practicas <span class="bg-uts-500 text-gray-800 font-bold me-2 px-2.5 py-0.5 rounded uppercase">Empresariales</span>
        </h2>
    </x-slot>

    <!--Dasboard-->
    <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-b">
        <h2 class="text-2xl text-center font-bold text-gray-800">
            Mis Prácticas <span class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Empresariales</span>
        </h2>
    

 <!-- ESTE ES EL ICONO ROJO -->
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
            <!--- AQUI SOLO VA LA VARIABLE $fechas--->
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
    </div>

        <div id="createModal" class="hidden fixed z-50 inset-0 overflow-y-auto">
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
                            <p class="text-sm mb-2">En este formulario el estudiante registrará la información necesaria para la solicitud de prácticas empresariales.</p>
                            <p class="text-sm mb-6"><strong>NOTA: </strong>El estudiante debe tener aprobado el 90% de los créditos (Tecnología: 97 / Profesional: 65).</p>
                            <p class="text-sm mb-6"><strong>Convenios: </strong>Verifique si la empresa tiene convenio vigente en la pagina de la Ori :<a href="https://oriapp.uts.edu.co/activities_guest" target="_blank"  class="text-uts-500 underline hover:text-uts-800"> Consultar convenios aquí </a></p>
                            
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

    @push('scripts')
    <script>
        function openCreateModal() {
            var añoActual = new Date().getFullYear();
            var mesActual = new Date().getMonth() + 1;
            var numero = mesActual <= 6 ? 1 : 2;
            var periodo_academico = añoActual + '-' + numero;

            $('#formTitle').html(`Solicitud de <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Prácticas Empresariales</span>`);

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
    @endpush


</x-app-layout>