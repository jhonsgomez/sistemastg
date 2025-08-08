<x-app-layout>
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

        #warningModal {
            visibility: hidden;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
        }

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

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="flex flex-col sm:flex-row justify-center items-center p-4 border-b">
        <h2 class="text-2xl text-center font-bold text-gray-800 uppercase">
            Sistema - Trabajos de <span class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Grado</span>
        </h2>
    </div>
    <div class="p-4">
        <p class="text-gray-600 mt-2 mb-4">Bienvenido al portal de sistemas trabajo de grado, aquí podrás acceder a:</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="{{ route('profile.show') }}" class="border bg-white p-6 text-gray-600 rounded-lg shadow-lg hover:bg-uts-500 hover:text-white transition ease-in-out duration-200 flex flex-col items-center justify-center">
                <i class="fa-solid fa-id-card text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Mi Perfil</span>
                <p class="text-center mt-2 text-sm">Cambia tu contraseña, cierra otras sesiones y actualiza tu información.</p>
            </a>

            <a href="{{ route('banco.index') }}" class="border bg-white p-6 text-gray-600 rounded-lg shadow-lg hover:bg-uts-500 hover:text-white transition ease-in-out duration-200 flex flex-col items-center justify-center">
                <i class="fa-solid fa-book text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Banco de ideas</span>
                <p class="text-center mt-2 text-sm">Accede al banco de ideas del periodo actual propuesto por los docentes.</p>
            </a>

            @can('view_propuestas_banco')
            <a href="{{ route('propuestas.index') }}" class="border bg-white p-6 text-gray-600 rounded-lg shadow-lg hover:bg-uts-500 hover:text-white transition ease-in-out duration-200 flex flex-col items-center justify-center">
                <i class="fa-solid fa-file-invoice text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Propuestas Banco</span>
                <p class="text-center mt-2 text-sm">Propone nuevas ideas de proyectos para el banco del periodo actual.</p>
            </a>
            @endcan

            @if (!auth()->user()->hasRole(['docente']))
            @can('view_proyectos_grado')
            <a href="{{ route('proyectos.index') }}" class="border bg-white p-6 text-gray-600 rounded-lg shadow-lg hover:bg-uts-500 hover:text-white transition ease-in-out duration-200 flex flex-col items-center justify-center">
                <i class="fa-solid fa-user-graduate text-4xl mb-4"></i>
                @if (auth()->user()->hasRole('estudiante'))
                <span class="text-center font-bold text-lg">Mis Proyectos</span>
                <p class="text-center mt-2 text-sm">Accede al modúlo de tus proyectos en curso, crear nuevos proyectos y segumiento.</p>
                @else
                <span class="text-center font-bold text-lg">Proyectos de grado</span>
                <p class="text-center mt-2 text-sm">Accede al modúlo de proyectos en curso y segumiento de los mismos.</p>
                @endif
            </a>
            @endcan
            @endif

            <a href="{{ route('documental.index') }}" class="border bg-white p-6 text-gray-600 rounded-lg shadow-lg hover:bg-uts-500 hover:text-white transition ease-in-out duration-200 flex flex-col items-center justify-center">
                <i class="fas fa-book-open text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Base Documental</span>
                <p class="text-center mt-2 text-sm">Accede a toda la información documental organizada de manera eficiente.</p>
            </a>

            <a href="https://www.uts.edu.co/sitio/calendario-academico/" target="_blank" class="border bg-white p-6 text-gray-600 rounded-lg shadow-lg hover:bg-uts-500 hover:text-white transition ease-in-out duration-200 flex flex-col items-center justify-center">
                <i class="fas fa-calendar-alt text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Calendario Académico</span>
                <p class="text-center mt-2 text-sm">Consulta las fechas importantes y eventos académicos.</p>
            </a>

            @if (!auth()->user()->hasRole(['super_admin', 'admin']))
            <button onclick="openWarningModal()" class="border bg-white p-6 text-gray-600 rounded-lg shadow-lg hover:bg-uts-500 hover:text-white transition ease-in-out duration-200 flex flex-col items-center justify-center">
                <i class="fa-solid fa-circle-exclamation text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">PQRSD</span>
                <p class="text-center mt-2 text-sm">Realiza tu sugerencia, queja, consulta o recomendacion del sistema.</p>
            </button>
            @endif
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
                            <label for="mensaje" class="block font-medium text-md text-gray-700 mb-4">
                                Sus comentarios son importantes para nosotros:
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

    @push('scripts')
    <script src="{{ asset('js/options/warning.js') }}"></script>
    @endpush
</x-app-layout>