// Create animated leaves
document.addEventListener('DOMContentLoaded', function() {
    const leavesContainer = document.getElementById('leaves');
    const numLeaves = 12;
    
    // Create leaf elements
    for (let i = 0; i < numLeaves; i++) {
      const leaf = document.createElement('div');
      leaf.className = 'leaf';
      leaf.style.left = `${Math.random() * 100}%`;
      leaf.style.animationDelay = `${Math.random() * 15}s`;
      leaf.style.animationDuration = `${15 + Math.random() * 10}s`;
      
      // Set random leaf colors according to the treasure theme
      const colors = ['#4CAF50', '#8BC34A', '#FFC107', '#FFD700'];
      const randomColor = colors[Math.floor(Math.random() * colors.length)];
      leaf.style.backgroundColor = randomColor;
      
      // Set random rotation
      leaf.style.transform = `rotate(${Math.random() * 360}deg)`;
      
      // Set random leaf shape (using border radius)
      const shape = Math.floor(Math.random() * 4);
      switch(shape) {
        case 0:
          leaf.style.borderRadius = '50% 0 50% 50%'; // Leaf shape
          break;
        case 1:
          leaf.style.borderRadius = '50%'; // Circle
          break;
        case 2:
          leaf.style.borderRadius = '0'; // Square/diamond (will be rotated)
          break;
        case 3:
          leaf.style.borderRadius = '50% 50% 0 50%'; // Another leaf shape
          break;
      }
      
      leavesContainer.appendChild(leaf);
    }
    
    // Add color adaptation based on time of day
    const hour = new Date().getHours();
    const body = document.body;
    const loginContainer = document.querySelector('.login-container');
    const button = document.querySelector('.login-btn');
    
    // Night mode (6 PM - 6 AM)
    if (hour < 6 || hour >= 18) {
      loginContainer.style.backgroundColor = 'rgba(40, 40, 40, 0.85)';
      document.querySelectorAll('.input-group label').forEach(label => {
        label.style.color = '#e0e0e0';
      });
      document.querySelector('h2').style.color = '#f0f0f0';
      document.querySelector('.register-link').style.color = '#e0e0e0';
    }
  });
  
  // Form submission handler
  document.getElementById('loginForm').addEventListener('submit', function (e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    
    if (email === '' || password === '') {
      showNotification('Please fill in all fields.', 'error');
      return;
    }
    
    // Animated button effect on click
    const button = this.querySelector('button');
    button.classList.add('clicked');
    
    setTimeout(() => {
      button.classList.remove('clicked');
      
      // Login validation
      if (email === 'admin@example.com' && password === '123456') {
        showNotification('Login successful!', 'success');
        
        // Add treasure finding animation
        showTreasureAnimation();
        
        // Redirect after animation
        setTimeout(() => {
          window.location.href = 'home.html';
        }, 2000);
      } else {
        showNotification('Invalid email or password.', 'error');
      }
    }, 300);
  });
  
  // Show notification function
  function showNotification(message, type) {
    // Remove any existing notification
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
      existingNotification.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    // Style the notification
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.left = '50%';
    notification.style.transform = 'translateX(-50%)';
    notification.style.padding = '12px 24px';
    notification.style.borderRadius = '8px';
    notification.style.color = '#fff';
    notification.style.fontSize = '16px';
    notification.style.fontWeight = 'bold';
    notification.style.zIndex = '1000';
    notification.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
    notification.style.animation = 'fadeIn 0.3s ease-out forwards';
    
    if (type === 'error') {
      notification.style.backgroundColor = '#f44336';
    } else if (type === 'success') {
      notification.style.backgroundColor = '#4CAF50';
    }
    
    // Add to DOM
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
      notification.style.animation = 'fadeOut 0.3s ease-out forwards';
      setTimeout(() => {
        notification.remove();
      }, 300);
    }, 3000);
  }
  
  // Create CSS animations for notifications
  const style = document.createElement('style');
  style.textContent = `
    @keyframes fadeIn {
      from { opacity: 0; transform: translate(-50%, -20px); }
      to { opacity: 1; transform: translate(-50%, 0); }
    }
    
    @keyframes fadeOut {
      from { opacity: 1; transform: translate(-50%, 0); }
      to { opacity: 0; transform: translate(-50%, -20px); }
    }
    
    .clicked {
      transform: scale(0.95) !important;
      opacity: 0.9;
    }
  `;
  document.head.appendChild(style);
  
  // Treasure finding animation on successful login
  function showTreasureAnimation() {
    // Create treasure found container
    const treasureContainer = document.createElement('div');
    treasureContainer.style.position = 'fixed';
    treasureContainer.style.top = '0';
    treasureContainer.style.left = '0';
    treasureContainer.style.width = '100%';
    treasureContainer.style.height = '100%';
    treasureContainer.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    treasureContainer.style.display = 'flex';
    treasureContainer.style.justifyContent = 'center';
    treasureContainer.style.alignItems = 'center';
    treasureContainer.style.zIndex = '1000';
    treasureContainer.style.opacity = '0';
    treasureContainer.style.transition = 'opacity 0.5s ease';
    
    // Create treasure message
    const treasureMessage = document.createElement('div');
    treasureMessage.textContent = 'TREASURE FOUND!';
    treasureMessage.style.color = 'gold';
    treasureMessage.style.fontSize = '3rem';
    treasureMessage.style.fontWeight = 'bold';
    treasureMessage.style.textShadow = '0 0 10px rgba(255, 215, 0, 0.8)';
    treasureMessage.style.animation = 'pulse 1s infinite';
    
    treasureContainer.appendChild(treasureMessage);
    document.body.appendChild(treasureContainer);
    
    // Fade in
    setTimeout(() => {
      treasureContainer.style.opacity = '1';
    }, 100);
    
    // Fade out
    setTimeout(() => {
      treasureContainer.style.opacity = '0';
      setTimeout(() => {
        treasureContainer.remove();
      }, 500);
    }, 2000);
  }