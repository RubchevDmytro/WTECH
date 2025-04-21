document.addEventListener("DOMContentLoaded", function () {
    const pagination = document.getElementById("pagination");
    const productList = document.querySelector(".product-list");

    pagination.addEventListener("click", function (e) {
        if (e.target.tagName === "A" && e.target.dataset.page) {
            e.preventDefault();
            const page = e.target.dataset.page;
            loadProducts(page);
        }
    });

    function loadProducts(page) {
        console.log("Загружаем страницу:", page);
    }

    const menuIcon = document.querySelector(".menu-icon");
    const sidebar = document.querySelector(".sidebar");

    menuIcon.addEventListener("click", function () {
        sidebar.classList.toggle("active");
    });

    // Секция фильтра цен
    const minInput = document.getElementById("min-price");
    const maxInput = document.getElementById("max-price");
    const slider = document.getElementById("slider");
    const minThumb = document.getElementById("min-thumb");
    const maxThumb = document.getElementById("max-thumb");
    const rangeTrack = document.querySelector(".range-track");
    const sliderContainer = document.querySelector(".slider-container");

    // Получаем динамические min и max из атрибутов
    const minLimit = parseFloat(sliderContainer.dataset.min) || 0;
    const maxLimit = parseFloat(sliderContainer.dataset.max) || 1000;

    // Обрабатываем случай, когда minLimit == maxLimit
    const adjustedMaxLimit = (minLimit === maxLimit) ? minLimit + 1 : maxLimit;

    // Парсим значения с учётом запятой
    function parsePrice(value) {
        const parsed = parseFloat(value.replace(',', '.'));
        return isNaN(parsed) ? (value === minInput ? minLimit : maxLimit) : parsed;
    }

    let minValue = parsePrice(minInput.value);
    let maxValue = parsePrice(maxInput.value);

    // Проверяем, чтобы значения были в пределах новых лимитов
    minValue = Math.max(minLimit, minValue);
    maxValue = Math.min(adjustedMaxLimit, maxValue);

    // Форматируем значения для отображения (с запятой)
    function formatPrice(value) {
        return value.toFixed(2).replace('.', ',');
    }

    minInput.value = formatPrice(minValue);
    maxInput.value = formatPrice(maxValue);

    function updateUI() {
        let minPercent = ((minValue - minLimit) / (adjustedMaxLimit - minLimit)) * 100;
        let maxPercent = ((maxValue - minLimit) / (adjustedMaxLimit - minLimit)) * 100;

        minThumb.style.left = minPercent + "%";
        maxThumb.style.left = maxPercent + "%";
        rangeTrack.style.left = minPercent + "%";
        rangeTrack.style.width = (maxPercent - minPercent) + "%";
    }

    function moveThumb(event, isMin) {
        // Поддержка мыши и сенсорных устройств
        const clientX = event.clientX || (event.touches && event.touches[0].clientX);
        if (!clientX) return;

        const rect = slider.getBoundingClientRect();
        const offsetX = clientX - rect.left;
        const percent = Math.max(0, Math.min(1, offsetX / rect.width));
        const value = minLimit + percent * (adjustedMaxLimit - minLimit);

        if (isMin) {
            minValue = Math.min(value, maxValue);
            minInput.value = formatPrice(minValue);
        } else {
            maxValue = Math.max(value, minValue);
            maxInput.value = formatPrice(maxValue);
        }

        updateUI();
    }

    // Поддержка мыши
    minThumb.addEventListener("mousedown", () => {
        minThumb.classList.add("active"); // Визуальная обратная связь
        document.onmousemove = (event) => moveThumb(event, true);
        document.onmouseup = () => {
            document.onmousemove = null;
            minThumb.classList.remove("active");
        };
    });

    maxThumb.addEventListener("mousedown", () => {
        maxThumb.classList.add("active");
        document.onmousemove = (event) => moveThumb(event, false);
        document.onmouseup = () => {
            document.onmousemove = null;
            maxThumb.classList.remove("active");
        };
    });

    // Поддержка сенсорных устройств
    minThumb.addEventListener("touchstart", (event) => {
        event.preventDefault();
        minThumb.classList.add("active");
        document.ontouchmove = (event) => moveThumb(event, true);
        document.ontouchend = () => {
            document.ontouchmove = null;
            minThumb.classList.remove("active");
        };
    });

    maxThumb.addEventListener("touchstart", (event) => {
        event.preventDefault();
        maxThumb.classList.add("active");
        document.ontouchmove = (event) => moveThumb(event, false);
        document.ontouchend = () => {
            document.ontouchmove = null;
            maxThumb.classList.remove("active");
        };
    });

    minInput.addEventListener("input", () => {
        minValue = parsePrice(minInput.value);
        minValue = Math.max(minLimit, Math.min(minValue, maxValue));
        minInput.value = formatPrice(minValue);
        updateUI();
    });

    maxInput.addEventListener("input", () => {
        maxValue = parsePrice(maxInput.value);
        maxValue = Math.min(adjustedMaxLimit, Math.max(maxValue, minValue));
        maxInput.value = formatPrice(maxValue);
        updateUI();
    });

    // Ограничим ввод только на цифры, точку и запятую
    [minInput, maxInput].forEach(input => {
        input.addEventListener("input", function () {
            this.value = this.value.replace(/[^0-9,.]/g, '');
            const parts = this.value.split(/[,|.]/);
            if (parts.length > 2) {
                this.value = parts[0] + ',' + parts.slice(1).join('');
            }
        });
    });

    // Секция автодополнения
    const searchInput = document.getElementById('search-input');
    const suggestionsContainer = document.getElementById('autocomplete-suggestions'); // Исправляем опечатку "angoId"
    let selectedIndex = -1;

    async function fetchSuggestions(query) {
        if (!query) {
            suggestionsContainer.innerHTML = '';
            suggestionsContainer.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`/autocomplete?search=${encodeURIComponent(query)}`);
            const suggestions = await response.json();
            displaySuggestions(suggestions);
        } catch (error) {
            console.error('Error fetching suggestions:', error);
            suggestionsContainer.innerHTML = '';
            suggestionsContainer.style.display = 'none';
        }
    }

    function displaySuggestions(suggestions) {
        suggestionsContainer.innerHTML = '';
        if (suggestions.length === 0) {
            suggestionsContainer.style.display = 'none';
            return;
        }

        suggestions.forEach((suggestion, index) => {
            const div = document.createElement('div');
            div.classList.add('suggestion-item');
            div.textContent = suggestion;
            div.addEventListener('click', () => {
                searchInput.value = suggestion;
                suggestionsContainer.innerHTML = '';
                suggestionsContainer.style.display = 'none';
                searchInput.closest('form').submit();
            });
            suggestionsContainer.appendChild(div);
        });

        suggestionsContainer.style.display = 'block';
        selectedIndex = -1;
    }

    searchInput.addEventListener('input', function () {
        const query = this.value.trim();
        fetchSuggestions(query);
    });

    searchInput.addEventListener('keydown', function (e) {
        const suggestionItems = suggestionsContainer.getElementsByClassName('suggestion-item');

        if (suggestionItems.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, suggestionItems.length - 1);
            updateSelection(suggestionItems);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateSelection(suggestionItems);
        } else if (e.key === 'Enter' && selectedIndex >= 0) {
            e.preventDefault();
            searchInput.value = suggestionItems[selectedIndex].textContent;
            suggestionsContainer.innerHTML = '';
            suggestionsContainer.style.display = 'none';
            searchInput.closest('form').submit();
        } else if (e.key === 'Escape') {
            suggestionsContainer.innerHTML = '';
            suggestionsContainer.style.display = 'none';
            selectedIndex = -1;
        }
    });

    function updateSelection(suggestionItems) {
        for (let i = 0; i < suggestionItems.length; i++) {
            if (i === selectedIndex) {
                suggestionItems[i].classList.add('selected');
                suggestionItems[i].scrollIntoView({ block: 'nearest' });
            } else {
                suggestionItems[i].classList.remove('selected');
            }
        }
    }

    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
            suggestionsContainer.innerHTML = '';
            suggestionsContainer.style.display = 'none';
            selectedIndex = -1;
        }
    });

    updateUI();
});
