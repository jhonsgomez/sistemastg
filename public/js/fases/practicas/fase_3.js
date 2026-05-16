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
    $('#fase3EstudianteTitle').html(`Prácticas empresariales: <span class="bg-uts-500 text-white px-3 py-1 rounded uppercase shadow-md text-xl">Fase 3</span>`);
    
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
    console.log('cerrar');
    $('#fase3EstudianteModal').removeClass('show');
}

// ==================== TOOLTIPS PARA FASE 3 ====================
$(document).ready(function() {
    // Tooltips para Fase 3
    $('.tooltip-icon').on('mouseenter', function() {
        const tooltipId = $(this).data('tooltip');
        $('#' + tooltipId).removeClass('hidden');
    }).on('mouseleave', function() {
        const tooltipId = $(this).data('tooltip');
        $('#' + tooltipId).addClass('hidden');
    });
    
    // Vista previa de archivos para ARL
    $('#arl').on('change', function(e) {
        const file = e.target.files[0];
        const fileList = $('#file-list-arl');

        fileList.empty();

        if (file) {

            const fileSizeMB = file.size / (1024 * 1024);

            if (fileSizeMB > 5) {
                Swal.fire('Error', 'El archivo no puede superar los 5MB', 'error');
                $(this).val('');
                return;
            }

            const extension = file.name.split('.').pop().toLowerCase();

            if (extension !== 'pdf') {
                Swal.fire('Error', 'El ARL debe ser PDF', 'error');
                $(this).val('');
                return;
            }

            fileList.append(`
                <li>
                    <i class="fa-regular fa-file-pdf text-red-500 mr-2"></i>
                    ${file.name}
                </li>
            `);
        }
    });
    
    // Vista previa de archivos para fdc-127
    $('#doc_fdc127').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc127');

    fileList.empty();

    if (file) {

        const fileSizeMB = file.size / (1024 * 1024);

        if (fileSizeMB > 5) {
            Swal.fire('Error', 'El archivo no puede superar los 5MB', 'error');
            $(this).val('');
            return;
        }

        const extension = file.name.split('.').pop().toLowerCase();

        if (!['doc', 'docx'].includes(extension)) {
            Swal.fire('Error', 'El documento debe ser WORD', 'error');
            $(this).val('');
            return;
        }

        fileList.append(`
            <li>
                <i class="fa-regular fa-file-word text-blue-500 mr-2"></i>
                ${file.name}
            </li>
        `);
    }
});

$('#doc_fdc195').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc195');

    fileList.empty();

    if (file) {

        const fileSizeMB = file.size / (1024 * 1024);

        if (fileSizeMB > 5) {
            Swal.fire('Error', 'El archivo no puede superar los 5MB', 'error');
            $(this).val('');
            return;
        }

        const extension = file.name.split('.').pop().toLowerCase();

        if (!['doc', 'docx'].includes(extension)) {
            Swal.fire('Error', 'El documento debe ser WORD', 'error');
            $(this).val('');
            return;
        }

        fileList.append(`
            <li>
                <i class="fa-regular fa-file-word text-blue-500 mr-2"></i>
                ${file.name}
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
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">
                        <p class="font-semibold text-gray-700 w-1/3">ARL:</p>

                        ${response.arl_url
                            ? `<a href="${response.arl_url}" target="_blank"
                                class="text-uts-500 underline hover:text-uts-800">
                                Ver ARL
                            </a>`
                            : '<span class="text-gray-500">No disponible</span>'
                        }
                    </div>

                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">
                        <p class="font-semibold text-gray-700 w-1/3">
                            Formato F-DC-127:
                        </p>
                
                        ${response.doc_fdc127_url
                            ? `<a href="${response.doc_fdc127_url}" target="_blank"
                                class="text-uts-500 underline hover:text-uts-800">
                                Ver documento
                            </a>`
                            : '<span class="text-gray-500">No disponible</span>'
                        }
                       
                    </div>

                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">
                        <p class="font-semibold text-gray-700 w-1/3">
                            Formato F-DC-195:
                        </p>

                        ${response.doc_fdc195_url
                            ? `<a href="${response.doc_fdc195_url}" target="_blank"
                                class="text-uts-500 underline hover:text-uts-800">
                                Ver documento
                            </a>`
                            : '<span class="text-gray-500">No disponible</span>'
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

// ==================== MODAL ADMINISTRADOR ====================

// ==================== MODAL ADMINISTRADOR FASE 2 ====================
/*
let quillFase3 = null;

function openFase3AdminModal(btn) {
    // Mostrar spinner y ocultar icono en el botón
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }
    
    // Limpiar campos
    $('#nro_acta_fase3').val('');
    $('#fecha_acta_fase3').val('');
    $('#estado_fase3').val('');
    $('#respuesta_fase3').val('');
    $('#director_id_fase3').val('');
    $('#evaluador_id_fase3').val('');
    $('#codirector_id_fase3').val('');
    $('#nro_acta_fase3Error').text('');
    $('#fecha_acta_fase3Error').text('');
    $('#estado_fase3Error').text('');
    $('#respuesta_fase3Error').text('');
    $('#director_id_fase3Error').text('');
    $('#evaluador_id_fase3Error').text('');
    $('#codirector_id_fase3Error').text('');
    
    // Ocultar contenedor de docentes inicialmente
    $('#container_docentes_fase3').addClass('hidden');
    
    // PRIMERO abrir el modal
    $('#fase3AdminModal').addClass('show');
    
    // ESPERAR a que el modal esté visible y luego inicializar Quill
    setTimeout(function() {
        // Verificar si el elemento existe
        if ($('#txt-editor-fase3').length > 0) {
            if (quillFase3 === null) {
                quillFase3 = new Quill('#txt-editor-fase3', {
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
                quillFase3.root.innerHTML = '';
            }
            quillFase3.update();
        } else {
            console.error('No se encontró el elemento #txt-editor-fase3');
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

function closeFase3AdminModal() {
    $('#fase3AdminModal').removeClass('show');
    if (quillFase3) {
        quillFase3.root.innerHTML = '';
    }
}

// Mostrar/ocultar contenedor de docentes según el estado seleccionado
$(document).ready(function() {
    $('#estado_fase3').on('change', function() {
        if ($(this).val() === 'Aprobada') {
            $('#container_docentes_fase3').removeClass('hidden');
        } else {
            $('#container_docentes_fase3').addClass('hidden');
            // Limpiar selecciones de docentes cuando se oculta
            $('#director_id_fase3').val('');
            $('#evaluador_id_fase3').val('');
            $('#codirector_id_fase3').val('');
        }
    });
});

// Manejo del formulario de administrador Fase 2
$('#fase3AdminForm').on('submit', function(e) {
    e.preventDefault();
    
    // Obtener el contenido del editor Quill
    if (quillFase3) {
        $('#respuesta_fase3').val(quillFase3.root.innerHTML);
    }
    
    // Validar que haya seleccionado un estado
    const estadoSeleccionado = $('#estado_fase3').val();
    if (!estadoSeleccionado) {
        $('#estado_fase3Error').text('Debe seleccionar un estado');
        return;
    }
    
    // Si está aprobado, validar que haya seleccionado director y evaluador
    if (estadoSeleccionado === 'Aprobada') {
        if (!$('#director_id_fase3').val()) {
            $('#director_id_fase3Error').text('Debe seleccionar un director');
            return;
        }
        if (!$('#evaluador_id_fase3').val()) {
            $('#evaluador_id_fase3Error').text('Debe seleccionar un evaluador');
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
            const spinner = $('#loadingSpinner-fase3-admin');
            const formData = $(this).serialize();
            
            button.prop('disabled', true);
            if (spinner.length) spinner.removeClass('hidden');
            
            $.ajax({
                url: ROUTES.fase3_reply,
                method: 'POST',
                data: formData,
                success: function(response) {
                    closeFase3AdminModal();
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
                        if (errors.nro_acta) $('#nro_acta_fase3Error').text(errors.nro_acta[0]);
                        if (errors.fecha_acta) $('#fecha_acta_fase3Error').text(errors.fecha_acta[0]);
                        if (errors.estado) $('#estado_fase3Error').text(errors.estado[0]);
                        if (errors.respuesta) $('#respuesta_fase3Error').text(errors.respuesta[0]);
                        if (errors.director_id) $('#director_id_fase3Error').text(errors.director_id[0]);
                        if (errors.evaluador_id) $('#evaluador_id_fase3Error').text(errors.evaluador_id[0]);
                        if (errors.codirector_id) $('#codirector_id_fase3Error').text(errors.codirector_id[0]);
                        
                        setTimeout(() => {
                            $('#nro_acta_fase3Error, #fecha_acta_fase3Error, #estado_fase3Error, #respuesta_fase3Error, #director_id_fase3Error, #evaluador_id_fase3Error, #codirector_id_fase3Error').text('');
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

function clearFase3EstudianteErrors() {
    const errorFields = ['arl', 'doc_fdc127', 'doc_fdc195'];
    errorFields.forEach(field => {
        const errorSpan = document.getElementById(`${field}Error`);
        if (errorSpan) errorSpan.innerHTML = '';
    });
}

function clearFase3AdminErrors() {
    const errorFields = ['nro_acta_fase3', 'fecha_acta_fase3', 'estado_fase3', 'director_id_fase3', 'evaluador_id_fase3', 'respuesta_fase3'];
    errorFields.forEach(field => {
        const errorSpan = document.getElementById(`${field}Error`);
        if (errorSpan) errorSpan.innerHTML = '';
    });
}

// ==================== EVENT LISTENERS ====================

// Mostrar/ocultar campos de docentes según el estado seleccionado
document.addEventListener('DOMContentLoaded', function() {
    const estadoSelect = document.getElementById('estado_fase3');
    if (estadoSelect) {
        estadoSelect.addEventListener('change', function() {
            const container = document.getElementById('container_docentes_fase3');
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
    

});*/