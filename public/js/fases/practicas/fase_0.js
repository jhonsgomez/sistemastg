// Listado de los archivos
document.addEventListener('DOMContentLoaded', function () {

    function setupFilePreview(inputId, listId, sizeId) {

        const input = document.getElementById(inputId);
        const list = document.getElementById(listId);
        const sizeText = document.getElementById(sizeId);

        if (!input) return;

        input.addEventListener('change', function () {

            list.innerHTML = '';
            sizeText.textContent = '';

            const files = this.files;

            if (files.length === 0) return;

            let totalSize = 0;

            for (let i = 0; i < files.length; i++) {

                const file = files[i];
                totalSize += file.size;

                const li = document.createElement('li');
                li.textContent = file.name;

                list.appendChild(li);
            }

            const sizeMB = (totalSize / (1024 * 1024)).toFixed(2);

            sizeText.textContent =
                `Tamaño total: ${sizeMB} MB`;
        });
    }

    // Primer integrante
    setupFilePreview(
        'hoja_vida',
        'file-list-fase0',
        'files-size-fase0'
    );

    // Segundo integrante
    setupFilePreview(
        'hoja_vida_2',
        'file-list-fase0-2',
        'files-size-fase0-2'
    );

});