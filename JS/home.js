// Toggle menu function
function toggleMenu() {
  const menuContent = document.getElementById('menuContent');
  menuContent.classList.toggle('show');
  
  // Close menu when clicking outside
  if (menuContent.classList.contains('show')) {
      document.addEventListener('click', closeMenuOutside);
  } else {
      document.removeEventListener('click', closeMenuOutside);
  }
}

function closeMenuOutside(event) {
  const menuContent = document.getElementById('menuContent');
  const hamburgerMenu = document.querySelector('.hamburger-menu');
  
  if (!menuContent.contains(event.target) && !hamburgerMenu.contains(event.target)) {
      menuContent.classList.remove('show');
      document.removeEventListener('click', closeMenuOutside);
  }
}

// Existing search and navigation functions
function handleSearch() {
  const searchTerm = document.getElementById('searchInput').value.trim();
  if (searchTerm) {
      document.querySelector('.container').style.animation = 'fadeOut 0.5s ease-out forwards';
      setTimeout(() => {
          window.location.href = `search.php?q=${encodeURIComponent(searchTerm)}`;
      }, 500);
  } else {
      const searchSection = document.querySelector('.search-section');
      searchSection.style.animation = 'none';
      setTimeout(() => {
          searchSection.style.animation = 'shake 0.5s';
      }, 10);
  }
}

function goToPage(page) {
  const button = event.currentTarget;
  button.style.transform = 'scale(0.95)';
  button.style.opacity = '0.8';
  document.querySelector('.container').style.animation = 'fadeOut 0.5s ease-out forwards';
  setTimeout(() => {
      window.location.href = `${page}.html`;
  }, 500);
}

// Initialize event listeners
document.addEventListener('DOMContentLoaded', () => {
  // Search button functionality
  const searchBtn = document.querySelector('.search-btn');
  searchBtn.addEventListener('click', handleSearch);
  
  // Keyboard event for search
  document.getElementById('searchInput').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
          handleSearch();
      }
  });
  
  // Button hover effects
  const buttons = document.querySelectorAll('.button-section button');
  buttons.forEach(button => {
      button.addEventListener('mouseenter', () => {
          button.style.transform = 'translateY(-3px)';
      });
      button.addEventListener('mouseleave', () => {
          button.style.transform = 'translateY(0)';
      });
  });
});
// Menu toggle function
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

// Search function
function handleSearch() {
  const searchTerm = document.getElementById('searchInput').value.trim();
  if (searchTerm) {
      window.location.href = `search.php?q=${encodeURIComponent(searchTerm)}`;
  }
}

// Navigation function
function goToPage(page) {
  window.location.href = `${page}.php`;
}

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
  // Search button
  document.querySelector('.search-btn').addEventListener('click', handleSearch);
  
  // Enter key in search
  document.getElementById('searchInput').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') handleSearch();
  });
});