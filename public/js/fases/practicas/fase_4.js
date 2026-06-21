// ==================== MODAL EVALUADOR FASE 4 ====================

let quillFase4Evaluador = null;

function openFase4EvaluadorModal(btn) {

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

    // ================= LIMPIAR ERRORES =================
    $('#estado_fase4Error').text('');
    $('#nro_acta_fase4Error').text('');
    $('#fecha_acta_fase4Error').text('');
    $('#respuesta_fase4Error').text('');
    $('#fdc127_fase4Error').text('');

    // ================= LIMPIAR LISTAS =================
    $('#file-list-fdc127-fase4').html('');

    // ================= ABRIR MODAL =================
    $('#fase4EvaluadorModal').addClass('show');

    setTimeout(function () {
        if ($('#txt-editor-fase4-evaluador').length > 0) {
            if (quillFase4Evaluador === null) {
                    quillFase4Evaluador = new Quill('#txt-editor-fase4-evaluador', {
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
                                    image: imageHandlerFase4Evaluador
                                }
                            }
                        }
                    });
       
            } else {
                quillFase4Evaluador.root.innerHTML = '';
            }
            quillFase4Evaluador.update();
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

function imageHandlerFase4Evaluador() {
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

            const range = quillFase4Evaluador.getSelection(true);

            quillFase4Evaluador.insertEmbed(range.index, 'image', data.url);
            quillFase4Evaluador.setSelection(range.index + 1);

        } catch (error) {
            console.error('ERROR REAL FASE 4 EVALUADOR:', error);

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

    const maxSizeBytes = MAX_FILE_SIZE_PR * 1024 * 1024;

    if (file.size > maxSizeBytes) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo demasiado grande',
            text: `El archivo no puede superar los ${MAX_FILE_SIZE_PR} MB.`,
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
            <div class="text-gray-600 text-sm mb-1">
                ${file.name}
            </div>
            <div class="text-sm text-gray-600">
                Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_PR} MB permitidos
            </div>
        </li>
    `);
});
    
// ==================== DOCUMENT READY ====================
$(document).ready(function () {

    $('#txt-editor-fase4-evaluador, #txt-editor-fase4-comite').on('drop', function(e) {
    e.preventDefault();

    Swal.fire({
        icon: 'warning',
        title: 'Use el botón de imagen',
        text: 'Para evitar errores, suba la imagen desde el botón de imagen del editor.',
        confirmButtonColor: '#C1D631',
        confirmButtonText: 'Aceptar'
    });
});

    // ==================== SUBMIT ====================
    $('#fase4EvaluadorForm').on('submit', function (e) {
        e.preventDefault();



        // ================= GUARDAR QUILL =================
        if (!quillFase4Evaluador) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El editor no se ha cargado correctamente.',
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        $('#respuesta_fase4').val(quillFase4Evaluador.root.innerHTML);

        const mensajeLimpio = quillFase4Evaluador.getText().trim();

        if (!mensajeLimpio) {
            $('#respuesta_fase4Error').text('Debe ingresar un mensaje de respuesta');
            return;
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
                    
                    setTimeout(() => {
                        $('#estado_fase4Error, #nro_acta_fase4Error, #fecha_acta_fase4Error, #respuesta_fase4Error, #fdc127_fase4Error').text('');
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
    $('#file-list-fdc127-fase4-comite').empty();
    $('#file-list-fdc195-fase4-comite').empty();
    $('#estado_fase4_comiteError').text('');
    $('#titulo_propuesta_fase4_comiteError').text('');
    $('#nro_acta_fase4_comiteError').text('');
    $('#fecha_acta_fase4_comiteError').text('');
    $('#respuesta_fase4_comiteError').text('');
    $('#fdc127_fase4_comiteError').text('');
    
    $('#fase4ComiteModal').addClass('show');
    
    setTimeout(function() {
        if ($('#txt-editor-fase4-comite').length > 0) {
            if (quillFase4Comite === null) {
                quillFase4Comite = new Quill('#txt-editor-fase4-comite', {
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
                                image: imageHandlerFase4Comite
                            }
                        }
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

function imageHandlerFase4Comite() {
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

            const range = quillFase4Comite.getSelection(true);

            quillFase4Comite.insertEmbed(range.index, 'image', data.url);
            quillFase4Comite.setSelection(range.index + 1);

        } catch (error) {
            console.error('ERROR REAL FASE 4 COMITÉ:', error);

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

    const maxSizeBytes = MAX_FILE_SIZE_PR * 1024 * 1024;

    if (file.size > maxSizeBytes) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo demasiado grande',
            text: `El archivo no puede superar los ${MAX_FILE_SIZE_PR} MB.`,
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
            <div class="text-gray-600 text-sm mb-1">
                ${file.name}
            </div>
            <div class="text-sm text-gray-600">
                Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_PR} MB permitidos
            </div>
        </li>
    `);
});

    // ENVIO FORMULARIO

    $('#fase4ComiteForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!quillFase4Comite) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El editor no se ha cargado correctamente.',
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        $('#respuesta_fase4_comite').val(quillFase4Comite.root.innerHTML);

        const mensajeLimpio = quillFase4Comite.getText().trim();

        if (!mensajeLimpio) {
            $('#respuesta_fase4_comiteError').text('Debe ingresar un mensaje de respuesta');
            return;
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
            text: mensajeConfirmacion,
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
