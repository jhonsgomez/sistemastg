function openCreateProyectoModal() {
    $('#createProyectoTitle').html(`Crear nuevo     <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Proyecto</span>`);

    // Limpiar los campos del modal
    $('#periodo_academico').val('');
    $('#codigo_tg').val('');
    $('#nivel').val('');
    $('#estudiante').val('');
    $('#correo').val('');
    $('#documento').val('');
    $('#celular').val('');
    $('#modalidad').val('');
    $('#titulo').val('');
    $('#director').val('');
    $('#evaluador').val('');
    $('#autores').val('');
    $('#inicio_tg').val('');
    $('#aprobacion_propuesta').val('');
    $('#final_tg').val('');

    // Asignar los valores a los campos del modal
    $('#create_periodo_academicoError').text('');
    $('#create_codigo_tgError').text('');
    $('#create_nivelError').text('');
    $('#create_estudianteError').text('');
    $('#create_correoError').text('');
    $('#create_documentoError').text('');
    $('#create_celularError').text('');
    $('#create_modalidadError').text('');
    $('#create_tituloError').text('');
    $('#create_directorError').text('');
    $('#create_evaluadorError').text('');
    $('#create_autoresError').text('');
    $('#create_inicio_tgError').text('');
    $('#create_aprobacion_propuestaError').text('');
    $('#create_final_tgError').text('');

    // Abrir el modal
    $('#createProyectoModal').addClass('show');
}

function openEditProyectoModal(proyecto) {
    $('#editProyectoTitle').html(`Editar <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Proyecto</span>`);
    
    // Cargar los campos del proyecto en el modal
    $('#proyecto_id').val(proyecto.id);
    $('#periodo_academico').val(proyecto.periodo_academico);
    $('#codigo_tg').val(proyecto.codigo_tg);
    $('#nivel').val(proyecto.nivel);
    $('#estudiante').val(proyecto.estudiante);
    $('#correo').val(proyecto.correo);
    $('#documento').val(proyecto.documento);
    $('#celular').val(proyecto.celular);
    $('#modalidad').val(proyecto.modalidad);
    $('#titulo').val(proyecto.titulo);
    $('#director').val(proyecto.director);
    $('#evaluador').val(proyecto.evaluador);
    $('#autores').val(proyecto.autores);
    $('#inicio_tg').val(proyecto.inicio_tg);
    $('#aprobacion_propuesta').val(proyecto.aprobacion_propuesta);
    $('#final_tg').val(proyecto.final_tg);

    // Asignar los valores a los campos del modal
    $('#periodo_academicoError').text('');
    $('#codigo_tgError').text('');
    $('#nivelError').text('');
    $('#estudianteError').text('');
    $('#correoError').text('');
    $('#documentoError').text('');
    $('#celularError').text('');
    $('#modalidadError').text('');
    $('#tituloError').text('');
    $('#directorError').text('');
    $('#evaluadorError').text('');
    $('#autoresError').text('');
    $('#inicio_tgError').text('');
    $('#aprobacion_propuestaError').text('');
    $('#final_tgError').text('');

    // Abrir el modal
    $('#editProyectoModal').addClass('show');
}

function closeCreateProyectoModal() {
    $('#createProyectoModal').removeClass('show');
}

function closeEditProyectoModal() {
    $('#editProyectoModal').removeClass('show');
}

$('#proyectosEditForm').on('submit', function(e) {
    e.preventDefault();

    const button = document.getElementById(`editarProyectoModalButton`);
    const loadingSpinner = document.getElementById(`loadingSpinner-editarProyecto`);

    const proyecto_id = $('#proyecto_id').val();

    const url = `/historico/${proyecto_id}`;
    const method = 'PUT';

    Swal.fire({
        heightAuto: false,
        title: '¿Está seguro?',
        text: "Desea editar este proyecto",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C1D631',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, editar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            loaderGeneral.classList.replace('hidden', 'flex');
            loadingSpinner.classList.remove('hidden');

            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                success: function(response) {
                    $('#historicoTable').DataTable().ajax.reload();
                    closeEditProyectoModal();
                    showToast('Proyecto editado exitosamente');
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON.errors;

                    $('#periodo_academicoError').text(errors?.periodo_academico?.[0] || '');
                    $('#codigo_tgError').text(errors?.codigo_tg?.[0] || '');
                    $('#nivelError').text(errors?.nivel?.[0] || '');
                    $('#estudianteError').text(errors?.estudiante?.[0] || '');
                    $('#correoError').text(errors?.correo?.[0] || '');
                    $('#documentoError').text(errors?.documento?.[0] || '');
                    $('#celularError').text(errors?.celular?.[0] || '');
                    $('#modalidadError').text(errors?.modalidad?.[0] || '');
                    $('#tituloError').text(errors?.titulo?.[0] || '');
                    $('#directorError').text(errors?.director?.[0] || '');
                    $('#evaluadorError').text(errors?.evaluador?.[0] || '');
                    $('#autoresError').text(errors?.autores?.[0] || '');
                    $('#inicio_tgError').text(errors?.inicio_tg?.[0] || '');
                    $('#aprobacion_propuestaError').text(errors?.aprobacion_propuesta?.[0] || '');
                    $('#final_tgError').text(errors?.final_tg?.[0] || '');
                },
                complete: function() {
                    loaderGeneral.classList.replace('flex', 'hidden');
                    loadingSpinner.classList.add('hidden');
                }
            });
        }
    });
});

$('#proyectosCreateForm').on('submit', function(e) {
    e.preventDefault();

    const button = document.getElementById(`crearProyectoModalButton`);
    const loadingSpinner = document.getElementById(`loadingSpinner-crearProyecto`);

    const url = '/historico';
    const method = 'POST';

    Swal.fire({
        heightAuto: false,
        title: '¿Está seguro?',
        text: "Desea crear este proyecto",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C1D631',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, crear',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            loaderGeneral.classList.replace('hidden', 'flex');
            loadingSpinner.classList.remove('hidden');

            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                success: function(response) {
                    $('#historicoTable').DataTable().ajax.reload();
                    closeCreateProyectoModal();
                    showToast('Proyecto creado exitosamente');
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON.errors;

                    $('#create_periodo_academicoError').text(errors?.periodo_academico?.[0] || '');
                    $('#create_codigo_tgError').text(errors?.codigo_tg?.[0] || '');
                    $('#create_nivelError').text(errors?.nivel?.[0] || '');
                    $('#create_estudianteError').text(errors?.estudiante?.[0] || '');
                    $('#create_correoError').text(errors?.correo?.[0] || '');
                    $('#create_documentoError').text(errors?.documento?.[0] || '');
                    $('#create_celularError').text(errors?.celular?.[0] || '');
                    $('#create_modalidadError').text(errors?.modalidad?.[0] || '');
                    $('#create_tituloError').text(errors?.titulo?.[0] || '');
                    $('#create_directorError').text(errors?.director?.[0] || '');
                    $('#create_evaluadorError').text(errors?.evaluador?.[0] || '');
                    $('#create_autoresError').text(errors?.autores?.[0] || '');
                    $('#create_inicio_tgError').text(errors?.inicio_tg?.[0] || '');
                    $('#create_aprobacion_propuestaError').text(errors?.aprobacion_propuesta?.[0] || '');
                    $('#create_final_tgError').text(errors?.final_tg?.[0] || '');
                },
                complete: function() {
                    loaderGeneral.classList.replace('flex', 'hidden');
                    loadingSpinner.classList.add('hidden');
                }
            });
        }
    });
});

function deleteProyecto(id) {
    Swal.fire({
        heightAuto: false,
        title: '¿Está seguro?',
        text: "No podrá revertir esta acción",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C1D631',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/historico/${id}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#historicoTable').DataTable().ajax.reload();
                    showToast('Proyecto eliminado exitosamente');
                },
                error: function(error) {
                    showToast('Error al eliminar el proyecto', 'error');
                }
            });
        }
    });
}


function openCreateMasiveProyectoModal() {
    new fileInput('file_proyectos', 'dropzone_file_proyectos', 'excel', 1, 15, 'file-list-file-proyectos', 'files-size-file-proyectos');
    
    $('#createMasiveProyectoTitle').html(`Crear <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Registro Masivo</span>`);

    // Limpiar los campos del modal
    $('#file_proyectos').val('');
    $('#file-list-file-proyectos').text('');
    $('#files-size-file-proyectos').text('');

    // Asignar los valores a los campos del modal
    $('#file_proyectosError').text('');

    // Abrir el modal
    $('#createMasiveProyectoModal').addClass('show');
}

function closeCreateMasiveProyectoModal() {
    $('#createMasiveProyectoModal').removeClass('show');
}

$('#proyectosMasiveCreateForm').on('submit', function(e) {
    e.preventDefault();

    const button = document.getElementById(`crearMasiveProyectoModalButton`);
    const loadingSpinner = document.getElementById(`loadingSpinner-crearMasiveProyecto`);

    const url = `${window.APP_URL}/historico/masivo`;
    const method = 'POST';

    Swal.fire({
        heightAuto: false,
        title: '¿Está seguro?',
        text: "Desea crear este registro masivo",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C1D631',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, crear',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            loaderGeneral.classList.replace('hidden', 'flex');
            loadingSpinner.classList.remove('hidden');

            $.ajax({
                url: url,
                method: method,
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#historicoTable').DataTable().ajax.reload();
                    closeCreateMasiveProyectoModal();
                    showToast('Registro masivo creado exitosamente');
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON.errors;

                    $('#file_proyectosError').text(errors?.file_proyectos?.[0] || '');
                },
                complete: function() {
                    loaderGeneral.classList.replace('flex', 'hidden');
                    loadingSpinner.classList.add('hidden');
                }
            });
        }
    });
});