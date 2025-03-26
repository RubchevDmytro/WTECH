function updateTotal() {
    let total = 0;
    document.querySelectorAll('.cart-item').forEach(item => {
        let count = parseInt(item.querySelector('.count').textContent);
        let price = parseInt(item.querySelector('.price').dataset.price);
        total += count * price;
    });
    document.getElementById('total-price').textContent = `$${total}`;
}

document.querySelectorAll('.increase').forEach(button => {
    button.addEventListener('click', () => {
        let countElement = button.previousElementSibling;
        countElement.textContent = parseInt(countElement.textContent) + 1;
        updateTotal();
    });
});

document.querySelectorAll('.decrease').forEach(button => {
    button.addEventListener('click', () => {
        let countElement = button.nextElementSibling;
        let count = parseInt(countElement.textContent);
        if (count > 1) {
            countElement.textContent = count - 1;
            updateTotal();
        }
    });
});

document.querySelectorAll('.remove').forEach(button => {
    button.addEventListener('click', () => {
        button.closest('.cart-item').remove();
        updateTotal();
    });
});

updateTotal();

