// ==================== FASE 5 - PRÁCTICAS EMPRESARIALES ====================

//variables globales
let currentFase4DetailsButton = null;
// ==================== MODAL ESTUDIANTE FASE 5 ====================

function openFase5EstudianteModal(btn) {
    // Spinner botón
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }

    // ================= LIMPIAR CAMPOS =================
    $('#doc_fdc128').val('');
    $('#doc_fdc129').val('');
    $('#doc_fdc196').val('');

    // ================= LIMPIAR LISTAS =================
    $('#file-list-doc-fdc128').empty();
    $('#file-list-doc-fdc129').empty();
    $('#file-list-doc-fdc196').empty();

    // ================= LIMPIAR ERRORES =================
    $('#doc_fdc128Error').text('');
    $('#doc_fdc129Error').text('');
    $('#doc_fdc196Error').text('');

    // ================= TITULO =================
    $('#fase5EstudianteTitle').html(`
        Prácticas 
        <span class="bg-uts-500 text-white px-3 py-1 rounded uppercase shadow-md text-xl">
            Fase 5
        </span>
    `);

    // ================= ABRIR MODAL =================
    $('#fase5EstudianteModal').addClass('show');

    // ================= RESTAURAR BOTÓN =================
    if (btn) {
        setTimeout(() => {
            const icon = btn.querySelector('i');
            const spinner = btn.querySelector('.loading-spinner');
            if (icon) icon.classList.remove('hidden');
            if (spinner) spinner.classList.add('hidden');
            btn.disabled = false;
        }, 200);
    }
}

// ==================== CERRAR MODAL ====================

function closeFase5EstudianteModal() {
    $('#fase5EstudianteModal').removeClass('show');
}



// ==================== TOOLTIPS PARA FASE 3 ====================
$(document).ready(function() {

    $('#txt-editor-fase5-dir').on('drop', function(e) {
    e.preventDefault();

    Swal.fire({
        icon: 'warning',
        title: 'Use el botón de imagen',
        text: 'Para evitar errores, suba la imagen desde el botón de imagen del editor.',
        confirmButtonColor: '#C1D631',
        confirmButtonText: 'Aceptar'
    });
});
// ==================== FDC128 DIRECTOR ====================

$('#fdc128_fase5').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc128-fase5');

    fileList.empty();

    if (!file) return;

    const maxSizeBytes = MAX_FILE_SIZE_IN * 1024 * 1024;

    if (file.size > maxSizeBytes) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo demasiado grande',
            text: `El archivo no puede superar los ${MAX_FILE_SIZE_IN} MB.`,
            confirmButtonColor: '#C1D631',
            confirmButtonText: 'Aceptar'
        });

        $(this).val('');
        return;
    }

    const extension = file.name.split('.').pop().toLowerCase();

    if (!['doc', 'docx'].includes(extension)) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'Solo se permiten archivos .DOC, .DOCX.',
            confirmButtonColor: '#C1D631',
            confirmButtonText: 'Aceptar'
        });

        $(this).val('');
        return;
    }

    const fileSizeMB2 = parseFloat(
        (file.size / (1024 * 1024)).toFixed(2)
    );

    fileList.append(`
        <li class="mb-2 mt-4">
            <div class="text-gray-600 text-sm mb-2">
                ${file.name}
            </div>
            <div class="text-sm text-gray-600">
                Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_IN} MB permitidos
            </div>
        </li>
    `);
});

// ==================== FDC129 DIRECTOR ====================

$('#fdc129_fase5').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc129-fase5');

    fileList.empty();

    if (!file) return;

    const maxSizeBytes = MAX_FILE_SIZE_IN * 1024 * 1024;

    if (file.size > maxSizeBytes) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo demasiado grande',
            text: `El archivo no puede superar los ${MAX_FILE_SIZE_IN} MB.`,
            confirmButtonColor: '#C1D631',
            confirmButtonText: 'Aceptar'
        });

        $(this).val('');
        return;
    }

    const extension = file.name.split('.').pop().toLowerCase();

    if (!['doc', 'docx'].includes(extension)) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'Solo se permiten archivos .DOC, .DOCX.',
            confirmButtonColor: '#C1D631',
            confirmButtonText: 'Aceptar'
        });

        $(this).val('');
        return;
    }

    const fileSizeMB2 = parseFloat(
        (file.size / (1024 * 1024)).toFixed(2)
    );

    fileList.append(`
        <li class="mb-2 mt-4">
            <div class="text-gray-600 text-sm  mb-2">
                ${file.name}
            </div>
            <div class="text-sm text-gray-600">
                Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_IN} MB permitidos
            </div>
        </li>
    `);
});

//
// ==================== FDC196 DIRECTOR ====================

$('#fdc196_fase5').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc196-fase5');

    fileList.empty();

    if (!file) return;

    const maxSizeBytes = MAX_FILE_SIZE_IN * 1024 * 1024;

    if (file.size > maxSizeBytes) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo demasiado grande',
            text: `El archivo no puede superar los ${MAX_FILE_SIZE_IN} MB.`,
            confirmButtonColor: '#C1D631',
            confirmButtonText: 'Aceptar'
        });

        $(this).val('');
        return;
    }

    const extension = file.name.split('.').pop().toLowerCase();

    if (!['pdf', 'doc', 'docx'].includes(extension)) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'Solo se permiten archivos .DOC, .DOCX. .PDF',
            confirmButtonColor: '#C1D631',
            confirmButtonText: 'Aceptar'
        });

        $(this).val('');
        return;
    }

    const fileSizeMB2 = parseFloat(
        (file.size / (1024 * 1024)).toFixed(2)
    );

    fileList.append(`
        <li class="mb-2 mt-4">
            <div class="text-gray-600 text-sm  mb-2">
                ${file.name}
            </div>
            <div class="text-sm text-gray-600">
                Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_IN} MB permitidos
            </div>
        </li>
    `);
});

// ==================== TURNITIN ====================

$('#turnitin_fase5').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-turnitin-fase5');

    fileList.empty();

    if (!file) return;

    const maxSizeBytes = MAX_FILE_SIZE_IN * 1024 * 1024;

    if (file.size > maxSizeBytes) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo demasiado grande',
            text: `El archivo no puede superar los ${MAX_FILE_SIZE_IN} MB.`,
            confirmButtonColor: '#C1D631',
            confirmButtonText: 'Aceptar'
        });

        $(this).val('');
        return;
    }

    const extension = file.name.split('.').pop().toLowerCase();

    if (extension !== 'pdf') {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'El archivo Turnitin debe ser PDF.',
            confirmButtonColor: '#C1D631',
            confirmButtonText: 'Aceptar'
        });

        $(this).val('');
        return;
    }

    const fileSizeMB2 = parseFloat(
        (file.size / (1024 * 1024)).toFixed(2)
    );

    fileList.append(`
        <li class="mb-2 mt-4">
            <div class="text-gray-600 text-sm  mb-2">
                ${file.name}
            </div>
            <div class="text-sm text-gray-600">
                Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_IN} MB permitidos
            </div>
        </li>
    `);
});

    // ==================== DOC_FDC128 ESTUDIANTE ====================

$('#doc_fdc128').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-doc-fdc128');

    fileList.empty();

    if (!file) return;

    const maxSizeBytes = MAX_FILE_SIZE_IN * 1024 * 1024;

    if (file.size > maxSizeBytes) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo demasiado grande',
            text: `El archivo no puede superar los ${MAX_FILE_SIZE_IN} MB.`,
            confirmButtonColor: '#C1D631'
        });

        $(this).val('');
        return;
    }

    const extension = file.name.split('.').pop().toLowerCase();

    if (!['doc', 'docx'].includes(extension)) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'Solo se permiten archivos .DOC y .DOCX.',
            confirmButtonColor: '#C1D631'
        });

        $(this).val('');
        return;
    }

    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);

    fileList.append(`
        <li class="mb-2 mt-4">
            <div class="text-gray-600 text-sm mb-2">
                ${file.name}
            </div>
            <div class="text-sm text-gray-600">
                Tamaño total: ${fileSizeMB} MB de ${MAX_FILE_SIZE_IN} MB permitidos
            </div>
        </li>
    `);

});

    // ==================== DOC_FDC129 ESTUDIANTE ====================

$('#doc_fdc129').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-doc-fdc129');

    fileList.empty();

    if (!file) return;

    const maxSizeBytes = MAX_FILE_SIZE_IN * 1024 * 1024;

    if (file.size > maxSizeBytes) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo demasiado grande',
            text: `El archivo no puede superar los ${MAX_FILE_SIZE_IN} MB.`,
            confirmButtonColor: '#C1D631'
        });

        $(this).val('');
        return;
    }

    const extension = file.name.split('.').pop().toLowerCase();

    if (!['doc', 'docx'].includes(extension)) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'Solo se permiten archivos .DOC y .DOCX.',
            confirmButtonColor: '#C1D631'
        });

        $(this).val('');
        return;
    }

    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);

    fileList.append(`
        <li class="mb-2 mt-4">
            <div class="text-gray-600 text-sm  mb-2">
                ${file.name}
            </div>
            <div class="text-sm text-gray-600">
                Tamaño total: ${fileSizeMB} MB de ${MAX_FILE_SIZE_IN} MB permitidos
            </div>
        </li>
    `);

});

    // ==================== DOCFDC196 ESTUDIANTE ====================

$('#doc_fdc196').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-doc-fdc196');

    fileList.empty();

    if (!file) return;

    const maxSizeBytes = MAX_FILE_SIZE_IN * 1024 * 1024;

    if (file.size > maxSizeBytes) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo demasiado grande',
            text: `El archivo no puede superar los ${MAX_FILE_SIZE_IN} MB.`,
            confirmButtonColor: '#C1D631'
        });

        $(this).val('');
        return;
    }

    const extension = file.name.split('.').pop().toLowerCase();

    if (!['pdf', 'doc', 'docx'].includes(extension)) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'Solo se permiten archivos .DOC y .DOCX. .PDF',
            confirmButtonColor: '#C1D631'
        });

        $(this).val('');
        return;
    }

    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);

    fileList.append(`
        <li class="mb-2 mt-4">
            <div class="text-gray-600 text-sm mb-2">
                ${file.name}
            </div>
            <div class="text-sm text-gray-600">
                Tamaño total: ${fileSizeMB} MB de ${MAX_FILE_SIZE_IN} MB permitidos
            </div>
        </li>
    `);

});
    
// ==================== ENVIO DEL FORMULARIO ESTUDIANTE CON CONFIRMACION SUBMIT FASE 5 ====================

$('#fase5EstudianteForm').on('submit', function (e) {

    e.preventDefault();

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

            const button = $(this).find('button[type="submit"]');
            const spinner = $('#loadingSpinner-fase5');
            const formData = new FormData(this);

            button.prop('disabled', true);
            if (spinner.length) spinner.removeClass('hidden');

            $.ajax({
                url: ROUTES.fase5_store,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,

                success: function (response) {
                  

                    closeFase5EstudianteModal();
                    showToast('Documentos enviados correctamente.', 'success');

                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                },

                error: function (xhr) {

                    if (xhr.status === 422 && xhr.responseJSON.errors) {

                        const errors = xhr.responseJSON.errors;

                     if (errors.doc_fdc128)
                        $('#fdc128_fase5Error').text(errors.doc_fdc128[0]);

                    if (errors.doc_fdc129)
                        $('#fdc129_fase5Error').text(errors.doc_fdc129[0]);

                    if (errors.doc_fdc196)
                        $('#fdc196_fase5Error').text(errors.doc_fdc196[0]);

                        // limpiar errores
                        setTimeout(() => {
                            $('#fdc128_fase5Error').text('');
                            $('#fdc129_fase5Error').text('');
                            $('#fdc196_fase5Error').text('');
                        }, 5000);

                    } else {
                        showToast(xhr.responseJSON?.error || 'Error al enviar', 'error');
                    }
                },

                complete: function () {
                    button.prop('disabled', false);
                    if (spinner.length) spinner.addClass('hidden');
                }
            });

        }

    });

});

});

// ==================== TOOLTIPS CERCA DEL CURSOR ====================
$(document).ready(function() {
    $('.tooltip-icon').on('mouseenter', function(e) {
        const tooltipId = $(this).data('tooltip');
        const $tooltip = $('#' + tooltipId);
        
        // Ocultar todos
        $('[id^="tooltip-"]').addClass('hidden');
        
        // Mostrar este
        $tooltip.removeClass('hidden');
        
        // Posicionar JUSTO cerca del cursor (sin desplazamiento grande)
        const mouseX = e.clientX;
        const mouseY = e.clientY;
        const tooltipHeight = $tooltip.outerHeight();
        const tooltipWidth = $tooltip.outerWidth();
        
        // Posición: arriba del cursor con poco espacio
        let top = mouseY - tooltipHeight;
        let left = mouseX - (tooltipWidth);
        
        $tooltip.css({
            position: 'fixed',
            top: top + 'px',
            left: left + 'px',
            zIndex: 99999
        });
    }).on('mouseleave', function() {
        const tooltipId = $(this).data('tooltip');
        $('#' + tooltipId).addClass('hidden');
    });
});





// FASE 5 VER DETALLES
// ==================== FASE 5 ====================

// Abrir modal de detalles Fase 5
function openFase5DetailsModal(btn) {


    // Mostrar spinner y ocultar icono
    if (btn) {

        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');

        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');

        btn.disabled = true;
    }

    // AJAX
    $.ajax({

        url: ROUTES.fase5_details,
        method: 'POST',

        data: {

            _token: $('meta[name="csrf-token"]').attr('content'),
            practica_id: $('input[name="practica_id"]').first().val()

        },

        success: function(response) {


            let html = `

                <div class="flex flex-col space-y-4">

                    <!-- INFORME FINAL -->
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">

                        <p class="font-semibold text-gray-700 w-1/3 min-w-[140px] mb-2 sm:mb-0">
                            F-DC-128:
                        </p>

                        ${
                            response.informe_final_url
                            ? `
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">

                                    <i class="fa-regular fa-file-word text-blue-600 mr-2"></i>

                                    <a href="${response.informe_final_url}"
                                        target="_blank"
                                        class="text-blue-600 underline hover:text-blue-800">

                                        Ver F-DC-128

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
                    <!-- REJILLA -->
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">

                        <p class="font-semibold text-gray-700 w-1/3 min-w-[140px] mb-2 sm:mb-0">
                            F-DC-129:
                        </p>

                        ${
                            response.rejilla_evaluacion_url
                            ? `
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">

                                    <i class="fa-regular fa-file-word text-blue-600 mr-2"></i>

                                    <a href="${response.rejilla_evaluacion_url}"
                                        target="_blank"
                                        class="text-blue-600 underline hover:text-blue-800">

                                        Ver F-DC-129

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

                    <!-- ACTA TERMINACIÓN -->
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">

                        <p class="font-semibold text-gray-700 w-1/3 min-w-[140px] mb-2 sm:mb-0">
                            F-DC-196:
                        </p>

                        ${
                            response.acta_terminacion_url
                            ? `
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">

                                    <i class="fa-regular fa-file-word text-blue-600 mr-2"></i>

                                    <a href="${response.acta_terminacion_url}"
                                        target="_blank"
                                        class="text-blue-600 underline hover:text-blue-800">

                                        Ver F-DC-196

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

                    

                    
                </div>

            `;

            $('#fase5DetailsContent').html(html);

            $('#fase5DetailsModal')
                .removeClass('hidden')
                .addClass('show');

        },

        error: function(xhr) {

            console.error(xhr);

            Swal.fire(
                'Error',
                'No se pudieron cargar los detalles',
                'error'
            );

        },

        complete: function() {

            // Restaurar botón
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

// ==================== CERRAR MODAL ====================

function closeFase5DetailsModal() {

    $('#fase5DetailsModal')
        .removeClass('show')
        .addClass('hidden');

}


// ==================== MODAL DIRECTOR FASE 3 ====================
let quillFase5Dir = null;

function openFase5DirModal(btn) {

    // ================= SPINNER BOTÓN =================

    if (btn) {

        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');

        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');

        btn.disabled = true;

    }

    // ================= LIMPIAR CAMPOS =================

    $('#estado_fase5_dir').val('');
    $('#respuesta_fase5_dir').val('');

    $('#fdc128_fase5').val('');
    $('#fdc129_fase5').val('');
    $('#fdc196_fase5').val('');
    $('#turnitin_fase5').val('');

    // ================= LIMPIAR ERRORES =================

    $('#estado_fase5_dirError').text('');
    $('#respuesta_fase5_dirError').text('');

    $('#fdc128_fase5Error').text('');
    $('#fdc129_fase5Error').text('');
    $('#fdc196_fase5Error').text('');
    $('#turnitin_fase5Error').text('');

    // ================= LIMPIAR LISTAS =================

    $('#file-list-fdc128-fase5').html('');
    $('#file-list-fdc129-fase5').html('');
    $('#file-list-fdc196-fase5').html('');
    $('#file-list-turnitin-fase5').html('');

    // ================= ABRIR MODAL =================

    $('#fase5DirModal').addClass('show');

    // ================= QUILL =================

    setTimeout(function () {

        if ($('#txt-editor-fase5-dir').length > 0) {

            if (quillFase5Dir === null) {

                quillFase5Dir = new Quill('#txt-editor-fase5-dir', {
                    theme: 'snow',
                    placeholder: 'Describa los detalles de la respuesta para el estudiante.',
                    modules: {
                        toolbar: {
                            container: [
                                [{ 'header': 1 }],
                                [{ 'header': 2 }],
                                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                ['bold', 'italic', 'underline'],
                                [{ 'color': [] }],
                                [{ 'align': [] }],
                                ['image'],
                                ['clean']
                            ],
                            handlers: {
                                image: imageHandlerFase5Dir
                            }
                        }
                    }
                });

            } else {

                quillFase5Dir.root.innerHTML = '';

            }

            quillFase5Dir.update();

        }

    }, 200);

    // ================= RESTAURAR BOTÓN =================

    if (btn) {

        setTimeout(() => {

            const icon = btn.querySelector('i');
            const spinner = btn.querySelector('.loading-spinner');

            if (icon) icon.classList.remove('hidden');
            if (spinner) spinner.classList.add('hidden');

            btn.disabled = false;

        }, 200);

    }

}


function imageHandlerFase5Dir() {
    let input = document.createElement('input');

    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/webp';
    input.style.display = 'none';

    document.body.appendChild(input);
    input.click();

    input.onchange = async function () {
        const file = input.files[0];

        if (!file) {
            document.body.removeChild(input);
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Imagen demasiado grande',
                text: 'La imagen no puede superar los 2 MB.',
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });

            document.body.removeChild(input);
            return;
        }

        const formData = new FormData();
        formData.append('image', file);

        try {
            const response = await fetch(window.quillUploadUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const text = await response.text();


            if (!response.ok) {
                throw new Error(text);
            }

            const data = JSON.parse(text);

            if (!data.url) {
                throw new Error('Laravel no devolvió data.url');
            }

            const range = quillFase5Dir.getSelection(true);

            quillFase5Dir.insertEmbed(range.index, 'image', data.url);
            quillFase5Dir.setSelection(range.index + 1);

        } catch (error) {
            console.error('ERROR REAL FASE 5 DIR:', error);

            Swal.fire({
                icon: 'error',
                title: 'Error al subir imagen',
                html: `<small style="text-align:left;display:block;max-height:200px;overflow:auto;">${error.message}</small>`,
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });
        }

        document.body.removeChild(input);
    };
}

// ==================== CERRAR MODAL ====================

function closeFase5DirModal() {

    $('#fase5DirModal').removeClass('show');

    if (quillFase5Dir) {

        quillFase5Dir.root.innerHTML = '';

    }

}

// ==================== SUBMIT ====================

$('#fase5DirForm').on('submit', function (e) {

    e.preventDefault();

    // ================= GUARDAR QUILL =================
    if (!quillFase5Dir) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El editor no se ha cargado correctamente.',
            confirmButtonColor: '#C1D631',
            confirmButtonText: 'Aceptar'
        });
        return;
    }

    $('#respuesta_fase5_dir').val(quillFase5Dir.root.innerHTML);

    const mensajeLimpio = quillFase5Dir.getText().trim();

    if (!mensajeLimpio) {
        $('#respuesta_fase5_dirError').text('Debe ingresar un mensaje de respuesta');
        return;
    }

    // ================= VALIDAR ESTADO =================

    const estado = $('#estado_fase5_dir').val();

    if (!estado) {

        $('#estado_fase5_dirError').text(
            'Debe seleccionar un estado'
        );

        return;

    }

    // ================= CONFIRMACIÓN =================

    Swal.fire({

        heightAuto: false,

        title: '¿Está seguro?',

        text: 'Esta acción no se puede deshacer',

        icon: 'warning',

        showCancelButton: true,

        confirmButtonColor: '#C1D631',

        cancelButtonColor: '#d33',

        confirmButtonText: 'Sí, responder',

        cancelButtonText: 'Cancelar'

    }).then((result) => {

        if (result.isConfirmed) {

            const button = $(this).find('button[type="submit"]');

            const spinner = $('#loadingSpinner-fase5-dir');

            // IMPORTANTE PARA ARCHIVOS
            const formData = new FormData(this);

            button.prop('disabled', true);

            if (spinner.length)
                spinner.removeClass('hidden');

            $.ajax({

                url: ROUTES.fase5_reply,

                method: 'POST',

                data: formData,

                processData: false,

                contentType: false,

                // ================= SUCCESS =================

                success: function (response) {

                

                    closeFase5DirModal();

                    showToast(
                        response.success || 'Respuesta enviada exitosamente',
                        'success'
                    );

                    setTimeout(() => {

                        location.reload();

                    }, 3000);

                },

                // ================= ERROR =================

                error: function (xhr) {


                    if (
                        xhr.status === 422 &&
                        xhr.responseJSON.errors
                    ) {

                        const errors = xhr.responseJSON.errors;

                        if (errors.estado)
                            $('#estado_fase5_dirError')
                                .text(errors.estado[0]);

                        if (errors.respuesta)
                            $('#respuesta_fase5_dirError')
                                .text(errors.respuesta[0]);

                        if (errors.fdc128)
                            $('#fdc128_fase5Error')
                                .text(errors.fdc128[0]);

                        if (errors.fdc129)
                            $('#fdc129_fase5Error')
                                .text(errors.fdc129[0]);

                        if (errors.fdc196)
                            $('#fdc196_fase5Error')
                                .text(errors.fdc196[0]);

                        if (errors.turnitin)
                            $('#turnitin_fase5Error')
                                .text(errors.turnitin[0]);

                    } else {

                        Swal.fire(
                            'Error',
                            xhr.responseJSON?.error || 'Error al enviar respuesta',
                            'error'
                        );

                    }

                },

                // ================= COMPLETE =================

                complete: function () {

                    button.prop('disabled', false);

                    if (spinner.length)
                        spinner.addClass('hidden');

                }

            });

        }

    });

});

// ==================== TOAST ====================

function showToast(message, type = 'success') {

    Swal.fire({

        title: type === 'success' ? '¡Éxito!' : 'Error',

        text: message,

        icon: type,

        toast: true,

        position: 'top-end',

        showConfirmButton: false,

        timer: 3000

    });

}