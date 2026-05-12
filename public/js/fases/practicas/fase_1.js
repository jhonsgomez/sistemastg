function openFase1EstudianteModal() {

    const modal = document.getElementById('fase1EstudianteModal');



    modal.classList.add('show');
    toggleNombreEmpresa();
}

function closeFase1EstudianteModal() {
     TooltipManager.closeTooltips();
    $('#fase1EstudianteModal').removeClass('show');
}

// public/js/fases/practicas/fase_1.js

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
                            `<a href="/storage/${response.doc_fdc126}" target="_blank" class="text-uts-500 underline hover:text-uts-800">Ver archivo</a>` : 
                            '<span class="text-gray-500">No disponible</span>'}
                    </div>
                </div>
            `;
            
            $('#fase1DetailsContent').html(html);
            $('#fase1DetailsModal').addClass('show');
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
                        ['bold', 'italic', 'underline'],
                        [{ 'color': [] }],
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
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.remove('hidden');
        if (spinner) spinner.classList.add('hidden');
        btn.disabled = false;
    }
}

function closeFase1AdminModal() {
    $('#fase1AdminModal').removeClass('show');
    if (quillFase1) {
        quillFase1.root.innerHTML = '';
    }
}

$(document).ready(function() {
    // ========== ENVÍO DEL FORMULARIO DEL ESTUDIANTE CON CONFIRMACIÓN ==========
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
                        Swal.fire('Éxito', 'Documentos enviados correctamente', 'success').then(() => {
                            location.reload();
                        });
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
                            Swal.fire('Error', xhr.responseJSON?.error || 'Error al enviar', 'error');
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
    
    // ========== RESPUESTA DEL COMITÉ (ADMIN) CON CONFIRMACIÓN ==========
$('#fase1AdminForm').on('submit', function(e) {
    e.preventDefault();
    
    // Obtener el contenido del editor Quill y asignarlo a ambos campos
    if (quillFase1) {
        const contenido = quillFase1.root.innerHTML;
        $('#respuesta_fase1').val(contenido);
        // También crear un campo respuesta si el backend lo espera
        if ($('#respuesta').length === 0) {
            $('<input>').attr({
                type: 'hidden',
                id: 'respuesta',
                name: 'respuesta'
            }).appendTo('#fase1AdminForm');
        }
        $('#respuesta').val(contenido);
    }
    
    // Verificar si el campo está vacío
    const contenidoRespuesta = quillFase1 ? quillFase1.root.innerHTML.trim() : '';
    if (!contenidoRespuesta || contenidoRespuesta === '<p><br></p>') {
        $('#respuesta_fase1Error').text('El campo respuesta es obligatorio');
        return;
    }
    
    // Obtener el estado seleccionado para personalizar el mensaje
    const estadoSeleccionado = $('#estado_fase1').val();
    let mensajeConfirmacion = "Esta acción no se puede deshacer";
    
    if (estadoSeleccionado === 'Aprobada') {
        mensajeConfirmacion = "Al aprobar, la solicitud pasará a Fase 2. Esta acción no se puede deshacer.";
    } else if (estadoSeleccionado === 'Rechazada') {
        mensajeConfirmacion = "Al rechazar, el estudiante deberá volver a enviar los documentos. Esta acción no se puede deshacer.";
    }
    
    // Validar que haya seleccionado un estado
    if (!estadoSeleccionado) {
        $('#estado_fase1Error').text('Debe seleccionar un estado');
        return;
    }
    
    Swal.fire({
        heightAuto: false,
        title: '¿Está seguro?',
        text: mensajeConfirmacion,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C1D631',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, responder',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Volver a asegurar el contenido antes de enviar
            if (quillFase1) {
                const contenido = quillFase1.root.innerHTML;
                $('#respuesta_fase1').val(contenido);
                $('#respuesta').val(contenido);
            }
            
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
                    
                    if (response.nuevo_estado === 'Fase 2') {
                        Swal.fire('Éxito', 'Solicitud aprobada. Pasando a Fase 2...', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Información', 'Solicitud rechazada. El estudiante podrá volver a enviar los documentos.', 'warning').then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.nro_acta) $('#nro_acta_fase1Error').text(errors.nro_acta[0]);
                        if (errors.fecha_acta) $('#fecha_acta_fase1Error').text(errors.fecha_acta[0]);
                        if (errors.estado) $('#estado_fase1Error').text(errors.estado[0]);
                        if (errors.respuesta) $('#respuesta_fase1Error').text(errors.respuesta[0]);
                        if (errors.respuesta_fase1) $('#respuesta_fase1Error').text(errors.respuesta_fase1[0]);
                    } else {
                        Swal.fire('Error', xhr.responseJSON?.error || 'Error al enviar respuesta', 'error');
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
                Swal.fire('Error', 'El archivo no puede superar los 5MB', 'error');
                $(this).val('');
                return;
            }
            
            const fileExtension = file.name.split('.').pop().toLowerCase();
            if (!['doc', 'docx'].includes(fileExtension)) {
                Swal.fire('Error', 'Solo se permiten archivos Word (.doc, .docx)', 'error');
                $(this).val('');
                return;
            }
            
            fileList.append(`<li><i class="fa-regular fa-file-word text-blue-500 mr-2"></i>${file.name}</li>`);
        }
    });
});

// ========== FUNCIONES EXISTENTES (sin cambios) ==========

function toggleNombreEmpresa() {
    const esInstitucional = $('#es_institucional').is(':checked');
    if (esInstitucional) {
        $('#nombre_empresa_container').slideUp();
        $('#nombre_empresa').val('');
    } else {
        $('#nombre_empresa_container').slideDown();
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}