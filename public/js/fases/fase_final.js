// Details Modal:

async function openFaseFinalDetailsModal(id) {
    const button = document.getElementById(`faseFinal-details-button`);
    const loadingSpinner = document.getElementById(`loadingSpinner-faseFinalDetails`);

    button.querySelector('i').classList.add('hidden');
    loadingSpinner.classList.remove('hidden');

    $('#faseFinalDetailsTitle').html(`Proyecto <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Finalizado</span>`);

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

    $('#content-details-faseFinal').html(detailsHtml);
    $('#faseFinalDetailsModal').addClass('show');

    loadingSpinner.classList.add('hidden');
    button.querySelector('i').classList.remove('hidden');
}

function obtenerValorPorNombre(campos, nombre) {
    const campoEncontrado = campos.find(item => item.campo.name === nombre);
    return campoEncontrado ? campoEncontrado.valor : null;
}

function closeFaseFinalDetailsModal() {
    $('#faseFinalDetailsModal').removeClass('show');
}