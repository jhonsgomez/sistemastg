// ==================== FASE 2 - PRÁCTICAS EMPRESARIALES ====================

// Variables globales
let currentFase2DetailsButton = null;

// ==================== MODAL ESTUDIANTE ====================

function openFase2EstudianteModal() {
    const modal = document.getElementById('fase2EstudianteModal');
    if (modal) {
        modal.classList.add('show');  // ← Usar clase .show
        // Limpiar formulario
        const form = document.getElementById('fase2EstudianteForm');
        if (form) form.reset();
        // Limpiar errores
        clearFase2EstudianteErrors();
        // Limpiar listas de archivos
        const listaLiquidacion = document.getElementById('file-list-liquidacion');
        const listaSoporte = document.getElementById('file-list-soporte');
        if (listaLiquidacion) listaLiquidacion.innerHTML = '';
        if (listaSoporte) listaSoporte.innerHTML = '';
    }
}

function closeFase2EstudianteModal() {
    const modal = document.getElementById('fase2EstudianteModal');
    if (modal) {
        modal.classList.remove('show');  // ← Usar clase .show
    }
}

// ==================== MODAL DETALLES ====================

function openFase2DetailsModal(button = null) {
    currentFase2DetailsButton = button;
    const modal = document.getElementById('fase2DetailsModal');
    if (modal) {
        modal.classList.add('show');  // ← Usar clase .show
        loadFase2Details();
    }
}

function closeFase2DetailsModal() {
    const modal = document.getElementById('fase2DetailsModal');
    if (modal) {
        modal.classList.remove('show');  // ← Usar clase .show
    }
}

// ==================== MODAL ADMINISTRADOR ====================

function openFase2AdminModal() {
    const modal = document.getElementById('fase2AdminModal');
    if (modal) {
        modal.classList.add('show');  // ← Usar clase .show
        // Limpiar formulario
        const form = document.getElementById('fase2AdminForm');
        if (form) form.reset();
        // Ocultar contenedor de docentes
        const containerDocentes = document.getElementById('container_docentes_fase2');
        if (containerDocentes) containerDocentes.classList.add('hidden');
        clearFase2AdminErrors();
    }
}

function closeFase2AdminModal() {
    const modal = document.getElementById('fase2AdminModal');
    if (modal) {
        modal.classList.remove('show');  // ← Usar clase .show
    }
}

// ==================== FUNCIONES DE LIMPIEZA ====================

function clearFase2EstudianteErrors() {
    const errorFields = ['liquidacion_pago', 'soporte_pago'];
    errorFields.forEach(field => {
        const errorSpan = document.getElementById(`${field}Error`);
        if (errorSpan) errorSpan.innerHTML = '';
    });
}

function clearFase2AdminErrors() {
    const errorFields = ['nro_acta_fase2', 'fecha_acta_fase2', 'estado_fase2', 'director_id_fase2', 'evaluador_id_fase2', 'respuesta_fase2'];
    errorFields.forEach(field => {
        const errorSpan = document.getElementById(`${field}Error`);
        if (errorSpan) errorSpan.innerHTML = '';
    });
}

// ==================== LOAD DETAILS ====================

async function loadFase2Details() {
    const contentDiv = document.getElementById('fase2DetailsContent');
    if (!contentDiv) return;
    
    contentDiv.innerHTML = `
        <div class="text-center py-4">
            <svg class="inline w-8 h-8 text-gray-400 animate-spin" viewBox="0 0 64 64" fill="none">
                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5"></path>
            </svg>
            <p class="mt-2 text-gray-500">Cargando detalles...</p>
        </div>
    `;
    
    const practicaId = document.querySelector('input[name="practica_id"]')?.value;
    if (!practicaId) return;
    
    try {
        const response = await fetch(ROUTES.fase2_details, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ practica_id: practicaId })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            let html = `
                <div class="space-y-4">
                    <div class="border-b pb-2">
                        <p class="text-sm text-gray-500">Fecha de envío:</p>
                        <p class="font-medium">${data.fecha_envio || 'No disponible'}</p>
                    </div>
                    
                    <div class="border-b pb-2">
                        <p class="text-sm text-gray-500">Liquidación de pago:</p>
                        ${data.liquidacion_url ? 
                            `<a href="${data.liquidacion_url}" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center gap-2 mt-1">
                                <i class="fa-regular fa-file-pdf text-red-500"></i>
                                Ver liquidación
                            </a>` : 
                            '<p class="text-gray-400 italic">No disponible</p>'
                        }
                    </div>
                    
                    <div class="border-b pb-2">
                        <p class="text-sm text-gray-500">Soporte de pago:</p>
                        ${data.soporte_url ? 
                            `<a href="${data.soporte_url}" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center gap-2 mt-1">
                                <i class="fa-regular fa-file-pdf text-red-500"></i>
                                Ver soporte
                            </a>` : 
                            '<p class="text-gray-400 italic">No disponible</p>'
                        }
                    </div>
            `;
            
            if (data.respuesta_comite) {
                html += `
                    <div>
                        <p class="text-sm text-gray-500">Respuesta del comité:</p>
                        <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                            ${data.respuesta_comite}
                        </div>
                    </div>
                `;
            }
            
            html += `</div>`;
            contentDiv.innerHTML = html;
        } else {
            contentDiv.innerHTML = `
                <div class="text-center py-4 text-red-500">
                    <i class="fa-solid fa-circle-exclamation text-2xl mb-2"></i>
                    <p>Error al cargar los detalles</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        contentDiv.innerHTML = `
            <div class="text-center py-4 text-red-500">
                <i class="fa-solid fa-circle-exclamation text-2xl mb-2"></i>
                <p>Error al cargar los detalles</p>
            </div>
        `;
    }
}

// ==================== EVENT LISTENERS ====================

// Mostrar/ocultar campos de docentes según el estado seleccionado
document.addEventListener('DOMContentLoaded', function() {
    const estadoSelect = document.getElementById('estado_fase2');
    if (estadoSelect) {
        estadoSelect.addEventListener('change', function() {
            const container = document.getElementById('container_docentes_fase2');
            if (this.value === 'Aprobada') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        });
    }
    
    // Manejo del formulario de estudiante
    const estudianteForm = document.getElementById('fase2EstudianteForm');
    if (estudianteForm) {
        estudianteForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[type="submit"]');
            const spinner = document.getElementById('loadingSpinner-fase2');
            
            submitButton.disabled = true;
            if (spinner) spinner.classList.remove('hidden');
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch(ROUTES.fase2_store, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Documentos enviados!',
                        text: data.success,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else if (data.errors) {
                    for (const [field, errors] of Object.entries(data.errors)) {
                        const errorSpan = document.getElementById(`${field}Error`);
                        if (errorSpan) errorSpan.innerHTML = errors[0];
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de validación',
                        text: 'Por favor revise los campos marcados',
                        timer: 3000
                    });
                } else {
                    throw new Error(data.error || 'Error al enviar los documentos');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ha ocurrido un error al enviar los documentos'
                });
            } finally {
                submitButton.disabled = false;
                if (spinner) spinner.classList.add('hidden');
            }
        });
    }
    
    // Manejo del formulario de administrador
    const adminForm = document.getElementById('fase2AdminForm');
    if (adminForm) {
        adminForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[type="submit"]');
            const spinner = document.getElementById('loadingSpinner-fase2-admin');
            
            submitButton.disabled = true;
            if (spinner) spinner.classList.remove('hidden');
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch(ROUTES.fase2_reply, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Respuesta enviada!',
                        text: data.success,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else if (data.errors) {
                    for (const [field, errors] of Object.entries(data.errors)) {
                        const errorSpan = document.getElementById(`${field}_fase2Error`);
                        if (errorSpan) errorSpan.innerHTML = errors[0];
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de validación',
                        text: 'Por favor revise los campos marcados'
                    });
                } else {
                    throw new Error(data.error || 'Error al enviar la respuesta');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ha ocurrido un error al enviar la respuesta'
                });
            } finally {
                submitButton.disabled = false;
                if (spinner) spinner.classList.add('hidden');
            }
        });
    }
    
    // Tooltips
    document.querySelectorAll('.tooltip-icon').forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            const tooltipId = this.getAttribute('data-tooltip');
            const tooltip = document.getElementById(tooltipId);
            if (tooltip) tooltip.classList.remove('hidden');
        });
        
        icon.addEventListener('mouseleave', function() {
            const tooltipId = this.getAttribute('data-tooltip');
            const tooltip = document.getElementById(tooltipId);
            if (tooltip) tooltip.classList.add('hidden');
        });
    });
    
    // Mostrar nombre de archivo seleccionado
    const liquidacionInput = document.getElementById('liquidacion_pago');
    if (liquidacionInput) {
        liquidacionInput.addEventListener('change', function() {
            const fileList = document.getElementById('file-list-liquidacion');
            if (fileList) {
                fileList.innerHTML = '';
                if (this.files.length > 0) {
                    const li = document.createElement('li');
                    li.innerHTML = `<i class="fa-regular fa-file-pdf text-red-500 mr-2"></i>${this.files[0].name}`;
                    fileList.appendChild(li);
                }
            }
        });
    }
    
    const soporteInput = document.getElementById('soporte_pago');
    if (soporteInput) {
        soporteInput.addEventListener('change', function() {
            const fileList = document.getElementById('file-list-soporte');
            if (fileList) {
                fileList.innerHTML = '';
                if (this.files.length > 0) {
                    const li = document.createElement('li');
                    li.innerHTML = `<i class="fa-regular fa-file-pdf text-red-500 mr-2"></i>${this.files[0].name}`;
                    fileList.appendChild(li);
                }
            }
        });
    }
});