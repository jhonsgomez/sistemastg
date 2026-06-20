// ==================== MODAL EVALUADOR FASE 6 ====================

let quillFase6Evaluador = null;

function openFase6EvaluadorModal(btn) {



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
                                    image: imageHandlerFase6Evaluador
                                }
                            }
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

function imageHandlerFase6Evaluador() {
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

            const range = quillFase6Evaluador.getSelection(true);

            quillFase6Evaluador.insertEmbed(range.index, 'image', data.url);
            quillFase6Evaluador.setSelection(range.index + 1);

        } catch (error) {
            console.error('ERROR REAL FASE 6 EVALUADOR:', error);

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

function closeFase6EvaluadorModal() {
    $('#fase6EvaluadorModal').removeClass('show');
    if (quillFase6Evaluador) {
        quillFase6Evaluador.root.innerHTML = '';
    }
}

// ==================== DOCUMENT READY ====================
$(document).ready(function () {

    $('#txt-editor-fase6-evaluador, #txt-editor-fase6-comite').on('drop', function(e) {
    e.preventDefault();

    Swal.fire({
        icon: 'warning',
        title: 'Use el botón de imagen',
        text: 'Para evitar errores, suba la imagen desde el botón de imagen del editor.',
        confirmButtonColor: '#C1D631',
        confirmButtonText: 'Aceptar'
    });
});

    // ==================== FDC128 EVALUADOR ====================

$('#fdc128_fase6').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc128-fase6');

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

    const fileExtension = file.name.split('.').pop().toLowerCase();

    if (!['doc', 'docx'].includes(fileExtension)) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'Solo se permiten archivos Word (.doc, .docx).',
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
        <li class="text-gray-600 text-sm">
            ${file.name}
            <span class="block text-gray-600 text-sm mt-2">
                Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_MB} MB permitidos
            </span>
        </li>
    `);
});

    // ==================== FDC128 EVALUADOR ====================

$('#fdc129_fase6').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc129-fase6');

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

    const fileExtension = file.name.split('.').pop().toLowerCase();

    if (!['doc', 'docx'].includes(fileExtension)) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'Solo se permiten archivos Word (.doc, .docx).',
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
        <li class="text-gray-600 text-sm">
            ${file.name}
            <span class="block text-gray-600 text-sm mt-2">
                Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_MB} MB permitidos
            </span>
        </li>
    `);
});
    
    // ==================== SUBMIT ====================
    $('#fase6EvaluadorForm').on('submit', function (e) {
        e.preventDefault();


        // ================= GUARDAR QUILL =================
        if (!quillFase6Evaluador) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El editor no se ha cargado correctamente.',
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        $('#respuesta_fase6').val(quillFase6Evaluador.root.innerHTML);

        const mensajeLimpio = quillFase6Evaluador.getText().trim();

        if (!mensajeLimpio) {
            $('#respuesta_fase6Error').text('Debe ingresar un mensaje de respuesta');
            return;
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
                                        Ver F-DC-128
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
                                        Ver F-DC-129
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
                                        Ver F-DC-196
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
                                        Ver Turnitin
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
                                image: imageHandlerFase6Comite
                            }
                        }
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

function imageHandlerFase6Comite() {
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

            const range = quillFase6Comite.getSelection(true);

            quillFase6Comite.insertEmbed(range.index, 'image', data.url);
            quillFase6Comite.setSelection(range.index + 1);

        } catch (error) {
            console.error('ERROR REAL FASE 6 COMITÉ:', error);

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

function closeFase6ComiteModal() {
    $('#fase6ComiteModal').removeClass('show');
    if (quillFase6Comite) {
        quillFase6Comite.root.innerHTML = '';
    }
}

// Submit del formulario del Comité
$(document).ready(function() {
    $('#txt-editor-fase6-evaluador, #txt-editor-fase6-comite').on('drop', function(e) {
    e.preventDefault();

    Swal.fire({
        icon: 'warning',
        title: 'Use el botón de imagen',
        text: 'Para evitar errores, suba la imagen desde el botón de imagen del editor.',
        confirmButtonColor: '#C1D631',
        confirmButtonText: 'Aceptar'
    });
});
    // Vista previa de archivos
    $('#fdc128_fase6_comite').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc128-fase6-comite');

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

    const fileExtension = file.name.split('.').pop().toLowerCase();

    if (!['doc', 'docx'].includes(fileExtension)) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'Solo se permiten archivos Word (.doc, .docx).',
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
        <li class="text-gray-600 text-sm">
            ${file.name}
            <span class="block text-gray-600 text-sm mt-2">
                Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_MB} MB permitidos
            </span>
        </li>
    `);
});
    
    $('#fdc129_fase6_comite').on('change', function(e) {

    const file = e.target.files[0];
    const fileList = $('#file-list-fdc129-fase6-comite');

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

    const fileExtension = file.name.split('.').pop().toLowerCase();

    if (!['doc', 'docx'].includes(fileExtension)) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'Solo se permiten archivos Word (.doc, .docx).',
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
        <li class="text-gray-600 text-sm">
            ${file.name}
            <span class="block text-gray-600 text-sm mt-2">
                Tamaño total: ${fileSizeMB2} MB de ${MAX_FILE_SIZE_MB} MB permitidos
            </span>
        </li>
    `);
});
    
    $('#fase6ComiteForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!quillFase6Comite) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El editor no se ha cargado correctamente.',
                confirmButtonColor: '#C1D631',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        $('#respuesta_fase6_comite').val(quillFase6Comite.root.innerHTML);

        const mensajeLimpio = quillFase6Comite.getText().trim();

        if (!mensajeLimpio) {
            $('#respuesta_fase6_comiteError').text('Debe ingresar un mensaje de respuesta');
            return;
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
