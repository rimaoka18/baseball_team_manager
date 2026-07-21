<script>
(function () {
    const AUTOCOMPLETE_URL = @json(route('players.autocomplete'));
    let debounceTimer = null;
    let activeList = null;

    function closeList() {
        if (activeList) {
            activeList.remove();
            activeList = null;
        }
    }

    function showSuggestions(input, names) {
        closeList();

        if (names.length === 0) {
            return;
        }

        const rect = input.getBoundingClientRect();
        const list = document.createElement('ul');
        list.className = 'absolute z-50 bg-bf-cream border border-gray-300 rounded shadow-md text-sm max-h-48 overflow-y-auto';
        list.style.top = (rect.bottom + window.scrollY) + 'px';
        list.style.left = (rect.left + window.scrollX) + 'px';
        list.style.width = rect.width + 'px';

        names.forEach(function (name) {
            const item = document.createElement('li');
            item.textContent = name;
            item.className = 'px-2 py-1 hover:bg-bf-gold/20 cursor-pointer';
            item.addEventListener('mousedown', function (e) {
                e.preventDefault();
                input.value = name;
                closeList();
            });
            list.appendChild(item);
        });

        document.body.appendChild(list);
        activeList = list;
    }

    document.addEventListener('input', function (e) {
        const input = e.target;
        if (!(input.matches && input.matches('input[name="player_names[]"]'))) {
            return;
        }

        clearTimeout(debounceTimer);
        const query = input.value.trim();

        if (query.length === 0) {
            closeList();
            return;
        }

        debounceTimer = setTimeout(function () {
            fetch(AUTOCOMPLETE_URL + '?q=' + encodeURIComponent(query))
                .then(function (response) { return response.ok ? response.json() : []; })
                .then(function (names) {
                    if (document.activeElement === input) {
                        showSuggestions(input, names);
                    }
                })
                .catch(function () {});
        }, 200);
    });

    document.addEventListener('focusout', function (e) {
        if (e.target.matches && e.target.matches('input[name="player_names[]"]')) {
            setTimeout(closeList, 150);
        }
    });
})();
</script>
