// Estudiante Modal

function openFase2EstudianteModal() {
    new fileInput('doc_propuesta_fase2', 'dropzone_fase2', 'word', 1, 4, 'file-list-fase2', 'files-size-fase2');
    $('#fase2EstudianteTitle').html(`Proyecto de grado <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Fase 2</span>`);

    // Campos del formulario
    $('#doc_propuesta').val('');

    // Campos para mostrar errores
    $('#doc_propuestaError').text('');

    $('#fase2EstudianteModal').addClass('show');
}

function closeFase2EstudianteModal() {
    $('#fase2EstudianteModal').removeClass('show');
}


// Admin Modal

function openFase2AdminModal() {
    new fileInput('doc_turnitin_fase2', 'dropzone_turnitin_fase2', 'pdf', 1, 4, 'file-list-turnitin-fase2', 'files-size-turnitin-fase2');
    new fileInput('doc_respuesta_fase2', 'dropzone_respuesta_fase2', 'word', 1, 4, 'file-list-respuesta-fase2', 'files-size-respuesta-fase2');
    new fileInput('doc_firmado_fase2', 'dropzone_firmado_fase2', 'word', 1, 4, 'file-list-firmado-fase2', 'files-size-firmado-fase2');

    initQuillEditor(undefined, "Describa los detalles de la respuesta para el estudiante.", 'txt-editor-fase2', 'respuesta_fase2');

    $('#fase2AdminTitle').html(`Proyecto de grado <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Fase 2</span>`);

    // Campos del formulario
    $('#estado_fase2').val('').trigger('change');
    $('#doc_respuesta_fase2').val('');
    $('#doc_firmado_fase2').val('');
    $('#doc_turnitin_fase2').val('');
    $('#respuesta_fase2').val('');

    // Campos para mostrar errores
    $('#estado_fase2Error').text('');
    $('#doc_respuesta_fase2Error').text('');
    $('#doc_firmado_fase2Error').text('');
    $('#doc_turnitin_fase2Error').val('');
    $('#respuesta_fase2Error').text('');

    $('#fase2AdminModal').addClass('show');
}

function closeFase2AdminModal() {
    $('#fase2AdminModal').removeClass('show');
}

$(document).ready(function () {
    function toggleEstadoFase2() {
        if ($('#estado_fase2').val() === 'Aprobado') {
            $('#required-turnitin').removeClass('hidden');
            $('#container-doc_respuesta_fase2').addClass('hidden');
            $('#container-doc_firmado_fase2').removeClass('hidden');
        } else if ($('#estado_fase2').val() === 'Rechazado') {
            $('#required-turnitin').addClass('hidden');
            $('#container-doc_respuesta_fase2').removeClass('hidden');
            $('#container-doc_firmado_fase2').addClass('hidden');
        }
    }

    toggleEstadoFase2();
    $('#estado_fase2').on('change', toggleEstadoFase2);
});


// Details Modal

async function openFase2DetailsModal(id) {
    const button = document.getElementById(`fase2-details-button`);
    const loadingSpinner = document.getElementById(`loadingSpinner-fase2Details`);

    button.querySelector('i').classList.add('hidden');
    loadingSpinner.classList.remove('hidden');

    $('#fase2DetailsTitle').html(`Detalles del <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Proyecto</span>`);

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

    $('#content-details-fase2').html(detailsHtml);
    $('#fase2DetailsModal').addClass('show');

    loadingSpinner.classList.add('hidden');
    button.querySelector('i').classList.remove('hidden');
}

function obtenerValorPorNombre(campos, nombre) {
    const campoEncontrado = campos.find(item => item.campo.name === nombre);
    return campoEncontrado ? campoEncontrado.valor : null;
}

function closeFase2DetailsModal() {
    $('#fase2DetailsModal').removeClass('show');
}


// Formularios

$('#fase2EstudianteForm').on('submit', function (e) {
    e.preventDefault();

    const loadingSpinner = document.getElementById(`loadingSpinner-fase2Estudiante`);

    const url = `/proyectos/fase2`;
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
                    $('#fase2-estudiante-button').addClass('hidden');
                    closeFase2EstudianteModal();
                    showToast('Información enviada, tendrá respuesta en los proximos 5 días hábiles');
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON.errors;
                    $('#doc_propuestaError').text(errors?.doc_propuesta?.[0] || '');
                },
                complete: function () {
                    loaderGeneral.classList.replace('flex', 'hidden');
                    loadingSpinner.classList.add('hidden');
                }
            });
        }
    });
});

$('#fase2AdminForm').on('submit', function (e) {
    e.preventDefault();

    const loadingSpinner = document.getElementById(`loadingSpinner-fase2AdminResponse`);

    const url = `/proyectos/fase2/responder`;
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
                    $('#buttons-admin-fase2').addClass('hidden');
                    closeFase2AdminModal();
                    sessionStorage.setItem('showToast', 'true');
                    sessionStorage.setItem('toastMessage', 'Respuesta enviada correctamente');
                    location.reload();
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON.errors;
                    $('#estado_fase2Error').text(errors?.estado_fase2?.[0] || '');
                    $('#doc_respuesta_fase2Error').text(errors?.doc_respuesta_fase2?.[0] || '');
                    $('#doc_firmado_fase2Error').text(errors?.doc_firmado_fase2?.[0] || '');
                    $('#doc_turnitin_fase2Error').text(errors?.doc_turnitin_fase2?.[0] || '');
                    $('#respuesta_fase2Error').text(errors?.respuesta_fase2?.[0] || '');
                },
                complete: function () {
                    loaderGeneral.classList.replace('flex', 'hidden');
                    loadingSpinner.classList.add('hidden');
                }
            });
        }
    });
});