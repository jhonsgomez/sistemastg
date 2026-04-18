
document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('hoja_vida');
    const list = document.getElementById('file-list-fase0');
    const sizeText = document.getElementById('files-size-fase0');

    if (input) {
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

            // Mostrar tamaño total
            const sizeMB = (totalSize / (1024 * 1024)).toFixed(2);
            sizeText.textContent = `Tamaño total: ${sizeMB} MB`;
        });
    }

});
