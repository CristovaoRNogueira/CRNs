document.addEventListener('DOMContentLoaded', function() {
    
    // Elementos da Gaveta
    const mobileTrigger = document.getElementById('mobile-menu-trigger');
    const drawer = document.getElementById('mobile-drawer');
    const overlay = document.getElementById('drawer-overlay');
    const closeDrawerBtn = document.getElementById('close-drawer');

    // Elementos da Busca
    const searchBtn = document.getElementById('search-toggle-btn');
    const searchBar = document.getElementById('search-expanded-bar');
    const closeSearchBtn = document.getElementById('close-search-btn');
    const searchInput = document.querySelector('.search-input-field');

    // --- FUNÇÕES AUXILIARES ---

    function closeDrawer() {
        if(drawer && overlay && drawer.classList.contains('open')) {
            drawer.classList.remove('open');
            overlay.classList.remove('open');
            document.body.style.overflow = ''; // Libera o scroll
        }
    }

    function closeSearch() {
        if(searchBar && searchBar.classList.contains('active')) {
            searchBar.classList.remove('active');
        }
    }

    // --- 1. LÓGICA DA GAVETA ---
    function toggleDrawer() {
        if(drawer && overlay) {
            // Se estiver abrindo a gaveta, garanta que a busca feche
            if (!drawer.classList.contains('open')) {
                closeSearch();
            }

            const isOpen = drawer.classList.toggle('open');
            overlay.classList.toggle('open');
            document.body.style.overflow = isOpen ? 'hidden' : '';
        }
    }

    if(mobileTrigger) mobileTrigger.addEventListener('click', toggleDrawer);
    if(closeDrawerBtn) closeDrawerBtn.addEventListener('click', toggleDrawer);
    if(overlay) overlay.addEventListener('click', toggleDrawer);

    // --- 2. LÓGICA DA BUSCA ---
    if(searchBtn && searchBar) {
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Se estiver abrindo a busca, garanta que o menu feche
            if (!searchBar.classList.contains('active')) {
                closeDrawer();
            }
            
            searchBar.classList.add('active');
            
            if(searchInput) {
                setTimeout(() => searchInput.focus(), 100);
            }
        });
    }

    if(closeSearchBtn && searchBar) {
        closeSearchBtn.addEventListener('click', closeSearch);
    }

    // --- 3. FECHAR COM A TECLA ESC (Melhoria de UX) ---
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDrawer();
            closeSearch();
        }
    });
});