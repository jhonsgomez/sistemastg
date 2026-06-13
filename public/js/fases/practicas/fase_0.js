document.addEventListener('DOMContentLoaded', function () {

    function setupFilePreview(inputId, listId, sizeId) {

        const input = document.getElementById(inputId);
        const list = document.getElementById(listId);
        const sizeText = document.getElementById(sizeId);

        if (!input || !list || !sizeText) return;

        input.addEventListener('change', function () {

    list.innerHTML = '';
    sizeText.textContent = '';

    if (!this.files.length) return;

    const file = this.files[0];

    const maxSizeBytes = MAX_FILE_SIZE_MB * 1024 * 1024;

if (file.size > maxSizeBytes) {

    Swal.fire({
        icon: 'error',
        title: 'Archivo demasiado grande',
        text: `El archivo no puede superar los ${MAX_FILE_SIZE_MB} MB.`,
        confirmButtonColor: '#C1D631',
        confirmButtonText: 'Aceptar'
    });

    this.value = '';

    list.innerHTML = '';
    sizeText.textContent = '';

    return;
}
    
    // VALIDAR PDF
    if (
        file.type !== 'application/pdf' &&
        !file.name.toLowerCase().endsWith('.pdf')
    ) {

        Swal.fire({
            icon: 'error',
            title: 'Archivo inválido',
            text: 'Solo se permiten archivos en formato PDF.',
            confirmButtonColor: '#C1D631',
            confirmButtonText: 'Aceptar'
        });

        this.value = '';

        return;
    }

    let totalSize = 0;

    Array.from(this.files).forEach(file => {

        totalSize += file.size;

        const fileSizeMB =
            (file.size / (1024 * 1024)).toFixed(2);

        const li = document.createElement('li');

        li.textContent =
            `${file.name} (${fileSizeMB} MB)`;

        list.appendChild(li);

    });

    sizeText.textContent =
        `Tamaño total: ${(totalSize / (1024 * 1024)).toFixed(8)} MB`;

});

    }

    // Fase 0
    setupFilePreview('hoja_vida', 'file-list-fase0', 'files-size-fase0');
    setupFilePreview('hoja_vida_2', 'file-list-fase0-2', 'files-size-fase0-2');

    


});