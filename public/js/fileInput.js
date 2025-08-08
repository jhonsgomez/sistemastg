class fileInput {
    constructor(inputID, dropzoneID, fileType, maxFiles, maxSizeMB, fileListID, fileSizeID) {
        const input = document.getElementById(inputID);
        const dropzone = document.getElementById(dropzoneID);
        const fileList = document.getElementById(fileListID);
        const fileSize = document.getElementById(fileSizeID);

        if (input && dropzone && fileList && fileSize) {
            input.addEventListener('change', function (e) {
                const files = this.files;
                const filesArray = Array.from(files);

                fileList.innerHTML = ``;
                fileSize.textContent = ``;

                switch (fileType) {
                    case 'pdf':
                        const areAllPDF = filesArray.every(file => file.type === 'application/pdf' ||
                            file.name.toLowerCase().endsWith('.pdf')
                        );

                        if (!areAllPDF) {
                            Swal.fire({
                                heightAuto: false,
                                icon: 'error',
                                title: 'Error',
                                text: 'Solo se permiten archivos PDF.',
                            });
                            this.value = '';
                            return;
                        }
                        break;
                    case 'word':
                        const areAllWord = filesArray.every(file => file.name.toLowerCase().endsWith('.doc') ||
                            file.name.toLowerCase().endsWith('.docx')
                        );

                        if (!areAllWord) {
                            Swal.fire({
                                heightAuto: false,
                                icon: 'error',
                                title: 'Error',
                                text: 'Solo se permiten archivos de Word.',
                            });
                            this.value = '';
                            return;
                        }
                        break;
                    case 'excel':
                        const areAllExcel = filesArray.every(file => file.name.toLowerCase().endsWith('.xls') ||
                            file.name.toLowerCase().endsWith('.xlsx')
                        );

                        if (!areAllExcel) {
                            Swal.fire({
                                heightAuto: false,
                                icon: 'error',
                                title: 'Error',
                                text: 'Solo se permiten archivos de Excel.',
                            });
                            this.value = '';
                            return;
                        }
                        break;
                }

                let totalSize = 0;

                for (let i = 0; i < files.length; i++) {
                    totalSize += files[i].size;
                }

                const totalSizeMB = (totalSize / (1024 * 1024)).toFixed(2);

                if (files.length > maxFiles) {
                    Swal.fire({
                        heightAuto: false,
                        icon: 'error',
                        title: 'Error',
                        text: `Solo puedes seleccionar máximo ${maxFiles} archivo(s).`,
                    });
                    this.value = '';
                    return;
                } else if (totalSizeMB > maxSizeMB) {
                    Swal.fire({
                        heightAuto: false,
                        icon: 'error',
                        title: 'Error',
                        text: `Has excedido el límite de ${maxSizeMB}MB permitidos.`,
                    });
                    this.value = '';
                    return;
                } else {
                    fileSize.textContent = `Tamaño total: ${totalSizeMB}MB de ${maxSizeMB}MB permitidos.`;
                    displayFilesNames(e);
                }

            });

            dropzone.addEventListener('dragover', function (e) {
                e.preventDefault();
                dropzone.classList.add('border-uts-500');
            });

            dropzone.addEventListener('dragleave', function (e) {
                dropzone.classList.remove('border-uts-500');
            });

            dropzone.addEventListener('drop', function (e) {
                e.preventDefault();

                let files = e.dataTransfer.files;
                let filesArray = Array.from(e.dataTransfer.files);

                dropzone.classList.remove('border-uts-500');

                switch (fileType) {
                    case 'pdf':
                        const areAllPDF = filesArray.every(file => file.type === 'application/pdf' ||
                            file.name.toLowerCase().endsWith('.pdf')
                        );

                        if (!areAllPDF) {
                            Swal.fire({
                                heightAuto: false,
                                icon: 'error',
                                title: 'Error',
                                text: 'Solo se permiten archivos PDF.',
                            });
                            return;
                        }
                        break;
                    case 'word':
                        const areAllWord = filesArray.every(file => file.name.toLowerCase().endsWith('.doc') ||
                            file.name.toLowerCase().endsWith('.docx')
                        );

                        if (!areAllWord) {
                            Swal.fire({
                                heightAuto: false,
                                icon: 'error',
                                title: 'Error',
                                text: 'Solo se permiten archivos de Word.',
                            });
                            return;
                        }
                        break;
                    case 'excel':
                        const areAllExcel = filesArray.every(file => file.name.toLowerCase().endsWith('.xls') ||
                            file.name.toLowerCase().endsWith('.xlsx')
                        );

                        if (!areAllExcel) {
                            Swal.fire({
                                heightAuto: false,
                                icon: 'error',
                                title: 'Error',
                                text: 'Solo se permiten archivos de Excel.',
                            });
                            return;
                        }
                        break;
                }

                let totalSize = 0;

                for (let i = 0; i < files.length; i++) {
                    totalSize += files[i].size;
                }

                const totalSizeMB = (totalSize / (1024 * 1024)).toFixed(2);

                if (files.length > maxFiles) {
                    Swal.fire({
                        heightAuto: false,
                        icon: 'error',
                        title: 'Error',
                        text: `Solo puedes seleccionar máximo ${maxFiles} archivo(s).`,
                    });
                    return;
                } else if (totalSizeMB > maxSizeMB) {
                    Swal.fire({
                        heightAuto: false,
                        icon: 'error',
                        title: 'Error',
                        text: `Has excedido el límite de ${maxSizeMB}MB permitidos.`,
                    });
                    return;
                } else {
                    const dataTransfer = new DataTransfer();
                    fileSize.textContent = `Tamaño total: ${totalSizeMB}MB de ${maxSizeMB}MB permitidos.`;

                    filesArray.forEach(file => dataTransfer.items.add(file));
                    input.files = dataTransfer.files;

                    displayFilesNames({
                        target: {
                            files: dataTransfer.files
                        }
                    });
                }
            });
        }

        function displayFilesNames(event) {
            const files = event.target.files;
            fileList.innerHTML = ``;

            if (files.length > 0) {
                for (const file of files) {
                    const listItem = document.createElement('li');
                    listItem.textContent = file.name;
                    listItem.classList.add('text-gray-600', 'text-sm', 'list-disc', 'mb-3');
                    fileList.appendChild(listItem);
                }
            }
        }
    }
}