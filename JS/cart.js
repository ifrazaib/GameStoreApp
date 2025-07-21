// cart.js
function toggleMenu() {
    const menuContent = document.getElementById('menuContent');
    menuContent.style.display = menuContent.style.display === 'block' ? 'none' : 'block';
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const menuContent = document.getElementById('menuContent');
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    
    if (!hamburgerMenu.contains(event.target) && !menuContent.contains(event.target)) {
        menuContent.style.display = 'none';
    }
});

// Quantity input validation
document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('change', function() {
        const max = parseInt(this.getAttribute('max'));
        const min = parseInt(this.getAttribute('min'));
        let value = parseInt(this.value);
        
        if (isNaN(value)) value = min;
        if (value < min) value = min;
        if (value > max) value = max;
        
        this.value = value;
    });
});