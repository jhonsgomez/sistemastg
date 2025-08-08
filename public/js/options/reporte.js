// Reportes

function openReporteModal() {
    $('#reporteTitle').html(`Generar <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Reporte</span>`);

    $('#periodo_reporte').val('');
    $('#periodo_reporteError').text('');

    $('#reporteModal').addClass('show');
}

function closeReporteModal() {
    $('#reporteModal').removeClass('show');
}