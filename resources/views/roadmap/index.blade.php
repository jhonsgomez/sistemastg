@php
    use Carbon\Carbon;

    $fechaActual = Carbon::now()->format('Y-m-d');
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Seguimiento del <span class="bg-uts-500 text-gray-800 font-bold me-2 px-2.5 py-0.5 rounded uppercase">Proyecto</span>
        </h2>
    </x-slot>

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

    <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-b">
        <h2 class="text-2xl text-center font-bold text-gray-800">
            Seguimiento del <span class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Proyecto</span>
        </h2>
        <div class="flex justify-center items-center space-x-2 buttons-container">
            <button type="button" id="warning" onclick="openWarningModal()"
                class="btn-action shadow bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded-lg relative">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <svg id="loadingSpinner-1" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
            @if (isset($submited_icfes))
                @if ($estado >= 4 && $estado <= 5)
                    @if ($submited_icfes === "false")
                        @if (auth()->user()->hasRole(['estudiante']))
                            <button type="button" id="icfes-estudiante-button" onclick="openIcfesEstudianteModal()"
                                class="btn-action shadow bg-green-700 hover:bg-green-800 text-white px-3 py-1 rounded-lg relative">
                                <i class="fa-solid fa-flag-checkered"></i>
                                <svg id="loadingSpinner-icfes-estudiante" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                        @else
                            @if (auth()->user()->hasRole(['super_admin', 'admin']))
                                <button type="button" id="icfes-admin-button" onclick="openIcfesAdminModal()"
                                    class="btn-action shadow bg-green-700 hover:bg-green-800 text-white px-3 py-1 rounded-lg relative">
                                    <i class="fa-solid fa-flag-checkered"></i>
                                    <svg id="loadingSpinner-icfes-admin" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                            @endif
                        @endif  
                    @endif
                @endif
            @endif

            <button type="button" id="calendar" onclick="openCalendarModal(false)"
                class="btn-action shadow @if (auth()->user()->hasRole('docente')) bg-gray-500 hover:bg-gray-700 @else bg-uts-500 hover:bg-uts-700 @endif text-white px-3 py-1 rounded-lg relative"
                @if ($estado < 2)style="margin-right: 0.3rem !important" @endif>
                <i class="fa-regular fa-calendar"></i>
                <svg id="loadingSpinner-1" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

            @if ($estado >= 1 && $estado <= 5 && $solicitud->estado != 'Finalizado')
                @if (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('estudiante'))
                <button type="button" id="config-proyecto" @if (auth()->user()->hasRole(['super_admin', 'admin'])) onclick="openConfigAdminModal()" @else onclick="openConfigModal()" @endif
                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                    <i class="fa-solid fa-gear"></i>
                    <svg id="loadingSpinner-config" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                @endif
                @endif
                <button
                    @if (request()->routeIs('director.roadmap')) onclick="window.location.href=`{{ route('director.index') }}`"
                    @elseif (request()->routeIs('evaluador.roadmap')) onclick="window.location.href=`{{ route('evaluador.index') }}`"
                    @else onclick="window.location.href=`{{ route('proyectos.index') }}`" @endif
                    class="bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg flex items-center shadow">
                    <i class="fa-solid fa-rotate-left mr-2"></i> Volver
                </button>
        </div>
    </div>

    <div class="p-4" id="container-fases-main">
        <p class="text-gray-600 mt-2">Aquí podrás llevar el seguimiento del progreso del proyecto de grado en curso</p>
        <div class="mt-8 grid w-full grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">

            <div id="fase-1" class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $estado >= 1 ? 'card-activated' : '' }} {{ $estado == 1 ? 'card-activated-animated' : '' }}">
                <i class="fas fa-check-circle text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Fase 1: Pago</span>
                <p class="text-center mt-2 text-sm mx-4">El estudiante describe su idea y sube el pago.</p>
                @if ($estado == 1)
                @if ($submited_fase1 != "true")
                @if (auth()->user()->hasRole('estudiante'))
                    @if(isset($fechas))
                        @if ($fechaActual <= $fechas['fecha_aprobacion_propuesta'] && !auth()->user()->hasRole(['super_admin', 'admin']))
                            <div class="flex justify-center items-center mt-3">
                                <button type="button" id="fase1-estudiante-button" onclick="openFase1EstudianteModal()"
                                    class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                                    <i class="fa-solid fa-user-pen"></i>
                                    <svg id="loadingSpinner-fase1-card" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                            </div>
                        @endif
                    @endif
                @endif
                @else
                @if (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin'))
                <div id="buttons-admin-fase1" class="flex justify-center items-center mt-3">
                    <button type="button" id="fase1-admin-details-button" onclick="openFase1AdminDetailsModal('{{ $solicitud->id }}')"
                        class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                        <i class="fa-solid fa-eye"></i>
                        <svg id="loadingSpinner-fase1AdminDetails" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                    <button type="button" id="fase1-admin-button" onclick="openFase1AdminModal('{{ $codigo_modalidad }}')"
                        class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                        <i class="fa-solid fa-share"></i>
                        <svg id="loadingSpinner-fase1Admin" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                </div>
                @endif
                @endif
                @elseif ($estado > 1)
                <div id="buttons-admin-fase1" class="flex justify-center items-center mt-3">
                    <button type="button" id="fase1-admin-details-button" onclick="openFase1AdminDetailsModal('{{ $solicitud->id }}')"
                        class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                        <i class="fa-solid fa-eye"></i>
                        <svg id="loadingSpinner-fase1AdminDetails" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                </div>
                @endif
            </div>

            <div id="fase-2" class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $estado >= 2 ? 'card-activated' : '' }} {{ $estado == 2 ? 'card-activated-animated' : '' }}">
                <i class="fas fa-hourglass-half text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Fase 2: Propuesta I</span>
                <p class="text-center mt-2 text-sm mx-4">El estudiante envía la propuesta al director.</p>

                @if ($estado == 2)
                    @if ($submited_fase2 != "true")
                        <div class="flex justify-center items-center mt-3">
                            <button type="button" id="fase2-details-button" onclick="openFase2DetailsModal('{{ $solicitud->id }}')"
                                class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                                <i class="fa-solid fa-eye"></i>
                                <svg id="loadingSpinner-fase2Details" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                                @if(isset($fechas))
                                    @if ($fechaActual <= $fechas['fecha_aprobacion_propuesta'] && !auth()->user()->hasRole(['super_admin', 'admin']))
                                        <button type="button" id="fase2-estudiante-button" onclick="openFase2EstudianteModal()"
                                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                                            <i class="fa-solid fa-user-pen"></i>
                                            <svg id="loadingSpinner-fase2-card" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                                    @endif
                                @endif
                            @endif
                        </div>
                    @else
                        <div id="buttons-admin-fase2" class="flex justify-center items-center mt-3">
                            <button type="button" id="fase2-details-button" onclick="openFase2DetailsModal('{{ $solicitud->id }}')"
                                class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                                <i class="fa-solid fa-eye"></i>
                                <svg id="loadingSpinner-fase2Details" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                            @if (auth()->user()->id == $director_actual || auth()->user()->hasRole(['super_admin', 'admin']))
                                @if(isset($fechas))
                                    @if ($fechaActual <= $fechas['fecha_aprobacion_propuesta'])
                                        <button type="button" id="fase2-admin-button" onclick="openFase2AdminModal()"
                                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                                            <i class="fa-solid fa-share"></i>
                                            <svg id="loadingSpinner-fase2Admin" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                                    @endif
                                @endif
                            @endif
                        </div>    
                    @endif
                @elseif ($estado > 2)
                    <div class="flex justify-center items-center mt-3">
                        <button type="button" id="fase2-details-button" onclick="openFase2DetailsModal('{{ $solicitud->id }}')"
                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                            <i class="fa-solid fa-eye"></i>
                            <svg id="loadingSpinner-fase2Details" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                    </div>
                @endif
            </div>

            <div id="fase-3" class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $estado >= 3 ? 'card-activated' : '' }} {{ $estado == 3 ? 'card-activated-animated' : '' }}">
                <i class="fas fa-paper-plane text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Fase 3: Propuesta II</span>
                <p class="text-center mt-2 text-sm mx-4">El director envía propuesta a evaluador y comité.</p>

                @if ($estado == 3)
                    <div id="buttons-admin-fase3" class="flex justify-center items-center mt-3">
                        <button type="button" id="fase3-details-button" onclick="openFase3DetailsModal('{{ $solicitud->id }}')"
                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                            <i class="fa-solid fa-eye"></i>
                            <svg id="loadingSpinner-fase3Details" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                        @if(isset($fechas))
                            @if ($fechaActual <= $fechas['fecha_aprobacion_propuesta'])
                                @if ($submited_fase3_director == "true" && $submited_fase3_evaluador != "true")
                                    @if (auth()->user()->id == $evaluador_actual || auth()->user()->hasRole(['super_admin', 'admin']))
                                        <button type="button" id="fase3-admin-button" onclick="openFase3AprobarModal()"
                                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                                            <i class="fa-solid fa-share"></i>
                                            <svg id="loadingSpinner-fase3AprobarModal" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                                    @endif
                                @elseif ($submited_fase3_evaluador == "true" && $submited_fase3_director == "true" && auth()->user()->hasRole(['super_admin', 'admin']))
                                    <button type="button" id="fase3-admin-button" onclick="openFase3AprobarModal()"
                                        class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                                        <i class="fa-solid fa-share"></i>
                                        <svg id="loadingSpinner-fase3AprobarModal" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                                @endif
                            @endif
                        @endif
                    </div>
                @elseif ($estado > 3)
                    <div id="buttons-admin-fase3" class="flex justify-center items-center mt-3">
                        <button type="button" id="fase3-details-button" onclick="openFase3DetailsModal('{{ $solicitud->id }}')"
                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                            <i class="fa-solid fa-eye"></i>
                            <svg id="loadingSpinner-fase3Details" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                    </div>
                @endif
            </div>

            <div id="fase-4" class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $estado >= 4 ? 'card-activated' : '' }} {{ $estado == 4 ? 'card-activated-animated' : '' }}">
                <i class="fas fa-hourglass-half text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Fase 4: Informe I</span>
                <p class="text-center mt-2 text-sm mx-4">El estudiante envía el informe al director.</p>
                
                @if ($estado == 4)
                    <div id="buttons-admin-fase4" class="flex justify-center items-center mt-3">
                        <button type="button" id="fase4-details-button" onclick="openFase4DetailsModal('{{ $solicitud->id }}')"
                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                            <i class="fa-solid fa-eye"></i>
                            <svg id="loadingSpinner-fase4Details" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

                        @if ($submited_fase4 != "true")
                            @if (auth()->user()->hasRole(['estudiante']))
                                @if(isset($fecha_minima_informe) && isset($fecha_maxima_informe))
                                    <!-- AQUI SE REALIZA EL CAMBIO DE FECHAS PARA 90 DIAS MINIMOS -->
                                    @if ($fechaActual >= $fecha_minima_informe && $fechaActual <= $fecha_maxima_informe && !auth()->user()->hasRole(['super_admin', 'admin']))
                                        <button type="button" id="fase4-estudiante-button" onclick="openFase4EstudianteModal()"
                                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                                            <i class="fa-solid fa-user-pen"></i>
                                            <svg id="loadingSpinner-fase4-card" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                                    @endif
                                @endif
                            @endif
                        @else
                            @if (auth()->user()->id == $director_actual || auth()->user()->hasRole(['super_admin', 'admin']))
                                @if(isset($fecha_maxima_informe))
                                    @if ($fechaActual <= $fecha_maxima_informe && !auth()->user()->hasRole(['super_admin', 'admin']))
                                        <button type="button" id="fase4-admin-button" onclick="openFase4AdminModal()"
                                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                                            <i class="fa-solid fa-share"></i>
                                            <svg id="loadingSpinner-fase4Admin" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                                    @endif
                                @endif
                            @endif
                        @endif
                    </div>
                @elseif ($estado > 4)
                    <div class="flex justify-center items-center mt-3">
                        <button type="button" id="fase4-details-button" onclick="openFase4DetailsModal('{{ $solicitud->id }}')"
                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                            <i class="fa-solid fa-eye"></i>
                            <svg id="loadingSpinner-fase4Details" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                    </div>
                @endif
            </div>

            <div id="fase-5" class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $estado >= 5 ? 'card-activated' : '' }} {{ $estado == 5 ? 'card-activated-animated' : '' }}">
                <i class="fas fa-paper-plane text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Fase 5: Informe II</span>
                <p class="text-center mt-2 text-sm mx-4">El director envía el informe al evaluador y comité.</p>

                @if ($estado == 5)
                    <div id="buttons-admin-fase5" class="flex justify-center items-center mt-3">
                        <button type="button" id="fase5-details-button" onclick="openFase5DetailsModal('{{ $solicitud->id }}')"
                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                            <i class="fa-solid fa-eye"></i>
                            <svg id="loadingSpinner-fase5Details" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                        @if(isset($fecha_maxima_informe))
                            @if ($fechaActual <= $fecha_maxima_informe)
                                @if ($submited_fase5_director == 'true' && $submited_fase5_evaluador != 'true')
                                    @if (auth()->user()->id == $evaluador_actual || auth()->user()->hasRole(['super_admin', 'admin']))
                                        <button type="button" id="fase5-admin-button" onclick="openFase5AprobarModal()"
                                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                                            <i class="fa-solid fa-share"></i>
                                            <svg id="loadingSpinner-fase5AprobarModal" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                                    @endif
                                @elseif ($submited_fase5_director == 'true' && $submited_fase5_evaluador == 'true' && auth()->user()->hasRole(['super_admin', 'admin']))
                                    <button type="button" id="fase5-admin-button" onclick="openFase5AprobarModal()"
                                        class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                                        <i class="fa-solid fa-share"></i>
                                        <svg id="loadingSpinner-fase5AprobarModal" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                                @endif
                            @endif
                        @endif
                    </div>
                @elseif ($estado == 'Finalizado')
                    <div id="buttons-admin-fase5" class="flex justify-center items-center mt-3">
                        <button type="button" id="fase5-details-button" onclick="openFase5DetailsModal('{{ $solicitud->id }}')"
                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                            <i class="fa-solid fa-eye"></i>
                            <svg id="loadingSpinner-fase5Details" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                    </div>
                @endif
            </div>

            <div id="fase-6" class="relative mx-auto flex flex-col items-center justify-center bg-white text-gray-600 rounded-lg shadow-lg h-60 w-full sm:w-50 border card-fase {{ $solicitud->estado == 'Finalizado' ? 'card-activated' : '' }} {{ $solicitud->estado == 'Finalizado' ? 'card-activated-animated' : '' }}">
                <i class="fa-solid fa-user-graduate text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Fase Final</span>
                <p class="text-center mt-2 text-sm mx-4">Estudiantes, director y evaluador programan sustentación.</p>
                @if ($estado == 'Finalizado')
                    <div id="buttons-admin-faseFinal" class="flex justify-center items-center mt-3">
                        <button type="button" id="faseFinal-details-button" onclick="openFaseFinalDetailsModal('{{ $solicitud->id }}')"
                            class="btn-action shadow bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded-lg relative">
                            <i class="fa-solid fa-eye"></i>
                            <svg id="loadingSpinner-faseFinalDetails" style="margin: 4px 1px" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($estado >= 1 && $estado <= 5)
        @if (!auth()->user()->hasRole(['super_admin', 'admin']) && ($solicitud->vencido || $solicitud->deshabilitado))
        <div id="vencidoModal" class="show fixed z-50 inset-0 overflow-y-auto">
            <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;">
                <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                        <div class="p-6 mt-2">
                            <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="vencidoTitle">
                                Proyecto <span class="bg-red-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Deshabilitado</span>
                            </p>
                            <div class="mb-4">
                                <p class="text-md">Estimado usuario, actualmente el proyecto al que está tratando de acceder se ha deshabilitado, algunas de las razones por las que esto ocurre son las siguientes:</p>
                                <ol class="ps-5 mt-6 mb-6 space-y-1 list-decimal list-inside">
                                    <li>Si el proyecto se encontraba en <strong>FASE 1, 2 o 3</strong> y no fue aprobado antes de la fecha.</li>
                                    <li>Si se venció el plazo de subida o carga de archivos en alguna de las fases.</li>
                                    <li>Si todos estudiantes se retiraron de forma voluntaria.</li>
                                    <li>Si los administradores deshabilitaron el proyecto.</li>
                                </ol>
                                <p><strong>NOTA: </strong>Si considera que esto es un error, póngase en contacto con los administradores inmediatamente.</p>
                            </div>
                            <div class="flex justify-end space-x-2 mt-6">
                                <a
                                    id="vencidoModalButton"
                                    href="javascript:history.back()"
                                    class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                    <svg id="loadingSpinner-vencido" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                        <path
                                            d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                            stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path
                                            d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                            stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                        </path>
                                    </svg>
                                    Aceptar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif

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
                            <p class="font-medium text-md text-gray-700 mb-2">Aquí podrá visualizar algunas fechas importantes del proyecto en curso.</p>

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
                                        @if (isset($fecha_minima_informe) && isset($fecha_maxima_informe))
                                            <tr class="bg-gray-50 border border-gray-300">
                                                <td class="px-4 py-3 border border-gray-300">Fechas para cargar informe final</td>
                                                <td class="px-4 py-3 border border-gray-300">Desde el <span class="font-semibold" style="font-size: 0.9rem;">{{ $fecha_minima_informe }}</span> hasta el <span class="font-semibold" style="font-size: 0.9rem;">{{ $fecha_maxima_informe }}</span></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- FASE 1 -->
    @if ($estado == 1 && $submited_fase1 != "true")
        <div id="fase1EstudianteModal" class="fixed z-50 inset-0 overflow-y-auto">
            <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase1EstudianteModal()">
                <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                        <button class="modal-close-btn-custom" onclick="closeFase1EstudianteModal()">
                            &times;
                        </button>
                        <form class="p-6 mt-2" id="fase1EstudianteForm" enctype="multipart/form-data">
                            @csrf
                            <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="fase1EstudianteTitle"></p>
                            <p class="font-medium text-sm text-gray-700 mb-4">En este formulario el estudiante podrá inscribir una idea propia especificando información de esta, o podrá seleccionar una idea perteneciente al banco actual.</p>
                            <p class="font-medium text-sm text-gray-700 mb-6"><strong>NOTA:</strong> Por cada integrante del proyecto se debe cargar la liquidación y los respectivos soportes de pago.</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-6">
                                <input type="hidden" name="solicitud_id" id="solicitud_id" value="{{ $solicitud->id }}">
                                @foreach($campos as $campo)
                                @if($campo->name != 'soporte_pago')<div @if($campo->type == 'checkbox')class="flex"@endif id="container-{{ $campo->name }}">@endif
                                    @if ($campo->label != null && $campo->type != 'hidden' && $campo->type != 'checkbox' && $campo->type != 'file')
                                    <div class="flex items-center gap-2">
                                        <label for="{{ $campo->name }}" class="block font-medium text-sm text-gray-700">
                                            <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                            <span class="text-red-600 mr-1 text-lg">*</span>{{ $campo->label }}
                                        </label>
                                        <div class="relative inline-block">
                                            <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                                data-tooltip="tooltip-{{ $campo->name }}"></i>
                                            <div id="tooltip-{{ $campo->name }}"
                                                style="width: 10rem; left: -100px;"
                                                class="hidden absolute z-10 max-w-[90vw] px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg left-1/2 -translate-x-1/2 bottom-full mb-2 sm:translate-x-0">
                                                <p class="uppercase font-bold mb-2">Instrucciones:</p>
                                                {!! $campo->instructions ?? '' !!}
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @switch($campo->type)

                                    @case('checkbox')
                                    <div class="flex items-center">
                                        <label for="{{ $campo->name }}" class="block font-medium text-sm text-gray-700">
                                            <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                            @if($campo->required)<span class="text-red-600 mr-1 text-lg">*</span>@endif{{ $campo->label }}
                                        </label>&nbsp;&nbsp;
                                        <input type="checkbox" name="{{ $campo->name }}" id="{{ $campo->name }}" class="border-gray-300 rounded-md shadow-sm mt-1 focus:ring-uts-500 focus:border-uts-500">
                                    </div>
                                    @break

                                    @case('select')
                                    <select name="{{ $campo->name }}" id="{{ $campo->name }}" lang="es"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                        <option value="" selected disabled>Selecciona una opción</option>
                                        @if ($campo->name == 'idea_banco')
                                        @foreach ($ideas_banco as $idea)
                                        <option value="{{ $idea->propuesta_id }}">{{ $idea->modalidad }} - {{ $idea->nivel }} | {{ $idea->titulo }} | {{ $idea->periodo }} | {{ $idea->docente }}</option>
                                        @endforeach
                                        @elseif ($campo->name == 'linea_investigacion')
                                            @foreach ($lineas_investigacion as $linea)
                                            <option value="{{ $linea->id }}">{{ $linea->nombre }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @break

                                    @case('textarea')
                                    <textarea name="{{ $campo->name }}" id="{{ $campo->name }}"
                                        placeholder="{{ $campo->placeholder ?? '' }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"></textarea>
                                    @break

                                    @case('text')
                                    <input type="text" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                        placeholder="{{ $campo->placeholder ?? '' }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                    @break

                                    @case('number')
                                    <input type="number" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                        placeholder="{{ $campo->placeholder ?? '' }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                    @break

                                    @case('date')
                                    <input type="date" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                        placeholder="{{ $campo->placeholder ?? '' }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                    @break

                                    @case('file')
                                    @if ($campo->name == 'soporte_pago')
                                </div>
                                <div class="grid grid-cols-1 gap-6">
                                    <div @if($campo->type == 'checkbox')class="flex"@endif id="container-{{ $campo->name }}" class="mt-soporte-original">
                                        <div class="flex items-center gap-2">
                                            <label for="{{ $campo->name }}" class="block font-medium text-sm text-gray-700">
                                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                                @if($campo->required)<span class="text-red-600 mr-1 text-lg">*</span>@endif{{ $campo->label }}
                                            </label>
                                            <div class="relative inline-block">
                                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                                    data-tooltip="tooltip-{{ $campo->name }}"></i>
                                                <div id="tooltip-{{ $campo->name }}"
                                                    style="width: 16rem; left: -100px;"
                                                    class="hidden absolute z-10 max-w-[90vw] px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg left-1/2 -translate-x-1/2 bottom-full mb-2 sm:translate-x-0">
                                                    <p class="uppercase font-bold mb-2">Instrucciones:</p>
                                                    {!! $campo->instructions ?? '' !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 mb-6">
                                            <ul class="list-disc pl-5 text-sm text-gray-500">
                                                <li class="mt-1">
                                                    Instructivo para pagar la liquidación: 
                                                    <a href="{{ asset('ejemplos/fase_1-1.pdf') }}" target="_blank" class="text-blue-500 underline">ABRIR ARCHIVO</a>
                                                </li>
                                                <li class="mt-1">
                                                    Liquidación con marca de agua (Ejemplo): 
                                                    <a href="{{ asset('ejemplos/fase_1-2.pdf') }}" target="_blank" class="text-blue-500 underline">ABRIR ARCHIVO</a>
                                                </li>
                                            </ul>
                                            <div class="flex items-start mb-4 mt-6">
                                                <p class="font-medium text-sm text-gray-700 mb-6">
                                                    <span class="mr-1 mt-1 inline-block align-middle">
                                                        <i class="fa-solid fa-circle-info text-uts-500 text-xl"></i>
                                                    </span>
                                                    <strong>NOTA:</strong>
                                                    <span class="text-gray-500">En caso de que el estudiante desee sugerir un director de trabajo de grado, deberá adjuntar una página adicional en el archivo PDF correspondiente a los pagos de la modalidad, indicando de manera formal el nombre del docente que desea sugerir como director de trabajo de grado. El comité evaluará la sugerencia y responderá al estudiante.</span>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone">
                                            <div class="grid gap-1">
                                                <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                                <h2 class="text-center text-gray-400 text-xs leading-4">Solo archivos PDF cuyo peso máximo sea {{ env('PESO_MAXIMO_PAGO') }}MB</h2>
                                            </div>
                                            <div class="grid gap-2">
                                                <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                                <div class="flex items-center justify-center">
                                                    <input
                                                        type="file"
                                                        name="{{ $campo->name }}[]"
                                                        id="{{ $campo->name }}"
                                                        multiple
                                                        class="absolute inset-0 opacity-0 cursor-pointer"
                                                        accept="application/pdf" />
                                                    <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                                </div>
                                            </div>
                                        </div>
                                        <span id="{{ $campo->name }}Error" class="text-red-500 text-sm"></span>
                                        <ul id="file-list" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                                        <span id="files-size" class="text-gray-800 text-sm"></span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-6">
                                    @else
                                    <input type="file" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                    @endif
                                    @break

                                    @case('hidden')
                                    @if ($campo->name == 'submited')
                                    <input type="hidden" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                        value="true">
                                    @else
                                    <input type="hidden" name="{{ $campo->name }}" id="{{ $campo->name }}" value="NULL">
                                    @endif
                                    @break

                                    @default
                                    <input type="text" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                        placeholder="{{ $campo->placeholder ?? '' }}"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                    @endswitch

                                    @if($campo->name != 'soporte_pago')
                                    <span id="{{ $campo->name }}Error" class="text-red-500 text-sm"></span>
                                </div>
                                @endif
                                @endforeach
                            </div>
                            <div class="mt-2 flex justify-end space-x-2">
                                <button
                                    type="button"
                                    onclick="closeFase1EstudianteModal()"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                    Cancelar
                                </button>
                                <button
                                    id="fase1EstudianteButton"
                                    type="submit"
                                    class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                    <svg id="loadingSpinner-fase1Estudiante" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
    @endif

    <div id="fase1AdminDetailsModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase1AdminDetailsModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeFase1AdminDetailsModal()">
                        &times;
                    </button>
                    <div class="p-6 mt-2">
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="fase1AdminDetailsTitle"></p>
                        <div id="content-details-fase1"></div>
                        <div class="mt-2 flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeFase1AdminDetailsModal()"
                                class="bg-gray-300 mt-4 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="fase1AdminModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase1AdminModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeFase1AdminModal()">
                        &times;
                    </button>
                    <form class="p-6 mt-2" id="fase1AdminForm" enctype="multipart/form-data">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="fase1AdminTitle"></p>
                        <input type="hidden" name="solicitud_id" value="{{ $solicitud->id }}">
                        <div class="mb-4">
                            <label for="estado" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Estado del proyecto:
                            </label>
                            <select name="estado" id="estado"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                <option value="" selected disabled>Selecciona una opción</option>
                                <option value="Aprobado">Aprobado</option>
                                <option value="Rechazado">Rechazado</option>
                            </select>
                            <span id="estadoError" class="text-red-500 text-sm"></span>
                        </div>

                        <div id="container_acta_codigo">
                            <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="nro_acta_fase_1" class="block font-medium text-sm text-gray-700">
                                        <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>
                                        Número de acta:
                                    </label>
                                    <input type="text" name="nro_acta_fase_1" id="nro_acta_fase_1"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                        placeholder="Ingrese el número de acta">
                                    <span id="nro_acta_fase_1Error" class="text-red-500 text-sm"></span>
                                </div>
                                <div>
                                    <label for="fecha_acta_fase_1" class="block font-medium text-sm text-gray-700">
                                        <i class="fa-regular fa-calendar-days mr-1 text-gray-500"></i>
                                        Fecha del acta:
                                    </label>
                                    <input type="date" name="fecha_acta_fase_1" id="fecha_acta_fase_1"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                    <span id="fecha_acta_fase_1Error" class="text-red-500 text-sm"></span>
                                </div>
                            </div>
                            <div class="mb-4 hidden" id="container_codigo_modalidad">
                                <label for="codigo_modalidad" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-hashtag mr-1 text-gray-500"></i>
                                    Código de modalidad:
                                </label>
                                <input type="text" name="codigo_modalidad" id="codigo_modalidad"
                                    class="bg-gray-200 border-gray-300 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500 cursor-default"
                                    placeholder="Ingrese el codigo de modalidad"
                                    >
                                <span id="codigo_modalidadError" class="text-red-500 text-sm"></span>
                            </div>
                        </div>

                        <div id="container-docentes" class="hidden">
                            <div class="mb-4">
                                <label for="director" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Asigne el director del proyecto:
                                </label>
                                <select name="director" id="director" lang="es"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                    <option value="" selected disabled>Selecciona una opción</option>
                                    @foreach ($docentes as $docente)
                                    <option value="{{ $docente->id }}">{{ $docente->name }}</option>
                                    @endforeach
                                </select>
                                <span id="directorError" class="text-red-500 text-sm"></span>
                            </div>
                            <div class="mb-4">
                                <label for="evaluador" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Asigne el evaluador del proyecto:
                                </label>
                                <select name="evaluador" id="evaluador" lang="es"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                    <option value="" selected disabled>Selecciona una opción</option>
                                    @foreach ($docentes as $docente)
                                    <option value="{{ $docente->id }}">{{ $docente->name }}</option>
                                    @endforeach
                                </select>
                                <span id="evaluadorError" class="text-red-500 text-sm"></span>
                            </div>
                            <div class="mb-4">
                                <label for="codirector" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Asigne el codirector del proyecto (opcional):
                                </label>
                                <select name="codirector" id="codirector" lang="es"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                    <option value="" selected disabled>Selecciona una opción</option>
                                    @foreach ($docentes as $docente)
                                    <option value="{{ $docente->id }}">{{ $docente->name }}</option>
                                    @endforeach
                                </select>
                                <span id="codirectorError" class="text-red-500 text-sm"></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="respuesta_fase1" class="block font-medium text-sm text-gray-700" style="margin-bottom: 5px;">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Comentarios de la respuesta:
                            </label>
                            <div id="txt-editor-fase1" class="txt-editor-quill shadow"></div>
                            <textarea name="respuesta_fase1" id="respuesta_fase1" class="hidden"></textarea>
                            <span id="respuesta_fase1Error" class="text-red-500 text-sm"></span>
                        </div>
                        <div class="mt-2 flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeFase1AdminModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button
                                id="fase1AdminButton"
                                type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-fase1AdminResponse" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

    <!-- FASE 2 -->

    @if ($estado == 2 && $submited_fase2 != 'true')
        <div id="fase2EstudianteModal" class="fixed z-50 inset-0 overflow-y-auto">
            <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase2EstudianteModal()">
                <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                        <button class="modal-close-btn-custom" onclick="closeFase2EstudianteModal()">
                            &times;
                        </button>
                        <form class="p-6 mt-2" id="fase2EstudianteForm" enctype="multipart/form-data">
                            @csrf
                            <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="fase2EstudianteTitle"></p>
                            <p class="font-medium text-sm text-gray-700">En este formulario el estudiante podrá cargar el documento de su propuesta de trabajo de grado únicamente en formato word.</p>
                            <p class="font-medium text-sm text-gray-700 mt-5"><strong>NOTA: </strong>Únicamente se deberá cargar el archivo en formato Word de la <a href="https://www.dropbox.com/scl/fo/pudgcaq639agy7t06ahjs/AF-dMByc71OWsxcfY2dLe3A?rlkey=6s0b9ajweteyx2ang7ywvk6xm&e=1&dl=0" target="_blank" class="text-blue-500 underline uppercase">propuesta</a> de trabajo de grado.</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-6">
                                <input type="hidden" name="solicitud_id" id="solicitud_id" value="{{ $solicitud->id }}">
                                @foreach($campos as $campo)
                                    @if($campo->name != 'doc_propuesta')
                                        <div @if($campo->type == 'checkbox')class="flex"@endif id="container-{{ $campo->name }}">
                                    @endif
                                    @if ($campo->label != null && $campo->type != 'hidden' && $campo->type != 'checkbox' && $campo->type != 'file')
                                        <div class="flex items-center gap-2">
                                            <label for="{{ $campo->name }}" class="block font-medium text-sm text-gray-700">
                                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                                <span class="text-red-600 mr-1 text-lg">*</span>{{ $campo->label }}
                                            </label>
                                            <div class="relative inline-block">
                                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                                    data-tooltip="tooltip-{{ $campo->name }}"></i>
                                                <div id="tooltip-{{ $campo->name }}"
                                                    style="width: 10rem; left: -100px;"
                                                    class="hidden absolute z-10 max-w-[90vw] px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg left-1/2 -translate-x-1/2 bottom-full mb-2 sm:translate-x-0">
                                                    <p class="uppercase font-bold mb-2">Instrucciones:</p>
                                                    {!! $campo->instructions ?? '' !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @switch($campo->type)
                                        @case('checkbox')
                                            <div class="flex items-center">
                                                <label for="{{ $campo->name }}" class="block font-medium text-sm text-gray-700">
                                                    <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                                    @if($campo->required)<span class="text-red-600 mr-1 text-lg">*</span>@endif{{ $campo->label }}
                                                </label>&nbsp;&nbsp;
                                                <input type="checkbox" name="{{ $campo->name }}" id="{{ $campo->name }}" class="border-gray-300 rounded-md shadow-sm mt-1 focus:ring-uts-500 focus:border-uts-500">
                                            </div>
                                        @break

                                        @case('select')
                                            <select name="{{ $campo->name }}" id="{{ $campo->name }}" lang="es"
                                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                                <option value="" selected disabled>Selecciona una opción</option>
                                            </select>
                                        @break

                                        @case('textarea')
                                            <textarea name="{{ $campo->name }}" id="{{ $campo->name }}"
                                                placeholder="{{ $campo->placeholder ?? '' }}"
                                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"></textarea>
                                        @break

                                        @case('text')
                                            <input type="text" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                                placeholder="{{ $campo->placeholder ?? '' }}"
                                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                        @break

                                        @case('number')
                                            <input type="number" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                                placeholder="{{ $campo->placeholder ?? '' }}"
                                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                        @break

                                        @case('date')
                                            <input type="date" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                                placeholder="{{ $campo->placeholder ?? '' }}"
                                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                        @break

                                        @case('file')
                                            @if ($campo->name == 'doc_propuesta')
                                        </div>
                                                <div class="grid grid-cols-1 gap-6">
                                                    <div @if($campo->type == 'checkbox')class="flex"@endif id="container-{{ $campo->name }}" class="mt-soporte-original">
                                                        <div class="flex items-center gap-2">
                                                            <label for="{{ $campo->name }}" class="block font-medium text-sm text-gray-700">
                                                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                                                @if($campo->required)<span class="text-red-600 mr-1 text-lg">*</span>@endif{{ $campo->label }}
                                                            </label>
                                                            <div class="relative inline-block">
                                                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                                                    data-tooltip="tooltip-{{ $campo->name }}"></i>
                                                                <div id="tooltip-{{ $campo->name }}"
                                                                    style="width: 10rem; left: -100px;"
                                                                    class="hidden absolute z-10 max-w-[90vw] px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg left-1/2 -translate-x-1/2 bottom-full mb-2 sm:translate-x-0">
                                                                    <p class="uppercase font-bold mb-2">Instrucciones:</p>
                                                                    {!! $campo->instructions ?? '' !!}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_fase2">
                                                                <div class="grid gap-1">
                                                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                                                    <h2 class="text-center text-gray-400   text-xs leading-4">Solo archivos de Word de máximo {{ env('PESO_MAXIMO_PROPUESTA') }}MB</h2>
                                                                </div>
                                                                <div class="grid gap-2">
                                                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                                                    <div class="flex items-center justify-center">
                                                                        <input
                                                                            type="file"
                                                                            name="{{ $campo->name }}[]"
                                                                            id="{{ $campo->name }}_fase2"
                                                                            class="absolute inset-0 opacity-0 cursor-pointer"
                                                                            accept=".doc,.docx" />
                                                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <span id="{{ $campo->name }}Error" class="text-red-500 text-sm"></span>
                                                            <ul id="file-list-fase2" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                                                            <span id="files-size-fase2" class="text-gray-800 text-sm"></span>
                                                        </div>
                                                </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-6">
                                            @else
                                                <input type="file" name="{{ $campo->name }}" id="{{ $campo->name }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                            @endif
                                        @break
                                        @case('hidden')
                                            @if ($campo->name == 'submited_fase2')
                                                <input type="hidden" name="{{ $campo->name }}" id="{{ $campo->name }}" value="true">
                                            @else
                                                <input type="hidden" name="{{ $campo->name }}" id="{{ $campo->name }}" value="NULL">
                                            @endif
                                        @break
                                        @default
                                            <input type="text" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                                placeholder="{{ $campo->placeholder ?? '' }}"
                                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                        @break
                                    @endswitch
                                    @if($campo->name != 'doc_propuesta')
                                            <span id="{{ $campo->name }}Error" class="text-red-500 text-sm"></span>
                                        </div>
                                    @endif           
                                @endforeach
                            </div>
                            <div class="mt-2 flex justify-end space-x-2">
                                <button
                                    type="button"
                                    onclick="closeFase2EstudianteModal()"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                    Cancelar
                                </button>
                                <button
                                    id="fase2EstudianteButton"
                                    type="submit"
                                    class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                    <svg id="loadingSpinner-fase2Estudiante" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
    @endif

    <div id="fase2DetailsModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase2DetailsModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeFase2DetailsModal()">
                        &times;
                    </button>
                    <div class="p-6 mt-2">
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="fase2DetailsTitle"></p>
                        <div id="content-details-fase2"></div>
                        <div class="mt-2 flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeFase2DetailsModal()"
                                class="bg-gray-300 mt-4 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="fase2AdminModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase2AdminModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeFase2AdminModal()">
                        &times;
                    </button>
                    <form class="p-6 mt-2" id="fase2AdminForm" enctype="multipart/form-data">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="fase2AdminTitle"></p>
                        <input type="hidden" name="solicitud_id" value="{{ $solicitud->id }}">
                        <div class="mb-4">
                            <label for="estado" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Estado del proyecto:
                            </label>
                            <select name="estado_fase2" id="estado_fase2"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                <option value="" selected disabled>Selecciona una opción</option>
                                <option value="Aprobado">Aprobado</option>
                                <option value="Rechazado">Aplazado</option>
                                <option value="Rechazado">No Aprobado</option>
                            </select>
                            <span id="estado_fase2Error" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="grid grid-cols-1 mb-4 hidden" id="container-doc_respuesta_fase2">
                            <label for="doc_respuesta_fase2" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Propuesta (F-DC-124) con comentarios:
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_respuesta_fase2">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400   text-xs leading-4">Solo archivos de Word de máximo {{ env('PESO_MAXIMO_PROPUESTA') }}MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input
                                            type="file"
                                            name="doc_respuesta_fase2[]"
                                            id="doc_respuesta_fase2"
                                            class="absolute inset-0 opacity-0 cursor-pointer"
                                            accept=".doc,.docx" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="doc_respuesta_fase2Error" class="text-red-500 text-sm"></span>
                            <ul id="file-list-respuesta-fase2" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-respuesta-fase2" class="text-gray-800 text-sm"></span>
                        </div>

                        <div class="grid grid-cols-1 mb-4 hidden" id="container-doc_firmado_fase2">
                            <label for="doc_firmado_fase2" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Propuesta (F-DC-124) firmada:
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_firmado_fase2">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400   text-xs leading-4">Solo archivos de Word de máximo {{ env('PESO_MAXIMO_PROPUESTA') }}MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input
                                            type="file"
                                            name="doc_firmado_fase2[]"
                                            id="doc_firmado_fase2"
                                            class="absolute inset-0 opacity-0 cursor-pointer"
                                            accept=".doc,.docx" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="doc_firmado_fase2Error" class="text-red-500 text-sm"></span>
                            <ul id="file-list-firmado-fase2" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-firmado-fase2" class="text-gray-800 text-sm"></span>
                        </div>

                        <div class="grid grid-cols-1 mb-4">
                            <label for="doc_turnitin_fase2" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg" id="required-turnitin">*</span>
                                Informe de plagio (Turnitin):
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_turnitin_fase2">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs leading-4">Solo archivos pdf de máximo {{ env('PESO_MAXIMO_PROPUESTA') }}MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input
                                            type="file"
                                            name="doc_turnitin_fase2[]"
                                            id="doc_turnitin_fase2"
                                            class="absolute inset-0 opacity-0 cursor-pointer"
                                            accept=".pdf" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="doc_turnitin_fase2Error" class="text-red-500 text-sm"></span>
                            <ul id="file-list-turnitin-fase2" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-turnitin-fase2" class="text-gray-800 text-sm"></span>
                        </div>

                        <div class="mb-4">
                            <label for="respuesta_fase2" class="block font-medium text-sm text-gray-700" style="margin-bottom: 5px;">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Comentarios de la respuesta:
                            </label>
                            <div id="txt-editor-fase2" class="txt-editor-quill shadow"></div>
                            <textarea name="respuesta_fase2" id="respuesta_fase2" class="hidden"></textarea>
                            <span id="respuesta_fase2Error" class="text-red-500 text-sm"></span>
                        </div>
                        <div class="mt-2 flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeFase2AdminModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button
                                id="fase2AdminButton"
                                type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-fase2AdminResponse" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

    <!-- FASE 3 -->

    <div id="fase3DetailsModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase3DetailsModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeFase3DetailsModal()">
                        &times;
                    </button>
                    <div class="p-6 mt-2">
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="fase3DetailsTitle"></p>
                        <div id="content-details-fase3"></div>
                        <div class="mt-2 flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeFase3DetailsModal()"
                                class="bg-gray-300 mt-4 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="fase3AprobarModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase3AprobarModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeFase3AprobarModal()">
                        &times;
                    </button>
                    <form class="p-6 mt-2" id="fase3AprobarForm" enctype="multipart/form-data">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="fase3AprobarTitle"></p>
                        <input type="hidden" name="solicitud_id" value="{{ $solicitud->id }}">
                        <input type="hidden" name="remitente" value="{{ auth()->user()->id == $evaluador_actual ? 0 : 1 }}">
                        
                        <div class="mb-4">
                            <label for="estado" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Estado del proyecto:
                            </label>
                            <select name="estado_fase3" id="estado_fase3"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                <option value="" selected disabled>Selecciona una opción</option>
                                <option value="Aprobado">Aprobado</option>
                                <option value="Rechazado">Aplazado</option>
                                <option value="Rechazado">No Aprobado</option>
                            </select>
                            <span id="estado_fase3Error" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="grid grid-cols-1 mb-4 hidden" id="container-doc_firmado_fase3">
                            <label for="doc_firmado_fase3" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Propuesta (F-DC-124) firmada:
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_firmado_fase3">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400   text-xs leading-4">Solo archivos de Word de máximo {{ env('PESO_MAXIMO_PROPUESTA') }}MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input
                                            type="file"
                                            name="doc_firmado_fase3[]"
                                            id="doc_firmado_fase3"
                                            class="absolute inset-0 opacity-0 cursor-pointer"
                                            accept=".doc,.docx" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="doc_firmado_fase3Error" class="text-red-500 text-sm"></span>
                            <ul id="file-list-firmado-fase3" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-firmado-fase3" class="text-gray-800 text-sm"></span>
                        </div>

                        <div class="grid grid-cols-1 mb-4 hidden" id="container-doc_respuesta_fase3">
                            <label for="doc_respuesta_fase3" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Propuesta (F-DC-124) con comentarios:
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_respuesta_fase3">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400   text-xs leading-4">Solo archivos de Word de máximo {{ env('PESO_MAXIMO_PROPUESTA') }}MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input
                                            type="file"
                                            name="doc_respuesta_fase3[]"
                                            id="doc_respuesta_fase3"
                                            class="absolute inset-0 opacity-0 cursor-pointer"
                                            accept=".doc,.docx" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="doc_respuesta_fase3Error" class="text-red-500 text-sm"></span>
                            <ul id="file-list-respuesta-fase3" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-respuesta-fase3" class="text-gray-800 text-sm"></span>
                        </div>

                        @if (auth()->user()->hasRole(['super_admin', 'admin']))
                            <div>
                                <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="nro_acta_fase3" class="block font-medium text-sm text-gray-700">
                                            <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>
                                            Número de acta:
                                        </label>
                                        <input type="text" name="nro_acta_fase3" id="nro_acta_fase3"
                                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                            placeholder="Ingrese el número de acta">
                                        <span id="nro_acta_fase3Error" class="text-red-500 text-sm"></span>
                                    </div>
                                    <div>
                                        <label for="fecha_acta_fase3" class="block font-medium text-sm text-gray-700">
                                            <i class="fa-regular fa-calendar-days mr-1 text-gray-500"></i>
                                            Fecha del acta:
                                        </label>
                                        <input type="date" name="fecha_acta_fase3" id="fecha_acta_fase3"
                                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                        <span id="fecha_acta_fase3Error" class="text-red-500 text-sm"></span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="respuesta_fase3" class="block font-medium text-sm text-gray-700" style="margin-bottom: 5px;">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Comentarios de la respuesta:
                            </label>
                            <div id="txt-editor-fase3" class="txt-editor-quill shadow"></div>
                            <textarea name="respuesta_fase3" id="respuesta_fase3" class="hidden"></textarea>
                            <span id="respuesta_fase3Error" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="mt-2 flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeFase3AprobarModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button
                                id="fase3AprobarButton"
                                type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-fase3AprobarResponse" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

    <!-- FASE 4 -->

    @if ($estado == 4 && $submited_fase4 != 'true')
        <div id="fase4EstudianteModal" class="fixed z-50 inset-0 overflow-y-auto">
            <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase4EstudianteModal()">
                <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                        <button class="modal-close-btn-custom" onclick="closeFase4EstudianteModal()">
                            &times;
                        </button>
                        <form class="p-6 mt-2" id="fase4EstudianteForm" enctype="multipart/form-data">
                            @csrf
                            <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="fase4EstudianteTitle"></p>
                            <p class="font-medium text-sm text-gray-700">En este formulario el estudiante deberá cargar el informe final de trabajo de grado (F-DC-125) completo y la rejilla de evaluación (F-DC-129) diligenciada en el apartado de "Información general del proyecto". Todos los documentos deben ir en formato de word.</p>
                            <p class="font-medium text-sm text-gray-700 mt-5"><strong>NOTA: </strong>Se deberá cargar el <a href="https://www.dropbox.com/scl/fo/pudgcaq639agy7t06ahjs/AF-dMByc71OWsxcfY2dLe3A?rlkey=6s0b9ajweteyx2ang7ywvk6xm&e=1&dl=0" target="_blank" class="text-blue-500 underline uppercase">Informe</a> y la <a href="https://www.dropbox.com/scl/fo/pudgcaq639agy7t06ahjs/AF-dMByc71OWsxcfY2dLe3A?rlkey=6s0b9ajweteyx2ang7ywvk6xm&e=1&dl=0" target="_blank" class="text-blue-500 underline uppercase">Rejilla</a> en formato de Word. Tenga en cuenta el tamaño máximo del archivo que puede cargar en cada campo, se le recomienda reducir o comprimir el peso del archivo antes de cargarlo (Puede usar herramientas online para ello o en su defecto la opción "Comprimir imágenes" del Word).</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-6">
                                <input type="hidden" name="solicitud_id" id="solicitud_id" value="{{ $solicitud->id }}">
                                @foreach($campos as $campo)
                                    @if($campo->name != 'doc_informe' && $campo->name != 'doc_rejilla' && $campo->name != 'doc_icfes')
                                        <div @if($campo->type == 'checkbox')class="flex"@endif id="container-{{ $campo->name }}">
                                    @endif
                                    @if ($campo->label != null && $campo->type != 'hidden' && $campo->type != 'checkbox' && $campo->type != 'file' && $campo->type != 'date')
                                        <div class="flex items-center gap-2">
                                            <label for="{{ $campo->name }}" class="block font-medium text-sm text-gray-700">
                                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                                <span class="text-red-600 mr-1 text-lg">*</span>{{ $campo->label }}
                                            </label>
                                            <div class="relative inline-block">
                                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                                    data-tooltip="tooltip-{{ $campo->name }}"></i>
                                                <div id="tooltip-{{ $campo->name }}"
                                                    style="width: 10rem; left: -100px;"
                                                    class="hidden absolute z-10 max-w-[90vw] px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg left-1/2 -translate-x-1/2 bottom-full mb-2 sm:translate-x-0">
                                                    <p class="uppercase font-bold mb-2">Instrucciones:</p>
                                                    {!! $campo->instructions ?? '' !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @switch($campo->type)
                                        @case('checkbox')
                                            <div class="flex items-center">
                                                <label for="{{ $campo->name }}" class="block font-medium text-sm text-gray-700">
                                                    <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                                    @if($campo->required)<span class="text-red-600 mr-1 text-lg">*</span>@endif{{ $campo->label }}
                                                </label>&nbsp;&nbsp;
                                                <input type="checkbox" name="{{ $campo->name }}" id="{{ $campo->name }}" class="border-gray-300 rounded-md shadow-sm mt-1 focus:ring-uts-500 focus:border-uts-500">
                                            </div>
                                        @break

                                        @case('select')
                                            <select name="{{ $campo->name }}" id="{{ $campo->name }}" lang="es"
                                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                                <option value="" selected disabled>Selecciona una opción</option>
                                            </select>
                                        @break

                                        @case('textarea')
                                            <textarea name="{{ $campo->name }}" id="{{ $campo->name }}"
                                                placeholder="{{ $campo->placeholder ?? '' }}"
                                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"></textarea>
                                        @break

                                        @case('text')
                                            <input type="text" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                                placeholder="{{ $campo->placeholder ?? '' }}"
                                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                        @break

                                        @case('number')
                                            <input type="number" name="{{ $campo->name }}" id="{{ $campo->name }}"
                                                placeholder="{{ $campo->placeholder ?? '' }}"
                                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                        @break

                                        @case('file')
                                            @if ($campo->name == 'doc_informe' || $campo->name == 'doc_rejilla')
                                        </div>
                                                <div class="grid grid-cols-1 gap-6">
                                                    <div @if($campo->type == 'checkbox')class="flex"@endif id="container-{{ $campo->name }}" class="mt-soporte-original">
                                                        <div class="flex items-center gap-2">
                                                            <label for="{{ $campo->name }}" class="block font-medium text-sm text-gray-700">
                                                                <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                                                @if($campo->required)<span class="text-red-600 mr-1 text-lg">*</span>@endif{{ $campo->label }}
                                                            </label>
                                                            <div class="relative inline-block">
                                                                <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                                                    data-tooltip="tooltip-{{ $campo->name }}"></i>
                                                                <div id="tooltip-{{ $campo->name }}"
                                                                    style="width: 10rem; left: -100px;"
                                                                    class="hidden absolute z-10 max-w-[90vw] px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg left-1/2 -translate-x-1/2 bottom-full mb-2 sm:translate-x-0">
                                                                    <p class="uppercase font-bold mb-2">Instrucciones:</p>
                                                                    {!! $campo->instructions ?? '' !!}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_{{ $campo->name }}_fase4">
                                                                <div class="grid gap-1">
                                                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                                                    <h2 class="text-center text-gray-400   text-xs leading-4">Solo archivos de Word de máximo {{ env('PESO_MAXIMO_INFORME') }}MB</h2>
                                                                </div>
                                                                <div class="grid gap-2">
                                                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                                                    <div class="flex items-center justify-center">
                                                                        <input
                                                                            type="file"
                                                                            name="{{ $campo->name }}[]"
                                                                            id="{{ $campo->name }}_fase4"
                                                                            class="absolute inset-0 opacity-0 cursor-pointer"
                                                                            accept=".doc,.docx" />
                                                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <span id="{{ $campo->name }}Error" class="text-red-500 text-sm"></span>
                                                            <ul id="{{ $campo->name }}-file-list-fase4" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                                                            <span id="{{ $campo->name }}-files-size-fase4" class="text-gray-800 text-sm"></span>
                                                        </div>
                                                </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-6">
                                            @else

                                            @endif
                                        @break
                                        @case('hidden')
                                            @if ($campo->name == 'submited_fase4')
                                                <input type="hidden" name="{{ $campo->name }}" id="{{ $campo->name }}" value="true">
                                            @endif
                                        @break
                                    @endswitch
                                    @if($campo->name != 'doc_informe' && $campo->name != 'doc_icfes' && $campo->name != 'doc_rejilla')
                                            <span id="{{ $campo->name }}Error" class="text-red-500 text-sm"></span>
                                        </div>
                                    @endif           
                                @endforeach
                            </div>
                            <div class="mt-2 flex justify-end space-x-2">
                                <button
                                    type="button"
                                    onclick="closeFase4EstudianteModal()"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                    Cancelar
                                </button>
                                <button
                                    id="fase2EstudianteButton"
                                    type="submit"
                                    class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                    <svg id="loadingSpinner-fase4Estudiante" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
    @endif

    <div id="fase4DetailsModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase4DetailsModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeFase4DetailsModal()">
                        &times;
                    </button>
                    <div class="p-6 mt-2">
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="fase4DetailsTitle"></p>
                        <div id="content-details-fase4"></div>
                        <div class="mt-2 flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeFase4DetailsModal()"
                                class="bg-gray-300 mt-4 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="fase4AdminModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase4AdminModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeFase4AdminModal()">
                        &times;
                    </button>
                    <form class="p-6 mt-2" id="fase4AdminForm" enctype="multipart/form-data">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="fase4AdminTitle"></p>
                        <input type="hidden" name="solicitud_id" value="{{ $solicitud->id }}">
                        <div class="mb-4">
                            <label for="estado" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Estado del proyecto:
                            </label>
                            <select name="estado_fase4" id="estado_fase4"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                <option value="" selected disabled>Selecciona una opción</option>
                                <option value="Aprobado">Aprobado</option>
                                <option value="Rechazado">Aplazado</option>
                                <option value="Rechazado">No Aprobado</option>
                            </select>
                            <span id="estado_fase4Error" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="grid grid-cols-1 mb-4 hidden" id="container-doc_firmado_fase4">
                            <label for="doc_firmado_fase4" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Informe (F-DC-125) firmado:
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_firmado_fase4">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400   text-xs leading-4">Solo archivos de Word de máximo {{ env('PESO_MAXIMO_INFORME') }}MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input
                                            type="file"
                                            name="doc_firmado_fase4[]"
                                            id="doc_firmado_fase4"
                                            class="absolute inset-0 opacity-0 cursor-pointer"
                                            accept=".doc,.docx" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="doc_firmado_fase4Error" class="text-red-500 text-sm"></span>
                            <ul id="file-list-firmado-fase4" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-firmado-fase4" class="text-gray-800 text-sm"></span>
                        </div>

                        <div class="grid grid-cols-1 mb-4 hidden" id="container-doc_rejilla_fase4">
                            <label for="doc_rejilla_fase4" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Rejilla de evaluación (F-DC-129):
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_rejilla_fase4">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs leading-4">Solo archivos de Word de máximo {{ env('PESO_MAXIMO_INFORME') }}MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input
                                            type="file"
                                            name="doc_rejilla_fase4[]"
                                            id="doc_rejilla_fase4"
                                            class="absolute inset-0 opacity-0 cursor-pointer"
                                            accept=".doc,.docx" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="doc_rejilla_fase4Error" class="text-red-500 text-sm"></span>
                            <ul id="file-list-rejilla-fase4" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-rejilla-fase4" class="text-gray-800 text-sm"></span>
                        </div>

                        <div class="grid grid-cols-1 mb-4 hidden" id="container-doc_respuesta_fase4">
                            <label for="doc_respuesta_fase4" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Informe (F-DC-125) con comentarios:
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_respuesta_fase4">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400   text-xs leading-4">Solo archivos de Word de máximo {{ env('PESO_MAXIMO_INFORME') }}MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input
                                            type="file"
                                            name="doc_respuesta_fase4[]"
                                            id="doc_respuesta_fase4"
                                            class="absolute inset-0 opacity-0 cursor-pointer"
                                            accept=".doc,.docx" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="doc_respuesta_fase4Error" class="text-red-500 text-sm"></span>
                            <ul id="file-list-respuesta-fase4" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-respuesta-fase4" class="text-gray-800 text-sm"></span>
                        </div>

                        <div class="grid grid-cols-1 mb-4">
                            <label for="doc_turnitin_fase4" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg" id="required-turnitin_fase4">*</span>
                                Informe de plagio (Turnitin):
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_turnitin_fase4">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400 text-xs leading-4">Solo archivos pdf de máximo {{ env('PESO_MAXIMO_INFORME') }}MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input
                                            type="file"
                                            name="doc_turnitin_fase4[]"
                                            id="doc_turnitin_fase4"
                                            class="absolute inset-0 opacity-0 cursor-pointer"
                                            accept=".pdf" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="doc_turnitin_fase4Error" class="text-red-500 text-sm"></span>
                            <ul id="file-list-turnitin-fase4" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-turnitin-fase4" class="text-gray-800 text-sm"></span>
                        </div>

                        <div class="mb-4">
                            <label for="respuesta_fase4" class="block font-medium text-sm text-gray-700" style="margin-bottom: 5px;">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Comentarios de la respuesta:
                            </label>
                            <div id="txt-editor-fase4" class="txt-editor-quill shadow"></div>
                            <textarea name="respuesta_fase4" id="respuesta_fase4" class="hidden"></textarea>
                            <span id="respuesta_fase4Error" class="text-red-500 text-sm"></span>
                        </div>
                        <div class="mt-2 flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeFase4AdminModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button
                                id="fase4AdminButton"
                                type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-fase4AdminResponse" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

    <!-- FASE 5 -->

    <div id="fase5DetailsModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase5DetailsModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeFase5DetailsModal()">
                        &times;
                    </button>
                    <div class="p-6 mt-2">
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="fase5DetailsTitle"></p>
                        <div id="content-details-fase5"></div>
                        <div class="mt-2 flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeFase5DetailsModal()"
                                class="bg-gray-300 mt-4 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="fase5AprobarModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFase5AprobarModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeFase5AprobarModal()">
                        &times;
                    </button>
                    <form class="p-6 mt-2" id="fase5AprobarForm" enctype="multipart/form-data">
                        @csrf
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="fase5AprobarTitle"></p>
                        <input type="hidden" name="solicitud_id" value="{{ $solicitud->id }}">
                        <input type="hidden" name="remitente" value="{{ auth()->user()->id == $evaluador_actual ? 0 : 1 }}">
                        <div class="mb-4">
                            <label for="estado" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Estado del proyecto:
                            </label>
                            <select name="estado_fase5" id="estado_fase5"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                <option value="" selected disabled>Selecciona una opción</option>
                                <option value="Aprobado">Aprobado</option>
                                <option value="Rechazado">Aplazado</option>
                                <option value="Rechazado">No Aprobado</option>
                            </select>
                            <span id="estado_fase5Error" class="text-red-500 text-sm"></span>
                        </div>

                        <div class="grid grid-cols-1 mb-4 hidden" id="container-doc_firmado_fase5">
                            <label for="doc_firmado_fase5" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Informe (F-DC-125) firmado:
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_firmado_fase5">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400   text-xs leading-4">Solo archivos de Word de máximo {{ env('PESO_MAXIMO_INFORME') }}MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input
                                            type="file"
                                            name="doc_firmado_fase5[]"
                                            id="doc_firmado_fase5"
                                            class="absolute inset-0 opacity-0 cursor-pointer"
                                            accept=".doc,.docx" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="doc_firmado_fase5Error" class="text-red-500 text-sm"></span>
                            <ul id="file-list-firmado-fase5" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-firmado-fase5" class="text-gray-800 text-sm"></span>
                        </div>

                        <div class="grid grid-cols-1 mb-4 hidden" id="container-doc_respuesta_fase5">
                            <label for="doc_respuesta_fase5" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg">*</span>
                                Informe (F-DC-125) con comentarios:
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_respuesta_fase5">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400   text-xs leading-4">Solo archivos de Word de máximo {{ env('PESO_MAXIMO_INFORME') }}MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input
                                            type="file"
                                            name="doc_respuesta_fase5[]"
                                            id="doc_respuesta_fase5"
                                            class="absolute inset-0 opacity-0 cursor-pointer"
                                            accept=".doc,.docx" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="doc_respuesta_fase5Error" class="text-red-500 text-sm"></span>
                            <ul id="file-list-respuesta-fase5" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-respuesta-fase5" class="text-gray-800 text-sm"></span>
                        </div>

                        <div class="mb-4">
                            <label for="doc_rejilla_fase5" class="block font-medium text-sm text-gray-700">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                <span class="text-red-600 mr-1 text-lg" id="required-rejilla">*</span>
                                Rejilla (F-DC-129) firmada:
                            </label>
                            <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_rejilla_fase5">
                                <div class="grid gap-1">
                                    <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                    <h2 class="text-center text-gray-400   text-xs leading-4">Solo archivos de Word de máximo {{ env('PESO_MAXIMO_INFORME') }}MB</h2>
                                </div>
                                <div class="grid gap-2">
                                    <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                    <div class="flex items-center justify-center">
                                        <input
                                            type="file"
                                            name="doc_rejilla_fase5[]"
                                            id="doc_rejilla_fase5"
                                            class="absolute inset-0 opacity-0 cursor-pointer"
                                            accept=".doc,.docx" />
                                        <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                    </div>
                                </div>
                            </div>
                            <span id="doc_rejilla_fase5Error" class="text-red-500 text-sm"></span>
                            <ul id="file-list-rejilla-fase5" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                            <span id="files-size-rejilla-fase5" class="text-gray-800 text-sm"></span>
                        </div>

                        @if (auth()->user()->hasRole(['admin', 'super_admin']))
                            <div>
                                <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="nro_acta_fase5" class="block font-medium text-sm text-gray-700">
                                            <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>
                                            Número de acta:
                                        </label>
                                        <input type="text" name="nro_acta_fase5" id="nro_acta_fase5"
                                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                            placeholder="Ingrese el número de acta">
                                        <span id="nro_acta_fase5Error" class="text-red-500 text-sm"></span>
                                    </div>
                                    <div>
                                        <label for="fecha_acta_fase5" class="block font-medium text-sm text-gray-700">
                                            <i class="fa-regular fa-calendar-days mr-1 text-gray-500"></i>
                                            Fecha del acta:
                                        </label>
                                        <input type="date" name="fecha_acta_fase5" id="fecha_acta_fase5"
                                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                        <span id="fecha_acta_fase5Error" class="text-red-500 text-sm"></span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="respuesta_fase5" class="block font-medium text-sm text-gray-700" style="margin-bottom: 5px;">
                                <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                Comentarios de la respuesta:
                            </label>
                            <div id="txt-editor-fase5" class="txt-editor-quill shadow"></div>
                            <textarea name="respuesta_fase5" id="respuesta_fase5" class="hidden"></textarea>
                            <span id="respuesta_fase5Error" class="text-red-500 text-sm"></span>
                        </div>
                        <div class="mt-2 flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeFase5AprobarModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button
                                id="fase5AprobarButton"
                                type="submit"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-fase5AprobarResponse" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

    <!-- FASE FINAL -->

    <div id="faseFinalDetailsModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeFaseFinalDetailsModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeFaseFinalDetailsModal()">
                        &times;
                    </button>
                    <div class="p-6 mt-2">
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="faseFinalDetailsTitle"></p>
                        <div id="content-details-faseFinal"></div>
                        <div class="mt-2 flex justify-end space-x-2">
                            <button
                                type="button"
                                onclick="closeFaseFinalDetailsModal()"
                                class="bg-gray-300 mt-4 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ICFES MODAL -->

    @if ($estado >= 4 && $estado <= 5)
        @if ($submited_icfes === "false")
            <div id="icfesEstudianteModal" class="fixed z-50 inset-0 overflow-y-auto">
                <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeIcfesEstudianteModal()">
                    <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                            <button class="modal-close-btn-custom" onclick="closeIcfesEstudianteModal()">
                                &times;
                            </button>
                            <form id="icfesEstudianteForm" class="p-6 mt-2">
                                @csrf
                                <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="icfesEstudianteTitle"></p>
                                <p class="font-medium text-sm text-gray-700 text-justify">En este formulario el estudiante podrá cargar los resultados de su prueba saber TyT/Pro en dado caso de que pueda hacer parte de los beneficios establecidos en el <a href="https://www.uts.edu.co/sitio/estudiantes/" target="_blank" class="text-blue-500 underline uppercase">reglamento de estímulos</a>. Debe cargar un solo archivo en formato PDF, el comité de trabajos de grado revisará la validéz del documento y dará respuesta al estudiante. Tenga en cuenta la siguiente información:</p>
                                <p class="font-medium text-sm text-gray-700 my-4">Si en su proyecto hay más de un integrante debe tener presente lo siguiente: </p>
                                <ul class="list-disc">
                                    <li class="ml-4 font-medium text-sm text-gray-700 text-justify">Si <strong>UN</strong> solo integrante cumple con el puntaje requerido, únicamente ese integrante deberá cargar desde su cuenta los resultados en formato PDF. El otro u otros integrantes deberán continuar el desarrollo del proyecto de forma normal.</li>
                                    <li class="ml-4 font-medium text-sm text-gray-700 text-justify">Si <strong>TODOS</strong> los integrantes cumplen con el puntaje, cada uno deberá cargar los resultados desde su propia cuenta en formato PDF. El comité se encargará de finalizar todo el proyecto.</li>
                                    <li class="ml-4 font-medium text-sm text-gray-700 text-justify">Si <strong>NINGUNO</strong> cumple con el puntaje, el comité no aprobará ningún beneficio a los estudiantes.</li>
                                </ul>
                                <input type="hidden" name="proyecto_id" id="proyecto_id" value="{{ $solicitud->id }}">
                                <input type="hidden" name="submited_icfes" id="submited_icfes" value="{{ auth()->user()->id }}_true">
                                <div class="grid grid-cols-1 gap-6 mt-4">
                                    <div class="flex items-center gap-2">
                                        <label for="doc_icfes" class="block font-medium text-sm text-gray-700">
                                            <i class="fa-regular fa-bookmark mr-1 text-gray-500"></i>
                                            <span class="text-red-600 mr-1 text-lg">*</span>Evidencias de resultados pruebas TyT/Pro
                                        </label>
                                        <div class="relative inline-block">
                                            <i class="fa-solid fa-circle-question text-uts-500 cursor-pointer tooltip-icon"
                                                data-tooltip="tooltip-doc_icfes"></i>
                                            <div id="tooltip-doc_icfes"
                                                style="width: 10rem; left: -100px;"
                                                class="hidden absolute z-10 max-w-[90vw] px-5 py-4 bg-gray-500 text-white text-sm rounded-lg shadow-lg left-1/2 -translate-x-1/2 bottom-full mb-2 sm:translate-x-0">
                                                <p class="uppercase font-bold mb-2">Instrucciones:</p>
                                                Solo se debe subir un archivo en formato PDF.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_doc_icfes">
                                        <div class="grid gap-1">
                                            <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                            <h2 class="text-center text-gray-400 text-xs leading-4">Solo archivos PDF de máximo 4MB</h2>
                                        </div>
                                        <div class="grid gap-2">
                                            <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                            <div class="flex items-center justify-center">
                                                <input
                                                    type="file"
                                                    name="doc_icfes[]"
                                                    id="doc_icfes"
                                                    class="absolute inset-0 opacity-0 cursor-pointer"
                                                    accept=".pdf" />
                                                <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <span id="doc_icfesError" class="text-red-500 text-sm"></span>
                                <ul id="doc_icfes-file-list" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                                <span id="doc_icfes-files-size" class="text-gray-800 text-sm"></span>
                                <div class="mt-8 flex justify-end space-x-2">
                                    <button
                                        type="button"
                                        onclick="closeIcfesEstudianteModal()"
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                        Cancelar
                                    </button>
                                    <button
                                        id="icfesEstudianteButton"
                                        type="submit"
                                        class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                        <svg id="loadingSpinner-icfesEstudiante" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

            <div id="icfesAdminModal" class="fixed z-50 inset-0 overflow-y-auto">
                <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeIcfesAdminModal()">
                    <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                            <button class="modal-close-btn-custom" onclick="closeIcfesAdminModal()">
                                &times;
                            </button>
                            <form id="icfesAdminForm" class="p-6 mt-2">
                                @csrf
                                <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="icfesAdminTitle"></p>

                                <input type="hidden" name="proyecto_id" id="proyecto_id" value="{{ $solicitud->id }}">

                                <div class="mb-4">
                                    <label for="estado" class="block font-medium text-sm text-gray-700">
                                        <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                        Estado de la solucitud:
                                    </label>
                                    <select name="estado_icfes" id="estado_icfes"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                        <option value="" selected disabled>Selecciona una opción</option>
                                        <option value="Aprobado">Aprobado</option>
                                        <option value="Rechazado">Rechazado</option>
                                    </select>
                                    <span id="estado_icfesError" class="text-red-500 text-sm"></span>
                                </div>

                                <div class="mb-4">
                                    <label for="estudiante_id" class="block font-medium text-sm text-gray-700">
                                        <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                        Integrante del proyecto:
                                    </label>
                                    <select name="estudiante_id" id="estudiante_id-icfes" lang="es"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                        <option value="" selected disabled>Selecciona una opción</option>
                                        @foreach ($lista_integrantes as $integrante)
                                            <option value="{{ $integrante->id }}">{{ $integrante->name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="estudiante_idError" class="text-red-500 text-sm"></span>
                                </div>

                                <div>
                                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                        <div>
                                            <label for="nro_acta_icfes" class="block font-medium text-sm text-gray-700">
                                                <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>
                                                Número de acta:
                                            </label>
                                            <input type="text" name="nro_acta_icfes" id="nro_acta_icfes"
                                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                                placeholder="Ingrese el número de acta">
                                            <span id="nro_acta_icfesError" class="text-red-500 text-sm"></span>
                                        </div>
                                        <div>
                                            <label for="fecha_acta_icfes" class="block font-medium text-sm text-gray-700">
                                                <i class="fa-regular fa-calendar-days mr-1 text-gray-500"></i>
                                                Fecha del acta:
                                            </label>
                                            <input type="date" name="fecha_acta_icfes" id="fecha_acta_icfes"
                                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                            <span id="fecha_acta_icfesError" class="text-red-500 text-sm"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="respuesta_icfes" class="block font-medium text-sm text-gray-700" style="margin-bottom: 5px;">
                                        <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                        Comentarios de la respuesta:
                                    </label>
                                    <div id="txt-editor-icfes" class="txt-editor-quill shadow"></div>
                                    <textarea name="respuesta_icfes" id="respuesta_icfes" class="hidden"></textarea>
                                    <span id="respuesta_icfesError" class="text-red-500 text-sm"></span>
                                </div>

                                <div class="mt-2 flex justify-end space-x-2">
                                    <button
                                        type="button"
                                        onclick="closeIcfesAdminModal()"
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                        Cancelar
                                    </button>
                                    <button
                                        id="icfesAprobarButton"
                                        type="submit"
                                        class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                        <svg id="loadingSpinner-icfesAdmin" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
        @endif
    @endif

    <!-- CONFIGURACIONES DEL PROYECTO -->

    <div id="configModal" class="fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;" onclick="closeConfigModal()">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <button class="modal-close-btn-custom" onclick="closeConfigModal()">
                        &times;
                    </button>
                    @if (auth()->user()->hasRole(['super_admin', 'admin']))
                        <form class="p-6 mt-2" id="configModalAdminForm" enctype="multipart/form-data">
                            @csrf
                            <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="configModalTitle"></p>
                            <input type="hidden" name="solicitud_id" value="{{ $solicitud->id }}">

                            @if ($estado >= 2 && $estado <= 5 && isset($director_actual) && isset($evaluador_actual))
                                <div class="mb-4">
                                    <label for="director_id" class="block font-medium text-sm text-gray-700">
                                        <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                        Director actual:
                                    </label>
                                    <select name="director_id" id="director_id-config" lang="es"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                        <option value="" selected disabled>Selecciona una opción</option>
                                        @foreach ($docentes as $docente)
                                            <option value="{{ $docente->id }}" @if (isset($director_actual) && $docente->id == $director_actual) selected @endif>{{ $docente->name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="director_idError" class="text-red-500 text-sm"></span>
                                </div>
                                <div class="mb-4">
                                    <label for="evaluador_id" class="block font-medium text-sm text-gray-700">
                                        <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                        Evaluador actual:
                                    </label>
                                    <select name="evaluador_id" id="evaluador_id-config" lang="es"
                                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                        <option value="" selected disabled>Selecciona una opción</option>
                                        @foreach ($docentes as $docente)
                                            <option value="{{ $docente->id }}" @if (isset($evaluador_actual) && $docente->id == $evaluador_actual) selected @endif>{{ $docente->name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="evaluador_idError" class="text-red-500 text-sm"></span>
                                </div>
                            @endif

                            @if (isset($fecha_inicio_informe) && isset($fecha_maxima_informe))
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-6 mb-4">
                                    <div>
                                        <label for="fecha_inicio_informe" class="block font-medium text-sm text-gray-700" style="margin-bottom: 5px;">
                                            <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                            Fecha de inicio F-DC-125:
                                        </label>
                                        <input type="date" name="fecha_inicio_informe" id="fecha_inicio_informe" lang="es"
                                            class="bg-gray-200 border-gray-300 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500 cursor-default"
                                            value="{{ $fecha_inicio_informe }}"
                                            readonly>
                                        <span id="fecha_inicio_informeError" class="text-red-500 text-sm"></span>
                                    </div>
                                    <div>
                                        <label for="fecha_maxima_informe" class="block font-medium text-sm text-gray-700" style="margin-bottom: 5px;">
                                            <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                            Fecha máxima para F-DC-125:
                                        </label>
                                        <input type="date" name="fecha_maxima_informe" id="fecha_maxima_informe" lang="es"
                                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                            value="{{ $fecha_maxima_informe }}">
                                        <span id="fecha_maxima_informeError" class="text-red-500 text-sm"></span>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-4">
                                <label for="retirar_estudiante" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Retirar estudiante:
                                </label>
                                <select name="retirar_estudiante" id="retirar_estudiante"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                    <option value="">Seleccione un estudiante</option>
                                    @foreach ($lista_integrantes as $integrante)
                                        <option value="{{ $integrante->id }}">{{ $integrante->name }}</option>
                                    @endforeach
                                </select>
                                <span id="retirar_estudianteError" class="text-red-500 text-sm"></span>
                            </div>

                            <div>
                                <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="nro_acta_fase_1" class="block font-medium text-sm text-gray-700">
                                            <i class="fa-regular fa-file-lines mr-1 text-gray-500"></i>
                                            Número de acta:
                                        </label>
                                        <input type="text" name="nro_acta_ajustes" id="nro_acta_ajustes"
                                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500"
                                            placeholder="Ingrese el número de acta">
                                        <span id="nro_acta_ajustesError" class="text-red-500 text-sm"></span>
                                    </div>
                                    <div>
                                        <label for="fecha_acta_ajustes" class="block font-medium text-sm text-gray-700">
                                            <i class="fa-regular fa-calendar-days mr-1 text-gray-500"></i>
                                            Fecha del acta:
                                        </label>
                                        <input type="date" name="fecha_acta_ajustes" id="fecha_acta_ajustes"
                                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                        <span id="fecha_acta_ajustesError" class="text-red-500 text-sm"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="comentarios_config_admin" class="block font-medium text-sm text-gray-700" style="margin-bottom: 5px;">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Comentarios de la respuesta:
                                </label>
                                <div id="txt-editor-config-admin" class="txt-editor-quill shadow"></div>
                                <textarea name="comentarios_config_admin" id="comentarios_config_admin" class="hidden"></textarea>
                                <span id="comentarios_config_adminError" class="text-red-500 text-sm"></span>
                            </div>  
                            <div class="mt-2 flex justify-end space-x-2">
                                <button
                                    type="button"
                                    onclick="closeConfigModal()"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                    Cancelar
                                </button>
                                <button
                                    id="configAdminModalButton"
                                    type="submit"
                                    class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                    <svg id="loadingSpinner-configAdminModalResponse" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
                    @else
                        <form class="p-6 mt-2" id="configModalForm" enctype="multipart/form-data">
                            @csrf
                            <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;" id="configModalTitle"></p>
                            <p class="font-medium text-sm text-gray-700 mb-6 text-justify">En este formulario el estudiante podrá realizar algunas solicitudes dentro de su proyecto, tales como cambio de director, cambio de evaluador, prórroga y retiro del proyecto.</p>
                            <p class="font-medium text-sm text-gray-700 mb-6 text-justify">- En caso de solicitar <strong>RETIRO</strong>, el estudiante que desee realizar esta solicitud deberá ingresar desde su <strong>PROPIA</strong> cuenta de usuario y adjuntar una carta solicitando el retiro de forma voluntaria.</p>

                            @if ($estado > 3 && $estado < 6)
                                <p class="font-medium text-sm text-gray-700 mb-6 text-justify">- En caso de solicitar <strong>PRÓRROGA</strong>, el estudiante debe tener en cuenta que esta solo se puede solicitar como máximo <strong>DOS</strong> veces en un proyecto y deberá adjuntar una carta solicitando la prórroga junto con el pago de la misma.</p>
                                <div class="flex items-start mb-6">
                                    <p class="font-medium text-sm text-red-700 text-justify">
                                        <i class="fa-solid fa-circle-info text-red-500 text-xl mr-2 mt-1"></i><strong>IMPORTANTE:</strong> El comité de trabajo de grado <strong>únicamente aprobará la prórroga si todos los integrantes activos han realizado el pago correspondiente</strong>. Por lo tanto, <strong>TODOS</strong> los integrantes deberán pagar la <strong>PRÓRROGA</strong>, o en su defecto, solicitar el retiro de forma voluntaria para que los demás integrantes puedan continuar con el proyecto. Para solicitar la prórroga <strong>ÚNICAMENTE</strong> deberá hacerlo un solo integrante del proyecto adjuntando todos los soportes de pago en un único archivo pdf junto con la carta solicitando la prórroga
                                    </p>
                                </div>
                            @endif

                            <input type="hidden" name="solicitud_id" value="{{ $solicitud->id }}">

                            <div class="mb-4">
                                <label for="tipo_solicitud" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Tipo de solicitud:
                                </label>
                                <select name="tipo_solicitud" id="tipo_solicitud"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full focus:ring-uts-500 focus:border-uts-500">
                                    <option value="" selected disabled>Selecciona una opción</option>
                                    <option value="retiro">Retiro del proyecto</option>
                                    @if ($estado > 1)
                                        <option value="Cambio de director">Cambio de director</option>
                                        <option value="Cambio de evaluador">Cambio de evaluador</option>
                                    @endif
                                    @if ($estado == 4 || $estado == 5)
                                        <option value="prorroga">Prórroga</option>
                                    @endif
                                </select>
                                <span id="tipo_solicitudError" class="text-red-500 text-sm"></span>
                            </div>
                            <div id="container-doc_prorroga_config" class="mb-5 hidden">
                                <div class="mb-4">
                                    <label for="carta_prorroga" class="block font-medium text-sm text-gray-700">
                                        <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                        <span class="text-red-600 mr-1 text-lg">*</span>
                                        Carta de solicitud de prórroga:
                                    </label>
                                    <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_carta_prorroga">
                                        <div class="grid gap-1">
                                            <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                            <h2 class="text-center text-gray-400   text-xs leading-4">Solo archivos de PDF de máximo 4MB</h2>
                                        </div>
                                        <div class="grid gap-2">
                                            <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                            <div class="flex items-center justify-center">
                                                <input
                                                    type="file"
                                                    name="carta_prorroga[]"
                                                    id="carta_prorroga"
                                                    class="absolute inset-0 opacity-0 cursor-pointer"
                                                    accept=".pdf" />
                                                <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="carta_prorrogaError" class="text-red-500 text-sm"></span>
                                    <ul id="file-list-prorroga-carta" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                                    <span id="files-size-prorroga-carta" class="text-gray-800 text-sm"></span>
                                </div>
                                <div>
                                    <label for="doc_prorroga" class="block font-medium text-sm text-gray-700">
                                        <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                        <span class="text-red-600 mr-1 text-lg">*</span>
                                        Liquidación y soporte de pago (Prórroga):
                                    </label>
                                    <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_doc_prorroga">
                                        <div class="grid gap-1">
                                            <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                            <h2 class="text-center text-gray-400   text-xs leading-4">Solo archivos de PDF de máximo 4MB</h2>
                                        </div>
                                        <div class="grid gap-2">
                                            <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                            <div class="flex items-center justify-center">
                                                <input
                                                    type="file"
                                                    name="doc_prorroga[]"
                                                    id="doc_prorroga"
                                                    class="absolute inset-0 opacity-0 cursor-pointer"
                                                    accept=".pdf" />
                                                <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="doc_prorrogaError" class="text-red-500 text-sm"></span>
                                    <ul id="file-list-prorroga" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                                    <span id="files-size-prorroga" class="text-gray-800 text-sm"></span>
                                </div>
                            </div>

                            <div id="container-doc_retiro_config" class="mb-5 hidden">
                                <label for="doc_retiro" class="block font-medium text-sm text-gray-700">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    <span class="text-red-600 mr-1 text-lg">*</span>
                                    Carta de solicitud de retiro:
                                </label>
                                <div class="w-full mt-2 relative py-9 bg-gray-50 rounded-2xl border border-2 border-gray-300 gap-3 grid border-dashed" id="dropzone_doc_retiro">
                                    <div class="grid gap-1">
                                        <i class="mx-auto text-4xl text-uts-500 fa-solid fa-cloud-arrow-up"></i>
                                        <h2 class="text-center text-gray-400   text-xs leading-4">Solo archivos de PDF de máximo 4MB</h2>
                                    </div>
                                    <div class="grid gap-2">
                                        <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Arrastra o carga tus archivos aquí</h4>
                                        <div class="flex items-center justify-center">
                                            <input
                                                type="file"
                                                name="doc_retiro[]"
                                                id="doc_retiro"
                                                class="absolute inset-0 opacity-0 cursor-pointer"
                                                accept=".pdf" />
                                            <div class="flex w-28 h-9 px-1 flex-col bg-uts-500 rounded-full shadow text-white text-sm font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">Cargar</div>
                                        </div>
                                    </div>
                                </div>
                                <span id="doc_retiroError" class="text-red-500 text-sm"></span>
                                <ul id="file-list-retiro" class="mt-4 text-gray-600 text-sm list-disc pl-5"></ul>
                                <span id="files-size-retiro" class="text-gray-800 text-sm"></span>
                            </div>

                            <div class="mb-4">
                                <label for="comentarios_config" class="block font-medium text-sm text-gray-700" style="margin-bottom: 5px;">
                                    <i class="fa-solid fa-flag-checkered mr-2 text-gray-500"></i>
                                    Comentarios de la solicitud:
                                </label>
                                <div id="txt-editor-config" class="txt-editor-quill shadow"></div>
                                <textarea name="comentarios_config" id="comentarios_config" class="hidden"></textarea>
                                <span id="comentarios_configError" class="text-red-500 text-sm"></span>
                            </div>
                            <div class="mt-4 flex justify-end space-x-2">
                                <button
                                    type="button"
                                    onclick="closeConfigModal()"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                                    Cancelar
                                </button>
                                <button
                                    id="configModalButton"
                                    type="submit"
                                    class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                    <svg id="loadingSpinner-configModalResponse" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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
        let peso_maximo_pago = Number("{{ env('PESO_MAXIMO_PAGO') }}") || 8;
        let peso_maximo_propuesta = Number("{{ env('PESO_MAXIMO_PROPUESTA') }}") || 8;
        let peso_maximo_informe = Number("{{ env('PESO_MAXIMO_INFORME') }}") || 8;
    </script>
    <script src="{{ asset('js/options/warning.js') }}"></script>
    <script src="{{ asset('js/options/calendar.js') }}"></script>
    <script src="{{ asset('js/fases/fase_1.js') }}"></script>
    <script src="{{ asset('js/fases/fase_2.js') }}"></script>
    <script src="{{ asset('js/fases/fase_3.js') }}"></script>
    <script src="{{ asset('js/fases/fase_4.js') }}"></script>
    <script src="{{ asset('js/fases/fase_5.js') }}"></script>
    <script src="{{ asset('js/options/icfes.js') }}"></script>
    <script src="{{ asset('js/fases/fase_final.js') }}"></script>
    <script>
        const configModalAdminForm = document.getElementById('configModalAdminForm');
        const configModalForm = document.getElementById('configModalForm');

        document.addEventListener("DOMContentLoaded", function() {
            if (sessionStorage.getItem('showToast') === 'true') {
                showToast(sessionStorage.getItem('toastMessage'));
                sessionStorage.removeItem('toastMessage');
                sessionStorage.removeItem('showToast');
            }
        });

        $(document).ready(function () {
            $('#director_id-config').select2({
                placeholder: 'Seleccione un director para el proyecto',
                allowClear: true,
                width: '100%',
                minimumInputLength: 5
            });

            $('#evaluador_id-config').select2({
                placeholder: 'Seleccione un evaluador para el proyecto',
                allowClear: true,
                width: '100%',
                minimumInputLength: 5
            });

            $('#estudiante_id-icfes').select2({
                placeholder: 'Seleccione un integrante del proyecto',
                allowClear: true,
                width: '100%',
            });

            $('#retirar_estudiante').select2({
                placeholder: 'Seleccione un estudiante para retirar',
                allowClear: true,
                width: '100%',
            });
        });

        function openConfigModal() {
            new fileInput('carta_prorroga', 'dropzone_carta_prorroga', 'pdf', 1, 4, 'file-list-prorroga-carta', 'files-size-prorroga-carta');
            new fileInput('doc_prorroga', 'dropzone_doc_prorroga', 'pdf', 1, 4, 'file-list-prorroga', 'files-size-prorroga');
            new fileInput('doc_retiro', 'dropzone_doc_retiro', 'pdf', 1, 4, 'file-list-retiro', 'files-size-retiro');

            initQuillEditor(undefined, "Describa su solicitud detalladamente.", 'txt-editor-config', 'comentarios_config');

            $('#configModalTitle').html(`Ajustes del <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Proyecto</span>`);

            $('#tipo_solicitud').val('').trigger('change');
            $('#carta_prorroga').val('');
            $('#doc_prorroga').val('');
            $('#doc_retiro').val('');
            $('#comentarios_config').val('');

            $('#tipo_solicitudError').text('');
            $('#carta_prorrogaError').text('');
            $('#doc_prorrogaError').text('');
            $('#doc_retiroError').text('');
            $('#comentarios_configError').text('');

            $('#configModal').addClass('show');
        }

        function openConfigAdminModal() {
            initQuillEditor(undefined, "Describa la respuesta para el estudiante.", 'txt-editor-config-admin', 'comentarios_config_admin');

            $('#configModalTitle').html(`Ajustes del <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Proyecto</span>`);

            $('#director_id').val('').trigger('change');
            $('#evaluador_id').val('').trigger('change');
            $('#retirar_estudiante').val('').trigger('change');
            $('#nro_acta_ajustes').val('');
            $('#fecha_acta_ajustes').val('');
            $('#comentarios_config_admin').val('');

            $('#director_idError').text('');
            $('#evaluador_idError').text('');
            $('#fecha_inicio_informeError').text('');
            $('#fecha_maxima_informeError').text('');
            $('#retirar_estudianteError').text('');
            $('#nro_acta_ajustesError').text('');
            $('#fecha_acta_ajustesError').text('');
            $('#comentarios_config_adminError').text('');

            $('#configModal').addClass('show');
        }

        $(document).ready(function () {
            function toggleTipoSolicitudConfig() {
                const tipoSolicitud = $('#tipo_solicitud').val();

                if (tipoSolicitud === 'prorroga') {
                    $('#container-doc_prorroga_config').removeClass('hidden');
                } else {
                    $('#container-doc_prorroga_config').addClass('hidden');
                }

                if (tipoSolicitud === 'retiro') {
                    $('#container-doc_retiro_config').removeClass('hidden');
                } else {
                    $('#container-doc_retiro_config').addClass('hidden');
                }
            }

            toggleTipoSolicitudConfig();
            $('#tipo_solicitud').on('change', toggleTipoSolicitudConfig);
        });

        function closeConfigModal() {
            $('#configModal').removeClass('show');
        }

        if (configModalAdminForm) {
            const originalValues = {
                'director_id-config': $('#director_id-config').val(),
                'evaluador_id-config': $('#evaluador_id-config').val(),
                'retirar_estudiante': $('#retirar_estudiante').val(),
            };

            $('#configModalAdminForm').on('submit', function(e) {
                e.preventDefault();

                const campos = [
                    'director_id-config',
                    'evaluador_id-config',
                    'fecha_inicio_informe',
                    'fecha_maxima_informe',
                    'retirar_estudiante',
                ];

                let cambios = [];
                
                campos.forEach(id => {
                    const $campo = $(`#${id}`);

                    if ($campo.length) {
                        const valorOriginal = originalValues[id] ?? $campo[0].defaultValue;
                        const valorActual = $campo.val();

                        if (valorActual !== valorOriginal) {
                            cambios.push(id);
                        }
                    }
                });

                if (cambios.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Sin cambios',
                        text: 'Debe realizar al menos un cambio.',
                        heightAuto: false
                    });
                    return;
                }

                if (cambios.length > 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Demasiados procesos',
                        text: 'Solo se permite realizar un proceso a la vez.',
                        heightAuto: false
                    });
                    return;
                }

                const button = document.getElementById(`configAdminModalButton`);
                const loadingSpinner = document.getElementById(`loadingSpinner-configAdminModalResponse`);

                const url = "{{ route('proyectos.configurar_admin') }}";
                const method = 'POST';

                const formData = new FormData(this);

                Swal.fire({
                    heightAuto: false,
                    title: '¿Está seguro?',
                    text: "Está a punto de enviar la respuesta al estudiante",
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
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                closeConfigModal();
                                showToast('Respuesta enviada correctamente');
                            },
                            error: function(xhr) {
                                const errors = xhr.responseJSON.errors;

                                $('#director_idError').text(errors?.director_id?.[0] || '');
                                $('#evaluador_idError').text(errors?.evaluador_id?.[0] || '');
                                $('#fecha_inicio_informeError').text(errors?.fecha_inicio_informe?.[0] || '');
                                $('#fecha_maxima_informeError').text(errors?.fecha_maxima_informe?.[0] || '');
                                $('#retirar_estudianteError').text(errors?.retirar_estudiante?.[0] || '');
                                $('#nro_acta_ajustesError').text(errors?.nro_acta_ajustes?.[0] || '');
                                $('#fecha_acta_ajustesError').text(errors?.fecha_acta_ajustes?.[0] || '');
                                $('#comentarios_config_adminError').text(errors?.comentarios_config_admin?.[0] || '');
                            },
                            complete: function() {
                                loaderGeneral.classList.replace('flex', 'hidden');
                                loadingSpinner.classList.add('hidden');
                            }
                        });
                    }
                });
            });
        }

        if (configModalForm) {
            $('#configModalForm').on('submit', function(e) {
                e.preventDefault();

                const button = document.getElementById(`fase1EstudianteButton`);
                const loadingSpinner = document.getElementById(`loadingSpinner-configModalResponse`);

                const url = "{{ route('proyectos.configurar_estudiante') }}";
                const method = 'POST';

                const formData = new FormData(this);

                Swal.fire({
                    heightAuto: false,
                    title: '¿Está seguro?',
                    text: "Está a punto de enviar la solicitud",
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
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                closeConfigModal();
                                showToast('Solicitud enviada, tendrá respuesta en los proximos 5 días hábiles');
                            },
                            error: function(xhr) {
                                const errors = xhr.responseJSON.errors;
                                $('#tipo_solicitudError').text(errors?.tipo_solicitud?.[0] || '');
                                $('#doc_prorrogaError').text(errors?.doc_prorroga?.[0] || '');
                                $('#carta_prorrogaError').text(errors?.carta_prorroga?.[0] || '');
                                $('#doc_retiroError').text(errors?.doc_retiro?.[0] || '');
                                $('#comentarios_configError').text(errors?.comentarios_config?.[0] || '');
                            },
                            complete: function() {
                                loaderGeneral.classList.replace('flex', 'hidden');
                                loadingSpinner.classList.add('hidden');
                            }
                        });
                    }
                });
            });
        }

        $('#fase1EstudianteForm').on('submit', function(e) {
            e.preventDefault();

            const button = document.getElementById(`configModalButton`);
            const loadingSpinner = document.getElementById(`loadingSpinner-fase1Estudiante`);

            const url = "{{ route('roadmap.fase_1') }}";
            const method = 'POST';

            const formData = new FormData(this);

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
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#fase1-estudiante-button').addClass('hidden');
                            closeFase1EstudianteModal();
                            showToast('Información enviada, tendrá respuesta en los proximos 5 días hábiles');
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON.errors;
                            $('#check_idea_bancoError').text(errors?.check_idea_banco?.[0] || '');
                            $('#idea_bancoError').text(errors?.idea_banco?.[0] || '');
                            $('#tituloError').text(errors?.titulo?.[0] || '');
                            $('#objetivoError').text(errors?.objetivo?.[0] || '');
                            $('#linea_investigacionError').text(errors?.linea_investigacion?.[0] || '');
                            $('#descripcionError').text(errors?.descripcion?.[0] || '');
                            $('#soporte_pagoError').text(errors?.soporte_pago?.[0] || '');
                        },
                        complete: function() {
                            loaderGeneral.classList.replace('flex', 'hidden');
                            loadingSpinner.classList.add('hidden');
                        }
                    });
                }
            });
        });

        $('#fase1AdminForm').on('submit', function(e) {
            e.preventDefault();

            const button = document.getElementById(`fase1AdminButton`);
            const loadingSpinner = document.getElementById(`loadingSpinner-fase1AdminResponse`);

            const url = "{{ route('roadmap.reply_fase1') }}";
            const method = 'POST';

            const formData = new FormData(this);

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
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#buttons-admin-fase1').addClass('hidden');
                            closeFase1AdminModal();
                            sessionStorage.setItem('showToast', 'true');
                            sessionStorage.setItem('toastMessage', 'Respuesta enviada correctamente');
                            location.reload();
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON.errors;

                            $('#estadoError').text(errors?.estado?.[0] || '');
                            $('#nro_acta_fase_1Error').text(errors?.nro_acta_fase_1?.[0] || '');
                            $('#fecha_acta_fase_1Error').text(errors?.fecha_acta_fase_1?.[0] || '');
                            $('#codigo_modalidadError').text(errors?.codigo_modalidad?.[0] || '');
                            $('#directorError').text(errors?.director?.[0] || '');
                            $('#evaluadorError').text(errors?.evaluador?.[0] || '');
                            $('#respuesta_fase1Error').text(errors?.respuesta_fase1?.[0] || '');
                        },
                        complete: function() {
                            loaderGeneral.classList.replace('flex', 'hidden');
                            loadingSpinner.classList.add('hidden');
                        }
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>