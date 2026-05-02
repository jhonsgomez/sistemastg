function openFase1EstudianteModal() {
    console.log('CLICK FUNCIONA');

    const modal = document.getElementById('fase1EstudianteModal');

    if (!modal) {
        console.log('NO EXISTE EL MODAL');
        return;
    }

    modal.classList.add('show');
}

function closeFase1EstudianteModal() {
     TooltipManager.closeTooltips();
    $('#fase1EstudianteModal').removeClass('show');
}