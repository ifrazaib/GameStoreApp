function toggleMenu() {
    document.getElementById('menuContent').classList.toggle('show');
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const menuContent = document.getElementById('menuContent');
    const hamburger = document.querySelector('.hamburger-menu');
    
    if (!menuContent.contains(event.target) && !hamburger.contains(event.target)) {
        menuContent.classList.remove('show');
    }
});

// Add to cart functionality
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function () {
            const gameId = this.getAttribute('data-game-id');
            alert('Game added to cart!');

            this.textContent = 'Added!';
            this.style.backgroundColor = '#2ecc71';
            setTimeout(() => {
                this.textContent = 'Add to Cart';
                this.style.backgroundColor = '#7B3F00';
            }, 2000);
        });
    });

    // Scroll to highlighted game
    const highlightId = document.body.dataset.highlightId;
    if (highlightId) {
        setTimeout(() => {
            const element = document.getElementById('game-' + highlightId);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 300);
    }
});
