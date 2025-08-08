<nav class="flex gap-4 mb-4">
    <style>
        @media screen and (max-width: 640px) {
            .nav-profile-bar {
                justify-content: center !important;
            }
        }
    </style>
    <aside
        class="w-64 bg-white rounded-xl shadow-md p-4 flex items-center justify-between transition-width duration-300 ease-in-out"
        id="sidebar" style="height: 79.35px !important;">
        <a href="{{ route('dashboard') }}" id="logo">
            <img src="{{ asset('img/programa-logo.png') }}" alt="Logo" class="w-28 transition-opacity duration-300">
        </a>
        <button onclick="toggleSidebar()"
            class="text-gray-600 hover:bg-uts-500 hover:text-white rounded-lg transition-colors p-2" id="iconMenu">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </aside>
    <div class="flex-1 nav-profile-bar bg-white rounded-xl shadow-md py-4 pl-[30px] pr-4 flex justify-between items-center">
        <div>
            <h1 id="text-welcome" class="text-2xl font-bold text-gray-800">Bienvenido</h1>
        </div>
        <!-- Settings Dropdown -->
        <div class="relative">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                        <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </button>
                    @else
                    <span class="inline-flex ">
                        <img src="{{ asset('img/user.png') }}" alt="Logo" id="logo" class="cursor-pointer my-auto" style="width: 40px !important; height: 40px !important;">
                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                            {{ Auth::user()->name }}

                            <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </span>
                    @endif
                </x-slot>

                <x-slot name="content">
                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Account') }}
                    </div>

                    <x-dropdown-link href="{{ route('profile.show') }}">
                        {{ __('Profile') }}
                    </x-dropdown-link>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-dropdown-link href="{{ route('api-tokens.index') }}">
                        {{ __('API Tokens') }}
                    </x-dropdown-link>
                    @endif

                    <div class="border-t border-gray-200"></div>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf

                        <x-dropdown-link href="{{ route('logout') }}"
                            @click.prevent="$root.submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</nav>