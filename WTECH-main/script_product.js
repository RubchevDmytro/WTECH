function changeImage(imgSrc) {
    document.getElementById("main-image").src = imgSrc;
}

function changeQuantity(amount) {
    let quantityElement = document.getElementById("quantity");
    let quantity = parseInt(quantityElement.innerText);
    
    if (quantity + amount > 0) {
        quantityElement.innerText = quantity + amount;
    }
}

