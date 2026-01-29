<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de <span class="bg-uts-500 text-gray-800 font-bold me-2 px-2.5 py-0.5 rounded uppercase">Permisos</span>
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

        .btn-edit {
            background-color: #C1D631;
            color: white;
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

        @media screen and (max-width: 640px) {
            .btn-create-item {
                margin-top: 1rem !important;
            }

            .container-actions {
                justify-content: flex-start !important;
            }
        }

        #permissionModal {
            visibility: hidden;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
        }

        #permissionModal.show {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        .modal-overlay {
            background-color: rgba(0, 0, 0, 0);
            transition: background-color 0.3s ease;
        }

        #permissionModal.show .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5) !important;
            transition: background-color 0.3s ease-out !important;
        }

        #permissionModal:target .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5) !important;
        }

        .modal-content {
            max-width: 600px !important;
            width: 100% !important;
            padding: 1rem !important;
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
            Gestión de <span class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Permisos</span>
        </h2>
        @can('create_permission')
        <button
            onclick="openCreatePermissionModal()"
            class="bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg flex items-center shadow btn-create-item">
            <i class="fas fa-plus mr-2"></i> Crear Nuevo Permiso
        </button>
        @endcan
    </div>

    <div class="p-4">
        @can("list_permissions")
        <table id="permissionsTable" class="min-w-full bg-white">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
        @endcan
    </div>

    <!-- Modal Crear/Editar Permiso -->
    <div id="permissionModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" onclick="closePermissionModal()"></div>
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative">
                <button class="modal-close-btn-custom" onclick="closePermissionModal()">
                    &times;
                </button>
                <form id="permissionForm" class="p-6 mt-2">
                    @csrf
                    <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="formTitle"></p>

                    <input type="hidden" id="permissionId" name="id">

                    <!-- Resto del formulario igual -->
                    <div class="mb-4">
                        <label for="name" class="block font-medium text-sm text-gray-700">
                            <i class="fas fa-tag mr-2 text-gray-500"></i>Nombre del Permiso
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Ingrese el nombre del permiso"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                        <span id="nameError" class="text-red-500 text-sm"></span>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block font-medium text-sm text-gray-700">
                            <i class="fas fa-comment-dots mr-2 text-gray-500"></i>Descripción
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            placeholder="Describa el propósito del rol"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"></textarea>
                        <span id="descriptionError" class="text-red-500 text-sm"></span>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button
                            type="button"
                            onclick="closePermissionModal()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                            <svg id="loadingSpinner-guardar" style="margin: 4px 8px 4px -2px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                <path
                                    d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                    stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path
                                    d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                    stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                </path>
                            </svg>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            initializeDataTable('#permissionsTable', '{{ route("permissions.data") }}',
                [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="flex items-center justify-center container-actions">
                                    @can('edit_permission')
                                        <button onclick="editPermission(${row.id})" class="btn-action btn-edit hover:bg-uts-800 shadow pointer">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endcan
                                    @can('delete_permission')
                                        <button onclick="deletePermission(${row.id})" class="btn-action btn-delete shadow pointer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            `;
                        }
                    }
                ],
            )
        });

        function openCreatePermissionModal() {
            $('#formTitle').html(`Crear nuevo <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Permiso</span>`);
            $('#permissionId').val('');
            $('#name').val('');
            $('#description').val('');
            $('#nameError').text('');
            $('#descriptionError').text('');
            $('#permissionModal').addClass('show');
        }

        function editPermission(id) {
            const route = "{{ route('permissions.edit', ['id' => 'ID_PERMISSION']) }}".replace('ID_PERMISSION', id);
            
            $.get(route, function(data) {
                $('#formTitle').html(`Editar permiso <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">` + data.name + `</span>`);
                $('#permissionId').val(data.id);
                $('#name').val(data.name);
                $('#description').val(data.description);
                $('#nameError').text('');
                $('#descriptionError').text('');
                $('#permissionModal').addClass('show');
            });
        }

        $('#permissionForm').on('submit', function(e) {
            e.preventDefault();

            const url = $('#permissionId').val() ? "{{ route('permissions.update' , ['id' => 'ID_PERMISSION']) }}".replace('ID_PERMISSION', $('#permissionId').val()) : '{{ route("permissions.store") }}';
            const method = $('#permissionId').val() ? 'PUT' : 'POST';
            const loadingSpinner = document.getElementById(`loadingSpinner-guardar`);
            loadingSpinner.classList.remove('hidden');

            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                success: function(response) {
                    $('#permissionsTable').DataTable().ajax.reload();
                    closePermissionModal();
                    showToast('Permiso guardado exitosamente');
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON.errors;
                    $('#nameError').text(errors?.name?.[0] || '');
                    $('#descriptionError').text(errors?.description?.[0] || '');
                },
                complete: function() {
                    loadingSpinner.classList.add('hidden');
                }
            });
        });

        function closePermissionModal() {
            $('#permissionModal').removeClass('show');
        }

        function deletePermission(id) {
            Swal.fire({
                heightAuto: false,
                title: '¿Está seguro?',
                text: "No podrá revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#C1D631',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('permissions.destroy', ['id' => 'ID_PERMISSION']) }}".replace('ID_PERMISSION', id),
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#permissionsTable').DataTable().ajax.reload();
                            showToast('Permiso eliminado exitosamente');
                        },
                        error: function(error) {
                            showToast('Error al eliminar el permiso', 'error');
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-app-layout>