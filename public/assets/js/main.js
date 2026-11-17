/* Mise en page uniquement : réserver la hauteur réelle du header pour les ancres. */
(() => {
    const header = document.querySelector('body.site-layout > .site-header');
    if (!header) return;

    const updateHeaderHeight = () => {
        document.documentElement.style.setProperty(
            '--site-header-height', `${Math.ceil(header.getBoundingClientRect().height)}px`
        );
    };

    updateHeaderHeight();
    if ('ResizeObserver' in window) {
        const observer = new ResizeObserver(updateHeaderHeight);
        observer.observe(header);
    } else {
        window.addEventListener('resize', updateHeaderHeight, { passive: true });
    }
})();
