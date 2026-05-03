function openFase1EstudianteModal() {
    console.log('CLICK FUNCIONA');

    const modal = document.getElementById('fase1EstudianteModal');

    if (!modal) {
        console.log('NO EXISTE EL MODAL');
        return;
    }

    modal.classList.add('show');
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

// Abrir modal del estudiante
function openFase1EstudianteModal() {
    $('#doc_fdc126Error').text('');
    $('#doc_fdc126').val('');
    $('#nombre_empresa').val('');
    $('#es_institucional').prop('checked', false);
    $('#nombre_empresa_container').hide();
    $('#file-list-fase1').empty();
    $('#fase1EstudianteModal').removeClass('hidden').addClass('show');
}

function closeFase1EstudianteModal() {
    $('#fase1EstudianteModal').removeClass('show').addClass('hidden');
}

// Abrir modal de detalles
function openFase1DetailsModal() {
    const loadingSpinner = $('#loadingSpinner-fase1-details');
    if (loadingSpinner.length) loadingSpinner.removeClass('hidden');
    
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
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">
                        <p class="font-semibold text-gray-700 w-1/3">Fecha de envío:</p>
                        <span class="text-gray-800">${response.fecha_envio}</span>
                    </div>
            `;
            
            if (response.respuesta_comite) {
                html += `
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">
                        <p class="font-semibold text-gray-700 w-1/3">Respuesta del comité:</p>
                        <span class="text-gray-800">${escapeHtml(response.respuesta_comite)}</span>
                    </div>
                `;
            }
            
            html += `</div>`;
            $('#fase1DetailsContent').html(html);
            $('#fase1DetailsModal').removeClass('hidden').addClass('show');
        },
        error: function(xhr) {
            console.error(xhr);
            Swal.fire('Error', 'No se pudieron cargar los detalles', 'error');
        },
        complete: function() {
            if (loadingSpinner.length) loadingSpinner.addClass('hidden');
        }
    });
}

function closeFase1DetailsModal() {
    $('#fase1DetailsModal').removeClass('show').addClass('hidden');
}

// Abrir modal de administrador (responder)
function openFase1AdminModal() {
    $('#nro_acta_fase1').val('');
    $('#fecha_acta_fase1').val('');
    $('#estado_fase1').val('');
    $('#respuesta_fase1').val('');
    $('#nro_acta_fase1Error').text('');
    $('#fecha_acta_fase1Error').text('');
    $('#estado_fase1Error').text('');
    $('#respuesta_fase1Error').text('');
    $('#fase1AdminModal').removeClass('hidden').addClass('show');
}

function closeFase1AdminModal() {
    $('#fase1AdminModal').removeClass('show').addClass('hidden');
}

// Envío del formulario del estudiante
$(document).ready(function() {
    $('#fase1EstudianteForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const button = $(this).find('button[type="submit"]');
        const spinner = $('#loadingSpinner-fase1');
        
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
                } else {
                    Swal.fire('Error', xhr.responseJSON?.error || 'Error al enviar', 'error');
                }
            },
            complete: function() {
                button.prop('disabled', false);
                if (spinner.length) spinner.addClass('hidden');
            }
        });
    });
    
    // Envío del formulario del administrador
$('#fase1AdminForm').on('submit', function(e) {
    e.preventDefault();
    
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
            } else {
                Swal.fire('Error', xhr.responseJSON?.error || 'Error al enviar respuesta', 'error');
            }
        },
        complete: function() {
            button.prop('disabled', false);
            if (spinner.length) spinner.addClass('hidden');
        }
    });
});
    
    // Tooltips
    $('.tooltip-icon').on('mouseenter', function() {
        const tooltipId = $(this).data('tooltip');
        $('#' + tooltipId).removeClass('hidden');
    }).on('mouseleave', function() {
        const tooltipId = $(this).data('tooltip');
        $('#' + tooltipId).addClass('hidden');
    });
    
    // Vista previa del archivo
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

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}