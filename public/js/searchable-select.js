(() => {
    const initializeSearchableSelects = () => {
        document.querySelectorAll('select[data-searchable-select]').forEach((select, index) => {
            if (select.dataset.searchableSelectReady === 'true' || select.multiple) return;

            const parent = select.parentElement;
            const label = select.labels?.[0] || parent.querySelector('label');
            const selectId = select.id || `searchableSelect${index + 1}`;
            const toggleId = `${selectId}Toggle`;
            const menuId = `${selectId}Menu`;
            const searchId = `${selectId}Search`;
            const listId = `${selectId}Options`;
            const placeholder = select.dataset.placeholder
                || select.selectedOptions[0]?.textContent.trim()
                || 'Pilih opsi';
            const wrapper = document.createElement('div');
            wrapper.className = 'searchable-select';

            select.id = selectId;
            select.dataset.searchableSelectReady = 'true';
            select.classList.add('searchable-select-native');
            select.setAttribute('aria-hidden', 'true');
            select.tabIndex = -1;
            parent.insertBefore(wrapper, select);
            wrapper.appendChild(select);

            const toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.id = toggleId;
            toggle.className = 'form-select searchable-select-toggle text-start text-wrap';
            toggle.setAttribute('aria-haspopup', 'listbox');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-controls', listId);
            toggle.setAttribute('aria-label', select.getAttribute('aria-label') || label?.textContent.trim() || placeholder);

            const menu = document.createElement('div');
            menu.id = menuId;
            menu.className = 'searchable-select-menu';
            menu.hidden = true;

            const search = document.createElement('input');
            search.type = 'search';
            search.id = searchId;
            search.className = 'form-control mb-2';
            search.placeholder = select.hasAttribute('data-search-placeholder')
                ? select.dataset.searchPlaceholder
                : 'Ketik untuk mencari...';
            search.setAttribute('role', 'combobox');
            search.setAttribute('aria-autocomplete', 'list');
            search.setAttribute('aria-expanded', 'false');
            search.setAttribute('aria-controls', listId);
            search.autocomplete = 'off';

            const list = document.createElement('div');
            list.id = listId;
            list.className = 'searchable-select-options';
            list.setAttribute('role', 'listbox');
            list.setAttribute('aria-label', toggle.getAttribute('aria-label'));

            const emptyMessage = document.createElement('div');
            emptyMessage.className = 'searchable-select-empty';
            emptyMessage.textContent = 'Tidak ada pilihan yang cocok.';
            emptyMessage.setAttribute('role', 'status');
            emptyMessage.hidden = true;

            const optionButtons = Array.from(select.options, (option, optionIndex) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.id = `${listId}Option${optionIndex}`;
                button.className = 'searchable-select-option';
                button.textContent = option.textContent.trim();
                button.disabled = option.disabled;
                button.setAttribute('role', 'option');
                button.setAttribute('aria-selected', String(option.selected));
                button.dataset.value = option.value;
                button.dataset.search = option.textContent.trim().toLocaleLowerCase('id');
                list.appendChild(button);
                return button;
            });

            menu.append(search, list, emptyMessage);
            wrapper.append(toggle, menu);
            if (label) label.htmlFor = toggleId;

            let matches = [];
            let activeIndex = -1;
            const syncSelection = () => {
                const selectedOption = select.selectedOptions[0];
                toggle.textContent = selectedOption?.textContent.trim() || placeholder;
                optionButtons.forEach((button) => {
                    button.setAttribute('aria-selected', String(button.dataset.value === select.value));
                });
            };
            const highlight = (index) => {
                activeIndex = index;
                optionButtons.forEach((button) => button.classList.remove('is-active'));
                const activeOption = matches[activeIndex];
                if (activeOption) {
                    activeOption.classList.add('is-active');
                    search.setAttribute('aria-activedescendant', activeOption.id);
                    activeOption.scrollIntoView({ block: 'nearest' });
                } else {
                    search.removeAttribute('aria-activedescendant');
                }
            };
            const filterOptions = () => {
                const query = search.value.trim().toLocaleLowerCase('id');
                matches = optionButtons.filter((button) => {
                    button.hidden = !button.dataset.search.includes(query);
                    return !button.hidden && !button.disabled;
                });
                emptyMessage.hidden = matches.length > 0;
                highlight(-1);
            };
            const close = (restoreFocus = false) => {
                menu.hidden = true;
                toggle.setAttribute('aria-expanded', 'false');
                search.setAttribute('aria-expanded', 'false');
                if (restoreFocus) toggle.focus();
            };
            const open = () => {
                search.value = '';
                menu.hidden = false;
                toggle.setAttribute('aria-expanded', 'true');
                search.setAttribute('aria-expanded', 'true');
                filterOptions();
                search.focus();
            };

            toggle.addEventListener('click', () => menu.hidden ? open() : close());
            search.addEventListener('input', filterOptions);
            search.addEventListener('keydown', (event) => {
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                    event.preventDefault();
                    if (matches.length) {
                        const nextIndex = event.key === 'ArrowDown'
                            ? (activeIndex + 1) % matches.length
                            : (activeIndex <= 0 ? matches.length - 1 : activeIndex - 1);
                        highlight(nextIndex);
                    }
                } else if (event.key === 'Enter') {
                    event.preventDefault();
                    const option = matches[activeIndex] || matches[0];
                    if (option) option.click();
                } else if (event.key === 'Escape') {
                    event.preventDefault();
                    close(true);
                }
            });
            optionButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    select.value = button.dataset.value;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    syncSelection();
                    close(true);
                });
            });
            select.addEventListener('change', syncSelection);
            select.addEventListener('invalid', (event) => {
                event.preventDefault();
                open();
            });
            wrapper.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !menu.hidden) {
                    event.preventDefault();
                    close(true);
                }
            });
            document.addEventListener('click', (event) => {
                if (!wrapper.contains(event.target)) close();
            });
            select.form?.addEventListener('reset', () => setTimeout(syncSelection, 0));
            syncSelection();
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeSearchableSelects, { once: true });
    } else {
        initializeSearchableSelects();
    }
})();
