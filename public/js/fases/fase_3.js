// Details Modal

async function openFase3DetailsModal(id) {
    const button = document.getElementById(`fase3-details-button`);
    const loadingSpinner = document.getElementById(`loadingSpinner-fase3Details`);


    button.querySelector('i').classList.add('hidden');
    loadingSpinner.classList.remove('hidden');

    $('#fase3DetailsTitle').html(`Detalles del <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Proyecto</span>`);

    let detailsHtml = ``;
    let info = {};

    async function obtenerCamposProyecto(id) {
        let response = await fetch(`/proyectos/${id}/campos`);
        let data = await response.json();

        return data.campos;
    }

    async function setHtml(id, info) {
        let doc_propuesta = ``;
        let doc_turnitin = ``;

        if (info.doc_propuesta) {
            info.doc_propuesta.forEach((documento, index) => {
                doc_propuesta += `<a target="_blank" class="text-blue-600 text-sm underline" href="/storage/documentos_proyectos/proyecto-00${id}/${documento}"><i class="fa-regular fa-file-word text-blue-600 mr-1"></i>Documento ${index + 1}</a><br>`;
            });

            detailsHtml += `<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                                <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Propuesta:</p>
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${doc_propuesta}</div>
                            </div>`;
        }

        if (info.doc_turnitin) {
            info.doc_turnitin.forEach((documento, index) => {
                doc_turnitin += `<a target="_blank" class="text-red-600 text-sm underline" href="/storage/documentos_proyectos/proyecto-00${id}/${documento}"><i class="fa-regular fa-file-pdf text-red-600 mr-1"></i>Documento ${index + 1}</a><br>`;
            });

            detailsHtml += `<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                                <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Turnitin:</p>
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${doc_turnitin}</div>
                            </div>`;
        }

        if (!doc_propuesta && !doc_turnitin) {
            detailsHtml = `<p class="text-center text-gray-500 mt-10">Aún no hay información disponible en esta FASE.</p>`;
        }
    }

    async function obtenerDatos(id) {
        let campos = await obtenerCamposProyecto(id);
        let doc_propuesta = JSON.parse(obtenerValorPorNombre(campos, 'doc_propuesta'));
        let doc_turnitin = JSON.parse(obtenerValorPorNombre(campos, 'doc_turnitin'));
        info = { doc_propuesta, doc_turnitin };

        await setHtml(id, info);
    }

    await obtenerDatos(id);

    $('#content-details-fase3').html(detailsHtml);
    $('#fase3DetailsModal').addClass('show');

    loadingSpinner.classList.add('hidden');
    button.querySelector('i').classList.remove('hidden');
}

function obtenerValorPorNombre(campos, nombre) {
    const campoEncontrado = campos.find(item => item.campo.name === nombre);
    return campoEncontrado ? campoEncontrado.valor : null;
}

function closeFase3DetailsModal() {
    $('#fase3DetailsModal').removeClass('show');
}

// Aprobar Modal

function openFase3AprobarModal() {
    new fileInput('doc_respuesta_fase3', 'dropzone_respuesta_fase3', 'word', 1, 4, 'file-list-respuesta-fase3', 'files-size-respuesta-fase3');
    initQuillEditor(undefined, "Describa los detalles de la respuesta para el estudiante.", 'txt-editor-fase3', 'respuesta_fase3');

    $('#fase3AprobarTitle').html(`Proyecto de grado <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Fase 3</span>`);

    // Campos del formulario
    $('#estado_fase3').val('').trigger('change');
    $('#doc_respuesta_fase3').val('');

    $('#nro_acta_fase3').val('');
    $('#fecha_acta_fase3').val('');

    $('#respuesta_fase3').val('');

    // Campos para mostrar errores
    $('#estado_fase3Error').text('');
    $('#doc_respuesta_fase3Error').text('');
    $('#nro_acta_fase3Error').text('');
    $('#fecha_acta_fase3Error').text('');
    $('#respuesta_fase3Error').text('');

    $('#fase3AprobarModal').addClass('show');
}

function closeFase3AprobarModal() {
    $('#fase3AprobarModal').removeClass('show');
}

$(document).ready(function () {
    function toggleEstadoFase3() {
        if ($('#estado_fase3').val() === 'Aprobado') {
            $('#container-doc_respuesta_fase3').addClass('hidden');
        } else if ($('#estado_fase3').val() === 'Rechazado') {
            $('#container-doc_respuesta_fase3').removeClass('hidden');
        }
    }

    toggleEstadoFase3();
    $('#estado_fase3').on('change', toggleEstadoFase3);
});

// Submits Forms

$('#fase3AprobarForm').on('submit', function (e) {
    e.preventDefault();

    const loadingSpinner = document.getElementById(`loadingSpinner-fase3AprobarResponse`);

    const url = `/proyectos/fase3/responder`;
    const method = 'POST';

    const formData = new FormData(this);

    Swal.fire({
        heightAuto: false,
        title: '¿Está seguro?',
        text: "No podrá editar la respuesta una vez se envíe",
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
                success: function (response) {
                    $('#buttons-admin-fase3').addClass('hidden');
                    closeFase3AprobarModal();
                    sessionStorage.setItem('showToast', 'true');
                    sessionStorage.setItem('toastMessage', 'Respuesta enviada correctamente');
                    location.reload();
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON.errors;

                    $('#estado_fase3Error').text(errors?.estado_fase3?.[0] || '');
                    $('#doc_respuesta_fase3Error').text(errors?.doc_respuesta_fase3?.[0] || '');
                    $('#nro_acta_fase3Error').text(errors?.nro_acta_fase3?.[0] || '');
                    $('#fecha_acta_fase3Error').text(errors?.fecha_acta_fase3?.[0] || '');
                    $('#respuesta_fase3Error').text(errors?.respuesta_fase3?.[0] || '');
                },
                complete: function () {
                    loaderGeneral.classList.replace('flex', 'hidden');
                    loadingSpinner.classList.add('hidden');
                }
            });
        }
    });
});