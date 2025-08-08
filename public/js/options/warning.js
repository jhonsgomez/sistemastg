function openWarningModal() {
    initQuillEditor(undefined, "Describa su reporte o inconveniente y déjenos saber sus recomendaciones.", 'txt-editor-warning', 'mensaje_warning');

    $('#warningModal').addClass('show');
    $('#warningTitle').html(`Enviar reporte <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">PQRSD</span>`);
    $('#mensaje_warning').val('');
    $('#mensaje_warningError').text('');
}

$('#warningForm').on('submit', function (e) {
    e.preventDefault();

    const button = document.getElementById(`warningModalButton`);
    const loadingSpinner = document.getElementById(`loadingSpinner-warning`);

    const url = `/reportes/enviar`;
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
                success: function (response) {
                    closeWarningModal();
                    showToast('Reporte enviado correctamente');
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON.errors;
                    $('#mensaje_warningError').text(errors?.mensaje_warning?.[0] || '');
                },
                complete: function () {
                    campo.value = '';
                    quill.root.innerHTML = '';
                    loaderGeneral.classList.replace('flex', 'hidden');
                    loadingSpinner.classList.add('hidden');
                }
            });
        }
    });
});

function closeWarningModal() {
    $('#warningModal').removeClass('show');
}