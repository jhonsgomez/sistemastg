// ==================== MODAL EVALUADOR FASE 4 ====================

let quillFase4Evaluador = null;

function openFase4EvaluadorModal(btn) {

    console.log("modal fase 4");

    // ================= SPINNER BOTÓN =================
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }

    // ================= LIMPIAR CAMPOS =================
    $('#estado_fase4').val('');
    $('#nro_acta_fase4').val('');
    $('#fecha_acta_fase4').val('');
    $('#respuesta_fase4').val('');
    $('#fdc127_fase4').val('');
    $('#fdc195_fase4').val('');

    // ================= LIMPIAR ERRORES =================
    $('#estado_fase4Error').text('');
    $('#nro_acta_fase4Error').text('');
    $('#fecha_acta_fase4Error').text('');
    $('#respuesta_fase4Error').text('');
    $('#fdc127_fase4Error').text('');
    $('#fdc195_fase4Error').text('');

    // ================= LIMPIAR LISTAS =================
    $('#file-list-fdc127-fase4').html('');
    $('#file-list-fdc195-fase4').html('');

    // ================= ABRIR MODAL =================
    $('#fase4EvaluadorModal').addClass('show');

    // ================= QUILL =================
    setTimeout(function () {
        if ($('#txt-editor-fase4-evaluador').length > 0) {
            if (quillFase4Evaluador === null) {
                quillFase4Evaluador = new Quill('#txt-editor-fase4-evaluador', {
                    theme: 'snow',
                    placeholder: 'Describa los detalles de la respuesta para el estudiante.',
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
                quillFase4Evaluador.root.innerHTML = '';
            }
            quillFase4Evaluador.update();
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

function closeFase4EvaluadorModal() {
    $('#fase4EvaluadorModal').removeClass('show');
    if (quillFase4Evaluador) {
        quillFase4Evaluador.root.innerHTML = '';
    }
}

    // VALIDACIONES EVALUADOR

    //FDC127

    $('#fdc127_fase4').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc127-fase4');

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
            <div class="text-gray-600 text-sm mb-4">
                ${file.name}
            </div>
            <div class="text-sm ml-6 text-gray-900">
                Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_MB} MB permitidos
            </div>
        </li>
    `);
});

    // FDC195

    $('#fdc195_fase4').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc195-fase4');

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
            <div class="text-gray-600 text-sm mb-4">
                ${file.name}
            </div>
            <div class="text-sm ml-6 text-gray-900">
                Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_MB} MB permitidos
            </div>
        </li>
    `);
});
    
// ==================== DOCUMENT READY ====================
$(document).ready(function () {

    // ==================== SUBMIT ====================
    $('#fase4EvaluadorForm').on('submit', function (e) {
        e.preventDefault();
        console.log('responder');



        // ================= GUARDAR QUILL =================
        if (typeof quillFase4Evaluador !== 'undefined' && quillFase4Evaluador) {
            $('#respuesta_fase4').val(quillFase4Evaluador.root.innerHTML);
        }

        // ================= VALIDAR ESTADO =================
        const estado = $('#estado_fase4').val();
        
        if (!estado) {
            $('#estado_fase4Error').text('Debe seleccionar un estado');
            return;
        }

        //JUAN DAVID ESTO GENERA PROBLEMA EL SWAL DEL EVALUADOR A LA HORA DE RESPONDER

        // ================= VALIDAR ACTA SI APRUEBA =================
       /* if (estado === 'Aprobada') {
            const nroActa = $('#nro_acta_fase4').val();
            if (!nroActa) {
                $('#nro_acta_fase4Error').text('Debe ingresar el número de acta');
                return;
            }
            const fechaActa = $('#fecha_acta_fase4').val();
            if (!fechaActa) {
                $('#fecha_acta_fase4Error').text('Debe seleccionar la fecha del acta');
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
        const spinner = $('#loadingSpinner-fase4-admin');
        const formData = new FormData(this);

        button.prop('disabled', true);
        if (spinner.length) spinner.removeClass('hidden');

        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        $.ajax({
            url: ROUTES.fase4_reply,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                closeFase4EvaluadorModal();
                showToast('Respuesta enviada correctamente', 'success');
                setTimeout(() => {
                    location.reload();
                }, 3000);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.estado) $('#estado_fase4Error').text(errors.estado[0]);
                    if (errors.nro_acta) $('#nro_acta_fase4Error').text(errors.nro_acta[0]);
                    if (errors.fecha_acta) $('#fecha_acta_fase4Error').text(errors.fecha_acta[0]);
                    if (errors.respuesta) $('#respuesta_fase4Error').text(errors.respuesta[0]);
                    if (errors.fdc127) $('#fdc127_fase4Error').text(errors.fdc127[0]);
                    if (errors.fdc195) $('#fdc195_fase4Error').text(errors.fdc195[0]);
                    
                    setTimeout(() => {
                        $('#estado_fase4Error, #nro_acta_fase4Error, #fecha_acta_fase4Error, #respuesta_fase4Error, #fdc127_fase4Error, #fdc195_fase4Error').text('');
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


// -------- FASE 4 COMITE -----
let quillFase4Comite = null;

function openFase4ComiteModal(btn) {
    if (btn) {
        const icon = btn.querySelector('i');
        const spinner = btn.querySelector('.loading-spinner');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        btn.disabled = true;
    }

    $('#container_titulo_fase4_comite').addClass('hidden');

    // Limpiar campos
    $('#estado_fase4_comite').val('');
    $('#titulo_propuesta_fase4_comite').val('');
    $('#nro_acta_fase4_comite').val('');
    $('#fecha_acta_fase4_comite').val('');
    $('#respuesta_fase4_comite').val('');
    $('#fdc127_fase4_comite').val('');
    $('#fdc195_fase4_comite').val('');
    $('#file-list-fdc127-fase4-comite').empty();
    $('#file-list-fdc195-fase4-comite').empty();
    $('#estado_fase4_comiteError').text('');
    $('#titulo_propuesta_fase4_comiteError').text('');
    $('#nro_acta_fase4_comiteError').text('');
    $('#fecha_acta_fase4_comiteError').text('');
    $('#respuesta_fase4_comiteError').text('');
    $('#fdc127_fase4_comiteError').text('');
    $('#fdc195_fase4_comiteError').text('');
    
    $('#fase4ComiteModal').addClass('show');
    
    setTimeout(function() {
        if ($('#txt-editor-fase4-comite').length > 0) {
            if (quillFase4Comite === null) {
                quillFase4Comite = new Quill('#txt-editor-fase4-comite', {
                    theme: 'snow',
                    placeholder: 'Describa los detalles de la respuesta para el estudiante.',
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
                quillFase4Comite.root.innerHTML = '';
            }
            quillFase4Comite.update();
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

function closeFase4ComiteModal() {
    $('#fase4ComiteModal').removeClass('show');
    if (quillFase4Comite) {
        quillFase4Comite.root.innerHTML = '';
    }
}

// Submit del formulario del Comité
$(document).ready(function() {
    // Vista previa de archivos
    $('#fdc127_fase4_comite').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc127-fase4-comite');

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
            <div class="text-gray-600 text-sm mb-4">
                ${file.name}
            </div>
            <div class="text-sm ml-6 text-gray-900">
                Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_MB} MB permitidos
            </div>
        </li>
    `);
});
    
    $('#fdc195_fase4_comite').on('change', function(e) {
        const file = e.target.files[0];
        const fileList = $('#file-list-fdc195-fase4-comite');
        fileList.empty();
        
        if (file) {
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
            <div class="text-gray-600 text-sm mb-4">
                ${file.name}
            </div>
            <div class="text-sm ml-6 text-gray-900">
                Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_MB} MB permitidos
            </div>
        </li>
    `);
        }
    });

    // ENVIO FORMULARIO

    $('#fase4ComiteForm').on('submit', function(e) {
        e.preventDefault();
        
        if (quillFase4Comite) {
            $('#respuesta_fase4_comite').val(quillFase4Comite.root.innerHTML);
        }
        
        const estado = $('#estado_fase4_comite').val();
        if (!estado) {
            $('#estado_fase4_comiteError').text('Debe seleccionar un estado');
            return;
        }
        
        // Validar título y acta solo si aprueba
        if (estado === 'Aprobada') {
            const titulo = $('#titulo_propuesta_fase4_comite').val();
            if (!titulo) {
                $('#titulo_propuesta_fase4_comiteError').text('Debe ingresar el título oficial de la propuesta');
                return;
            }
            const nroActa = $('#nro_acta_fase4_comite').val();
            if (!nroActa) {
                $('#nro_acta_fase4_comiteError').text('Debe ingresar el número de acta');
                return;
            }
            const fechaActa = $('#fecha_acta_fase4_comite').val();
            if (!fechaActa) {
                $('#fecha_acta_fase4_comiteError').text('Debe seleccionar la fecha del acta');
                return;
            }
        }

        let mensajeConfirmacion = "Esta acción no se puede deshacer";

        Swal.fire({
            heightAuto: false,
            title: '¿Está seguro?',
            text: estado === mensajeConfirmacion,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#C1D631',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, responder',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const button = $(this).find('button[type="submit"]');
                const spinner = $('#loadingSpinner-fase4-comite');
                const formData = new FormData(this);
                
                button.prop('disabled', true);
                spinner.removeClass('hidden');
                console.log(ROUTES.fase4_comite_reply);
                $.ajax({
                    url: ROUTES.fase4_comite_reply,
                    
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        closeFase4ComiteModal();
                        showToast('Respuesta enviada correctamente', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            if (errors.estado) $('#estado_fase4_comiteError').text(errors.estado[0]);
                            if (errors.titulo_propuesta) $('#titulo_propuesta_fase4_comiteError').text(errors.titulo_propuesta[0]);
                            if (errors.nro_acta) $('#nro_acta_fase4_comiteError').text(errors.nro_acta[0]);
                            if (errors.fecha_acta) $('#fecha_acta_fase4_comiteError').text(errors.fecha_acta[0]);
                            if (errors.fdc127) $('#fdc127_fase4_comiteError').text(errors.fdc127[0]);
                            if (errors.fdc195) $('#fdc195_fase4_comiteError').text(errors.fdc195[0]);
                            if (errors.respuesta) $('#respuesta_fase4_comiteError').text(errors.respuesta[0]);
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

    $('#estado_fase4_comite').on('change', function() {

    if ($(this).val() === 'Aprobada') {

        $('#container_titulo_fase4_comite')
            .removeClass('hidden');

    } else {

        $('#container_titulo_fase4_comite')
            .addClass('hidden');

        $('#titulo_propuesta_fase4_comite').val('');
        $('#titulo_propuesta_fase4_comiteError').text('');
    }
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
