// ==================== FUNCIONES DEL MODAL ====================

function openRepublicarModal() {
    $('#republicarTitle').html(`Republicar <span class="bg-uts-500 text-lg text-white font-bold me-2 px-2.5 py-0.5 rounded uppercase shadow">Ideas</span>`);

    // Cargar datos en la tabla
    loadIdeasTable();

    $('#republicarModal').addClass('show');
}

function closeRepublicarModal() {
    $('#republicarModal').removeClass('show');

    // Limpiar la tabla al cerrar
    clearIdeasTable();
}

// ==================== FUNCIONES DE LA TABLA ====================

/**
 * Carga los datos en la tabla de ideas
 */
async function loadIdeasTable() {
    try {
        // Mostrar loader mientras se cargan los datos
        showTableLoader();

        // TODO: Reemplazar con la llamada real al endpoint
        const ideas = await fetchIdeasFromServer();

        // Simular delay de carga (eliminar cuando uses el endpoint real)
        await new Promise(resolve => setTimeout(resolve, 1000));

        renderIdeasTable(ideas);
        initCheckboxListeners();

    } catch (error) {
        showTableError('Error al cargar las ideas. Por favor, intente nuevamente.');
    }
}

/**
 * Llamada al endpoint para obtener las ideas (para implementar después)
 */
async function fetchIdeasFromServer() {
    const response = await fetch(`${window.APP_URL}/banco/ideas-republicar`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    });

    if (!response.ok) {
        throw new Error('Error al obtener las ideas');
    }

    return await response.json();
}

/**
 * Renderiza la tabla con los datos proporcionados
 */
function renderIdeasTable(ideas) {
    const tbody = document.querySelector('.table-ideas-republicar tbody');

    // Limpiar contenido anterior
    tbody.innerHTML = '';

    if (ideas.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-8 text-gray-500">
                    No hay ideas disponibles para republicar.
                </td>
            </tr>
        `;
        return;
    }

    // Generar filas
    ideas.forEach(idea => {
        const row = createIdeaRow(idea);
        tbody.appendChild(row);
    });
}

/**
 * Muestra un skeleton loader en la tabla mientras se cargan los datos
 */
function showTableLoader() {
    const tbody = document.querySelector('.table-ideas-republicar tbody');
    const skeletonRows = 4;

    let skeletonHTML = '';

    for (let i = 0; i < skeletonRows; i++) {
        skeletonHTML += `
            <tr class="animate-pulse">
                <td class="text-center">
                    <div class="h-4 w-4 bg-gray-200 rounded mx-auto"></div>
                </td>
                <td>
                    <div class="h-4 bg-gray-200 rounded w-12"></div>
                </td>
                <td>
                    <div class="h-4 bg-gray-200 rounded w-full"></div>
                    <div class="h-4 bg-gray-200 rounded w-3/4 mt-2"></div>
                </td>
                <td>
                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                </td>
                <td>
                    <div class="h-4 bg-gray-200 rounded w-32"></div>
                </td>
                <td>
                    <div class="h-4 bg-gray-200 rounded w-24"></div>
                </td>
                 <td>
                    <div class="h-4 bg-gray-200 rounded w-12"></div>
                </td>
                 <td>
                    <div class="h-4 bg-gray-200 rounded w-12"></div>
                </td>
            </tr>
        `;
    }

    tbody.innerHTML = skeletonHTML;
}

/**
 * Crea una fila de la tabla para una idea
 */
function createIdeaRow(idea) {
    const tr = document.createElement('tr');
    tr.classList.add('cursor-default');

    tr.innerHTML = `
        <td>
            <input 
                type="checkbox" 
                class="check-item cursor-pointer border-gray-300 rounded-md shadow-sm mt-1 focus:ring-uts-500 focus:border-uts-500 accent-uts-500 text-uts-500"
                data-id="${idea.id}"
                checked
            >
        </td>
        <td>BAN-00${idea.id}</td>
        <td>${idea.titulo}</td>
        <td>${idea.nivel}</td>
        <td>${idea.modalidad}</td>
        <td>${idea.linea_investigacion}</td>
        <td>${idea.docente}</td>
        <td>${idea.periodo}</td>
    `;

    // Evento para marcar/desmarcar el checkbox al hacer clic en la fila
    tr.addEventListener('click', function (e) {
        // Evitar que se dispare si el clic fue directamente en el checkbox
        if (e.target.type === 'checkbox') return;

        const checkbox = tr.querySelector('.check-item');
        checkbox.checked = !checkbox.checked;

        // Actualizar el estado del checkbox principal
        updateCheckAllState();
    });

    return tr;
}

/**
 * Limpia la tabla
 */
function clearIdeasTable() {
    const tbody = document.querySelector('.table-ideas-republicar tbody');
    tbody.innerHTML = '';
}

/**
 * Muestra un mensaje de error en la tabla
 */
function showTableError(message) {
    const tbody = document.querySelector('.table-ideas-republicar tbody');
    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-8 text-red-500">
                <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                ${message}
            </td>
        </tr>
    `;
}

// ==================== LÓGICA DE CHECKBOXES ====================

/**
 * Inicializa los listeners de los checkboxes
 * Se llama cada vez que se renderiza la tabla
 */
function initCheckboxListeners() {
    const checkAll = document.getElementById('checkAll');
    const checkItems = document.querySelectorAll('.check-item');

    // Marcar el checkbox principal como seleccionado (todos vienen checked por defecto)
    checkAll.checked = true;
    checkAll.indeterminate = false;

    // Remover listeners anteriores para evitar duplicados
    checkAll.replaceWith(checkAll.cloneNode(true));
    const newCheckAll = document.getElementById('checkAll');

    // Evento para el checkbox principal
    newCheckAll.addEventListener('change', function () {
        const currentCheckItems = document.querySelectorAll('.check-item');
        currentCheckItems.forEach(function (checkbox) {
            checkbox.checked = newCheckAll.checked;
        });
    });

    // Evento para cada checkbox individual
    checkItems.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            updateCheckAllState();
        });
    });
}

/**
 * Actualiza el estado del checkbox principal según los checkboxes individuales
 */
function updateCheckAllState() {
    const checkAll = document.getElementById('checkAll');
    const totalItems = document.querySelectorAll('.check-item').length;
    const checkedItems = document.querySelectorAll('.check-item:checked').length;

    if (checkedItems === 0) {
        checkAll.checked = false;
        checkAll.indeterminate = false;
    } else if (checkedItems === totalItems) {
        checkAll.checked = true;
        checkAll.indeterminate = false;
    } else {
        checkAll.checked = false;
        checkAll.indeterminate = true;
    }
}

/**
 * Obtiene los IDs de las ideas seleccionadas
 */
function getSelectedIds() {
    const selected = document.querySelectorAll('.check-item:checked');
    return Array.from(selected).map(checkbox => checkbox.dataset.id);
}

// ==================== ACCIONES ====================

/**
 * Maneja el clic en el botón Republicar
 */
async function handleRepublicarClick() {
    const selectedIds = getSelectedIds();

    if (selectedIds.length === 0) {
        // Mostrar alerta de que no hay ideas seleccionadas
        Swal.fire({
            heightAuto: false,
            title: 'No hay ideas',
            text: 'Por favor, seleccione al menos una idea para republicar.',
            icon: 'warning',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#C1D631',
        });
        return;
    }

    const loadingSpinner = document.getElementById(`loadingSpinner-republicar`);
    const url = `${window.APP_URL}/banco/ideas-republicar`;
    const method = 'POST';

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Mostrar una confirmación antes de republicar
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
            loaderGeneral.classList.replace('hidden', 'flex');
            loadingSpinner.classList.remove('hidden');

            $.ajax({
                url: url,
                method: method,
                data: { 'ideas_ids': selectedIds },
                processData: true,
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (response) {
                    // Cerrar el modal
                    closeRepublicarModal();

                    // Recargar la tabla de DataTables
                    $('#bancoTable').DataTable().ajax.reload();

                    // Mostrar mensaje de éxito
                    showToast('Ideas republicadas exitosamente');
                },
                error: function (error) {
                    showToast('Error al republicar las ideas', 'error');
                },
                complete: function () {
                    loaderGeneral.classList.replace('flex', 'hidden');
                    loadingSpinner.classList.add('hidden');
                }
            });
        }
    });
}

// ==================== INICIALIZACIÓN ====================

document.addEventListener('DOMContentLoaded', function () {
    // Listener para el botón de republicar
    const btnRepublicar = document.getElementById('btnRepublicarIdeas');

    if (btnRepublicar) {
        btnRepublicar.addEventListener('click', handleRepublicarClick);
    }
});