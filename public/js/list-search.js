(() => {
    const initializeListSearch = () => {
        document.querySelectorAll('input[data-list-search]').forEach((input) => {
            const list = document.getElementById(input.dataset.listSearch);
            const emptyMessage = document.getElementById(input.dataset.emptyMessage);
            if (!list || !emptyMessage) return;

            const rows = [...list.querySelectorAll('tr[data-search]')];
            input.addEventListener('input', () => {
                const query = input.value.trim().toLocaleLowerCase('id');
                let visibleCount = 0;

                rows.forEach((row) => {
                    row.hidden = !row.dataset.search.toLocaleLowerCase('id').includes(query);
                    if (!row.hidden) visibleCount++;
                });

                emptyMessage.hidden = query === '' || visibleCount > 0 || rows.length === 0;
            });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeListSearch);
    } else {
        initializeListSearch();
    }
})();
