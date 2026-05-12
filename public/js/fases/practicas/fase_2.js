// ==================== FASE 2 - PRÁCTICAS EMPRESARIALES ====================

// Variables globales
let currentFase2DetailsButton = null;

// ==================== MODAL ESTUDIANTE ====================

// ==================== FUNCIONES FASE 2 ESTUDIANTE ====================

function openFase2EstudianteModal(btn) {
    // Mostrar spinner y ocultar icono en el botón
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }
    
    // Limpiar campos del formulario
    $('#liquidacion_pago').val('');
    $('#soporte_pago').val('');
    $('#file-list-liquidacion').empty();
    $('#file-list-soporte').empty();
    $('#liquidacion_pagoError').text('');
    $('#soporte_pagoError').text('');
    
    // Cambiar el título si es necesario
    $('#fase2EstudianteTitle').html(`Prácticas empresariales: <span class="bg-uts-500 text-white px-3 py-1 rounded uppercase shadow-md text-xl">Fase 2</span>`);
    
    // Abrir el modal con animación
    $('#fase2EstudianteModal').addClass('show');
    
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

function closeFase2EstudianteModal() {
    $('#fase2EstudianteModal').removeClass('show');
}

// ==================== TOOLTIPS PARA FASE 2 ====================
$(document).ready(function() {
    // Tooltips para Fase 2
    $('.tooltip-icon').on('mouseenter', function() {
        const tooltipId = $(this).data('tooltip');
        $('#' + tooltipId).removeClass('hidden');
    }).on('mouseleave', function() {
        const tooltipId = $(this).data('tooltip');
        $('#' + tooltipId).addClass('hidden');
    });
    
    // Vista previa de archivos para Liquidación
    $('#liquidacion_pago').on('change', function(e) {
        const file = e.target.files[0];
        const fileList = $('#file-list-liquidacion');
        fileList.empty();
        
        if (file) {
            const fileSizeMB = file.size / (1024 * 1024);
            if (fileSizeMB > 5) {
                Swal.fire('Error', 'El archivo no puede superar los 5MB', 'error');
                $(this).val('');
                return;
            }
            
            const fileExtension = file.name.split('.').pop().toLowerCase();
            if (fileExtension !== 'pdf') {
                Swal.fire('Error', 'Solo se permiten archivos PDF', 'error');
                $(this).val('');
                return;
            }
            
            fileList.append(`<li><i class="fa-regular fa-file-pdf text-red-500 mr-2"></i>${file.name}</li>`);
        }
    });
    
    // Vista previa de archivos para Soporte
    $('#soporte_pago').on('change', function(e) {
        const file = e.target.files[0];
        const fileList = $('#file-list-soporte');
        fileList.empty();
        
        if (file) {
            const fileSizeMB = file.size / (1024 * 1024);
            if (fileSizeMB > 5) {
                Swal.fire('Error', 'El archivo no puede superar los 5MB', 'error');
                $(this).val('');
                return;
            }
            
            const fileExtension = file.name.split('.').pop().toLowerCase();
            if (fileExtension !== 'pdf') {
                Swal.fire('Error', 'Solo se permiten archivos PDF', 'error');
                $(this).val('');
                return;
            }
            
            fileList.append(`<li><i class="fa-regular fa-file-pdf text-red-500 mr-2"></i>${file.name}</li>`);
        }
    });
    
    // ========== ENVÍO DEL FORMULARIO DEL ESTUDIANTE CON CONFIRMACIÓN ==========
    $('#fase2EstudianteForm').on('submit', function(e) {
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
                const button = $('#fase2EstudianteForm').find('button[type="submit"]');
                const spinner = $('#loadingSpinner-fase2');
                const formData = new FormData(this);
                
                button.prop('disabled', true);
                if (spinner.length) spinner.removeClass('hidden');
                
                $.ajax({
                    url: ROUTES.fase2_store,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        closeFase2EstudianteModal();
                        Swal.fire({
                            title: '¡Éxito!',
                            text: 'Documentos enviados correctamente. Tendrá respuesta en los próximos 5 días hábiles.',
                            icon: 'success',
                            confirmButtonText: 'Ok',
                            confirmButtonColor: '#C1D631'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            if (errors.liquidacion_pago) $('#liquidacion_pagoError').text(errors.liquidacion_pago[0]);
                            if (errors.soporte_pago) $('#soporte_pagoError').text(errors.soporte_pago[0]);
                            // Limpiar errores después de 5 segundos
                            setTimeout(() => {
                                $('#liquidacion_pagoError').text('');
                                $('#soporte_pagoError').text('');
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
});

// Abrir modal de detalles con spinner en el botón (exactamente como Fase 1)
function openFase2DetailsModal(btn) {
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
        url: ROUTES.fase2_details,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            practica_id: $('input[name="practica_id"]').first().val()
        },
        success: function(response) {
            let html = `
                <div class="flex flex-col space-y-3">
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">
                        <p class="font-semibold text-gray-700 w-1/3">Liquidación de pago:</p>
                        ${response.liquidacion_url ? 
                            `<a href="${response.liquidacion_url}" target="_blank" class="text-uts-500 underline hover:text-uts-800">Ver Liquidación</a>` : 
                            '<span class="text-gray-500">No disponible</span>'}
                    </div>
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">
                        <p class="font-semibold text-gray-700 w-1/3">Soporte de pago:</p>
                        ${response.soporte_url ? 
                            `<a href="${response.soporte_url}" target="_blank" class="text-uts-500 underline hover:text-uts-800">Ver Soporte de pago</a>` : 
                            '<span class="text-gray-500">No disponible</span>'}
                    </div>
                </div>
            `;
            
            $('#fase2DetailsContent').html(html);
            $('#fase2DetailsModal').addClass('show');
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
function closeFase2DetailsModal() {
    $('#fase2DetailsModal').removeClass('show');
}

// ==================== MODAL ADMINISTRADOR ====================

// ==================== MODAL ADMINISTRADOR FASE 2 ====================

let quillFase2 = null;

function openFase2AdminModal(btn) {
    // Mostrar spinner y ocultar icono en el botón
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }
    
    // Limpiar campos
    $('#nro_acta_fase2').val('');
    $('#fecha_acta_fase2').val('');
    $('#estado_fase2').val('');
    $('#respuesta_fase2').val('');
    $('#director_id_fase2').val('');
    $('#evaluador_id_fase2').val('');
    $('#codirector_id_fase2').val('');
    $('#nro_acta_fase2Error').text('');
    $('#fecha_acta_fase2Error').text('');
    $('#estado_fase2Error').text('');
    $('#respuesta_fase2Error').text('');
    $('#director_id_fase2Error').text('');
    $('#evaluador_id_fase2Error').text('');
    $('#codirector_id_fase2Error').text('');
    
    // Ocultar contenedor de docentes inicialmente
    $('#container_docentes_fase2').addClass('hidden');
    
    // PRIMERO abrir el modal
    $('#fase2AdminModal').addClass('show');
    
    // ESPERAR a que el modal esté visible y luego inicializar Quill
    setTimeout(function() {
        // Verificar si el elemento existe
        if ($('#txt-editor-fase2').length > 0) {
            if (quillFase2 === null) {
                quillFase2 = new Quill('#txt-editor-fase2', {
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
                quillFase2.root.innerHTML = '';
            }
            quillFase2.update();
        } else {
            console.error('No se encontró el elemento #txt-editor-fase2');
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

function closeFase2AdminModal() {
    $('#fase2AdminModal').removeClass('show');
    if (quillFase2) {
        quillFase2.root.innerHTML = '';
    }
}

// Mostrar/ocultar contenedor de docentes según el estado seleccionado
$(document).ready(function() {
    $('#estado_fase2').on('change', function() {
        if ($(this).val() === 'Aprobada') {
            $('#container_docentes_fase2').removeClass('hidden');
        } else {
            $('#container_docentes_fase2').addClass('hidden');
            // Limpiar selecciones de docentes cuando se oculta
            $('#director_id_fase2').val('');
            $('#evaluador_id_fase2').val('');
            $('#codirector_id_fase2').val('');
        }
    });
});

// Manejo del formulario de administrador Fase 2
$('#fase2AdminForm').on('submit', function(e) {
    e.preventDefault();
    
    // Obtener el contenido del editor Quill
    if (quillFase2) {
        $('#respuesta_fase2').val(quillFase2.root.innerHTML);
    }
    
    // Validar que haya seleccionado un estado
    const estadoSeleccionado = $('#estado_fase2').val();
    if (!estadoSeleccionado) {
        $('#estado_fase2Error').text('Debe seleccionar un estado');
        return;
    }
    
    // Si está aprobado, validar que haya seleccionado director y evaluador
    if (estadoSeleccionado === 'Aprobada') {
        if (!$('#director_id_fase2').val()) {
            $('#director_id_fase2Error').text('Debe seleccionar un director');
            return;
        }
        if (!$('#evaluador_id_fase2').val()) {
            $('#evaluador_id_fase2Error').text('Debe seleccionar un evaluador');
            return;
        }
    }
    
    let mensajeConfirmacion = "Esta acción no se puede deshacer";
    if (estadoSeleccionado === 'Aprobada') {
        mensajeConfirmacion = "Al aprobar, se asignarán los docentes seleccionados. Esta acción no se puede deshacer.";
    } else if (estadoSeleccionado === 'Rechazada') {
        mensajeConfirmacion = "Esta acción no se puede deshacer.";
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
            const button = $(this).find('button[type="submit"]');
            const spinner = $('#loadingSpinner-fase2-admin');
            const formData = $(this).serialize();
            
            button.prop('disabled', true);
            if (spinner.length) spinner.removeClass('hidden');
            
            $.ajax({
                url: ROUTES.fase2_reply,
                method: 'POST',
                data: formData,
                success: function(response) {
                    closeFase2AdminModal();
                    Swal.fire({
                        title: '¡Éxito!',
                        text: response.success || 'Respuesta enviada exitosamente',
                        icon: 'success',
                        confirmButtonText: 'Ok',
                        confirmButtonColor: '#C1D631'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.nro_acta) $('#nro_acta_fase2Error').text(errors.nro_acta[0]);
                        if (errors.fecha_acta) $('#fecha_acta_fase2Error').text(errors.fecha_acta[0]);
                        if (errors.estado) $('#estado_fase2Error').text(errors.estado[0]);
                        if (errors.respuesta) $('#respuesta_fase2Error').text(errors.respuesta[0]);
                        if (errors.director_id) $('#director_id_fase2Error').text(errors.director_id[0]);
                        if (errors.evaluador_id) $('#evaluador_id_fase2Error').text(errors.evaluador_id[0]);
                        if (errors.codirector_id) $('#codirector_id_fase2Error').text(errors.codirector_id[0]);
                        
                        setTimeout(() => {
                            $('#nro_acta_fase2Error, #fecha_acta_fase2Error, #estado_fase2Error, #respuesta_fase2Error, #director_id_fase2Error, #evaluador_id_fase2Error, #codirector_id_fase2Error').text('');
                        }, 5000);
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

// ==================== FUNCIONES DE LIMPIEZA ====================

function clearFase2EstudianteErrors() {
    const errorFields = ['liquidacion_pago', 'soporte_pago'];
    errorFields.forEach(field => {
        const errorSpan = document.getElementById(`${field}Error`);
        if (errorSpan) errorSpan.innerHTML = '';
    });
}

function clearFase2AdminErrors() {
    const errorFields = ['nro_acta_fase2', 'fecha_acta_fase2', 'estado_fase2', 'director_id_fase2', 'evaluador_id_fase2', 'respuesta_fase2'];
    errorFields.forEach(field => {
        const errorSpan = document.getElementById(`${field}Error`);
        if (errorSpan) errorSpan.innerHTML = '';
    });
}

// ==================== EVENT LISTENERS ====================

// Mostrar/ocultar campos de docentes según el estado seleccionado
document.addEventListener('DOMContentLoaded', function() {
    const estadoSelect = document.getElementById('estado_fase2');
    if (estadoSelect) {
        estadoSelect.addEventListener('change', function() {
            const container = document.getElementById('container_docentes_fase2');
            if (this.value === 'Aprobada') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        });
    }
    
    // Tooltips
    document.querySelectorAll('.tooltip-icon').forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            const tooltipId = this.getAttribute('data-tooltip');
            const tooltip = document.getElementById(tooltipId);
            if (tooltip) tooltip.classList.remove('hidden');
        });
        
        icon.addEventListener('mouseleave', function() {
            const tooltipId = this.getAttribute('data-tooltip');
            const tooltip = document.getElementById(tooltipId);
            if (tooltip) tooltip.classList.add('hidden');
        });
    });
    
    // Mostrar nombre de archivo seleccionado
    const liquidacionInput = document.getElementById('liquidacion_pago');
    if (liquidacionInput) {
        liquidacionInput.addEventListener('change', function() {
            const fileList = document.getElementById('file-list-liquidacion');
            if (fileList) {
                fileList.innerHTML = '';
                if (this.files.length > 0) {
                    const li = document.createElement('li');
                    li.innerHTML = `<i class="fa-regular fa-file-pdf text-red-500 mr-2"></i>${this.files[0].name}`;
                    fileList.appendChild(li);
                }
            }
        });
    }
    
    const soporteInput = document.getElementById('soporte_pago');
    if (soporteInput) {
        soporteInput.addEventListener('change', function() {
            const fileList = document.getElementById('file-list-soporte');
            if (fileList) {
                fileList.innerHTML = '';
                if (this.files.length > 0) {
                    const li = document.createElement('li');
                    li.innerHTML = `<i class="fa-regular fa-file-pdf text-red-500 mr-2"></i>${this.files[0].name}`;
                    fileList.appendChild(li);
                }
            }
        });
    }
});