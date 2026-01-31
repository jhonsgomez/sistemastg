@php
    use Carbon\Carbon;

    $anio_inicio = 2025;
    $anio_actual = Carbon::now()->year;
    $mes_actual = Carbon::now()->month;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Banco de <span class="bg-uts-500 text-gray-800 font-bold me-2 px-2.5 py-0.5 rounded uppercase">Ideas</span>
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

        #reporteModal,
        #republicarModal {
            visibility: hidden;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
        }

        #reporteModal.show,
        #republicarModal.show {
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

        #republicarModal .modal-content {
            max-width: 700px !important;
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

        /* Contenedor de la tabla con scroll */
        .republicar-table-container {
            max-height: 300px;
            max-width: 100%;
            overflow-x: auto;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 0px;
        }

        /* Estilos de la tabla */
        .table-ideas-republicar {
            width: 100%;
            min-width: 800px; /* Ancho mínimo para forzar scroll horizontal si es necesario */
            border-collapse: collapse;
            font-size: 0.875rem;
            margin-top: 0px !important;
        }

        /* Encabezados fijos (sticky) */
        .table-ideas-republicar thead {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .table-ideas-republicar thead th {
            background-color: #f3f4f6;
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
            white-space: nowrap; /* Evita que el texto del header se rompa */
        }

        /* Celdas del cuerpo */
        .table-ideas-republicar tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
            vertical-align: middle;
            user-select: none !important;
        }

        /* Estilo del scrollbar (opcional, para navegadores webkit) */
        .republicar-table-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .republicar-table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .republicar-table-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .republicar-table-container::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* Columna 1: Checkbox */
        .table-ideas-republicar th:nth-child(1),
        .table-ideas-republicar td:nth-child(1) {
            width: 50px;
            min-width: 50px;
            text-align: center;
        }

        /* Columna 2: ID */
        .table-ideas-republicar th:nth-child(2),
        .table-ideas-republicar td:nth-child(2) {
            width: 180px;
            min-width: 180px;
            text-align: center;
        }

        /* Columna 3: Título de la idea */
        .table-ideas-republicar th:nth-child(3),
        .table-ideas-republicar td:nth-child(3) {
            width: 300px;
            min-width: 300px;
            white-space: normal; /* Permite que el texto haga wrap */
            line-height: 1.4;
        }

        /* Columna 4: Nivel */
        .table-ideas-republicar th:nth-child(4),
        .table-ideas-republicar td:nth-child(4) {
            width: 150px;
            min-width: 150px;
            text-align: center;
        }

        /* Columna 5: Modalidad */
        .table-ideas-republicar th:nth-child(5),
        .table-ideas-republicar td:nth-child(5) {
            width: 150px;
            min-width: 150px;
            text-align: center;
        }

        /* Columna 6: Línea de investigación */
        .table-ideas-republicar th:nth-child(6),
        .table-ideas-republicar td:nth-child(6) {
            width: 250px;
            min-width: 250px;
            white-space: normal;
            line-height: 1.4;
            text-align: center;
        }

        /* Columna 7: Docente */
        .table-ideas-republicar th:nth-child(7),
        .table-ideas-republicar td:nth-child(7) {
            width: 200px;
            min-width: 200px;
        }

        /* Columna 8: Periodo Académico */
        .table-ideas-republicar th:nth-child(8),
        .table-ideas-republicar td:nth-child(8) {
            width: 150px;
            min-width: 150px;
            text-align: center;
        }

        /* ========== ESTILOS ADICIONALES ========== */

        /* Filas alternadas (zebra) */
        .table-ideas-republicar tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        /* Hover en filas */
        .table-ideas-republicar tbody tr:hover {
            background-color: #f3f4f6;
        }

    </style>
    @endpush

    <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-b">
        <h2 class="text-2xl text-center font-bold text-gray-800">
            Banco de <span class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Ideas</span>
        </h2>
        <div class="flex justify-center items-center space-x-2 buttons-container">
            @if (auth()->user()->hasRole(['super_admin', 'admin', 'coordinador']))
                <button type="button" id="reporte" onclick="openReporteModal()"
                    class="btn-action shadow bg-green-700 hover:bg-green-800 text-white px-3 py-1 rounded-lg relative">
                    <i class="fa-solid fa-download"></i>
                </button>
                <button
                    onclick="openRepublicarModal()"
                    id="openRepublicarModal"
                    class="bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg flex items-center shadow">
                    <i class="fa-solid fa-rotate mr-2"></i>
                    Republicar ideas
                </button>
            @endif
        </div>
    </div>

    <div class="p-4">
        @can("list_banco_ideas")
        <table id="bancoTable" class="w-full">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título de la idea</th>
                    <th>Disponible</th>
                    <th>Periodo</th>
                    <th>Nivel</th>
                    <th>Docente</th>
                    <th>Correo electrónico:</th>
                    <th>Objetivo:</th>
                    <th>Línea de investigación:</th>
                    <th>Modalidad:</th>
                    @can('delete_banco_ideas')
                    <th>Acciones</th>
                    @endcan
                </tr>
            </thead>
        </table>
        @endcan
    </div>

    <!-- REPORTES DEL BANCO -->
    <div id="reporteModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeReporteModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeReporteModal()">
                        &times;
                    </button>
                    <form action="{{ route('banco.reporte') }}" method="POST" id="reporteForm" class="p-6 mt-2">
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

    <!-- REPUBLICAR IDEAS -->
    <div id="republicarModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeRepublicarModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeRepublicarModal()">
                        &times;
                    </button>
                    <div class="modal-content-main">
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="republicarTitle"></p>
                        <p class="font-medium text-sm text-gray-700 mb-6 text-start">A continuación se muestran las ideas que pueden ser republicadas, seleccione las que desea republicar para el periodo actual.</p>
                        <div class="republicar-table-container">
                            <table class="table-ideas-republicar">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="checkAll" class="cursor-pointer border-gray-300 rounded-md shadow-sm mt-1 focus:ring-uts-500 focus:border-uts-500 accent-uts-500 text-uts-500">
                                        </th>
                                        <th>ID</th>
                                        <th>Título de la idea</th>
                                        <th>Nivel</th>
                                        <th>Modalidad</th>
                                        <th>Línea de investigación</th>
                                        <th>Docente</th>
                                        <th>Periodo</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="flex justify-end space-x-2 mt-8">
                            <button
                                type="button"
                                onclick="closeRepublicarModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button
                                type="button"
                                id="btnRepublicarIdeas"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-republicar" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                    </path>
                                </svg>
                                Republicar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/options/reporte.js') }}"></script>
    <script src="{{ asset('js/options/republicar.js') }}"></script>
    <script>
        const capitalize = (str, lower = false) =>
            (lower ? str.toLowerCase() : str).replace(/(?:^|\s|["'([{])+\S/g, match => match.toUpperCase());;

        $(document).ready(function() {
            initializeDataTable('#bancoTable', '{{ route("banco.data") }}',
                [{
                        data: 'formatted_id',
                        name: 'formatted_id',
                        orderable: true,
                        sortable: true,
                    },
                    {
                        data: 'propuesta_titulo',
                        name: 'propuesta_titulo',
                        orderable: false,
                        render: function(data) {
                            return `<p style="width: 300px !important;">${data.charAt(0).toUpperCase() + data.slice(1).toLowerCase()}</p>`;
                        }
                    },
                    {
                        data: 'propuesta_disponible',
                        name: 'propuesta_disponible',
                        orderable: false,
                        sortable: true,
                        render: function(data) {
                            let renderHtml = `<p style="text-align: left;">`;
                            if (data === 'true') {
                                renderHtml += `<span class="shadow bg-green-100 text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded border border-green-300">Sí</span>`;
                            } else {
                                renderHtml += `<span class="shadow bg-red-100 text-red-800 text-sm font-medium me-2 px-2 py-0.5 rounded border border-red-300">No</span>`;
                            }
                            renderHtml += `</p>`;
                            return renderHtml;
                        }
                    },
                    {
                        data: 'propuesta_periodo',
                        name: 'propuesta_periodo',
                        orderable: false,
                        sortable: true,
                    },
                    {
                        data: 'propuesta_nivel',
                        name: 'propuesta_nivel',
                        orderable: false
                    },
                    {
                        data: 'propuesta_docente',
                        name: 'propuesta_docente',
                        orderable: false,
                        sortable: true,
                        render: function(data) {
                            return `<p style="width: 250px !important;">${capitalize(data)}</p>`;
                        }
                    },
                    {
                        data: 'propuesta_correo',
                        name: 'propuesta_correo',
                        orderable: false,
                        render: function(data) {
                            return `<span class="text-blue-500 hover:underline">${data}</span>`;
                        }
                    },
                    {
                        data: 'propuesta_objetivo',
                        name: 'propuesta_objetivo',
                        orderable: false,
                        render: function(data) {
                            return `<p>${data.charAt(0).toUpperCase() + data.slice(1).toLowerCase()}</p>`;
                        }
                    },
                    {
                        data: 'propuesta_linea_investigacion',
                        name: 'propuesta_linea_investigacion',
                        orderable: false
                    },
                    {
                        data: 'propuesta_modalidad',
                        name: 'propuesta_modalidad',
                        orderable: false
                    },
                    {
                        data: 'acciones',
                        name: 'acciones',
                        orderable: false,
                        searchable: false
                    }
                ]);
        });

        function deleteSolicitud(id) {
            Swal.fire({
                heightAuto: false,
                title: '¿Está seguro?',
                text: "No podrá revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#C1D631',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    loaderGeneral.classList.replace('hidden', 'flex');

                    $.ajax({
                        url: "{{ route('banco.destroy', ['id' => 'ID_PROPUESTA']) }}".replace('ID_PROPUESTA', id),
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#bancoTable').DataTable().ajax.reload();
                            showToast('Propuesta eliminada exitosamente');
                        },
                        error: function(error) {
                            showToast('Error al eliminar la propuesta', 'error');
                        },
                        complete: function() {
                            loaderGeneral.classList.replace('flex', 'hidden');
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-app-layout>