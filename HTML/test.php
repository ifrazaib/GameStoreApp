// Example using JavaScript to send cart data via POST
const cart = [
    { game_id: 1, title: "Test Game", price: 20, quantity: 1 },
    { game_id: 2, title: "Another Game", price: 30, quantity: 2 }
];

const xhr = new XMLHttpRequest();
xhr.open("POST", "confirm_order.php", true);
xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
xhr.onload = function () {
    if (xhr.status == 200) {
        console.log(xhr.responseText); // Check the response
    }
};
xhr.send("cart_data=" + encodeURIComponent(JSON.stringify(cart)));
