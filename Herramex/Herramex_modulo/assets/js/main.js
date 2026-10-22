document.addEventListener('DOMContentLoaded', function () {
    const paymentMethodSelect = document.querySelector('[data-payment-method-select]');
    const cardFields = document.querySelector('[data-card-fields]');
    if (paymentMethodSelect && cardFields) {
        const cardInputs = cardFields.querySelectorAll('[data-card-required]');
        const updateCardFields = function () {
            const selectedOption = paymentMethodSelect.options[paymentMethodSelect.selectedIndex];
            const requiresCard = selectedOption && selectedOption.dataset.requiresCard === 'true';
            cardFields.hidden = !requiresCard;
            cardInputs.forEach(function (input) {
                input.required = Boolean(requiresCard);
                if (!requiresCard) input.value = '';
            });
        };

        paymentMethodSelect.addEventListener('change', updateCardFields);
        updateCardFields();
    }

    const triggerButtons = document.querySelectorAll('[data-quick-add]');
    triggerButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const productId = button.dataset.quickAdd;
            if (!productId) return;

            fetch('carrito.php?action=add&id=' + encodeURIComponent(productId), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(function (response) {
                if (!response.ok) {
                    throw new Error('Error al agregar producto');
                }
                return response.text();
            }).then(function () {
                window.location.href = 'carrito.php';
            }).catch(function () {
                alert('No se pudo agregar el producto al carrito.');
            });
        });
    });
});
