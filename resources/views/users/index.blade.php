<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de <span class="bg-uts-500 text-gray-800 font-bold me-2 px-2.5 py-0.5 rounded uppercase">Usuarios</span>
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

        .btn-roles {
            background-color: #6b7280;
            color: white;
        }

        .btn-roles:hover {
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
            .btn-create-item {
                margin-top: 1rem !important;
            }

            .container-actions {
                justify-content: flex-start !important;
            }

            .title-roles {
                text-align: center;
            }

            .separator-title {
                display: none;
            }

            .space-title {
                display: block !important;
            }
        }

        #userModal,
        #rolesModal {
            visibility: hidden;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
        }

        #userModal.show,
        #rolesModal.show {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5) !important;
            transition: background-color 0.3s ease;
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
            Gestión de <span class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Usuarios</span>
        </h2>
        @can('create_user')
        <button
            onclick="openCreateUserModal()"
            class="bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg flex items-center shadow btn-create-item">
            <i class="fas fa-plus mr-2"></i> Crear Nuevo Usuario
        </button>
        @endcan
    </div>

    <div class="p-4">
        @can("list_users")
        <table id="usersTable" class="w-full">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
        @endcan
    </div>

    <!-- Modal para Crear/Editar Usuario -->
    <div id="userModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" onclick="closeUserModal()"></div>
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative">
                <button class="modal-close-btn-custom" onclick="closeUserModal()">
                    &times;
                </button>
                <form id="userForm" class="p-6 mt-2">
                    @csrf
                    <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="formTitle"></p>
                    <input type="hidden" id="userId" name="id">

                    <div class="mb-4">
                        <label for="name" class="block font-medium text-sm text-gray-700">
                            <i class="fas fa-user mr-2 text-gray-500"></i>Nombre
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Ingrese el nombre del usuario"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                        <span id="nameError" class="text-red-500 text-sm"></span>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block font-medium text-sm text-gray-700">
                            <i class="fas fa-envelope mr-2 text-gray-500"></i>Email
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Ingrese el email del usuario"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                        <span id="emailError" class="text-red-500 text-sm"></span>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block font-medium text-sm text-gray-700">
                            <i class="fa-solid fa-lock mr-2 text-gray-500"></i>Contraseña
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Ingrese la contraseña de usuario"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                        <span id="passwordError" class="text-red-500 text-sm"></span>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button
                            type="button"
                            onclick="closeUserModal()"
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

    <!-- Modal para Gestionar Roles -->
    <div id="rolesModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" onclick="closeRolesModal()"></div>
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative">
                <button class="modal-close-btn-custom" onclick="closeRolesModal()">
                    &times;
                </button>
                <form id="rolesForm" class="p-6 mt-2">
                    @csrf
                    <p class="text-2xl font-bold mb-4 title-roles">Roles del <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Usuario</span></p>
                    <div id="rolesList" class="space-y-2">
                    </div>
                    <div class="flex justify-end space-x-2 mt-4">
                        <button
                            type="button"
                            onclick="closeRolesModal()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                            <svg id="loadingSpinnerRoles-guardar" style="margin: 4px 8px 4px -2px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
            initializeDataTable('#usersTable', '{{ route("users.data") }}',
                [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'roles',
                        name: 'roles',
                        render: function(data, type, row) {
                            if (typeof row.roles === 'string') {
                                try {
                                    row.roles = JSON.parse(row.roles);
                                } catch (e) {
                                    return `<span class="bg-red-500 text-white text-sm px-3 py-1 rounded-full mr-1 shadow">
                                        Sin roles
                                    </span>`;
                                }
                            }

                            if (row.roles != '' && Array.isArray(row.roles)) {
                                return row.roles.map(role =>
                                    `<div style="margin: 0.5rem 0;">
                                        <span class="bg-uts-500 text-white text-sm px-3 py-1 rounded-full mr-1 shadow">${role.name}</span>
                                    </div>`
                                ).join('');
                            }

                            return `<span class="bg-red-500 text-white text-sm px-3 py-1 rounded-full mr-1 shadow">
                                        Sin roles
                                    </span>`;
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]);
        });

        function openCreateUserModal() {
            $('#formTitle').html(`Crear nuevo <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Usuario</span>`);
            $('#userId').val('');
            $('#name').val('');
            $('#email').val('');
            $('#password').val('');
            $('#nameError').text('');
            $('#emailError').text('');
            $('#passwordError').text('');
            $('#userModal').addClass('show');
        }

        function editUser(id) {
            const route = "{{ route('users.edit', ['id' => ':id']) }}".replace(':id', id);

            $.get(route, function(data) {
                $('#formTitle').html(`Editar <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Usuario</span>`);
                $('#userId').val(data.id);
                $('#name').val(data.name);
                $('#email').val(data.email);
                $('#password').val('');
                $('#nameError').text('');
                $('#emailError').text('');
                $('#passwordError').text('');
                $('#userModal').addClass('show');
            });
        }

        function viewRoles(id) {
            const route = "{{ route('users.roles', ['id' => ':id']) }}".replace(':id', id);
            $.get(route, function(data) {
                let rolesHtml = '';
                data.roles.forEach(role => {
                    const checked = data.userRoles.includes(role.id) ? 'checked' : '';
                    rolesHtml += `
                        <div class="flex items-center">
                            <input type="checkbox" id="role_${role.id}" name="roles[]" value="${role.id}" ${checked}
                                class="rounded border-gray-300 text-uts-500 shadow-sm focus:border-uts-500 focus:ring focus:ring-uts-500 focus:ring-opacity-50">
                            <label for="role_${role.id}" class="ml-2 block text-sm text-gray-900">
                                ${role.name}
                            </label>
                        </div>`;
                });
                $('#rolesList').html(rolesHtml);
                $('#rolesModal').addClass('show');
                currentUserId = id;
            });
        }

        $('#userForm').on('submit', function(e) {
            e.preventDefault();
            const url = $('#userId').val() ? "{{ route('users.update', ['id' => ':id']) }}".replace(':id', $('#userId').val()) : "{{  route('users.store') }}";
            const method = $('#userId').val() ? 'PUT' : 'POST';
            const loadingSpinner = document.getElementById(`loadingSpinner-guardar`);
            loadingSpinner.classList.remove('hidden');

            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                success: function(response) {
                    $('#usersTable').DataTable().ajax.reload();
                    closeUserModal();
                    showToast('Usuario guardado exitosamente');
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON.errors;
                    $('#nameError').text(errors?.name?.[0] || '');
                    $('#emailError').text(errors?.email?.[0] || '');
                    $('#passwordError').text(errors?.password?.[0] || '');
                },
                complete: function() {
                    loadingSpinner.classList.add('hidden');
                }
            });
        });

        $('#rolesForm').on('submit', function(e) {
            e.preventDefault();
            const loadingSpinner = document.getElementById(`loadingSpinnerRoles-guardar`);
            loadingSpinner.classList.remove('hidden');

            $.ajax({
                url: "{{ route('users.updateRoles', ['id' => ':id']) }}".replace(':id', currentUserId),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#usersTable').DataTable().ajax.reload();
                    closeRolesModal();
                    showToast('Roles actualizados exitosamente');
                },
                error: function(error) {
                    showToast('Error al actualizar los roles', 'error');
                },
                complete: function() {
                    loadingSpinner.classList.add('hidden');
                }
            });
        });

        function deleteUser(id) {
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
                        url: "{{ route('users.destroy', ['id' => ':id']) }}".replace(':id', id),
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#usersTable').DataTable().ajax.reload();
                            showToast('Usuario eliminado exitosamente');
                        },
                        error: function(error) {
                            showToast('Error al eliminar el usuario', 'error');
                        }
                    });
                }
            });
        }

        function closeUserModal() {
            $('#userModal').removeClass('show');
        }

        function closeRolesModal() {
            $('#rolesModal').removeClass('show');
        }

        let currentUserId = null;
    </script>
    @endpush
</x-app-layout>