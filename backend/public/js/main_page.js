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
        // Здесь можно добавить AJAX-запрос для загрузки продуктов
    }

    const menuIcon = document.querySelector(".menu-icon");
    const sidebar = document.querySelector(".sidebar");

    menuIcon.addEventListener("click", function () {
        sidebar.classList.toggle("active");
    });
});

const minInput = document.getElementById("min-price");
const maxInput = document.getElementById("max-price");
const slider = document.getElementById("slider");
const minThumb = document.getElementById("min-thumb");
const maxThumb = document.getElementById("max-thumb");
const rangeTrack = document.querySelector(".range-track");

let minValue = parseInt(minInput.value);
let maxValue = parseInt(maxInput.value);
const minLimit = 0;
const maxLimit = 1000;

function updateUI() {
    let minPercent = ((minValue - minLimit) / (maxLimit - minLimit)) * 100;
    let maxPercent = ((maxValue - minLimit) / (maxLimit - minLimit)) * 100;

    minThumb.style.left = minPercent + "%";
    maxThumb.style.left = maxPercent + "%";
    rangeTrack.style.left = minPercent + "%";
    rangeTrack.style.width = (maxPercent - minPercent) + "%";
}

function moveThumb(event, isMin) {
    const rect = slider.getBoundingClientRect();
    const offsetX = event.clientX - rect.left;
    const percent = Math.max(0, Math.min(1, offsetX / rect.width));
    const value = Math.round(minLimit + percent * (maxLimit - minLimit));

    if (isMin) {
        minValue = Math.min(value, maxValue);
        minInput.value = minValue;
    } else {
        maxValue = Math.max(value, minValue);
        maxInput.value = maxValue;
    }

    updateUI();
}

minThumb.addEventListener("mousedown", () => {
    document.onmousemove = (event) => moveThumb(event, true);
    document.onmouseup = () => (document.onmousemove = null);
});

maxThumb.addEventListener("mousedown", () => {
    document.onmousemove = (event) => moveThumb(event, false);
    document.onmouseup = () => (document.onmousemove = null);
});

minInput.addEventListener("input", () => {
    minValue = Math.max(minLimit, Math.min(parseInt(minInput.value), maxValue));
    updateUI();
});

maxInput.addEventListener("input", () => {
    maxValue = Math.min(maxLimit, Math.max(parseInt(maxInput.value), minValue));
    updateUI();
});

updateUI();
