function openFase4EvaluadorModal() {
    $('#estado_fase4').val('');
    $('#respuesta_fase4').val('');
    $('#estado_fase4Error').text('');
    $('#respuesta_fase4Error').text('');
    $('#fase4EvaluadorModal').removeClass('hidden');
}

function closeFase4EvaluadorModal() {
    $('#fase4EvaluadorModal').addClass('hidden');
}

$('#fase4EvaluadorForm').on('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C1D631',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, responder',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("practicas.fase4.reply") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    closeFase4EvaluadorModal();
                    Swal.fire('Éxito', 'Respuesta enviada correctamente', 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.estado) $('#estado_fase4Error').text(errors.estado[0]);
                        if (errors.respuesta) $('#respuesta_fase4Error').text(errors.respuesta[0]);
                    } else {
                        Swal.fire('Error', xhr.responseJSON?.error || 'Error al enviar respuesta', 'error');
                    }
                }
            });
        }
    });
});