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
    $('#respuesta_fase4').val('');

    $('#fdc127_fase4').val('');

    // ================= LIMPIAR ERRORES =================

    $('#estado_fase4Error').text('');
    $('#titulo_propuesta_fase4Error').text('');
    $('#respuesta_fase4Error').text('');
    $('#fdc127_fase4Error').text('');

    // ================= LIMPIAR LISTAS =================

    $('#file-list-fdc127-fase4').html('');

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
                            [{ 'header': 1 }],
                            [{ 'header': 2 }],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
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

// ==================== FDC127 EVALUADOR ====================

$(document).ready(function () {

    $('#fdc127_fase4').on('change', function (e) {

        const file = e.target.files[0];
        const fileList = $('#file-list-fdc127-fase4');

        fileList.empty();

        if (file) {

            const fileSizeMB = file.size / (1024 * 1024);

            if (fileSizeMB > 5) {

                Swal.fire(
                    'Error',
                    'El archivo no puede superar los 5MB',
                    'error'
                );

                $(this).val('');

                return;
            }

            const extension = file.name
                .split('.')
                .pop()
                .toLowerCase();

            if (!['pdf', 'doc', 'docx'].includes(extension)) {

                Swal.fire(
                    'Error',
                    'Solo PDF, DOC o DOCX',
                    'error'
                );

                $(this).val('');

                return;
            }

            let icon = 'fa-file-word text-blue-500';

            if (extension === 'pdf') {

                icon = 'fa-file-pdf text-red-500';

            }

            fileList.append(`
                <li>
                    <i class="fa-regular ${icon} mr-2"></i>
                    ${file.name}
                </li>
            `);
        }
    });

    // ==================== SUBMIT ====================

    $('#fase4EvaluadorForm').on('submit', function (e) {

        e.preventDefault();

        // ================= GUARDAR QUILL =================

        if (quillFase4Evaluador) {

            $('#respuesta_fase4').val(
                quillFase4Evaluador.root.innerHTML
            );

        }

        // ================= VALIDAR ESTADO =================

        const estado = $('#estado_fase4').val();

        if (!estado) {

            $('#estado_fase4Error')
                .text('Debe seleccionar un estado');

            return;
        }

        Swal.fire({

            heightAuto: false,

            title: '¿Está seguro?',

            text: 'Esta acción no se puede deshacer',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#C1D631',

            cancelButtonColor: '#d33',

            confirmButtonText: 'Sí, responder',

            cancelButtonText: 'Cancelar'

        }).then((result) => {

            if (result.isConfirmed) {

                const button = $(this).find('button[type="submit"]');

                // IMPORTANTE PARA ARCHIVOS
                const formData = new FormData(this);

                button.prop('disabled', true);

                $.ajax({

                    url: ROUTES.fase4_reply,

                    method: 'POST',

                    data: formData,

                    processData: false,
                    contentType: false,

                    success: function (response) {

                        closeFase4EvaluadorModal();

                        Swal.fire({

                            title: '¡Éxito!',

                            text: response.success || 'Respuesta enviada correctamente',

                            icon: 'success',

                            confirmButtonText: 'Ok',

                            confirmButtonColor: '#C1D631'

                        }).then(() => {

                            location.reload();

                        });

                    },

                    error: function (xhr) {

                        if (xhr.status === 422 && xhr.responseJSON.errors) {

                            const errors = xhr.responseJSON.errors;

                            if (errors.estado)
                                $('#estado_fase4Error')
                                    .text(errors.estado[0]);

                            if (errors.titulo_propuesta)
                                $('#titulo_propuesta_fase4Error')
                                    .text(errors.titulo_propuesta[0]);

                            if (errors.respuesta)
                                $('#respuesta_fase4Error')
                                    .text(errors.respuesta[0]);

                            if (errors.fdc127)
                                $('#fdc127_fase4Error')
                                    .text(errors.fdc127[0]);

                        } else {

                            Swal.fire(
                                'Error',
                                xhr.responseJSON?.error || 'Error al enviar respuesta',
                                'error'
                            );

                        }

                    },

                    complete: function () {

                        button.prop('disabled', false);

                    }

                });

            }

        });

    });

});

// ==================== INPUT SIMPLE FASE 4 ====================

$(document).ready(function () {

    setupSimpleFileInput(
        'fdc127_fase4',
        'file-list-fdc127-fase4',
        5,
        ['pdf', 'doc', 'docx']
    );

});