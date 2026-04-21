<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Seguimiento de Prácticas Empresariales
        </h2>
    </x-slot>

    <div class="p-4">
        <div class="mb-4">
            <a href="{{ route('practicas.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                ← Volver al listado
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4">Práctica #{{ $practica->id }} - Estado: {{ $estado }}</h3>
            <p>Aquí se mostrarán los formularios según la fase actual.</p>
            <!-- Aquí irán los botones y formularios para cada fase -->
        </div>
    </div>
</x-app-layout>