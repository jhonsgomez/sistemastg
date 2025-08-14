<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Base <span class="bg-uts-500 text-gray-800 font-bold me-2 px-2.5 py-0.5 rounded uppercase">Documental</span>
        </h2>
    </x-slot>

    @push('styles')
    <style>

    </style>
    @endpush

    <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-b">
        <h2 class="text-2xl text-center font-bold text-gray-800">
            Base <span class="bg-uts-500 text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Documental</span>
        </h2>
    </div>

    <div class="p-4">
        <p class="text-gray-600 mt-2 mb-8">Aquí podrás acceder a la información institucional vigente para tener en cuenta durante el desarrollo del proyecto de grado:</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="https://www.uts.edu.co/sitio/calendario-academico/" target="_blank" class="border bg-white p-6 text-gray-600 rounded-lg shadow-lg hover:bg-uts-500 hover:text-white transition ease-in-out duration-200 flex flex-col items-center justify-center">
                <i class="fas fa-calendar-alt text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Calendario Académico</span>
                <p class="text-center mt-2 text-sm">Consulta las fechas importantes y eventos académicos.</p>
            </a>

            <a href="https://www.dropbox.com/scl/fo/pudgcaq639agy7t06ahjs/AF-dMByc71OWsxcfY2dLe3A?rlkey=6s0b9ajweteyx2ang7ywvk6xm&e=1&dl=0" target="_blank" class="border bg-white p-6 text-gray-600 rounded-lg shadow-lg hover:bg-uts-500 hover:text-white transition ease-in-out duration-200 flex flex-col items-center justify-center">
                <i class="fas fa-book-open text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Formatos institucionales</span>
                <p class="text-center mt-2 text-sm">Accede a todos los formatos de presentación institucional de manera eficiente.</p>
            </a>

            <a href="https://www.dropbox.com/scl/fo/pudgcaq639agy7t06ahjs/AEsdfx-BCJKzfEs37G82Bzk/2.%20MISIONALES/DOCENCIA/2.%20DOCUMENTOS%20DEL%20PROCESO/REQUISITOS%20RELACIONADOS%20ESTUDIANTE/PROCEDIMIENTOS?e=5&preview=P-DC-28+Presentaci%C3%B3n+de+la+propuesta+de+trabajo+de+grado+V3.pdf&rlkey=6s0b9ajweteyx2ang7ywvk6xm&subfolder_nav_tracking=1&dl=0" target="_blank" class="border bg-white p-6 text-gray-600 rounded-lg shadow-lg hover:bg-uts-500 hover:text-white transition ease-in-out duration-200 flex flex-col items-center justify-center">
                <i class="fa-solid fa-book-atlas text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Presentación de propuesta</span>
                <p class="text-center mt-2 text-sm">Procedimiento a seguir para la presentación del la propuesta.</p>
            </a>

            <a href="https://www.dropbox.com/scl/fo/pudgcaq639agy7t06ahjs/AEsdfx-BCJKzfEs37G82Bzk/2.%20MISIONALES/DOCENCIA/2.%20DOCUMENTOS%20DEL%20PROCESO/REQUISITOS%20RELACIONADOS%20ESTUDIANTE/PROCEDIMIENTOS?e=5&preview=P-DC-28+Presentaci%C3%B3n+de+la+propuesta+de+trabajo+de+grado+V3.pdf&rlkey=6s0b9ajweteyx2ang7ywvk6xm&subfolder_nav_tracking=1&dl=0" target="_blank" class="border bg-white p-6 text-gray-600 rounded-lg shadow-lg hover:bg-uts-500 hover:text-white transition ease-in-out duration-200 flex flex-col items-center justify-center">
                <i class="fa-solid fa-file-invoice text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Presentación de informe final</span>
                <p class="text-center mt-2 text-sm">Procedimiento a seguir para la presentación del informe final.</p>
            </a>

            <a href="https://www.uts.edu.co/sitio/normatividad/" target="_blank" class="border bg-white p-6 text-gray-600 rounded-lg shadow-lg hover:bg-uts-500 hover:text-white transition ease-in-out duration-200 flex flex-col items-center justify-center">
                <i class="fa-solid fa-trophy text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Reglamento de estímulos</span>
                <p class="text-center mt-2 text-sm">Procedimiento a obtener distinciones o estímulos en un proyecto.</p>
            </a>

            <a href="https://www.uts.edu.co/sitio/normatividad/" target="_blank" class="border bg-white p-6 text-gray-600 rounded-lg shadow-lg hover:bg-uts-500 hover:text-white transition ease-in-out duration-200 flex flex-col items-center justify-center">
                <i class="fa-solid fa-list-check text-4xl mb-4"></i>
                <span class="text-center font-bold text-lg">Reglamento de trabajos de grado</span>
                <p class="text-center mt-2 text-sm">Procedimiento para elaborar un trabajo de grado.</p>
            </a>
        </div>
    </div>

    @push('scripts')
    <script>

    </script>
    @endpush
</x-app-layout>