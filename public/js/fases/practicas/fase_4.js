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
                    placeholder: 'Ingrese comentarios de respuesta...',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'color': [] }],
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
    setupFase4FileInput('fdc127_fase4', 'file-list-fdc127-fase4', 5, ['doc', 'docx']);
    setupFase4FileInput195('fdc195_fase4', 'file-list-fdc195-fase4', 5, ['doc', 'docx']);

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
        /*if (estado === 'Aprobada') {
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
        if (estado === 'Aprobada') {
            mensajeConfirmacion = "Al aprobar, la práctica pasará a Fase 5 (Comité).";
        } else {
            mensajeConfirmacion = "Al rechazar, la práctica volverá a Fase 3 y el estudiante deberá reiniciar el ciclo.";
        }
  

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