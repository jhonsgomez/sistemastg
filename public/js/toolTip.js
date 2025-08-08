class TooltipManager {
    constructor(modalSelector = '.modal-content') {
        this.modalSelector = modalSelector;
        this.init();
    }

    init() {
        // Inicializar los tooltips cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', () => {
            this.setupTooltips();
        });
    }

    setupTooltips() {
        // Buscar todos los modales en la página
        const modals = document.querySelectorAll(this.modalSelector);

        modals.forEach(modal => {
            // Configurar los listeners para cada modal
            this.setupModalTooltips(modal);
        });
    }

    setupModalTooltips(modal) {
        modal.addEventListener('click', (event) => {
            const tooltipIcon = event.target.closest('.tooltip-icon');

            if (tooltipIcon) {
                event.stopPropagation();
                const tooltipId = tooltipIcon.dataset.tooltip;
                this.toggleTooltip(tooltipId, modal);
            } else if (!event.target.closest('[id^="tooltip-"]')) {
                this.closeAllTooltips(modal);
            }
        });
    }

    toggleTooltip(tooltipId, context = document) {
        const tooltip = document.getElementById(tooltipId);
        const allTooltips = context.querySelectorAll('[id^="tooltip-"]');

        // Cerrar todos los otros tooltips
        allTooltips.forEach(t => {
            if (t.id !== tooltipId) {
                t.classList.add('hidden');
            }
        });

        // Toggle el tooltip seleccionado
        if (tooltip) {
            tooltip.classList.toggle('hidden');
        }
    }

    closeAllTooltips(context = document) {
        const allTooltips = context.querySelectorAll('[id^="tooltip-"]');
        allTooltips.forEach(tooltip => {
            tooltip.classList.add('hidden');
        });
    }

    // Método público para cerrar tooltips desde fuera de la clase
    static closeTooltips() {
        const allTooltips = document.querySelectorAll('[id^="tooltip-"]');
        allTooltips.forEach(tooltip => {
            tooltip.classList.add('hidden');
        });
    }
}