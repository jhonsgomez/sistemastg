async function getPeriodoActual() {
    const añoActual = new Date().getFullYear();
    const mesActual = new Date().getMonth() + 1;
    const numero = mesActual <= 6 ? 1 : 2;
    const periodo_academico = añoActual + '-' + numero;

    return periodo_academico
}

async function openCalendarModal(withPerido = true) {
    $('#calendarModal').addClass('show');

    if (withPerido) {
        const periodo_academico = await getPeriodoActual();
        $('#calendarTitle').html(`Calendario del <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">${periodo_academico}</span>`);
    } else {
        $('#calendarTitle').html(`Calendario del <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Proyecto</span>`);
    }
}

function closeCalendarModal() {
    $('#calendarModal').removeClass('show');
}