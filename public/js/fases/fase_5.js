// Details Modal

async function openFase5DetailsModal(id) {
    const button = document.getElementById(`fase5-details-button`);
    const loadingSpinner = document.getElementById(`loadingSpinner-fase5Details`);

    button.querySelector('i').classList.add('hidden');
    loadingSpinner.classList.remove('hidden');

    $('#fase5DetailsTitle').html(`Detalles del <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Proyecto</span>`);

    let detailsHtml = ``;
    let info = {};

    async function obtenerCamposProyecto(id) {
        let response = await fetch(`/proyectos/${id}/campos`);
        let data = await response.json();

        return data.campos;
    }

    async function setHtml(id, info) {
        let doc_informe = ``;
        let doc_rejilla = ``;
        let doc_turnitin_informe = ``;
        let doc_propuesta = ``;
        let doc_turnitin = ``;

        if (info.doc_informe) {
            info.doc_informe.forEach((documento, index) => {
                doc_informe += `<a target="_blank" class="text-blue-600 text-sm underline" href="/storage/documentos_proyectos/proyecto-00${id}/${documento}"><i class="fa-regular fa-file-word text-blue-600 mr-1"></i>Documento ${index + 1}</a><br>`;
            });

            detailsHtml += `<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                                <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Informe - F-DC-125:</p>
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${doc_informe}</div>
                            </div>`;
        }

        if (info.doc_rejilla) {
            info.doc_rejilla.forEach((documento, index) => {
                doc_rejilla += `<a target="_blank" class="text-blue-600 text-sm underline" href="/storage/documentos_proyectos/proyecto-00${id}/${documento}"><i class="fa-regular fa-file-word text-blue-600 mr-1"></i>Documento ${index + 1}</a><br>`;
            });

            detailsHtml += `<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                                <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Rejilla - F-DC-129:</p>
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${doc_rejilla}</div>
                            </div>`;
        }

        if (info.doc_turnitin_informe) {
            info.doc_turnitin_informe.forEach((documento, index) => {
                doc_turnitin_informe += `<a target="_blank" class="text-red-600 text-sm underline" href="/storage/documentos_proyectos/proyecto-00${id}/${documento}"><i class="fa-regular fa-file-pdf text-red-600 mr-1"></i>Documento ${index + 1}</a><br>`;
            });

            detailsHtml += `<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                                <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Turnitin - F-DC-125:</p>
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${doc_turnitin_informe}</div>
                            </div>`;
        }

        if (info.doc_propuesta) {
            info.doc_propuesta.forEach((documento, index) => {
                doc_propuesta += `<a target="_blank" class="text-blue-600 text-sm underline" href="/storage/documentos_proyectos/proyecto-00${id}/${documento}"><i class="fa-regular fa-file-word text-blue-600 mr-1"></i>Documento ${index + 1}</a><br>`;
            });

            detailsHtml += `<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                                <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Propuesta - F-DC-124:</p>
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${doc_propuesta}</div>
                            </div>`;
        }

        if (info.doc_turnitin) {
            info.doc_turnitin.forEach((documento, index) => {
                doc_turnitin += `<a target="_blank" class="text-red-600 text-sm underline" href="/storage/documentos_proyectos/proyecto-00${id}/${documento}"><i class="fa-regular fa-file-pdf text-red-600 mr-1"></i>Documento ${index + 1}</a><br>`;
            });

            detailsHtml += `<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                                <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Turnitin - F-DC-124:</p>
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${doc_turnitin}</div>
                            </div>`;
        }

        if (!doc_informe && !doc_rejilla && !doc_turnitin_informe && !doc_propuesta && !doc_turnitin) {
            detailsHtml = `<p class="text-center text-gray-500 mt-10">Aún no hay información disponible en esta FASE.</p>`;
        }
    }

    async function obtenerDatos(id) {
        let campos = await obtenerCamposProyecto(id);
        let doc_informe = JSON.parse(obtenerValorPorNombre(campos, 'doc_informe'));
        let doc_rejilla = JSON.parse(obtenerValorPorNombre(campos, 'doc_rejilla'));
        let doc_turnitin_informe = JSON.parse(obtenerValorPorNombre(campos, 'doc_turnitin_informe'));
        let doc_propuesta = JSON.parse(obtenerValorPorNombre(campos, 'doc_propuesta'));
        let doc_turnitin = JSON.parse(obtenerValorPorNombre(campos, 'doc_turnitin'));
        info = { doc_informe, doc_rejilla, doc_turnitin_informe, doc_propuesta, doc_turnitin };

        await setHtml(id, info);
    }

    await obtenerDatos(id);

    $('#content-details-fase5').html(detailsHtml);
    $('#fase5DetailsModal').addClass('show');

    loadingSpinner.classList.add('hidden');
    button.querySelector('i').classList.remove('hidden');
}

function obtenerValorPorNombre(campos, nombre) {
    const campoEncontrado = campos.find(item => item.campo.name === nombre);
    return campoEncontrado ? campoEncontrado.valor : null;
}

function closeFase5DetailsModal() {
    $('#fase5DetailsModal').removeClass('show');
}

// Aprobar Modal:

function openFase5AprobarModal() {
    new fileInput('doc_respuesta_fase5', 'dropzone_respuesta_fase5', 'word', 1, 8, 'file-list-respuesta-fase5', 'files-size-respuesta-fase5');
    new fileInput('doc_rejilla_fase5', 'dropzone_rejilla_fase5', 'word', 1, 4, 'file-list-rejilla-fase5', 'files-size-rejilla-fase5');
    new fileInput('doc_firmado_fase5', 'dropzone_firmado_fase5', 'word', 1, 8, 'file-list-firmado-fase5', 'files-size-firmado-fase5');

    initQuillEditor(undefined, "Describa los detalles de la respuesta para el estudiante.", 'txt-editor-fase5', 'respuesta_fase5');

    $('#fase5AprobarTitle').html(`Proyecto de grado <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Fase 5</span>`);

    // Campos del formulario
    $('#estado_fase5').val('').trigger('change');
    $('#doc_firmado_fase5').val('');
    $('#doc_respuesta_fase5').val('');
    $('#doc_rejilla_fase5').val('');
    $('#nro_acta_fase5').val('');
    $('#fecha_acta_fase5').val('');
    $('#respuesta_fase5').val('');

    // Campos para mostrar errores
    $('#estado_fase5Error').text('');
    $('#doc_firmado_fase5Error').text('');
    $('#doc_respuesta_fase5Error').text('');
    $('#doc_rejilla_fase5Error').text('');
    $('#nro_acta_fase5Error').text('');
    $('#fecha_acta_fase5Error').text('');
    $('#respuesta_fase5Error').text('');

    $('#fase5AprobarModal').addClass('show');
}

function closeFase5AprobarModal() {
    $('#fase5AprobarModal').removeClass('show');
}

$(document).ready(function () {
    function toggleEstadoFase5() {
        if ($('#estado_fase5').val() === 'Aprobado') {
            $('#container-doc_respuesta_fase5').addClass('hidden');
            $('#required-rejilla').removeClass('hidden');
            $('#container-doc_firmado_fase5').removeClass('hidden');
        } else if ($('#estado_fase5').val() === 'Rechazado') {
            $('#container-doc_respuesta_fase5').removeClass('hidden');
            $('#required-rejilla').addClass('hidden');
            $('#container-doc_firmado_fase5').addClass('hidden');
        }
    }

    toggleEstadoFase5();
    $('#estado_fase5').on('change', toggleEstadoFase5);
});

// Submit Forms

$('#fase5AprobarForm').on('submit', function (e) {
    e.preventDefault();

    const loadingSpinner = document.getElementById(`loadingSpinner-fase5AprobarResponse`);

    const url = `/proyectos/fase5/responder`;
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
                    $('#buttons-admin-fase5').addClass('hidden');
                    closeFase5AprobarModal();
                    sessionStorage.setItem('showToast', 'true');
                    sessionStorage.setItem('toastMessage', 'Respuesta enviada correctamente');
                    location.reload();
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON.errors;

                    $('#estado_fase5Error').text(errors?.estado_fase5?.[0] || '');
                    $('#doc_firmado_fase5Error').text(errors?.doc_firmado_fase5?.[0] || '');
                    $('#doc_respuesta_fase5Error').text(errors?.doc_respuesta_fase5?.[0] || '');
                    $('#doc_rejilla_fase5Error').text(errors?.doc_rejilla_fase5?.[0] || '');
                    $('#nro_acta_fase5Error').text(errors?.nro_acta_fase5?.[0] || '');
                    $('#fecha_acta_fase5Error').text(errors?.fecha_acta_fase5?.[0] || '');
                    $('#respuesta_fase5Error').text(errors?.respuesta_fase5?.[0] || '');
                },
                complete: function () {
                    loaderGeneral.classList.replace('flex', 'hidden');
                    loadingSpinner.classList.add('hidden');
                }
            });
        }
    });
});