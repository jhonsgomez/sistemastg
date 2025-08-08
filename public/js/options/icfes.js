// ESTUDIANTE

function openIcfesEstudianteModal(id) {
    new fileInput('doc_icfes', 'dropzone_doc_icfes', 'pdf', 1, 4, 'doc_icfes-file-list', 'doc_icfes-files-size');

    $('#icfesEstudianteTitle').html(`Beneficio saber <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">TYT/PRO</span>`);

    // Campos del formulario
    $('#doc_icfes').val('');

    // Campos para mostrar errores
    $('#doc_icfesError').text('');

    $('#icfesEstudianteModal').addClass('show');
}

function closeIcfesEstudianteModal() {
    $('#icfesEstudianteModal').removeClass('show');
}

// COMITÉ

function openIcfesAdminModal() {
    initQuillEditor(undefined, "Describa los detalles de la respuesta para el estudiante.", 'txt-editor-icfes', 'respuesta_icfes');

    $('#icfesAdminTitle').html(`Beneficio saber <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">TYT/PRO</span>`);

    // Campos del formulario
    $('#estado_icfes').val('');
    $('#estudiante_id-icfes').val('').trigger('change');
    $('#nro_acta_icfes').val('');
    $('#fecha_acta_icfes').val('');
    $('#respuesta_icfes').val('');

    // Campos para mostrar errores
    $('#estado_icfesError').text('');
    $('#estudiante_idError').text('');
    $('#nro_acta_icfesError').text('');
    $('#fecha_acta_icfesError').text('');
    $('#respuesta_icfesError').text('');

    $('#icfesAdminModal').addClass('show');
}

function closeIcfesAdminModal() {
    $('#icfesAdminModal').removeClass('show');
}

// FORMULARIOS

$('#icfesEstudianteForm').on('submit', function (e) {
    e.preventDefault();

    const loadingSpinner = document.getElementById(`loadingSpinner-icfesEstudiante`);

    const url = `/proyectos/icfes`;
    const method = 'POST';

    const formData = new FormData(this);

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
            loaderGeneral.classList.replace('hidden', 'flex');
            loadingSpinner.classList.remove('hidden');

            $.ajax({
                url: url,
                method: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function () {
                    $('#icfes-estudiante-button').addClass('hidden');
                    closeIcfesEstudianteModal();
                    showToast('Información enviada, tendrá respuesta en los proximos 5 días hábiles');
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON.errors;
                    
                    $('#doc_icfesError').text(errors?.doc_icfes?.[0] || '');
                },
                complete: function () {
                    loaderGeneral.classList.replace('flex', 'hidden');
                    loadingSpinner.classList.add('hidden');
                }
            });
        }
    });
});

$('#icfesAdminForm').on('submit', function (e) {
    e.preventDefault();

    const loadingSpinner = document.getElementById(`loadingSpinner-icfesAdmin`);

    const url = `/proyectos/icfes/responder`;
    const method = 'POST';

    const formData = new FormData(this);

    Swal.fire({
        heightAuto: false,
        title: '¿Está seguro?',
        text: "Desea responder la solicitud del estudiante",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C1D631',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            loaderGeneral.classList.replace('hidden', 'flex');
            loadingSpinner.classList.remove('hidden');

            $.ajax({
                url: url,
                method: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function () {
                    closeIcfesAdminModal();
                    sessionStorage.setItem('showToast', 'true');
                    sessionStorage.setItem('toastMessage', 'Información enviada, tendrá respuesta en los proximos 5 días hábiles');
                    location.reload();
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON.errors;
                    
                    $('#estado_icfesError').text(errors?.estado_icfes?.[0] || '');
                    $('#estudiante_idError').text(errors?.estudiante_id?.[0] || '');
                    $('#nro_acta_icfesError').text(errors?.nro_acta_icfes?.[0] || '');
                    $('#fecha_acta_icfesError').text(errors?.fecha_acta_icfes?.[0] || '');
                    $('#respuesta_icfesError').text(errors?.respuesta_icfes?.[0] || '');
                },
                complete: function () {
                    loaderGeneral.classList.replace('flex', 'hidden');
                    loadingSpinner.classList.add('hidden');
                }
            });
        }
    });
});