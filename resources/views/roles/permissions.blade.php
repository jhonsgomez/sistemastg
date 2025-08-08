<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Permisos del <span class="bg-uts-500 text-gray-800 font-bold me-2 px-2.5 py-0.5 rounded uppercase">{{ $role->name }}</span>
            </h2>
        </div>
    </x-slot>

    @push('styles')
    <style>
        @media screen and (max-width: 640px) {
            .btn-create-item {
                margin-top: 1rem !important;
            }
        }
    </style>
    @endpush

    <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-b">
        <h2 class="text-2xl text-center font-bold text-gray-800">
            Permisos del <span class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">{{ $role->name }}</span>
        </h2>
        <button
            onclick="window.location.href=`{{ route('roles.index') }}`"
            class="bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg flex items-center shadow btn-create-item">
            <i class="fa-solid fa-rotate-left mr-2"></i> Volver
        </button>
    </div>

    <form id="permissionsForm" class="space-y-8 mx-5">
        @csrf
        @foreach($groupedPermissions as $group => $permissions)
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4 flex items-center">
                <i class="fas fa-layer-group mr-2 text-uts-500"></i>
                {{ ucfirst($group) }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($permissions as $permission)
                <div class="flex items-center space-x-3 p-4 border rounded-lg hover:bg-gray-50 transition-colors shadow">
                    <input type="checkbox"
                        id="permission_{{ $permission->id }}"
                        name="permissions[]"
                        value="{{ $permission->id }}"
                        {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                        class="w-4 h-4 text-uts-500 border-gray-300 rounded focus:ring-uts-500">
                    <label for="permission_{{ $permission->id }}" class="flex items-center space-x-2 text-sm font-medium text-gray-700 cursor-pointer">
                        <span>{{ str_replace('_', ' ', $permission->name) }}</span>
                        @if(strpos($permission->name, 'create') !== false)
                        <i class="fas fa-plus text-green-500"></i>
                        @elseif(strpos($permission->name, 'list') !== false)
                        <i class="fas fa-list text-blue-500"></i>
                        @elseif(strpos($permission->name, 'edit') !== false || strpos($permission->name, 'update') !== false)
                        <i class="fas fa-edit text-yellow-500"></i>
                        @elseif(strpos($permission->name, 'delete') !== false)
                        <i class="fas fa-trash text-red-500"></i>
                        @endif
                    </label>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="flex justify-end mt-6 space-x-3">
            <button type="button"
                onclick='window.location.href=`{{ route("roles.index") }}`'
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                Cancelar
            </button>
            <button type="submit"
                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-save mr-2" id="icon-save"></i>
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
                Guardar Permisos
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#permissionsForm').on('submit', function(e) {
                e.preventDefault();
                const iconSave = document.getElementById(`icon-save`);
                iconSave.classList.add('hidden');
                const loadingSpinner = document.getElementById(`loadingSpinner-guardar`);
                loadingSpinner.classList.remove('hidden');

                let selectedPermissions = [];
                $('input[name="permissions[]"]:checked').each(function() {
                    selectedPermissions.push($(this).val());
                });

                $.ajax({
                    url: '{{ route("roles.updatePermissions", ["id" => $role->id]) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        permissions: selectedPermissions
                    },
                    success: function(response) {
                        showToast('Permisos actualizados correctamente');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            heightAuto: false,
                            title: 'Error',
                            text: 'Hubo un error al actualizar los permisos',
                            icon: 'error',
                            confirmButtonColor: '#dc2626'
                        });
                    },
                    complete: function() {
                        iconSave.classList.remove('hidden');
                        loadingSpinner.classList.add('hidden');
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>