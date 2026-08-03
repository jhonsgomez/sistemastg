// ==================== FASE 3 - PRÁCTICAS EMPRESARIALES ====================

// Variables globales
let currentFase3DetailsButton = null;

// ==================== MODAL ESTUDIANTE ====================

// ==================== FUNCIONES FASE 3 ESTUDIANTE ====================

function openFase3EstudianteModal(btn) {
    // Mostrar spinner y ocultar icono en el botón
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }
    
    // Limpiar campos del formulario
    $('#arl').val('');
    $('#doc_fdc127').val('');
    $('#doc_fdc195').val('');

    $('#file-list-arl').empty();
    $('#file-list-fdc127').empty();
    $('#file-list-fdc195').empty();

    $('#arlError').text('');
    $('#doc_fdc127Error').text('');
    $('#doc_fdc195Error').text('');
    
    // Cambiar el título si es necesario
    $('#fase3EstudianteTitle').html(`Prácticas <span class="bg-uts-500 text-white px-3 py-1 rounded uppercase shadow-md text-xl">Fase 3</span>`);
    
    // Abrir el modal con animación
    $('#fase3EstudianteModal').addClass('show');
    
    // Restaurar el botón después de abrir el modal
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

function closeFase3EstudianteModal() {

    $('#fase3EstudianteModal').removeClass('show');
}

// ==================== TOOLTIPS PARA FASE 3 ====================
$(document).ready(function() {

        $('#txt-editor-fase3-dir').on('drop', function(e) {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Use el botón de imagen',
            text: 'Para evitar errores, suba la imagen desde el botón de imagen del editor.',
            confirmButtonColor: '#C1D631',
            confirmButtonText: 'Aceptar'
        });
    });

    // CAMPOS VALIDACIONES PARA DIRECTOR

// ==================== FDC127 DIRECTOR ====================

$('#fdc127_fase3').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc127-fase3');

    fileList.empty();

    if (file) {

        const maxSizeBytes = MAX_FILE_SIZE_PR * 1024 * 1024;

if (file.size > maxSizeBytes) {

    Swal.fire({
        icon: 'error',
        title: 'Archivo demasiado grande',
        text: `El archivo no puede superar los ${MAX_FILE_SIZE_PR} MB.`,
        confirmButtonColor: '#C1D631',
        confirmButtonText: 'Aceptar'
    });

    $(this).val('');
    return;
}

        const extension = file.name
            .split('.')
            .pop()
            .toLowerCase();

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

        let icon = 'fa-file-word text-blue-500';

        if (extension === 'pdf') {
            icon = 'fa-file-pdf text-red-500';
        }
    
const fileSizeMB2 = parseFloat(
        (file.size / (1024 * 1024)).toFixed(2)
    );
    
fileList.append(`
    <li class="mb-2 mt-4">
        <div class="text-gray-600 text-sm  mb-2">
            ${escapeHtml(file.name)}
        </div>
        <div class="text-sm text-gray-600">
            Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_PR} MB permitidos
        </div>
    </li>
`);
    }
});

// ==================== FDC195 DIRECTOR ====================

$('#fdc195_fase3').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc195-fase3');

    fileList.empty();

    if (file) {

        const maxSizeBytes = MAX_FILE_SIZE_PR * 1024 * 1024;

if (file.size > maxSizeBytes) {

    Swal.fire({
        icon: 'error',
        title: 'Archivo demasiado grande',
        text: `El archivo no puede superar los ${MAX_FILE_SIZE_PR} MB.`,
        confirmButtonColor: '#C1D631',
        confirmButtonText: 'Aceptar'
    });

    $(this).val('');
    return;
}

        const extension = file.name
            .split('.')
            .pop()
            .toLowerCase();

        if (!['pdf', 'doc', 'docx'].includes(extension)) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'Solo se permiten archivos .DOC, .DOCX., .PDF',
            confirmButtonColor: '#C1D631',
            confirmButtonText: 'Aceptar'
        });

        $(this).val('');
        return;
    }

        let icon = 'fa-file-word text-blue-500';

        if (extension === 'pdf') {
            icon = 'fa-file-pdf text-red-500';
        }

        const fileSizeMB2 = parseFloat(
        (file.size / (1024 * 1024)).toFixed(2)
    );
    
fileList.append(`
    <li class="mb-2 mt-4">
        <div class="text-gray-600 text-sm mb-2">
            ${escapeHtml(file.name)}
        </div>
        <div class="text-sm text-gray-600">
            Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_PR} MB permitidos
        </div>
    </li>
`);
    }
});

// ==================== TURNITIN ====================

$('#turnitin_fase3').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-turnitin-fase3');

    fileList.empty();

    if (file) {

        const maxSizeBytes = MAX_FILE_SIZE_PR * 1024 * 1024;

if (file.size > maxSizeBytes){

    Swal.fire({
        icon: 'error',
        title: 'Archivo demasiado grande',
        text: `El archivo no puede superar los ${MAX_FILE_SIZE_PR} MB.`,
        confirmButtonColor: '#C1D631',
        confirmButtonText: 'Aceptar'
    });

    $(this).val('');

    return;
}

        const extension = file.name
            .split('.')
            .pop()
            .toLowerCase();

        if (extension !== 'pdf') {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'Solo se permiten archivos PDF.',
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
            ${escapeHtml(file.name)}
        </div>
        <div class="text-sm text-gray-600">
            Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_PR} MB permitidos
        </div>
    </li>
`);
    }
});
   
    
    // ========== ENVÍO DEL FORMULARIO DEL ESTUDIANTE CON CONFIRMACIÓN ==========
$('#fase3EstudianteForm').on('submit', function(e) {
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
            const button = $('#fase3EstudianteForm').find('button[type="submit"]');
            const spinner = $('#loadingSpinner-fase3');
            const formData = new FormData(this);
            
            button.prop('disabled', true);
            if (spinner.length) spinner.removeClass('hidden');
            
            $.ajax({
                url: ROUTES.fase3_store,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    closeFase3EstudianteModal();
                    showToast('Información enviada, tendrá respuesta en los proximos 5 días hábiles', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.arl) 
                            $('#arlError').text(errors.arl[0]);

                        if (errors.doc_fdc127)
                            $('#doc_fdc127Error').text(errors.doc_fdc127[0]);

                        if (errors.doc_fdc195)
                            $('#doc_fdc195Error').text(errors.doc_fdc195[0]);
                        // Limpiar errores después de 5 segundos
                        setTimeout(() => {
                            $('#arlError').text('');
                            $('#doc_fdc127Error').text('');
                            $('#doc_fdc195Error').text('');
                        }, 5000);
                    } else {
                        showToast(xhr.responseJSON?.error || 'Error al enviar', 'error');
                    }
                },
                complete: function() {
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

 // CAMPOS VALIDACIÓN PARA ESTUDIANTE

    // ARL
    $('#arl').on('change', function (e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-arl');

    fileList.empty();

    if (!file) return;

    const maxSizeBytes = MAX_FILE_SIZE_PR * 1024 * 1024;

if (file.size > maxSizeBytes){

    Swal.fire({
        icon: 'error',
        title: 'Archivo demasiado grande',
        text: `El archivo no puede superar los ${MAX_FILE_SIZE_PR} MB.`,
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
            text: 'Solo se permiten archivos PDF.',
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
            ${escapeHtml(file.name)}
        </div>
        <div class="text-sm text-gray-600">
            Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_PR} MB permitidos
        </div>
    </li>
`);
});

    //FDC127

    $('#doc_fdc127').on('change', function (e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc127');

    fileList.empty();

    if (!file) return;

    const maxSizeBytes = MAX_FILE_SIZE_PR * 1024 * 1024;

if (file.size > maxSizeBytes) {

    Swal.fire({
        icon: 'error',
        title: 'Archivo demasiado grande',
        text: `El archivo no puede superar los ${MAX_FILE_SIZE_PR} MB.`,
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
            text: 'Solo se permiten archivos DOC o DOCX.',
            confirmButtonColor: '#C1D631'
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
            ${escapeHtml(file.name)}
        </div>
        <div class="text-sm text-gray-600">
            Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_PR} MB permitidos
        </div>
    </li>
`);
});

    // FDC195

    $('#doc_fdc195').on('change', function (e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc195');

    fileList.empty();

    if (!file) return;

    const maxSizeBytes = MAX_FILE_SIZE_PR * 1024 * 1024;

if (file.size > maxSizeBytes) {

    Swal.fire({
        icon: 'error',
        title: 'Archivo demasiado grande',
        text: `El archivo no puede superar los ${MAX_FILE_SIZE_PR} MB.`,
        confirmButtonColor: '#C1D631',
        confirmButtonText: 'Aceptar'
    });

    $(this).val('');
    return;
}

    const extension = file.name.split('.').pop().toLowerCase();

    if (!['doc', 'docx', 'pdf'].includes(extension)) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'Solo se permiten archivos .DOC, .DOCX o .PDF.',
            confirmButtonColor: '#C1D631'
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
            ${escapeHtml(file.name)}
        </div>
        <div class="text-sm text-gray-600">
            Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_PR} MB permitidos
        </div>
    </li>
`);
});

// Abrir modal de detalles con spinner en el botón (exactamente como Fase 1)
function openFase3DetailsModal(btn) {

    // Mostrar spinner y ocultar icono en el botón
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }
    
    // Hacer la petición AJAX
    $.ajax({
        url: ROUTES.fase3_details,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            practica_id: $('input[name="practica_id"]').first().val()
        },
        success: function(response) {

            let html = `
                <div class="flex flex-col space-y-3">

                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg mt-3">
                        <p class="font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">
                            ARL:
                        </p>

                        ${response.arl_url
                            ? `
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">
                                    <i class="fa-regular fa-file-pdf text-red-600 mr-2"></i>
                                    <a href="${response.arl_url}" target="_blank"
                                        class="text-red-600 underline hover:text-red-800">
                                        Ver ARL
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

                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg mt-3">
                        <p class="font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">
                            F-DC-127:
                        </p>

                        ${response.doc_fdc127_url
                            ? `
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">
                                    <i class="fa-regular fa-file-word text-blue-600 mr-2"></i>
                                    <a href="${response.doc_fdc127_url}" target="_blank"
                                        class="text-blue-600 underline hover:text-blue-800">
                                        Ver F-DC-127
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

                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg mt-3">
                        <p class="font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">
                            F-DC-195:
                        </p>

                        ${response.doc_fdc195_url
                            ? `
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">
                                    <i class="fa-regular fa-file-word text-blue-600 mr-2"></i>
                                    <a href="${response.doc_fdc195_url}" target="_blank"
                                        class="text-blue-600 underline hover:text-blue-800">
                                        Ver F-DC-195
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
            
            $('#fase3DetailsContent').html(html);
            $('#fase3DetailsModal').addClass('show');
        },
        error: function(xhr) {
            console.error(xhr);
            Swal.fire('Error', 'No se pudieron cargar los detalles', 'error');
        },
        complete: function() {
            // Restaurar el botón: ocultar spinner y mostrar icono
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

// Cerrar modal de detalles
function closeFase3DetailsModal() {
    $('#fase3DetailsModal').removeClass('show');
}

// ==================== MODAL DIRECTOR FASE 3 ====================

let quillFase3Dir = null;

function openFase3DirModal(btn) {

    // Spinner botón
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');

        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');

        btn.disabled = true;
    }

    // ================= LIMPIAR CAMPOS =================

    $('#estado_fase3_dir').val('');
    $('#titulo_propuesta_fase3').val('');
    $('#respuesta_fase3_dir').val('');

    $('#fdc127_fase3').val('');
    $('#fdc195_fase3').val('');
    $('#turnitin_fase3').val('');

    // ================= LIMPIAR ERRORES =================

    $('#estado_fase3_dirError').text('');
    $('#titulo_propuesta_fase3Error').text('');
    $('#respuesta_fase3_dirError').text('');

    $('#fdc127_fase3Error').text('');
    $('#fdc195_fase3Error').text('');
    $('#turnitin_fase3Error').text('');

    // ================= LIMPIAR LISTAS =================

    $('#file-list-fdc127-fase3').html('');
    $('#file-list-fdc195-fase3').html('');
    $('#file-list-turnitin-fase3').html('');

    // ================= ABRIR MODAL =================

    $('#fase3DirModal').addClass('show');

    // ================= QUILL =================

    setTimeout(function () {

        if ($('#txt-editor-fase3-dir').length > 0) {

            if (quillFase3Dir === null) {

                quillFase3Dir = new Quill('#txt-editor-fase3-dir', {
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
                                image: imageHandlerFase3Dir
                            }
                        }
                    }
                });

            } else {

                quillFase3Dir.root.innerHTML = '';

            }

            quillFase3Dir.update();

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

function imageHandlerFase3Dir() {
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

            const range = quillFase3Dir.getSelection(true);

            quillFase3Dir.insertEmbed(range.index, 'image', data.url);
            quillFase3Dir.setSelection(range.index + 1);

        } catch (error) {
            console.error('ERROR REAL FASE 3 DIR:', error);

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

function closeFase3DirModal() {

    $('#fase3DirModal').removeClass('show');

    if (quillFase3Dir) {
        quillFase3Dir.root.innerHTML = '';
    }
}

// ==================== SUBMIT ====================

$('#fase3DirForm').on('submit', function (e) {

    e.preventDefault();

    // Guardar contenido quill
    if (!quillFase3Dir) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El editor no se ha cargado correctamente.',
            confirmButtonColor: '#C1D631',
            confirmButtonText: 'Aceptar'
        });
        return;
    }

    $('#respuesta_fase3_dir').val(quillFase3Dir.root.innerHTML);

    const mensajeLimpio = quillFase3Dir.getText().trim();

    if (!mensajeLimpio) {
        $('#respuesta_fase3_dirError').text('Debe ingresar un mensaje de respuesta');
        return;
    }

    // Validar estado
    const estado = $('#estado_fase3_dir').val();

    if (!estado) {
        $('#estado_fase3_dirError').text('Debe seleccionar un estado');
        return;
    }

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
            const spinner = $('#loadingSpinner-fase3-admin');

            // IMPORTANTE para archivos
            const formData = new FormData(this);

            button.prop('disabled', true);

            if (spinner.length)
                spinner.removeClass('hidden');

            $.ajax({

                url: ROUTES.fase3_reply,

                method: 'POST',

                data: formData,

                processData: false,
                contentType: false,

                success: function (response) {

                    closeFase3DirModal();
                    
                    // ÉXITO: Toast (no modal)
                    showToast(response.success || 'Respuesta enviada exitosamente', 'success');
                    
                    setTimeout(() => {
                        location.reload();
                    }, 3000);

                },

                error: function (xhr) {

                    if (xhr.status === 422 && xhr.responseJSON.errors) {

                        const errors = xhr.responseJSON.errors;

                        if (errors.estado)
                            $('#estado_fase3_dirError').text(errors.estado[0]);

                        if (errors.titulo_propuesta)
                            $('#titulo_propuesta_fase3Error').text(errors.titulo_propuesta[0]);

                        if (errors.respuesta)
                            $('#respuesta_fase3_dirError').text(errors.respuesta[0]);

                        if (errors.fdc127)
                            $('#fdc127_fase3Error').text(errors.fdc127[0]);

                        if (errors.fdc195)
                            $('#fdc195_fase3Error').text(errors.fdc195[0]);

                        if (errors.turnitin)
                            $('#turnitin_fase3Error').text(errors.turnitin[0]);

                    } else {

                        // ERROR: Swal.fire modal (se mantiene igual)
                        Swal.fire(
                            'Error',
                            xhr.responseJSON?.error || 'Error al enviar respuesta',
                            'error'
                        );

                    }

                },

                complete: function () {

                    button.prop('disabled', false);

                    if (spinner.length)
                        spinner.addClass('hidden');

                }

            });

        }

    });

});

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
