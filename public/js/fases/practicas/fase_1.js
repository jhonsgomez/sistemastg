function openFase1EstudianteModal(btn) {
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }
    
    const modal = document.getElementById('fase1EstudianteModal');
    modal.classList.add('show');
    const tieneEmpresa = $(btn).data('tiene-empresa');

    if (tieneEmpresa == 1) {
        $('#contenedorPracticaInstitucional').hide();
        $('#es_institucional').prop('checked', false);
        $('#nombre_empresa_container').show();
        $('#nombre_empresa').prop('required', true);
    } else {
        $('#contenedorPracticaInstitucional').show();
        toggleNombreEmpresa();
    }

    configurarFormularioEmpresa(tieneEmpresa);

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

function closeFase1EstudianteModal() {
    TooltipManager.closeTooltips();
    $('#fase1EstudianteModal').removeClass('show');

    $('#doc_fdc126').val('');
    $('#nombre_empresa').val('');
    $('#file-list-fase1').empty();
    $('#doc_fdc126Error').text('');

    $('#contenedorPracticaInstitucional').show();
    $('#es_institucional').prop('checked', false);
    
}

    function configurarFormularioEmpresa(tieneEmpresa) {

        if (tieneEmpresa == 1) {

            $('#es_institucional_container').hide();
            $('#es_institucional').prop('checked', false);
            $('#nombre_empresa_container').show();
            $('#nombre_empresa').prop('required', true);
            $('#nombre_empresa').prop('readonly', false);

        } else {
            $('#es_institucional_container').show();
            toggleNombreEmpresa();
        }
    }

// ========== FUNCIONES PARA FASE 1 ==========

function toggleNombreEmpresa() {
    const esInstitucional = $('#es_institucional').is(':checked');
    if (esInstitucional) {
        $('#nombre_empresa_container').slideUp();
        $('#nombre_empresa').val('');
    } else {
        $('#nombre_empresa_container').slideDown();
    }
}

function closeFase1DetailsModal() {
    $('#fase1DetailsModal').removeClass('show');
}

function openFase1DetailsModal(btn) {
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }
    
    $.ajax({
        url: ROUTES.fase1_details,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            practica_id: $('input[name="practica_id"]').first().val()
        },
        success: function(response) {

            let html = `
                <div class="flex flex-col space-y-3">
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg mt-3">
                        <p class="font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">¿Es práctica institucional?:</p>
                        <span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">
                                ${response.es_institucional == 1 ? 'Sí' : 'No'}
                        </span>
                        

                    </div>
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg mt-3">
                        <p class="font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Empresa:</p>
                        <span class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">${escapeHtml(response.nombre_empresa)}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg mt-3">
                        <p class="font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">Formato F-DC-126:</p>
                        ${response.doc_fdc126 ? 
                            `<div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2" ><i class="fa-regular fa-file-word text-blue-500 mr-2"></i> <a href="/storage/${response.doc_fdc126}" target="_blank" class="text-blue-500 underline hover:text-blue-800">Ver F-DC-126</a></div>` : 
                            '<span class="text-gray-800 w-full sm:flex-1 sm:ml-2">No disponible</span>'}
                    </div>
                </div>
            `;
            
            $('#fase1DetailsContent').html(html);
            $('#fase1DetailsModal').addClass('show');
        },
        error: function(xhr) {
            console.error(xhr);
            showToast('No se pudieron cargar los detalles', 'error');
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


let quillFase1 = null;

function openFase1AdminModal(btn) {
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }
    
    $('#nro_acta_fase1').val('');
    $('#fecha_acta_fase1').val('');
    $('#estado_fase1').val('');
    $('#respuesta_fase1').val('');
    $('#nro_acta_fase1Error').text('');
    $('#fecha_acta_fase1Error').text('');
    $('#estado_fase1Error').text('');
    $('#respuesta_fase1Error').text('');
    
    $('#fase1AdminModal').addClass('show');
    
    setTimeout(function() {
        if ($('#txt-editor-fase1').length > 0) {
            if (!window.quillFase1) {
                window.quillFase1 = new Quill('#txt-editor-fase1', {
                    
                    theme: 'snow',
                    placeholder: 'Ingrese el mensaje de respuesta indicando detalles al destinatario.',
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
                                image: imageHandlerFase1
                            }
                        }
                    }
                });
            }else {
                quillFase1.root.innerHTML = '';
                quillFase1 = window.quillFase1;
            }
            quillFase1.update();
        } else {
            console.error('No se encontró el elemento #txt-editor-fase1');
        }
    }, 200);
    
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

function imageHandlerFase1() {
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

            const range = window.quillFase1.getSelection(true);

            window.quillFase1.insertEmbed(range.index, 'image', data.url);
            window.quillFase1.setSelection(range.index + 1);

        } catch (error) {
            console.error('ERROR REAL:', error);

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

function closeFase1AdminModal() {
    $('#fase1AdminModal').removeClass('show');
    if (quillFase1) {
        quillFase1.root.innerHTML = '';
    }

    $('#nro_acta_fase1').val('');
    $('#fecha_acta_fase1').val('');
    $('#estado_fase1').val('');
    $('#respuesta_fase1').val('');
    $('#nro_acta_fase1Error').text('');
    $('#fecha_acta_fase1Error').text('');
    $('#estado_fase1Error').text('');
    $('#respuesta_fase1Error').text('');

}

$(document).ready(function() {
    // ========== ENVÍO DEL FORMULARIO DEL ESTUDIANTE FASE 1 ==========
$('#fase1EstudianteForm').on('submit', function(e) {
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
            const button = $('#fase1EstudianteForm').find('button[type="submit"]');
            const spinner = $('#loadingSpinner-fase1');
            const formData = new FormData(this);
            
            button.prop('disabled', true);
            if (spinner.length) spinner.removeClass('hidden');
            
            $.ajax({
                url: ROUTES.fase1_store,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    closeFase1EstudianteModal();
                    showToast('Información enviada, tendrá respuesta en los proximos 5 días hábiles', 'success');
                    // Esperar 3 segundos (duración del toast) antes de recargar
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.doc_fdc126) $('#doc_fdc126Error').text(errors.doc_fdc126[0]);
                        if (errors.nombre_empresa) $('#nombre_empresaError').text(errors.nombre_empresa[0]);
                        setTimeout(() => {
                            $('#doc_fdc126Error').text('');
                            $('#nombre_empresaError').text('');
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

// ========== RESPUESTA DEL COMITÉ (ADMIN) FASE 1 ==========
$('#fase1AdminForm').on('submit', function(e) {
    e.preventDefault();
    
    // Obtener el contenido del editor Quill
        quillFase1 = window.quillFase1;

        if (!quillFase1) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El editor no se ha cargado correctamente.',
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        $('#respuesta_fase1').val(quillFase1.root.innerHTML);
    
    const estadoSeleccionado = $('#estado_fase1').val();
    if (!estadoSeleccionado) {
        $('#estado_fase1Error').text('Debe seleccionar un estado');
        return;
    }
    
    const mensaje = quillFase1.root.innerHTML;

    $('#respuesta_fase1').val(mensaje);

    const mensajeLimpio = quillFase1.getText().trim();

    if (!mensajeLimpio) {
        $('#respuesta_fase1Error').text('Debe ingresar un mensaje de respuesta');
        return;
    }
    
    let mensajeConfirmacion = "No podrá editar la información una vez se envíe.";
    
    Swal.fire({
        heightAuto: false,
        title: '¿Está seguro?',
        text: mensajeConfirmacion,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C1D631',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = $(this).serialize();
            const button = $(this).find('button[type="submit"]');
            const spinner = $('#loadingSpinner-fase1-admin');
            
            button.prop('disabled', true);
            if (spinner.length) spinner.removeClass('hidden');
            
            $.ajax({
                url: ROUTES.fase1_reply,
                method: 'POST',
                data: formData,
                success: function(response) {
                    closeFase1AdminModal();
                    showToast('Respuesta enviada correctamente', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.nro_acta) $('#nro_acta_fase1Error').text(errors.nro_acta[0]);
                        if (errors.fecha_acta) $('#fecha_acta_fase1Error').text(errors.fecha_acta[0]);
                        if (errors.estado) $('#estado_fase1Error').text(errors.estado[0]);
                        if (errors.respuesta_fase1) $('#respuesta_fase1Error').text(errors.respuesta_fase1[0]);
                    } else {
                        showToast(xhr.responseJSON?.error || 'Error al enviar respuesta', 'error');
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
    
    // ========== TOOLTIPS ==========
    $('.tooltip-icon').on('mouseenter', function() {
        const tooltipId = $(this).data('tooltip');
        $('#' + tooltipId).removeClass('hidden');
    }).on('mouseleave', function() {
        const tooltipId = $(this).data('tooltip');
        $('#' + tooltipId).addClass('hidden');
    });
    
    // ========== VISTA PREVIA DEL ARCHIVO ==========
    $('#doc_fdc126').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fase1');

    fileList.empty();

    if (!file) return;

    const maxSizeMB = MAX_FILE_SIZE_MB;
    const fileSizeMB = file.size / (1024 * 1024);

    if (fileSizeMB > maxSizeMB) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo demasiado grande',
            text: `El archivo no puede superar los ${maxSizeMB} MB.`,
            confirmButtonColor: '#C1D631',
            confirmButtonText: 'Aceptar'
        });

        $(this).val('');
        return;
    }

    const fileExtension = file.name.split('.').pop().toLowerCase();

    if (!['doc', 'docx', 'pdf'].includes(fileExtension)) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'Solo se permiten archivos Word o Pdf (.doc, .docx, .pdf).',
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
            ${escapeHtml(file.name)}
        </div>

        <div class="text-sm text-gray-600">
            Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_MB} MB permitidos
        </div>
    </li>
`);
});
});

// ========== FUNCIONES EXISTENTES ==========

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ========== TOAST ==========

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