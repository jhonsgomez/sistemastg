function openFase7DetailsModal(btn) {

    if (btn) {

        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');

        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');

        btn.disabled = true;
    }

    $.ajax({

        url: ROUTES.fase7_details,
        method: 'POST',

        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            practica_id: $('input[name="practica_id"]').first().val()
        },

        success: function(response) {

            const documentos = [

                {
                    nombre: 'Rejilla F-DC-129',
                    url: response.rejilla_fdc129_url,
                    icono: 'fa-file-word',
                    color: 'text-blue-600'
                },

                {
                    nombre: 'Informe Final F-DC-128',
                    url: response.informe_final_fdc128_url,
                    icono: 'fa-file-word',
                    color: 'text-blue-600'
                },

                {
                    nombre: 'Turnitin F-DC-128',
                    url: response.turnitin_fdc128_url,
                    icono: 'fa-file-pdf',
                    color: 'text-red-600'
                },

                {
                    nombre: 'Propuesta F-DC-127',
                    url: response.propuesta_fdc127_url,
                    icono: 'fa-file-word',
                    color: 'text-blue-600'
                },

                {
                    nombre: 'Turnitin F-DC-127',
                    url: response.turnitin_fdc127_url,
                    icono: 'fa-file-pdf',
                    color: 'text-red-600'
                }

            ];

            let html = `
                <div class="flex flex-col space-y-4">
            `;

            documentos.forEach(doc => {

                html += `
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">

                        <p class="font-semibold text-gray-700 w-1/3 min-w-[180px] mb-2 sm:mb-0">
                            ${doc.nombre}:
                        </p>

                        ${
                            doc.url
                            ? `
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">

                                    <i class="fa-regular ${doc.icono} ${doc.color} mr-2"></i>

                                    <a href="${doc.url}"
                                        target="_blank"
                                        class="${doc.color} underline hover:opacity-80">

                                        Ver documento

                                    </a>

                                </div>
                            `
                            : `
                                <span class="text-gray-500 w-full sm:flex-1 sm:ml-2">
                                    No disponible
                                </span>
                            `
                        }

                    </div>
                `;
            });

            html += `</div>`;

            $('#fase7DetailsContent').html(html);

            $('#fase7DetailsModal')
                .removeClass('hidden')
                .addClass('show');
        },

        error: function(xhr) {

            console.error(xhr);

            Swal.fire(
                'Error',
                'No se pudieron cargar los documentos',
                'error'
            );

        },

        complete: function() {

            if (btn) {

                const icon = btn.querySelector('i');
                const spinner = btn.querySelector('.loading-spinner');

                if (icon) icon.classList.remove('hidden');
                if (spinner) spinner.classList.add('hidden');

                btn.disabled = false;
            }

        }

    });

}

function closeFase7DetailsModal() {

    $('#fase7DetailsModal')
        .removeClass('show')
        .addClass('hidden');

}