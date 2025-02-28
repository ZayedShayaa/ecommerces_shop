document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".qty_input").forEach(function (container) {
        let quantityInput = container.querySelector(".quantity-input");
        let increaseBtn = container.querySelector(".increase-btn");
        let decreaseBtn = container.querySelector(".decrease-btn");

        increaseBtn.addEventListener("click", function () {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue < 7) {
                quantityInput.value = currentValue + 1;
            }
        });

        decreaseBtn.addEventListener("click", function () {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });
    });
});