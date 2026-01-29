// Estudiante Modal

function openFase4EstudianteModal() {
    new fileInput('doc_informe_fase4', 'dropzone_doc_informe_fase4', 'word', 1, peso_maximo_informe, 'doc_informe-file-list-fase4', 'doc_informe-files-size-fase4');
    new fileInput('doc_rejilla_fase4', 'dropzone_doc_rejilla_fase4', 'word', 1, peso_maximo_informe, 'doc_rejilla-file-list-fase4', 'doc_rejilla-files-size-fase4');

    $('#fase4EstudianteTitle').html(`Proyecto de grado <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Fase 4</span>`);

    // Campos del formulario
    $('#doc_informe_fase4').val('');
    $('#doc_rejilla_fase4').val('');

    // Campos para mostrar errores
    $('#doc_informeError').text('');
    $('#doc_rejillaError').text('');

    $('#fase4EstudianteModal').addClass('show');
}

function closeFase4EstudianteModal() {
    $('#fase4EstudianteModal').removeClass('show');
}

// Details Modal

async function openFase4DetailsModal(id) {
    const button = document.getElementById(`fase4-details-button`);
    const loadingSpinner = document.getElementById(`loadingSpinner-fase4Details`);

    button.querySelector('i').classList.add('hidden');
    loadingSpinner.classList.remove('hidden');

    $('#fase4DetailsTitle').html(`Detalles del <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Proyecto</span>`);

    let detailsHtml = ``;
    let info = {};

    async function obtenerCamposProyecto(id) {
        let response = await fetch(`${window.APP_URL}/proyectos/${id}/campos`);
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
                doc_informe += `<a target="_blank" class="text-blue-600 text-sm underline" href="${window.APP_URL}/storage/documentos_proyectos/proyecto-00${id}/${documento}"><i class="fa-regular fa-file-word text-blue-600 mr-1"></i>Documento ${index + 1}</a><br>`;
            });

            detailsHtml += `<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                                <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Informe - F-DC-125:</p>
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${doc_informe}</div>
                            </div>`;
        }

        if (info.doc_rejilla) {
            info.doc_rejilla.forEach((documento, index) => {
                doc_rejilla += `<a target="_blank" class="text-blue-600 text-sm underline" href="${window.APP_URL}/storage/documentos_proyectos/proyecto-00${id}/${documento}"><i class="fa-regular fa-file-word text-blue-600 mr-1"></i>Documento ${index + 1}</a><br>`;
            });

            detailsHtml += `<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                                <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Rejilla - F-DC-129:</p>
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${doc_rejilla}</div>
                            </div>`;
        }

        if (info.doc_turnitin_informe) {
            info.doc_turnitin_informe.forEach((documento, index) => {
                doc_turnitin_informe += `<a target="_blank" class="text-red-600 text-sm underline" href="${window.APP_URL}/storage/documentos_proyectos/proyecto-00${id}/${documento}"><i class="fa-regular fa-file-pdf text-red-600 mr-1"></i>Documento ${index + 1}</a><br>`;
            });

            detailsHtml += `<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                                <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Turnitin - F-DC-125:</p>
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${doc_turnitin_informe}</div>
                            </div>`;
        }

        if (info.doc_propuesta) {
            info.doc_propuesta.forEach((documento, index) => {
                doc_propuesta += `<a target="_blank" class="text-blue-600 text-sm underline" href="${window.APP_URL}/storage/documentos_proyectos/proyecto-00${id}/${documento}"><i class="fa-regular fa-file-word text-blue-600 mr-1"></i>Documento ${index + 1}</a><br>`;
            });

            detailsHtml += `<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                                <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Propuesta - F-DC-124:</p>
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${doc_propuesta}</div>
                            </div>`;
        }

        if (info.doc_turnitin) {
            info.doc_turnitin.forEach((documento, index) => {
                doc_turnitin += `<a target="_blank" class="text-red-600 text-sm underline" href="${window.APP_URL}/storage/documentos_proyectos/proyecto-00${id}/${documento}"><i class="fa-regular fa-file-pdf text-red-600 mr-1"></i>Documento ${index + 1}</a><br>`;
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

    $('#content-details-fase4').html(detailsHtml);
    $('#fase4DetailsModal').addClass('show');

    loadingSpinner.classList.add('hidden');
    button.querySelector('i').classList.remove('hidden');
}

function obtenerValorPorNombre(campos, nombre) {
    const campoEncontrado = campos.find(item => item.campo.name === nombre);
    return campoEncontrado ? campoEncontrado.valor : null;
}

function closeFase4DetailsModal() {
    $('#fase4DetailsModal').removeClass('show');
}


// Admin Modal

function openFase4AdminModal() {
    new fileInput('doc_rejilla_fase4', 'dropzone_rejilla_fase4', 'word', 1, peso_maximo_informe, 'file-list-rejilla-fase4', 'files-size-rejilla-fase4');
    new fileInput('doc_respuesta_fase4', 'dropzone_respuesta_fase4', 'word', 1, peso_maximo_informe, 'file-list-respuesta-fase4', 'files-size-respuesta-fase4');
    new fileInput('doc_turnitin_fase4', 'dropzone_turnitin_fase4', 'pdf', 1, peso_maximo_informe, 'file-list-turnitin-fase4', 'files-size-turnitin-fase4');
    new fileInput('doc_firmado_fase4', 'dropzone_firmado_fase4', 'word', 1, peso_maximo_informe, 'file-list-firmado-fase4', 'files-size-firmado-fase4');

    initQuillEditor(undefined, "Describa los detalles de la respuesta para el estudiante.", 'txt-editor-fase4', 'respuesta_fase4');

    $('#fase4AdminTitle').html(`Proyecto de grado <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Fase 4</span>`);

    // Campos del formulario
    $('#estado_fase4').val('').trigger('change');
    $('#doc_rejilla_fase4').val('');
    $('#doc_firmado_fase4').val('');
    $('#doc_respuesta_fase4').val('');
    $('#doc_turnitin_fase4').val('');
    $('#respuesta_fase4').val('');

    // Campos para mostrar errores
    $('#estado_fase4Error').text('');
    $('#doc_firmado_fase4Error').text('');
    $('#doc_rejilla_fase4Error').text('');
    $('#doc_respuesta_fase4Error').text('');
    $('#doc_turnitin_fase4Error').val('');
    $('#respuesta_fase4Error').text('');

    $('#fase4AdminModal').addClass('show');
}

function closeFase4AdminModal() {
    $('#fase4AdminModal').removeClass('show');
}

$(document).ready(function () {
    function toggleEstadoFase4() {
        if ($('#estado_fase4').val() === 'Aprobado') {
            $('#required-turnitin_fase4').removeClass('hidden');
            $('#container-doc_respuesta_fase4').addClass('hidden');
            $('#container-doc_rejilla_fase4').removeClass('hidden');
            $('#container-doc_firmado_fase4').removeClass('hidden');
        } else if ($('#estado_fase4').val() === 'Rechazado') {
            $('#required-turnitin_fase4').addClass('hidden');
            $('#container-doc_respuesta_fase4').removeClass('hidden');
            $('#container-doc_rejilla_fase4').addClass('hidden');
            $('#container-doc_firmado_fase4').addClass('hidden');
        }
    }

    toggleEstadoFase4();
    $('#estado_fase4').on('change', toggleEstadoFase4);
});

// Forms submit

$('#fase4EstudianteForm').on('submit', function (e) {
    e.preventDefault();

    const loadingSpinner = document.getElementById(`loadingSpinner-fase4Estudiante`);

    const url = `${window.APP_URL}/proyectos/fase4`;
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
                success: function (response) {
                    $('#fase4-estudiante-button').addClass('hidden');
                    closeFase4EstudianteModal();
                    showToast('Información enviada, tendrá respuesta en los proximos 5 días hábiles');
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON.errors;

                    $('#doc_informeError').text(errors?.doc_informe?.[0] || '');
                    $('#doc_rejillaError').text(errors?.doc_rejilla?.[0] || '');
                },
                complete: function () {
                    loaderGeneral.classList.replace('flex', 'hidden');
                    loadingSpinner.classList.add('hidden');
                }
            });
        }
    });
});

$('#fase4AdminForm').on('submit', function (e) {
    e.preventDefault();

    const loadingSpinner = document.getElementById(`loadingSpinner-fase4AdminResponse`);

    const url = `${window.APP_URL}/proyectos/fase4/responder`;
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
                success: function (response) {
                    $('#buttons-admin-fase4').addClass('hidden');
                    closeFase4AdminModal();
                    sessionStorage.setItem('showToast', 'true');
                    sessionStorage.setItem('toastMessage', 'Respuesta enviada correctamente');
                    location.reload();
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON.errors;

                    $('#estado_fase4Error').text(errors?.estado_fase4?.[0] || '');
                    $('#doc_firmado_fase4Error').text(errors?.doc_firmado_fase4?.[0] || '');
                    $('#doc_rejilla_fase4Error').text(errors?.doc_rejilla_fase4?.[0] || '');
                    $('#doc_respuesta_fase4Error').text(errors?.doc_respuesta_fase4?.[0] || '');
                    $('#doc_turnitin_fase4Error').text(errors?.doc_turnitin_fase4?.[0] || '');
                    $('#respuesta_fase4Error').text(errors?.respuesta_fase4?.[0] || '');
                },
                complete: function () {
                    loaderGeneral.classList.replace('flex', 'hidden');
                    loadingSpinner.classList.add('hidden');
                }
            });
        }
    });
});