@once
    <style>
        .name-suggestion-picker { position: relative; }
        .name-suggestion-menu { position: absolute; z-index: 1060; top: calc(100% + 4px); left: 0; right: 0; max-height: 220px; overflow-y: auto; padding: 6px; border: 1px solid var(--border, #dee2e6); border-radius: 8px; background: var(--card-bg, #fff); color: var(--text, #2d2d2d); box-shadow: 0 10px 25px rgba(0, 0, 0, .15); }
        .name-suggestion-menu[hidden], .name-suggestion-option[hidden] { display: none; }
        .name-suggestion-option { display: block; width: 100%; padding: 8px 12px; border: 0; border-radius: 6px; background: transparent; color: inherit; text-align: left; font-size: .9rem; cursor: pointer; }
        .name-suggestion-option:hover, .name-suggestion-option:focus { outline: none; background: var(--primary-soft, #f0ece5); color: var(--primary, #303684); }
        body.dark-mode .name-suggestion-menu { background: #1e293b; color: #f8fafc; border-color: rgba(255, 255, 255, .12); box-shadow: 0 10px 25px rgba(0, 0, 0, .5); }
        body.dark-mode .name-suggestion-option:hover, body.dark-mode .name-suggestion-option:focus { background: rgba(212, 185, 141, .18); color: #e2cb9f; }
    </style>
@endonce

<div class="mb-3 name-suggestion-picker" data-name-suggestion-picker>
    <label class="form-label" for="{{ $inputId }}">{{ $label }}</label>
    <div class="position-relative">
        <input
            type="text"
            id="{{ $inputId }}"
            name="{{ $fieldName }}"
            class="form-control"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            maxlength="255"
            autocomplete="off"
            autocorrect="off"
            spellcheck="false"
            aria-expanded="false"
            aria-controls="{{ $inputId }}Menu"
            aria-autocomplete="list"
            role="combobox"
            required>
        <div id="{{ $inputId }}Menu" class="name-suggestion-menu" role="listbox" hidden>
            <input type="search" class="form-control form-control-sm mb-2" data-name-suggestion-filter placeholder="{{ $searchPlaceholder }}" autocomplete="off" aria-label="{{ $searchPlaceholder }}">
            <button type="button" class="btn btn-sm btn-outline-primary w-100 text-start mb-2" data-name-suggestion-add hidden></button>
            <div data-name-suggestion-options style="max-height: 180px; overflow-y: auto;">
                @foreach($suggestions as $suggestion)
                    <button type="button" class="name-suggestion-option" role="option" data-suggestion-value="{{ $suggestion }}">{{ $suggestion }}</button>
                @endforeach
            </div>
            <p class="small text-secondary mb-0 p-1" data-name-suggestion-empty hidden>Tidak ada nama yang cocok.</p>
        </div>
    </div>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-name-suggestion-picker]').forEach(picker => {
                const input = picker.querySelector('[role="combobox"]');
                const menu = picker.querySelector('.name-suggestion-menu');
                const filterInput = picker.querySelector('[data-name-suggestion-filter]');
                const addButton = picker.querySelector('[data-name-suggestion-add]');
                const optionsContainer = picker.querySelector('[data-name-suggestion-options]');
                const emptyMessage = picker.querySelector('[data-name-suggestion-empty]');
                const options = [...optionsContainer.querySelectorAll('[data-suggestion-value]')];
                let suppressFocusOpen = false;

                const filter = () => {
                    const value = filterInput.value.trim();
                    const query = value.toLocaleLowerCase('id');
                    let matchCount = 0;
                    options.forEach(option => {
                        const matches = option.dataset.suggestionValue.toLocaleLowerCase('id').includes(query);
                        option.hidden = !matches;
                        if (matches) matchCount++;
                    });
                    const exactMatch = options.some(option => option.dataset.suggestionValue.toLocaleLowerCase('id') === query);
                    addButton.hidden = !value || exactMatch;
                    addButton.textContent = '+ Gunakan "' + value + '"';
                    emptyMessage.hidden = matchCount > 0;
                };

                const close = () => {
                    menu.hidden = true;
                    input.setAttribute('aria-expanded', 'false');
                };

                const select = value => {
                    input.value = value;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    close();
                    suppressFocusOpen = true;
                    input.focus();
                    suppressFocusOpen = false;
                };

                const open = focusFilter => {
                    filterInput.value = input.value;
                    filter();
                    menu.hidden = false;
                    input.setAttribute('aria-expanded', 'true');
                    if (focusFilter) filterInput.focus();
                };

                input.addEventListener('click', () => open(true));
                input.addEventListener('focus', () => {
                    if (!suppressFocusOpen) open(false);
                });
                input.addEventListener('input', () => {
                    filterInput.value = input.value;
                    filter();
                    menu.hidden = false;
                    input.setAttribute('aria-expanded', 'true');
                });
                input.addEventListener('keydown', event => {
                    if (event.key === 'Escape') close();
                });
                filterInput.addEventListener('input', filter);
                filterInput.addEventListener('keydown', event => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        const firstOption = options.find(option => !option.hidden);
                        if (!addButton.hidden) {
                            select(filterInput.value.trim());
                        } else if (firstOption) {
                            select(firstOption.dataset.suggestionValue);
                        }
                    } else if (event.key === 'Escape') {
                        close();
                        input.focus();
                    }
                });
                addButton.addEventListener('click', () => select(filterInput.value.trim()));
                optionsContainer.addEventListener('click', event => {
                    const option = event.target.closest('[data-suggestion-value]');
                    if (option) select(option.dataset.suggestionValue);
                });
                document.addEventListener('click', event => {
                    if (!picker.contains(event.target)) close();
                });
            });
        });
    </script>
@endonce
