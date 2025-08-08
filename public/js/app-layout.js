let isDesktopCollapsed = false;
let resizeTimeout;

function toggleSidebar() {
    const sidebar = document.getElementById('sidebarMenu');
    const overlay = document.getElementById('overlay');
    const isMobile = window.innerWidth <= 768;

    if (isMobile) {
        sidebar.classList.remove('hidden');
        sidebar.classList.toggle('expanded');
        overlay.classList.toggle('active');
    } else {
        const sidebarhead = document.getElementById('sidebar');
        const iconMenu = document.getElementById('iconMenu');
        const logo = document.getElementById('logo');
        const navText = document.querySelectorAll('.nav-text');

        isDesktopCollapsed = !isDesktopCollapsed;
        sidebar.classList.toggle('collapsed');
        sidebarhead.classList.toggle('collapsed');
        iconMenu.classList.toggle('mx-auto');

        if (isDesktopCollapsed) {
            logo.style.opacity = '0';
            setTimeout(() => {
                logo.classList.add('hidden');
            }, 200);
        } else {
            logo.classList.remove('hidden');
            setTimeout(() => {
                logo.style.opacity = '1';
            }, 50);
        }

        navText.forEach(text => {
            if (isDesktopCollapsed) {
                text.style.opacity = '0';
                setTimeout(() => {
                    text.classList.add('hidden');
                }, 200);
            } else {
                text.classList.remove('hidden');
                setTimeout(() => {
                    text.style.opacity = '1';
                }, 50);
            }
        });
    }
}

function handleResize() {
    clearTimeout(resizeTimeout);

    const sidebar = document.getElementById('sidebarMenu');
    const sidebarhead = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const logo = document.getElementById('logo');
    const textWelcome = document.getElementById('text-welcome');
    const navTexts = document.querySelectorAll('.nav-text');
    const iconMenu = document.getElementById('iconMenu');
    const isMobile = window.innerWidth <= 768;

    sidebar.classList.add('transitioning');

    sidebar.classList.remove('expanded');
    overlay.classList.remove('active');

    if (isMobile) {
        sidebar.classList.add('hidden');
        sidebar.style.left = '-256px';
        logo.classList.add('mobile-hidden');
        textWelcome.classList.add('mobile-hidden');

        sidebar.classList.remove('collapsed');
        sidebarhead.classList.add('collapsed');
        iconMenu.classList.remove('mx-auto');

        navTexts.forEach(text => {
            text.classList.remove('hidden');
            text.style.opacity = '1';
        });

        setTimeout(() => {
            sidebar.classList.remove('hidden');
            sidebar.classList.remove('transitioning');
        }, 10);
    } else {
        sidebar.style.left = '0';
        logo.classList.remove('mobile-hidden');
        textWelcome.classList.remove('mobile-hidden');

        if (isDesktopCollapsed) {
            sidebar.classList.add('collapsed');
            sidebarhead.classList.add('collapsed');
            iconMenu.classList.add('mx-auto');
            logo.classList.add('hidden');
            navTexts.forEach(text => {
                text.style.opacity = '0';
                text.classList.add('hidden');
            });
        } else {
            sidebar.classList.remove('collapsed');
            sidebarhead.classList.remove('collapsed');
            iconMenu.classList.remove('mx-auto');
            logo.classList.remove('hidden');
            navTexts.forEach(text => {
                text.classList.remove('hidden');
                text.style.opacity = '1';
            });
        }

        setTimeout(() => {
            sidebar.classList.remove('transitioning');
        }, 10);
    }
}

window.addEventListener('load', handleResize);
window.addEventListener('resize', handleResize);

document.getElementById('overlay').addEventListener('click', () => {
    if (window.innerWidth <= 768) {
        toggleSidebar();
    }
});


// Manejo de errores y peticiones AJAX

$(document).ajaxError(function (event, jqXHR, settings, thrownError) {
    let errorMessage = 'Ha ocurrido un error. Por favor, inténtelo de nuevo.';

    if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
        errorMessage = jqXHR.responseJSON.message;
    }

    Swal.fire({
        heightAuto: false,
        icon: 'error',
        title: 'Oops...',
        text: errorMessage,
    });
});


// Mostrar TOAST

function showToast(message, type = 'success') {
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        icon: type,
        title: message
    });
}