function openFase1EstudianteModal(btn) {
    // Mostrar spinner y ocultar icono en el botón
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }
    
    const modal = document.getElementById('fase1EstudianteModal');
    modal.classList.add('show');
    toggleNombreEmpresa();
    
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

function closeFase1EstudianteModal() {
    TooltipManager.closeTooltips();
    $('#fase1EstudianteModal').removeClass('show');
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

// Función para cerrar el modal de detalles
function closeFase1DetailsModal() {
    $('#fase1DetailsModal').removeClass('show');
}

// Abrir modal de detalles con spinner en el botón
function openFase1DetailsModal(btn) {
    // Mostrar spinner y ocultar icono en el botón
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
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">
                        <p class="font-semibold text-gray-700 w-1/3">¿Es práctica institucional?:</p>
                        <span class="text-gray-800">${response.es_institucional ? 'Sí' : 'No'}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">
                        <p class="font-semibold text-gray-700 w-1/3">Empresa:</p>
                        <span class="text-gray-800">${escapeHtml(response.nombre_empresa)}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">
                        <p class="font-semibold text-gray-700 w-1/3">Formato F-DC-126:</p>
                        ${response.doc_fdc126 ? 
                            `<div class="flex items-center"><i class="fa-regular fa-file-word text-blue-500 mr-2"></i> <a href="/storage/${response.doc_fdc126}" target="_blank" class="text-blue-500 underline hover:text-blue-800">Ver archivo</a></div>` : 
                            '<span class="text-gray-500">No disponible</span>'}
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

// Función para abrir modal de administrador (responder) con spinner

let quillFase1 = null;

function openFase1AdminModal(btn) {
    // Mostrar spinner y ocultar icono en el botón
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
    
    // PRIMERO abrir el modal
    $('#fase1AdminModal').addClass('show');
    
    // ESPERAR a que el modal esté visible y luego inicializar Quill
    setTimeout(function() {
        // Verificar si el elemento existe
        if ($('#txt-editor-fase1').length > 0) {
            if (quillFase1 === null) {
                quillFase1 = new Quill('#txt-editor-fase1', {
                    theme: 'snow',
                    placeholder: 'Ingrese el mensaje de respuesta indicando detalles al destinatario.',
                    modules: {
                    toolbar: [
                        [{ 'header': 1}],
                        [{ 'header': 2}],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'color': [] }],
                        ['bold', 'italic', 'underline'],
                        ['clean']
                    ]
                }
                });
            } else {
                quillFase1.root.innerHTML = '';
            }
            // Forzar actualización
            quillFase1.update();
        } else {
            console.error('No se encontró el elemento #txt-editor-fase1');
        }
    }, 200);
    
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

function closeFase1AdminModal() {
    $('#fase1AdminModal').removeClass('show');
    if (quillFase1) {
        quillFase1.root.innerHTML = '';
    }
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
                    showToast('Datos enviados correctamente', 'success');
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
    if (quillFase1) {
        $('#respuesta_fase1').val(quillFase1.root.innerHTML);
    }
    
    // Validar que haya seleccionado un estado
    const estadoSeleccionado = $('#estado_fase1').val();
    if (!estadoSeleccionado) {
        $('#estado_fase1Error').text('Debe seleccionar un estado');
        return;
    }
    
    // Validar que el mensaje no esté vacío
    const mensaje = $('#respuesta_fase1').val();
    if (!mensaje || mensaje === '<p><br></p>') {
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
                    // Esperar 3 segundos (duración del toast) antes de recargar
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
                        if (errors.respuesta) $('#respuesta_fase1Error').text(errors.respuesta[0]);
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
        
        if (file) {
            const fileSizeMB = file.size / (1024 * 1024);
            if (fileSizeMB > 5) {
                showToast('El archivo no puede superar los 5MB', 'error');
                $(this).val('');
                return;
            }
            
            const fileExtension = file.name.split('.').pop().toLowerCase();
            if (!['doc', 'docx'].includes(fileExtension)) {
                showToast('Solo se permiten archivos Word (.doc, .docx)', 'error');
                $(this).val('');
                return;
            }
            
            fileList.append(`<li><i class="fa-regular fa-file-word text-blue-500 mr-2"></i>${file.name}</li>`);
        }
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