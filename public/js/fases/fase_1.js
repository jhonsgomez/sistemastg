$(document).ready(function () {
    $('#idea_banco').select2({
        placeholder: 'Selecciona una idea del banco',
        allowClear: true,
        width: '100%',
        minimumInputLength: 5
    });

    $('#director').select2({
        placeholder: 'Seleccione un director para el proyecto',
        allowClear: true,
        width: '100%',
        minimumInputLength: 5
    });

    $('#evaluador').select2({
        placeholder: 'Seleccione un evaluador para el proyecto',
        allowClear: true,
        width: '100%',
        minimumInputLength: 5
    });

    $('#codirector').select2({
        placeholder: 'Seleccione un codirector para el proyecto',
        allowClear: true,
        width: '100%',
        minimumInputLength: 5
    });

    // Checkbox

    $('#container-titulo, #container-objetivo, #container-linea_investigacion, #container-descripcion').removeClass('hidden-inputs');
    $('#container-idea_banco').addClass('hidden-inputs');
    $('#container-titulo').addClass('mt-titulo');

    $('#check_idea_banco').change(function () {
        if ($(this).is(':checked')) {
            $('#container-titulo').removeClass('mt-titulo');

            $('#container-titulo, #container-objetivo, #container-linea_investigacion, #container-descripcion')
                .addClass('hidden-inputs')
                .removeClass('visible-inputs');

            $('#container-idea_banco')
                .removeClass('hidden-inputs')
                .addClass('visible-inputs');

            $('#container-soporte_pago').removeClass('mt-soporte-original').addClass('mt-soporte');
        } else {
            $('#idea_banco').val('').trigger('change');
            $('#container-titulo').addClass('mt-titulo');

            $('#container-titulo, #container-objetivo, #container-linea_investigacion, #container-descripcion')
                .removeClass('hidden-inputs')
                .addClass('visible-inputs');

            $('#container-idea_banco')
                .addClass('hidden-inputs')
                .removeClass('visible-inputs');

            $('#container-soporte_pago').removeClass('mt-soporte').addClass('mt-soporte-original');
        }
    });


    // Docentes

    function toggleDocentesContainer() {
        if ($('#estado').val() === 'Aprobado') {
            $('#container-docentes').removeClass('hidden');
            $('#container_codigo_modalidad').removeClass('hidden');
        } else {
            $('#container-docentes').addClass('hidden');
            $('#container_codigo_modalidad').addClass('hidden');
        }
    }

    toggleDocentesContainer();
    $('#estado').on('change', toggleDocentesContainer);
});

function openFase1EstudianteModal() {
    new fileInput('soporte_pago', 'dropzone', 'pdf', 2, peso_maximo_pago, 'file-list', 'files-size');
    $('#fase1EstudianteTitle').html(`Proyecto de grado <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Fase 1</span>`);

    $('#check_idea_banco').val('');
    $('#idea_banco').val('').trigger('change');
    $('#titulo').val('');
    $('#objetivo').val('');
    $('#linea_investigacion').val('');
    $('#descripcion').val('');
    $('#soporte_pago').val('');
    $('#file-list').text('');
    $('#files-size').text('');

    $('#check_idea_bancoError').text('');
    $('#idea_bancoError').text('');
    $('#tituloError').text('');
    $('#objetivoError').text('');
    $('#linea_investigacionError').text('');
    $('#descripcionError').text('');
    $('#soporte_pagoError').text('');

    $('#fase1EstudianteModal').addClass('show');
}

function closeFase1EstudianteModal() {
    TooltipManager.closeTooltips();
    $('#fase1EstudianteModal').removeClass('show');
}

function openFase1AdminModal(codigo_modalidad) {
    initQuillEditor(undefined, "Describa los detalles de la respuesta para el estudiante.", 'txt-editor-fase1', 'respuesta_fase1');

    $('#fase1AdminTitle').html(`Proyecto <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Fase 1</span>`);

    $('#estado').val('').trigger('change');
    $('#nro_acta_fase_1').val('');
    $('#fecha_acta_fase_1').val('');
    $('#codigo_modalidad').val(codigo_modalidad || '');
    $('#director').val('').trigger('change');
    $('#evaluador').val('').trigger('change');
    $('#respuesta_fase1').val('');

    $('#estadoError').text('');
    $('#nro_acta_fase_1Error').text('');
    $('#fecha_acta_fase_1Error').text('');
    $('#codigo_modalidadError').text('');
    $('#directorError').text('');
    $('#evaluadorError').text('');
    $('#respuesta_fase1Error').text('');

    $('#fase1AdminModal').addClass('show');
}

function closeFase1AdminModal() {
    $('#fase1AdminModal').removeClass('show');
}

async function openFase1AdminDetailsModal(id) {
    const button = document.getElementById(`fase1-admin-details-button`);
    const loadingSpinner = document.getElementById(`loadingSpinner-fase1AdminDetails`);

    button.querySelector('i').classList.add('hidden');
    loadingSpinner.classList.remove('hidden');

    $('#fase1AdminDetailsTitle').html(`Detalles del <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Proyecto</span>`);

    let detailsHtml = ``;
    let info = {};

    async function obtenerCamposProyecto(id) {
        let response = await fetch(`/proyectos/${id}/campos`);
        let data = await response.json();

        return data.campos;
    }

    async function getLinea(id) {
        return new Promise((resolve, reject) => {
            $.get(`/lineas-investigacion/${id}`, function (data) {
                if (data) {
                    resolve(data);
                } else {
                    reject("No se encontró la línea de investigación.");
                }
            });
        });
    }

    async function setHtml(id, info) {
        let soportes = ``;

        info.soporte_pago.forEach((soporte, index) => {
            soportes += `<a target="_blank" class="text-red-600 text-sm underline" href="/storage/documentos_proyectos/proyecto-00${id}/${soporte}"><i class="fa-solid fa-file-pdf text-red-600 mr-1"></i>Documento ${index + 1}</a><br>`;
        });

        detailsHtml += `<div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                            <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Tipo de idea:</p>
                            <span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${info.idea_banco === "true" ? 'Banco de ideas' : 'Idea propia del estudiante'}</span>
                        </div>

                        <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                            <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Título de la idea:</p>
                            <span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${info.titulo.toUpperCase()}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                            <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Objetivo de la idea:</p>
                            <span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${info.objetivo.charAt(0).toUpperCase() + info.objetivo.slice(1).toLowerCase()}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                            <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Línea de invesigación:</p>
                            <span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${info.linea_investigacion}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                            <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Descripción de la idea:</p>
                            <span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${info.descripcion ? info.descripcion : 'No Aplica'}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-start justify-between my-3 p-3 bg-gray-50 rounded-lg shadow-sm">
                            <p class="items-details font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Soportes de pago:</p>
                            <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${soportes}</div>
                        </div>`;
    }

    async function obtenerDatos(id) {
        let campos = await obtenerCamposProyecto(id);
        let idea_banco = obtenerValorPorNombre(campos, 'check_idea_banco');
        let titulo, objetivo, linea_investigacion, descripcion;

        if (idea_banco === "true") {
            let idea_id = obtenerValorPorNombre(campos, 'idea_banco');
            let camposIdea = await obtenerCamposProyecto(idea_id);
            let linea = await getLinea(obtenerValorPorNombre(camposIdea, 'linea_investigacion'));
            titulo = obtenerValorPorNombre(camposIdea, 'titulo');
            objetivo = obtenerValorPorNombre(camposIdea, 'objetivo');
            linea_investigacion = linea.nombre;
        } else {
            let linea = await getLinea(obtenerValorPorNombre(campos, 'linea_investigacion'));
            titulo = obtenerValorPorNombre(campos, 'titulo');
            objetivo = obtenerValorPorNombre(campos, 'objetivo');
            linea_investigacion = linea.nombre;
            descripcion = obtenerValorPorNombre(campos, 'descripcion');
        }

        let soporte_pago = JSON.parse(obtenerValorPorNombre(campos, 'soporte_pago'));
        info = { idea_banco, titulo, objetivo, linea_investigacion, descripcion, soporte_pago };

        await setHtml(id, info);
    }

    await obtenerDatos(id);

    $('#content-details-fase1').html(detailsHtml);
    $('#fase1AdminDetailsModal').addClass('show');

    loadingSpinner.classList.add('hidden');
    button.querySelector('i').classList.remove('hidden');
}

function obtenerValorPorNombre(campos, nombre) {
    const campoEncontrado = campos.find(item => item.campo.name === nombre);
    return campoEncontrado ? campoEncontrado.valor : null;
}

function closeFase1AdminDetailsModal() {
    $('#fase1AdminDetailsModal').removeClass('show');
}