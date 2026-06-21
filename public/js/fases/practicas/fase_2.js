// ==================== FASE 2 - PRÁCTICAS EMPRESARIALES ====================

// Variables globales
let quillFase2 = null;

// ==================== MODAL ESTUDIANTE ====================

function openFase2EstudianteModal(btn) {
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }

    

    $('#liquidacion_pago').val('');
    $('#soporte_pago').val('');

    $('#liquidacion_pagoError').text('');
    $('#soporte_pagoError').text('');
    
    $('#fase2EstudianteTitle').html(`Prácticas <span class="bg-uts-500 text-white px-3 py-1 rounded uppercase shadow-md text-xl">Fase 2</span>`);
    $('#fase2EstudianteModal').addClass('show');
    
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

// ==================== MODAL DETALLES ====================

function openFase2DetailsModal(btn) {
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }
    
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
                    <p class="font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">
                        Liquidación de pago:
                    </p>

                    ${response.liquidacion_url
                        ? `
                            <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">
                                <i class="fa-regular fa-file-pdf text-red-600 mr-2"></i>
                                <a href="${response.liquidacion_url}" target="_blank"
                                    class="text-red-600 underline hover:text-red-800">
                                    Ver Liquidación
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

                <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">
                    <p class="font-semibold text-gray-700 w-1/3 min-w-[100px] mb-2 sm:mb-0">
                        Soporte de pago:
                    </p>

                    ${response.soporte_url
                        ? `
                            <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">
                                <i class="fa-regular fa-file-pdf text-red-600 mr-2"></i>
                                <a href="${response.soporte_url}" target="_blank"
                                    class="text-red-600 underline hover:text-red-800">
                                    Ver Soporte de pago
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
            $('#fase2DetailsContent').html(html);
            $('#fase2DetailsModal').addClass('show');
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

function closeFase2DetailsModal() {
    $('#fase2DetailsModal').removeClass('show');
}

// ==================== MODAL ADMINISTRADOR ====================

function openFase2AdminModal(btn) {
    // Mostrar spinner y ocultar icono en el botón
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }

    setTimeout(() => {

    // Director
    if (!$('#director_id_fase2').hasClass("select2-hidden-accessible")) {
        $('#director_id_fase2').select2({
            dropdownParent: $('#fase2AdminModal'),
            placeholder: 'Seleccione un director para la práctica',
            allowClear: true,
            width: '100%',
            minimumInputLength: 5
        });
    }

    // Evaluador
    if (!$('#evaluador_id_fase2').hasClass("select2-hidden-accessible")) {
        $('#evaluador_id_fase2').select2({
            dropdownParent: $('#fase2AdminModal'),
            placeholder: 'Seleccione un evaluador para la práctica',
            allowClear: true,
            width: '100%',
            minimumInputLength: 5
        });
    }

    // Codirector
    if (!$('#codirector_id_fase2').hasClass("select2-hidden-accessible")) {
        $('#codirector_id_fase2').select2({
            dropdownParent: $('#fase2AdminModal'),
            placeholder: 'Seleccione un codirector para la práctica',
            allowClear: true,
            width: '100%',
            minimumInputLength: 5
        });
    }

}, 100);
    
    // Ocultar contenedores inicialmente
    $('#container_docentes_fase2').addClass('hidden');
    $('#container_codigo_modalidad_fase2').addClass('hidden');
    
    // Abrir el modal
    $('#fase2AdminModal').addClass('show');
    
    // Inicializar Quill
    setTimeout(function() {
        if ($('#txt-editor-fase2').length > 0) {
            if (quillFase2 === null) {
                quillFase2 = new Quill('#txt-editor-fase2', {
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
                            image: imageHandlerFase2
                        }
                    }
                }
            });
            } else {
                quillFase2.root.innerHTML = '';
            }
            quillFase2.update();
        }
    }, 200);
    
    // Restaurar el botón
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

function imageHandlerFase2() {
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

            const range = quillFase2.getSelection(true);

            quillFase2.insertEmbed(range.index, 'image', data.url);
            quillFase2.setSelection(range.index + 1);

        } catch (error) {
            console.error('ERROR REAL FASE 2:', error);

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

function closeFase2AdminModal() {
    $('#fase2AdminModal').removeClass('show');
    if (quillFase2) {
        quillFase2.root.innerHTML = '';
    }

    // Limpiar campos
    $('#nro_acta_fase2').val('');
    $('#fecha_acta_fase2').val('');
    $('#estado_fase2').val('');
    $('#respuesta_fase2').val('');
    $('#director_id_fase2').val('');
    $('#evaluador_id_fase2').val('');
    $('#codirector_id_fase2').val('');
    // NO limpiar el código de modalidad porque ya viene de la vista
    $('#nro_acta_fase2Error').text('');
    $('#fecha_acta_fase2Error').text('');
    $('#estado_fase2Error').text('');
    $('#respuesta_fase2Error').text('');
    $('#director_id_fase2Error').text('');
    $('#evaluador_id_fase2Error').text('');
    $('#codirector_id_fase2Error').text('');

    $('#director_id_fase2').val(null).trigger('change');
    $('#evaluador_id_fase2').val(null).trigger('change');
    $('#codirector_id_fase2').val(null).trigger('change');

    
}

// ==================== EVENTOS Y FORMULARIOS ====================

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
    
    // Tooltips
    $('.tooltip-icon').on('mouseenter', function() {
        const tooltipId = $(this).data('tooltip');
        $('#' + tooltipId).removeClass('hidden');
    }).on('mouseleave', function() {
        const tooltipId = $(this).data('tooltip');
        $('#' + tooltipId).addClass('hidden');
    });
    
    // ==================== LIQUIDACIÓN ====================

$('#liquidacion_pago').on('change', function (e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-liquidacion');

    fileList.empty();

    if (!file) return;

    const maxSizeBytes = MAX_FILE_SIZE_MB * 1024 * 1024;

    if (file.size > maxSizeBytes) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo demasiado grande',
            text: `El archivo no puede superar los ${MAX_FILE_SIZE_MB} MB.`,
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
        <div class="text-gray-600 text-sm mb-1">
            ${escapeHtml(file.name)}
        </div>
        <div class="text-sm text-gray-600">
            Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_MB} MB permitidos
        </div>
    </li>
`);
});
    
    // ==================== SOPORTE ====================

$('#soporte_pago').on('change', function (e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-soporte');

    fileList.empty();

    if (!file) return;

    const maxSizeBytes = MAX_FILE_SIZE_MB * 1024 * 1024;

    if (file.size > maxSizeBytes) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo demasiado grande',
            text: `El archivo no puede superar los ${MAX_FILE_SIZE_MB} MB.`,
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
        <div class="text-gray-600 text-sm mb-1">
            ${escapeHtml(file.name)}
        </div>
        <div class="text-sm text-gray-600">
            Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_MB} MB permitidos
        </div>
    </li>
`);
});
    
    // Mostrar/ocultar docentes y código de modalidad según estado
$(document).ready(function() {
    $('#estado_fase2').on('change', function() {
        if ($(this).val() === 'Aprobada') {
            // Mostrar contenedores
            $('#container_docentes_fase2').removeClass('hidden');
            $('#container_codigo_modalidad_fase2').removeClass('hidden');
            
            // Limpiar campos anteriores de docentes
            $('#director_id_fase2').val('');
            $('#evaluador_id_fase2').val('');
            $('#codirector_id_fase2').val('');
            
            // El código ya está en el input desde el servidor, no necesita AJAX
        } else {
            // Ocultar contenedores y limpiar campos
            $('#container_docentes_fase2').addClass('hidden');
            $('#container_codigo_modalidad_fase2').addClass('hidden');
            $('#director_id_fase2').val('');
            $('#evaluador_id_fase2').val('');
            $('#codirector_id_fase2').val('');
            // No limpiar el código porque se oculta
        }
    });
});
    
    // ========== ENVÍO FORMULARIO ESTUDIANTE FASE 2 ==========
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
                        showToast('Datos enviados correctamente. Recibirá respuesta en los próximos 5 días hábiles.', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            if (errors.liquidacion_pago) $('#liquidacion_pagoError').text(errors.liquidacion_pago[0]);
                            if (errors.soporte_pago) $('#soporte_pagoError').text(errors.soporte_pago[0]);
                            setTimeout(() => {
                                $('#liquidacion_pagoError').text('');
                                $('#soporte_pagoError').text('');
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
    
    // ========== RESPUESTA DEL COMITÉ (ADMIN) FASE 2 ==========
    $('#fase2AdminForm').on('submit', function(e) {
        e.preventDefault();
        if (!quillFase2) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El editor no se ha cargado correctamente.',
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        $('#respuesta_fase2').val(quillFase2.root.innerHTML);
        
        const estadoSeleccionado = $('#estado_fase2').val();
        if (!estadoSeleccionado) {
            $('#estado_fase2Error').text('Debe seleccionar un estado');
            return;
        }
        
        const mensajeLimpio = quillFase2.getText().trim();

        if (!mensajeLimpio) {
            $('#respuesta_fase2Error').text('Debe ingresar un mensaje de respuesta');
            return;
        }
        
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
                        showToast('Respuesta enviada correctamente', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
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