document.addEventListener('DOMContentLoaded', function() {
    // Confirmation de suppression
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                e.preventDefault();
            }
        });
    });

    // Tri des colonnes
    const tableHeaders = document.querySelectorAll('th a');
    tableHeaders.forEach(header => {
        header.addEventListener('click', function(e) {
            e.preventDefault();
            const url = new URL(this.href);
            const sort = url.searchParams.get('sort');
            const order = url.searchParams.get('order');
            
            // Mise à jour visuelle du tri
            tableHeaders.forEach(h => h.classList.remove('sorted-asc', 'sorted-desc'));
            this.classList.add(order === 'asc' ? 'sorted-asc' : 'sorted-desc');

            // Ici, vous pourriez implémenter un tri côté client ou recharger la page
            // Pour cet exemple, nous rechargeons simplement la page
            window.location.href = this.href;
        });
    });

    // Recherche en temps réel (optionnel, nécessite AJAX)
    const searchInput = document.querySelector('input[name="search"]');
    const searchForm = document.querySelector('.search-filter-bar form');
    let searchTimeout;

    if (searchInput && searchForm) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchForm.submit();
            }, 500); // Délai de 500ms avant de soumettre la recherche
        });
    }
});
