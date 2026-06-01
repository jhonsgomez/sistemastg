// ==================== MODAL EVALUADOR FASE 6 ====================

let quillFase6Evaluador = null;

function openFase6EvaluadorModal(btn) {

    console.log("modal fase 6");

    // ================= SPINNER BOTÓN =================
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }

    // ================= LIMPIAR CAMPOS =================
    $('#estado_fase6').val('');
    $('#nro_acta_fase6').val('');
    $('#fecha_acta_fase6').val('');
    $('#respuesta_fase6').val('');
    $('#fdc128_fase6').val('');
    $('#fdc129_fase6').val('');

    // ================= LIMPIAR ERRORES =================
    $('#estado_fase6Error').text('');
    $('#nro_acta_fase6Error').text('');
    $('#fecha_acta_fase6Error').text('');
    $('#respuesta_fase6Error').text('');
    $('#fdc128_fase6Error').text('');
    $('#fdc129_fase6Error').text('');

    // ================= LIMPIAR LISTAS =================
    $('#file-list-fdc128-fase6').html('');
    $('#file-list-fdc129-fase6').html('');

    // ================= ABRIR MODAL =================
    $('#fase6EvaluadorModal').addClass('show');

    // ================= QUILL =================
    setTimeout(function () {
        if ($('#txt-editor-fase6-evaluador').length > 0) {
            if (quillFase6Evaluador === null) {
                quillFase6Evaluador = new Quill('#txt-editor-fase6-evaluador', {
                    theme: 'snow',
                    placeholder: 'Ingrese comentarios de respuesta...',
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
                quillFase6Evaluador.root.innerHTML = '';
            }
            quillFase6Evaluador.update();
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

// ==================== CERRAR MODAL ====================

function closeFase6EvaluadorModal() {
    $('#fase6EvaluadorModal').removeClass('show');
    if (quillFase6Evaluador) {
        quillFase6Evaluador.root.innerHTML = '';
    }
}

// ==================== VISTA PREVIA FDC127 ====================
function setupFase4FileInput(inputId, listId, maxSizeMB = 5, allowedExtensions = ['doc', 'docx']) {
    $(`#${inputId}`).off('change').on('change', function(e) {
        const file = e.target.files[0];
        const $fileList = $(`#${listId}`);
        $fileList.empty();
        
        if (file) {
            const fileSizeMB = file.size / (1024 * 1024);
            if (fileSizeMB > maxSizeMB) {
                showToast(`El archivo no puede superar los ${maxSizeMB}MB`, 'error');
                $(this).val('');
                return;
            }
            
            const extension = file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(extension)) {
                showToast(`Solo archivos ${allowedExtensions.join(', ')}`, 'error');
                $(this).val('');
                return;
            }
            
            let icon = 'fa-file-word text-blue-500';
            if (extension === 'pdf') {
                icon = 'fa-file-pdf text-red-500';
            }
            
            $fileList.append(`
                <li>
                    <i class="fa-regular ${icon} mr-2"></i>
                    ${file.name}
                </li>
            `);
        }
    });
}

// ==================== VISTA PREVIA FDC195 ====================
function setupFase4FileInput195(inputId, listId, maxSizeMB = 5, allowedExtensions = ['doc', 'docx']) {
    $(`#${inputId}`).off('change').on('change', function(e) {
        const file = e.target.files[0];
        const $fileList = $(`#${listId}`);
        $fileList.empty();
        
        if (file) {
            const fileSizeMB = file.size / (1024 * 1024);
            if (fileSizeMB > maxSizeMB) {
                showToast(`El archivo no puede superar los ${maxSizeMB}MB`, 'error');
                $(this).val('');
                return;
            }
            
            const extension = file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(extension)) {
                showToast(`Solo archivos ${allowedExtensions.join(', ')}`, 'error');
                $(this).val('');
                return;
            }
            
            let icon = 'fa-file-word text-blue-500';
            if (extension === 'pdf') {
                icon = 'fa-file-pdf text-red-500';
            }
            
            $fileList.append(`
                <li>
                    <i class="fa-regular ${icon} mr-2"></i>
                    ${file.name}
                </li>
            `);
        }
    });
}

// ==================== DOCUMENT READY ====================
$(document).ready(function () {

    // Configurar inputs de archivos
    setupFase4FileInput('fdc128_fase6', 'file-list-fdc128-fase6', 5, ['doc', 'docx']);
    setupFase4FileInput195('fdc129_fase6', 'file-list-fdc129-fase6', 5, ['doc', 'docx']);

    // ==================== SUBMIT ====================
    $('#fase6EvaluadorForm').on('submit', function (e) {
        e.preventDefault();
        console.log('responder');



        // ================= GUARDAR QUILL =================
        if (typeof quillFase6Evaluador !== 'undefined' && quillFase6Evaluador) {
            $('#respuesta_fase6').val(quillFase6Evaluador.root.innerHTML);
        }

        // ================= VALIDAR ESTADO =================
        const estado = $('#estado_fase6').val();
        
        if (!estado) {
            $('#estado_fase6Error').text('Debe seleccionar un estado');
            return;
        }

        //JUAN DAVID ESTO GENERA PROBLEMA EL SWAL DEL EVALUADOR A LA HORA DE RESPONDER

        // ================= VALIDAR ACTA SI APRUEBA =================
       /* if (estado === 'Aprobada') {
            const nroActa = $('#nro_acta_fase6').val();
            if (!nroActa) {
                $('#nro_acta_fase6Error').text('Debe ingresar el número de acta');
                return;
            }
            const fechaActa = $('#fecha_acta_fase6').val();
            if (!fechaActa) {
                $('#fecha_acta_fase6Error').text('Debe seleccionar la fecha del acta');
                return;
            }
        }*/

        let mensajeConfirmacion = "Esta acción no se puede deshacer";

Swal.fire({
    target: document.body,
    heightAuto: false,
    backdrop: true,
    allowOutsideClick: false,
    title: '¿Está seguro?',
    text: mensajeConfirmacion,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#C1D631',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, responder',
    cancelButtonText: 'Cancelar',
}).then((result) => {
    if (result.isConfirmed) {
        const button = $(this).find('button[type="submit"]');
        const spinner = $('#loadingSpinner-fase6-admin');
        const formData = new FormData(this);

        button.prop('disabled', true);
        if (spinner.length) spinner.removeClass('hidden');

        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        $.ajax({
            url: ROUTES.fase6_reply,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                closeFase6EvaluadorModal();
                showToast('Respuesta enviada correctamente', 'success');
                setTimeout(() => {
                    location.reload();
                }, 3000);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.estado) $('#estado_fase6Error').text(errors.estado[0]);
                    if (errors.nro_acta) $('#nro_acta_fase6Error').text(errors.nro_acta[0]);
                    if (errors.fecha_acta) $('#fecha_acta_fase6Error').text(errors.fecha_acta[0]);
                    if (errors.respuesta) $('#respuesta_fase6Error').text(errors.respuesta[0]);
                    if (errors.fdc128) $('#fdc128_fase6Error').text(errors.fdc128[0]);
                    if (errors.fdc129) $('#fdc129_fase6Error').text(errors.fdc129[0]);
                    
                    setTimeout(() => {
                        $('#estado_fase6Error, #nro_acta_fase6Error, #fecha_acta_fase6Error, #respuesta_fase6Error, #fdc128_fase6Error, #fdc129_fase6Error').text('');
                    }, 5000);
                } else {
                    showToast(xhr.responseJSON?.error || 'Error al enviar respuesta', 'error');
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

 /* Fase 6 DETALLES */ 

    // Abrir modal de detalles Fase 6
function openFase6DetailsModal(btn) {

    console.log('click fase 6');

    if (btn) {

        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');

        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');

        btn.disabled = true;
    }

    $.ajax({

        url: ROUTES.fase6_details,
        method: 'POST',

        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            practica_id: $('input[name="practica_id"]').first().val()
        },

        success: function(response) {

            let html = `

                <div class="flex flex-col space-y-4">

                    <!-- F-DC-128 -->
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
                                        Ver documento
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

                    <!-- F-DC-129 -->
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
                                        Ver documento
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

                    <!-- F-DC-196 -->
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
                                        Ver documento
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

                    <!-- TURNITIN -->
                    <div class="flex flex-col sm:flex-row items-start justify-between p-3 bg-gray-50 rounded-lg">

                        <p class="font-semibold text-gray-700 w-1/3 min-w-[140px] mb-2 sm:mb-0">
                            Informe Turnitin:
                        </p>

                        ${
                            response.turnitin_url
                            ? `
                                <div class="items-details text-gray-800 w-full sm:flex-1 sm:ml-2">
                                    <i class="fa-regular fa-file-pdf text-red-600 mr-2"></i>
                                    <a href="${response.turnitin_url}"
                                        target="_blank"
                                        class="text-red-600 underline hover:text-red-800">
                                        Ver informe
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

            $('#fase6DetailsContent').html(html);

            $('#fase6DetailsModal')
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

// Cerrar modal
function closeFase6DetailsModal() {

    $('#fase6DetailsModal')
        .removeClass('show')
        .addClass('hidden');

}


// -------- FASE 6 COMITE -----
let quillFase6Comite = null;

function openFase6ComiteModal(btn) {
    console.log('MODAL COMITE')
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }
    
    // Limpiar campos
    $('#estado_fase6_comite').val('');
    $('#nro_acta_fase6_comite').val('');
    $('#fecha_acta_fase6_comite').val('');
    $('#respuesta_fase6_comite').val('');
    $('#fdc128_fase6_comite').val('');
    $('#fdc129_fase6_comite').val('');
    $('#file-list-fdc128-fase6-comite').empty();
    $('#file-list-fdc129-fase6-comite').empty();
    $('#estado_fase6_comiteError').text('');
    $('#nro_acta_fase6_comiteError').text('');
    $('#fecha_acta_fase6_comiteError').text('');
    $('#respuesta_fase6_comiteError').text('');
    $('#fdc128_fase6_comiteError').text('');
    $('#fdc129_fase6_comiteError').text('');
    
    $('#fase6ComiteModal').addClass('show');
    
    setTimeout(function() {
        if ($('#txt-editor-fase6-comite').length > 0) {
            if (quillFase6Comite === null) {
                quillFase6Comite = new Quill('#txt-editor-fase6-comite', {
                    theme: 'snow',
                    placeholder: 'Ingrese comentarios de respuesta...',
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
                quillFase6Comite.root.innerHTML = '';
            }
            quillFase6Comite.update();
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

function closeFase6ComiteModal() {
    $('#fase6ComiteModal').removeClass('show');
    if (quillFase6Comite) {
        quillFase6Comite.root.innerHTML = '';
    }
}

// Submit del formulario del Comité
$(document).ready(function() {
    // Vista previa de archivos
    $('#fdc128_fase6_comite').on('change', function(e) {
        const file = e.target.files[0];
        const fileList = $('#file-list-fdc128-fase6-comite');
        fileList.empty();
        
        if (file) {
            const fileSizeMB = file.size / (1024 * 1024);
            if (fileSizeMB > 5) {
                showToast('El archivo no puede superar los 5MB', 'error');
                $(this).val('');
                return;
            }
            const extension = file.name.split('.').pop().toLowerCase();
            if (!['doc', 'docx'].includes(extension)) {
                showToast('Solo archivos .doc o .docx', 'error');
                $(this).val('');
                return;
            }
            let icon = 'fa-file-word text-blue-500';
            fileList.append(`<li><i class="fa-regular ${icon} mr-2"></i>${file.name}</li>`);
        }
    });
    
    $('#fdc129_fase6_comite').on('change', function(e) {
        const file = e.target.files[0];
        const fileList = $('#file-list-fdc129-fase6-comite');
        fileList.empty();
        
        if (file) {
            const fileSizeMB = file.size / (1024 * 1024);
            if (fileSizeMB > 5) {
                showToast('El archivo no puede superar los 5MB', 'error');
                $(this).val('');
                return;
            }
            const extension = file.name.split('.').pop().toLowerCase();
            if (!['doc', 'docx'].includes(extension)) {
                showToast('Solo archivos .doc o .docx', 'error');
                $(this).val('');
                return;
            }
            let icon = 'fa-file-word text-blue-500';
            fileList.append(`<li><i class="fa-regular ${icon} mr-2"></i>${file.name}</li>`);
        }
    });
    
    $('#fase6ComiteForm').on('submit', function(e) {
        e.preventDefault();
        
        if (quillFase6Comite) {
            $('#respuesta_fase6_comite').val(quillFase6Comite.root.innerHTML);
        }
        
        const estado = $('#estado_fase6_comite').val();
        if (!estado) {
            $('#estado_fase6_comiteError').text('Debe seleccionar un estado');
            return;
        }
        
        // Validar título y acta solo si aprueba
        if (estado === 'Aprobada') {
         
            const nroActa = $('#nro_acta_fase6_comite').val();
            if (!nroActa) {
                $('#nro_acta_fase6_comiteError').text('Debe ingresar el número de acta');
                return;
            }
            const fechaActa = $('#fecha_acta_fase6_comite').val();
            if (!fechaActa) {
                $('#fecha_acta_fase6_comiteError').text('Debe seleccionar la fecha del acta');
                return;
            }
        }
        
        Swal.fire({
            heightAuto: false,
            title: '¿Está seguro?',
            text:estado === 'Aprobada'
            ? 'Al aprobar, la práctica se finalizará.'
            : 'Al rechazar, la práctica volverá a Fase 5.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#C1D631',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, responder',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const button = $(this).find('button[type="submit"]');
                const spinner = $('#loadingSpinner-fase6-comite');
                const formData = new FormData(this);
                
                button.prop('disabled', true);
                spinner.removeClass('hidden');
                console.log(ROUTES.fase6_comite_reply);
                $.ajax({
                    url: ROUTES.fase6_comite_reply,
                    
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        closeFase6ComiteModal();
                        showToast('Respuesta enviada correctamente', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            if (errors.estado) $('#estado_fase6_comiteError').text(errors.estado[0]);
                            if (errors.nro_acta) $('#nro_acta_fase6_comiteError').text(errors.nro_acta[0]);
                            if (errors.fecha_acta) $('#fecha_acta_fase6_comiteError').text(errors.fecha_acta[0]);
                            if (errors.fdc128) $('#fdc128_fase6_comiteError').text(errors.fdc128[0]);
                            if (errors.fdc129) $('#fdc129_fase6_comiteError').text(errors.fdc129[0]);
                            if (errors.respuesta) $('#respuesta_fase6_comiteError').text(errors.respuesta[0]);
                        } else {
                            showToast(xhr.responseJSON?.error || 'Error al enviar respuesta', 'error');
                        }
                    },
                    complete: function() {
                        button.prop('disabled', false);
                        spinner.addClass('hidden');
                    }
                });
            }
        });
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
