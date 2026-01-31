<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="Sistemas trabajo de grado UTS">
    <meta name="keywords" content="Sistemas trabajo de grado UTS">

    <title>Trabajos de grado - Panel de control</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}" type="image/x-icon">

    <!-- DATATABLES -->
    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/date-1.5.4/fc-5.0.4/fh-4.0.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sb-1.8.1/datatables.min.css" rel="stylesheet">

    <!-- Quill CDN -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

    <!-- Select2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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

        #completeProfileModal {
            visibility: hidden;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
        }

        #completeProfileModal.show {
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

        input[type="checkbox"] {
            accent-color: #C1D631 !important;
        }
    </style>

    @stack('styles')

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/datatables.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="bg-gray-100 h-screen flex flex-col p-4">
    @livewire('navigation-menu')

    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

    <div class="overlay" id="overlay"></div>

    <div id="loaderGeneralOverlay" class="flex">
        <div class="loader-general-spinner-container">
            <div class="loader-general-spinner"></div>
        </div>
    </div>

    <div class="flex gap-4 flex-1 min-h-0">
        <aside id="sidebarMenu"
            class="w-64 bg-white rounded-xl shadow-md p-4 overflow-y-auto transition-width duration-300 ease-in-out">
            <nav>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 w-full p-3 {{ request()->routeIs('dashboard') ? 'bg-uts-500 text-white' : 'text-gray-600 hover:bg-uts-500 hover:text-white' }} rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" data-name="Layer 1" viewBox="0 0 24 24" id="house-user">
                                <path fill="currentColor" d="m21.664 10.252-9-8a.999.999 0 0 0-1.328 0l-9 8a1 1 0 0 0 1.328 1.496L4 11.449V21a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-9.551l.336.299a1 1 0 0 0 1.328-1.496ZM9.184 20a2.982 2.982 0 0 1 5.632 0Zm1.316-5.5A1.5 1.5 0 1 1 12 16a1.502 1.502 0 0 1-1.5-1.5ZM18 20h-1.101a5 5 0 0 0-2.259-3.228 3.468 3.468 0 0 0 .86-2.272 3.5 3.5 0 0 0-7 0 3.468 3.468 0 0 0 .86 2.272A5 5 0 0 0 7.1 20H6V9.671l6-5.333 6 5.333Z"></path>
                            </svg>
                            <span class="nav-text">Inicio</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profile.show') }}"
                            class="flex items-center gap-3 w-full p-3 {{ request()->routeIs('profile.show') ? 'bg-uts-500 text-white' : 'text-gray-600 hover:bg-uts-500 hover:text-white' }} rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" id="user-square">
                                <path fill="currentColor" d="M14.81,12.28a3.73,3.73,0,0,0,1-2.5,3.78,3.78,0,0,0-7.56,0,3.73,3.73,0,0,0,1,2.5A5.94,5.94,0,0,0,6,16.89a1,1,0,0,0,2,.22,4,4,0,0,1,7.94,0A1,1,0,0,0,17,18h.11a1,1,0,0,0,.88-1.1A5.94,5.94,0,0,0,14.81,12.28ZM12,11.56a1.78,1.78,0,1,1,1.78-1.78A1.78,1.78,0,0,1,12,11.56ZM19,2H5A3,3,0,0,0,2,5V19a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V5A3,3,0,0,0,19,2Zm1,17a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V5A1,1,0,0,1,5,4H19a1,1,0,0,1,1,1Z"></path>
                            </svg>
                            <span class="nav-text">Mi perfil</span>
                        </a>
                    </li>
                    <li>
                        @can('view_users')
                        <a href="{{ route('users.index') }}"
                            class="flex items-center gap-3 w-full p-3 {{ request()->routeIs('users.index') ? 'bg-uts-500 text-white' : 'text-gray-600 hover:bg-uts-500 hover:text-white' }} rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" id="users-alt">
                                <path fill="currentColor" d="M12.3,12.22A4.92,4.92,0,0,0,14,8.5a5,5,0,0,0-10,0,4.92,4.92,0,0,0,1.7,3.72A8,8,0,0,0,1,19.5a1,1,0,0,0,2,0,6,6,0,0,1,12,0,1,1,0,0,0,2,0A8,8,0,0,0,12.3,12.22ZM9,11.5a3,3,0,1,1,3-3A3,3,0,0,1,9,11.5Zm9.74.32A5,5,0,0,0,15,3.5a1,1,0,0,0,0,2,3,3,0,0,1,3,3,3,3,0,0,1-1.5,2.59,1,1,0,0,0-.5.84,1,1,0,0,0,.45.86l.39.26.13.07a7,7,0,0,1,4,6.38,1,1,0,0,0,2,0A9,9,0,0,0,18.74,11.82Z"></path>
                            </svg>
                            <span class="nav-text">Usuarios</span>
                        </a>
                        @endcan
                    </li>
                    <li>
                        @can('view_roles')
                        <a href="{{ route('roles.index') }}"
                            class="flex items-center gap-3 w-full p-3 {{ request()->routeIs('roles.index') || request()->routeIs('roles.permissions') ? 'bg-uts-500 text-white' : 'text-gray-600 hover:bg-uts-500 hover:text-white' }} rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" id="shield-check">
                                <path fill="currentColor" d="M19.63,3.65a1,1,0,0,0-.84-.2,8,8,0,0,1-6.22-1.27,1,1,0,0,0-1.14,0A8,8,0,0,1,5.21,3.45a1,1,0,0,0-.84.2A1,1,0,0,0,4,4.43v7.45a9,9,0,0,0,3.77,7.33l3.65,2.6a1,1,0,0,0,1.16,0l3.65-2.6A9,9,0,0,0,20,11.88V4.43A1,1,0,0,0,19.63,3.65ZM18,11.88a7,7,0,0,1-2.93,5.7L12,19.77,8.93,17.58A7,7,0,0,1,6,11.88V5.58a10,10,0,0,0,6-1.39,10,10,0,0,0,6,1.39ZM13.54,9.59l-2.69,2.7-.89-.9a1,1,0,0,0-1.42,1.42l1.6,1.6a1,1,0,0,0,1.42,0L15,11a1,1,0,0,0-1.42-1.42Z"></path>
                            </svg>
                            <span class="nav-text">Roles</span>
                        </a>
                        @endcan
                    </li>
                    <li>
                        @can('view_permissions')
                        <a href="{{ route('permissions.index') }}"
                            class="flex items-center gap-3 w-full p-3 {{ request()->routeIs('permissions.index') ? 'bg-uts-500 text-white' : 'text-gray-600 hover:bg-uts-500 hover:text-white' }} rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" id="padlock">
                                <path fill="currentColor" d="M12,13a1.49,1.49,0,0,0-1,2.61V17a1,1,0,0,0,2,0V15.61A1.49,1.49,0,0,0,12,13Zm5-4V7A5,5,0,0,0,7,7V9a3,3,0,0,0-3,3v7a3,3,0,0,0,3,3H17a3,3,0,0,0,3-3V12A3,3,0,0,0,17,9ZM9,7a3,3,0,0,1,6,0V9H9Zm9,12a1,1,0,0,1-1,1H7a1,1,0,0,1-1-1V12a1,1,0,0,1,1-1H17a1,1,0,0,1,1,1Z"></path>
                            </svg>
                            <span class="nav-text">Permisos</span>
                        </a>
                        @endcan
                    </li>
                    <li>
                        @can('view_banco_ideas')
                        <a href="{{ route('banco.index') }}"
                            class="flex items-center gap-3 w-full p-3 {{ request()->routeIs('banco.index') ? 'bg-uts-500 text-white' : 'text-gray-600 hover:bg-uts-500 hover:text-white' }} rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" data-name="Layer 1" viewBox="0 0 24 24" id="notes">
                                <path fill="currentColor" d="M16,14H8a1,1,0,0,0,0,2h8a1,1,0,0,0,0-2Zm0-4H10a1,1,0,0,0,0,2h6a1,1,0,0,0,0-2Zm4-6H17V3a1,1,0,0,0-2,0V4H13V3a1,1,0,0,0-2,0V4H9V3A1,1,0,0,0,7,3V4H4A1,1,0,0,0,3,5V19a3,3,0,0,0,3,3H18a3,3,0,0,0,3-3V5A1,1,0,0,0,20,4ZM19,19a1,1,0,0,1-1,1H6a1,1,0,0,1-1-1V6H7V7A1,1,0,0,0,9,7V6h2V7a1,1,0,0,0,2,0V6h2V7a1,1,0,0,0,2,0V6h2Z"></path>
                            </svg>
                            <span class="nav-text">Banco de ideas</span>
                        </a>
                        @endcan
                    </li>
                    <li>
                        @if (!auth()->user()->hasRole('docente') && !auth()->user()->hasRole('director') && !auth()->user()->hasRole('evaluador'))
                        @can('view_proyectos_grado')
                        <a href="{{ route('proyectos.index') }}"
                            class="flex items-center gap-3 w-full p-3 {{ request()->routeIs('proyectos.index') || request()->routeIs('roadmap.index') ? 'bg-uts-500 text-white' : 'text-gray-600 hover:bg-uts-500 hover:text-white' }} rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" data-name="Layer 1" viewBox="0 0 24 24" id="graduation-cap">
                                <path fill="currentColor" d="M21.49,10.19l-1-.55h0l-9-5-.11,0a1.06,1.06,0,0,0-.19-.06l-.19,0-.18,0a1.17,1.17,0,0,0-.2.06l-.11,0-9,5a1,1,0,0,0,0,1.74L4,12.76V17.5a3,3,0,0,0,3,3h8a3,3,0,0,0,3-3V12.76l2-1.12V14.5a1,1,0,0,0,2,0V11.06A1,1,0,0,0,21.49,10.19ZM16,17.5a1,1,0,0,1-1,1H7a1,1,0,0,1-1-1V13.87l4.51,2.5.15.06.09,0a1,1,0,0,0,.25,0h0a1,1,0,0,0,.25,0l.09,0a.47.47,0,0,0,.15-.06L16,13.87Zm-5-3.14L4.06,10.5,11,6.64l6.94,3.86Z"></path>
                            </svg>
                            @if (auth()->user()->hasRole('estudiante'))
                            <span class="nav-text">Mis proyectos</span>
                            @else
                            <span class="nav-text">Proyectos de grado</span>
                            @endif
                        </a>
                        @endcan
                        @endif
                    </li>
                    <li>
                        @can('view_propuestas_banco')
                        <a href="{{ route('propuestas.index') }}"
                            class="flex items-center gap-3 w-full p-3 {{ request()->routeIs('propuestas.index') ? 'bg-uts-500 text-white' : 'text-gray-600 hover:bg-uts-500 hover:text-white' }} rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" data-name="Layer 1" viewBox="0 0 24 24" id="panel-add">
                                <path fill="currentColor" d="M18 10h-4V3a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v5H3a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h15a1 1 0 0 0 1-1V11a1 1 0 0 0-1-1ZM7 20H4V10h3Zm5 0H9V4h3Zm5 0h-3v-8h3Zm4-16h-1V3a1 1 0 0 0-2 0v1h-1a1 1 0 0 0 0 2h1v1a1 1 0 0 0 2 0V6h1a1 1 0 0 0 0-2Z"></path>
                            </svg>
                            <span class="nav-text">Propuestas Banco</span>
                        </a>
                        @endcan
                    </li>
                    @if (auth()->user()->hasRole(['director']))
                    <li>
                        <a href="{{ route('director.index') }}"
                            class="flex items-center gap-3 w-full p-3 {{ request()->routeIs('director.index') || request()->routeIs('director.roadmap') ? 'bg-uts-500 text-white' : 'text-gray-600 hover:bg-uts-500 hover:text-white' }} rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" id="user-check">
                                <path fill="currentColor" d="M13.3,12.22A4.92,4.92,0,0,0,15,8.5a5,5,0,0,0-10,0,4.92,4.92,0,0,0,1.7,3.72A8,8,0,0,0,2,19.5a1,1,0,0,0,2,0,6,6,0,0,1,12,0,1,1,0,0,0,2,0A8,8,0,0,0,13.3,12.22ZM10,11.5a3,3,0,1,1,3-3A3,3,0,0,1,10,11.5ZM21.71,9.13a1,1,0,0,0-1.42,0l-2,2-.62-.63a1,1,0,0,0-1.42,0,1,1,0,0,0,0,1.41l1.34,1.34a1,1,0,0,0,1.41,0l2.67-2.67A1,1,0,0,0,21.71,9.13Z"></path>
                            </svg>
                            <span class="nav-text">Docente Director</span>
                        </a>
                    </li>
                    @endif
                    @if (auth()->user()->hasRole(['evaluador']))
                    <li>
                        <a href="{{ route('evaluador.index') }}"
                            class="flex items-center gap-3 w-full p-3 {{ request()->routeIs('evaluador.index') || request()->routeIs('evaluador.roadmap') ? 'bg-uts-500 text-white' : 'text-gray-600 hover:bg-uts-500 hover:text-white' }} rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" data-name="Layer 1" viewBox="0 0 24 24" id="user-md">
                                <path fill="currentColor" d="m15.899 13.229-.005-.002c-.063-.027-.124-.058-.188-.083A5.988 5.988 0 0 0 18 8.434a5.29 5.29 0 0 0-.045-.63.946.946 0 0 0 .038-.122l.281-2.397a3.006 3.006 0 0 0-2.442-3.302l-.79-.143a16.931 16.931 0 0 0-6.083 0l-.791.143a3.006 3.006 0 0 0-2.442 3.302l.28 2.397a.946.946 0 0 0 .039.122 5.29 5.29 0 0 0-.045.63 5.988 5.988 0 0 0 2.294 4.71c-.064.025-.125.056-.188.083l-.005.002a9.948 9.948 0 0 0-6.035 8.097 1 1 0 0 0 1.988.217 7.948 7.948 0 0 1 4.216-6.185l3.023 3.023a1 1 0 0 0 1.414 0l3.023-3.023a7.948 7.948 0 0 1 4.216 6.185 1 1 0 0 0 .992.892 1.048 1.048 0 0 0 .11-.006 1 1 0 0 0 .886-1.103 9.948 9.948 0 0 0-6.036-8.097ZM7.712 5.051a1.002 1.002 0 0 1 .814-1.1l.79-.143a14.93 14.93 0 0 1 5.368 0l.79.143a1.002 1.002 0 0 1 .814 1.1l-.178 1.514H7.89ZM12 16.261l-1.65-1.651a7.85 7.85 0 0 1 3.3 0Zm0-3.826a4.005 4.005 0 0 1-3.998-3.87h7.996A4.005 4.005 0 0 1 12 12.435Z"></path>
                            </svg>
                            <span class="nav-text">Docente Evaluador</span>
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ route('documental.index') }}"
                            class="flex items-center gap-3 w-full p-3 {{ request()->routeIs('documental.index') ? 'bg-uts-500 text-white' : 'text-gray-600 hover:bg-uts-500 hover:text-white' }} rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" data-name="Layer 1" viewBox="0 0 24 24" id="cloud-download">
                                <path fill="currentColor" d="M14.29,17.29,13,18.59V13a1,1,0,0,0-2,0v5.59l-1.29-1.3a1,1,0,0,0-1.42,1.42l3,3a1,1,0,0,0,.33.21.94.94,0,0,0,.76,0,1,1,0,0,0,.33-.21l3-3a1,1,0,0,0-1.42-1.42ZM18.42,6.22A7,7,0,0,0,5.06,8.11,4,4,0,0,0,6,16a1,1,0,0,0,0-2,2,2,0,0,1,0-4A1,1,0,0,0,7,9a5,5,0,0,1,9.73-1.61,1,1,0,0,0,.78.67,3,3,0,0,1,.24,5.84,1,1,0,1,0,.5,1.94,5,5,0,0,0,.17-9.62Z"></path>
                            </svg>
                            <span class="nav-text">Base documental</span>
                        </a>
                    </li>
                    @if (auth()->user()->hasRole(['super_admin', 'admin', 'coordinador']))
                    <li>
                        <a href="{{ route('historico.index') }}"
                            class="flex items-center gap-3 w-full p-3 {{ request()->routeIs('historico.index') ? 'bg-uts-500 text-white' : 'text-gray-600 hover:bg-uts-500 hover:text-white' }} rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" id="newspaper">
                                <path fill="currentColor" d="M17,11H16a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm0,4H16a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2ZM11,9h6a1,1,0,0,0,0-2H11a1,1,0,0,0,0,2ZM21,3H7A1,1,0,0,0,6,4V7H3A1,1,0,0,0,2,8V18a3,3,0,0,0,3,3H18a4,4,0,0,0,4-4V4A1,1,0,0,0,21,3ZM6,18a1,1,0,0,1-2,0V9H6Zm14-1a2,2,0,0,1-2,2H7.82A3,3,0,0,0,8,18V5H20Zm-9-4h1a1,1,0,0,0,0-2H11a1,1,0,0,0,0,2Zm0,4h1a1,1,0,0,0,0-2H11a1,1,0,0,0,0,2Z"></path>
                            </svg>
                            <span class="nav-text">Módulo Histórico</span>
                        </a>
                    </li>
                    @endif
                    @if (auth()->user()->hasRole(['super_admin', 'admin', 'coordinador']))
                    <li>
                        <a href="{{ route('ajustes.index') }}"
                            class="flex items-center gap-3 w-full p-3 {{ request()->routeIs('ajustes.index') ? 'bg-uts-500 text-white' : 'text-gray-600 hover:bg-uts-500 hover:text-white' }} rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" data-name="Layer 1" viewBox="0 0 640 512" id="settings-panel">
                                <path fill="currentColor" d="M308.5 135.3c7.1-6.3 9.9-16.2 6.2-25c-2.3-5.3-4.8-10.5-7.6-15.5L304 89.4c-3-5-6.3-9.9-9.8-14.6c-5.7-7.6-15.7-10.1-24.7-7.1l-28.2 9.3c-10.7-8.8-23-16-36.2-20.9L199 27.1c-1.9-9.3-9.1-16.7-18.5-17.8C173.9 8.4 167.2 8 160.4 8l-.7 0c-6.8 0-13.5 .4-20.1 1.2c-9.4 1.1-16.6 8.6-18.5 17.8L115 56.1c-13.3 5-25.5 12.1-36.2 20.9L50.5 67.8c-9-3-19-.5-24.7 7.1c-3.5 4.7-6.8 9.6-9.9 14.6l-3 5.3c-2.8 5-5.3 10.2-7.6 15.6c-3.7 8.7-.9 18.6 6.2 25l22.2 19.8C32.6 161.9 32 168.9 32 176s.6 14.1 1.7 20.9L11.5 216.7c-7.1 6.3-9.9 16.2-6.2 25c2.3 5.3 4.8 10.5 7.6 15.6l3 5.2c3 5.1 6.3 9.9 9.9 14.6c5.7 7.6 15.7 10.1 24.7 7.1l28.2-9.3c10.7 8.8 23 16 36.2 20.9l6.1 29.1c1.9 9.3 9.1 16.7 18.5 17.8c6.7 .8 13.5 1.2 20.4 1.2s13.7-.4 20.4-1.2c9.4-1.1 16.6-8.6 18.5-17.8l6.1-29.1c13.3-5 25.5-12.1 36.2-20.9l28.2 9.3c9 3 19 .5 24.7-7.1c3.5-4.7 6.8-9.5 9.8-14.6l3.1-5.4c2.8-5 5.3-10.2 7.6-15.5c3.7-8.7 .9-18.6-6.2-25l-22.2-19.8c1.1-6.8 1.7-13.8 1.7-20.9s-.6-14.1-1.7-20.9l22.2-19.8zM112 176a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zM504.7 500.5c6.3 7.1 16.2 9.9 25 6.2c5.3-2.3 10.5-4.8 15.5-7.6l5.4-3.1c5-3 9.9-6.3 14.6-9.8c7.6-5.7 10.1-15.7 7.1-24.7l-9.3-28.2c8.8-10.7 16-23 20.9-36.2l29.1-6.1c9.3-1.9 16.7-9.1 17.8-18.5c.8-6.7 1.2-13.5 1.2-20.4s-.4-13.7-1.2-20.4c-1.1-9.4-8.6-16.6-17.8-18.5L583.9 307c-5-13.3-12.1-25.5-20.9-36.2l9.3-28.2c3-9 .5-19-7.1-24.7c-4.7-3.5-9.6-6.8-14.6-9.9l-5.3-3c-5-2.8-10.2-5.3-15.6-7.6c-8.7-3.7-18.6-.9-25 6.2l-19.8 22.2c-6.8-1.1-13.8-1.7-20.9-1.7s-14.1 .6-20.9 1.7l-19.8-22.2c-6.3-7.1-16.2-9.9-25-6.2c-5.3 2.3-10.5 4.8-15.6 7.6l-5.2 3c-5.1 3-9.9 6.3-14.6 9.9c-7.6 5.7-10.1 15.7-7.1 24.7l9.3 28.2c-8.8 10.7-16 23-20.9 36.2L315.1 313c-9.3 1.9-16.7 9.1-17.8 18.5c-.8 6.7-1.2 13.5-1.2 20.4s.4 13.7 1.2 20.4c1.1 9.4 8.6 16.6 17.8 18.5l29.1 6.1c5 13.3 12.1 25.5 20.9 36.2l-9.3 28.2c-3 9-.5 19 7.1 24.7c4.7 3.5 9.5 6.8 14.6 9.8l5.4 3.1c5 2.8 10.2 5.3 15.5 7.6c8.7 3.7 18.6 .9 25-6.2l19.8-22.2c6.8 1.1 13.8 1.7 20.9 1.7s14.1-.6 20.9-1.7l19.8 22.2zM464 304a48 48 0 1 1 0 96 48 48 0 1 1 0-96z" />
                            </svg>
                            <span class="nav-text">Configuraciones</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </nav>
        </aside>

        <main class="flex-1 bg-white rounded-xl shadow-md p-6 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>

    @if(auth()->user()->hasRole(['estudiante']) && !isset(auth()->user()->nivel_id) && !request()->routeIs('profile.show'))
    <div id="completeProfileModal" class="show fixed z-50 inset-0 overflow-y-auto">
        <div class="modal-overlay absolute inset-0" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;">
            <div class="flex items-center justify-center min-h-screen pt-3 text-center relative">
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content relative" onclick="event.stopPropagation()">
                    <div class="p-6 mt-2">
                        <p class="text-2xl font-bold" style="margin: 0.8rem 0 1.5rem 0;">
                            Completar <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Perfil</span>
                        </p>
                        <div class="mb-4">
                            <p class="text-md">Estimado usuario, actualmente evidenciamos que su perfil se encuentra incompleto, debe ingresar al modúlo "Mi Perfil" y completar la siguiente información:</p>
                            <ol class="ps-5 mt-6 mb-6 space-y-1 list-decimal list-inside">
                                <li>Nombre completo.</li>
                                <li>Tipo de documento.</li>
                                <li>Número de documento.</li>
                                <li>Nivel académico.</li>
                                <li>Número de teléfono.</li>
                                <li>Correo institucional.</li>
                            </ol>
                            <p><strong>NOTA: </strong>Si considera que esto es un error, póngase en contacto con los administradores inmediatamente.</p>
                        </div>
                        <div class="flex justify-end space-x-2 mt-6">
                            <a
                                id="completeProfileModalButton"
                                href="{{ route('profile.show') }}"
                                class="flex bg-uts-500 hover:bg-uts-800 text-white px-4 py-2 rounded-lg">
                                <svg id="loadingSpinner-completeProfile" style="margin: 4px 10px 4px 0" class="hidden w-4 h-4 text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
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

    @stack('modals')

    @livewireScripts

    <script src="{{ asset('js/loader.js') }}" defer></script>
    <script src="{{ asset('js/app-layout.js') }}" defer></script>
    <script src="{{ asset('js/initDatatable.js') }}" defer></script>
    <script src="{{ asset('js/fileInput.js') }}" defer></script>
    <script src="{{ asset('js/toolTip.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DATATABLE -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/date-1.5.4/fc-5.0.4/fh-4.0.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sb-1.8.1/datatables.min.js"></script>

    <!-- Quill CDN -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="{{ asset('js/quill-custom.js') }}"></script>

    <!-- Select2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/es.min.js" integrity="sha512-xntXNPHoIOoLxuqmYhDB6MA67yimB0HxKb20FTgBcAO7RUk2jwctNYIkencPjG4hdxde8ee6FHqACJqGYYSiSg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        const loaderGeneral = document.getElementById(`loaderGeneralOverlay`);
        const tooltipManager = new TooltipManager();

        window.APP_URL = "{{ config('app.url') }}";
    </script>

    @stack('scripts')
</body>

</html>