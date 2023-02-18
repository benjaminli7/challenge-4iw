import './styles/app.scss';
import 'flowbite';

// client
window.addEventListener('load', function() {
    const addButtons = document.querySelectorAll('.add-to-cart');
    addButtons.forEach(button => {
      button.addEventListener('click', function() {
        const name = button.dataset.name;
        const price = button.dataset.price;
        addToCart(name, price);
        updateCartCount();
      });
    });
  });
  
  function addToCart(name, price) {
    const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
    cartItems.push({ name: name, price: price });
    localStorage.setItem('cartItems', JSON.stringify(cartItems));
  }
  
  function updateCartCount() {
    const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
    const cartCount = document.querySelector('.cart-count');
    cartCount.textContent = cartItems.length;
  }
  