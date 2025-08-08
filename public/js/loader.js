function showLoader() {
    const loader = document.getElementById('loaderGeneralOverlay');
    if (loader) {
        loader.classList.replace('hidden', 'flex');
    }
}

function hideLoader() {
    const loader = document.getElementById('loaderGeneralOverlay');
    if (loader) {
        loader.classList.replace('flex', 'hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    showLoader();
});

window.onload = () => {
    setTimeout(() => {
        hideLoader();
    }, 500);
};