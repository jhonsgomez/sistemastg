<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Seguimiento de <span
                class="bg-uts-500 text-gray-800 font-bold me-2 px-2.5 py-0.5 rounded uppercase">Prácticas
                Empresariales</span>
        </h2>
    </x-slot>

    <div class="p-4">
        <div class="mb-4">
            <a href="{{ route('practicas.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                ← Volver al listado
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4">Práctica #{{ $practica->id }} - Estado: {{ $estado }}</h3>
            <p>Aquí se mostrarán los formularios según la fase actual.</p>
            <!-- Aquí irán los botones y formularios para cada fase -->
        </div>
    </div>
    <!-- Estilos -->
    @push('styles')
        <style>
            .mt-soporte-original {
                margin-top: 1.5rem;
            }

            .mt-soporte {
                margin-top: -2.5rem;
            }

            @media screen and (max-width: 640px) {
                .buttons-container {
                    padding-top: 1rem !important;
                }

                .mt-soporte {
                    margin-top: -4.5rem;
                }

                .mt-titulo {
                    margin-top: -1.5rem;
                }
            }

            .card-fase {
                cursor: default;
            }

            .card-activated {
                cursor: pointer !important;
                background-color: #C1D631 !important;
                color: white !important;
            }

            .card-activated-animated {
                animation: pulse-animation 2s infinite;
            }

            @keyframes pulse-animation {
                0% {
                    box-shadow: 0 0 0 0px rgba(0, 0, 0, 0.2);
                }

                100% {
                    box-shadow: 0 0 0 20px rgba(0, 0, 0, 0);
                }
            }

            .btn-action {
                padding: 6px 12px;
                margin: 0 4px;
                transition: all 0.3s ease;
                border-radius: 0.375rem;
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

            #vencidoModal,
            #configModal,
            #fase1AdminDetailsModal,
            #fase1AdminModal,
            #fase1EstudianteModal,
            #fase2EstudianteModal,
            #fase2DetailsModal,
            #fase2AdminModal,
            #fase3DetailsModal,
            #fase3AprobarModal,
            #fase4EstudianteModal,
            #fase4DetailsModal,
            #fase4AdminModal,
            #fase5DetailsModal,
            #fase5AprobarModal,
            #faseFinalDetailsModal,
            #icfesEstudianteModal,
            #icfesAdminModal,
            #calendarModal,
            #warningModal {
                visibility: hidden;
                opacity: 0;
                transform: translateY(-10px);
                transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
            }

            #vencidoModal.show,
            #configModal.show,
            #fase1AdminDetailsModal.show,
            #fase1AdminModal.show,
            #fase1EstudianteModal.show,
            #fase2EstudianteModal.show,
            #fase2DetailsModal.show,
            #fase2AdminModal.show,
            #fase3DetailsModal.show,
            #fase3AprobarModal.show,
            #fase4EstudianteModal.show,
            #fase4DetailsModal.show,
            #fase4AdminModal.show,
            #fase5DetailsModal.show,
            #fase5AprobarModal.show,
            #faseFinalDetailsModal.show,
            #icfesEstudianteModal.show,
            #icfesAdminModal.show,
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

            #check_idea_banco:checked {
                background-color: #C1D631 !important;
            }

            .hidden-inputs {
                opacity: 0;
                height: 0;
                overflow: hidden;
                transition: opacity 0.3s ease-in-out, height 0.3s ease-in-out;
                margin: 0;
                padding: 0;
            }

            .visible-inputs {
                opacity: 1;
                height: auto;
                transition: opacity 0.3s ease-in-out, height 0.3s ease-in-out;
            }
        </style>
    @endpush
    
    <!--Colocar el calendario , el warning, icfes -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-b">
        <h2 class="text-2xl text-center font-bold text-gray-800">
            Seguimiento de las <span class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Prácticas</span>
        </h2>
    </div>

    <!-- Container donde aparecen las fases -->
    <div class="p-4" id="container-fases-main">
        <p class="text-gray-600 mt-2">Aquí podrás llevar el seguimiento del progreso del proyecto de grado en curso</p>
        <div class="mt-8 grid w-full grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            <div id="fase-1" class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $estado >= 1 ? 'card-activated' : '' }} {{ $estado == 1 ? 'card-activated-animated' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-12">
                <path fill-rule="evenodd" d="M5.625 1.5H9a3.75 3.75 0 0 1 3.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 0 1 3.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 0 1-1.875-1.875V3.375c0-1.036.84-1.875 1.875-1.875Zm6.905 9.97a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.72-1.72V18a.75.75 0 0 0 1.5 0v-4.19l1.72 1.72a.75.75 0 1 0 1.06-1.06l-3-3Z" clip-rule="evenodd" />
                <path d="M14.25 5.25a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963A5.23 5.23 0 0 0 16.5 7.5h-1.875a.375.375 0 0 1-.375-.375V5.25Z" />
                </svg>

                <span class="text-center font-bold text-lg">Fase 1: F-DC- 127</span>
                <p class="text-center mt-2 text-sm mx-4">El estudiante envía el formato de solicitud de practicantes de la empresa.</p>
                


            </div>
        </div>
    
    </div>











</x-app-layout>
