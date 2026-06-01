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
                `Tamaño total: ${(totalSize / (1024 * 1024)).toFixed(2)} MB`;

        });

    }

    // Fase 0
    setupFilePreview('hoja_vida', 'file-list-fase0', 'files-size-fase0');
    setupFilePreview('hoja_vida_2', 'file-list-fase0-2', 'files-size-fase0-2');

    // Fase 1
    setupFilePreview('doc_fdc126', 'file-list-fase1', 'files-size-fase1');

    //Fase 2 
    setupFilePreview('liquidacion_pago', 'file-list-liquidacion', 'files-size-liquidacion');
    setupFilePreview('soporte_pago', 'file-list-soporte', 'files-size-soporte');

    // Fase 3
    setupFilePreview('arl', 'file-list-arl', 'files-size-arl');
    setupFilePreview('fdc127', 'file-list-fdc127', 'files-size-fdc127');

    //Fase 4
    setupFilePreview('fdc127_fase4', 'file-list-fdc127-fase4', 'files-size-fdc127-fase4');
    setupFilePreview('fdc195_fase4', 'file-list-fdc195-fase4', 'files-size-fdc195-fase4');

    //Fase 4 Comite
    setupFilePreview('fdc127_fase4_comite', 'file-list-fdc127-fase4-comite', 'files-size-fdc127-fase4-comite');
    setupFilePreview('fdc195_fase4_comite', 'file-list-fdc195-fase4-comite', 'files-size-fdc195-fase4-comite');


    // Fase 5
    setupFilePreview('fdc128', 'file-list-fdc128', 'files-size-fdc128');
    setupFilePreview('fdc129', 'file-list-fdc129', 'files-size-fdc129');
    setupFilePreview('turnitin', 'file-list-turnitin', 'files-size-turnitin');

    //Fase 6
    
    setupFilePreview('fdc128_fase6', 'file-list-fdc128-fase6', 'files-size-fdc128-fase6');
    setupFilePreview('fdc129_fase6', 'file-list-fdc129-fase6', 'files-size-fdc129-fase6');

    setupFilePreview('fdc128_fase6_comite', 'file-list-fdc128-fase6-comite', 'files-size-fdc128-fase6-comite');
    setupFilePreview('fdc129_fase6_comite', 'file-list-fdc129-fase6-comite', 'files-size-fdc129-fase6-comite');

    


});