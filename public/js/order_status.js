// Add a click event listener to all buttons with class "order-button"
let buttons = document.querySelectorAll('.order-button');
for (var i = 0; i < buttons.length; i++) {
    buttons[i].addEventListener('click', function(event) {
        // Get the order ID from the button's data-order-id attribute
        var orderId = event.target.dataset.orderId;

        // Make an AJAX request to update the order status
        updateOrderStatus(orderId, 'TO_PICK_UP');
    });
}

function updateOrderStatus(orderId, newStatus) {
    // Make an AJAX request to update the order status
    console.log(`Updating order ${orderId} to status ${newStatus}`);
    // ...
}
