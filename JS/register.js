// Event listener for form submission
document.getElementById("registerForm").addEventListener("submit", function (e) {
    e.preventDefault();
  
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;
  
    if (password !== confirmPassword) {
      alert("Passwords do not match!");
      return;
    }
  
    // Simulated registration action
    alert(`Registration successful!\nWelcome, ${name}!`);
  
    // You can redirect or send data to backend here
    // window.location.href = "login.html";
  });
  // Add four animated corner circles
function addCornerCircles() {
    const formWrapper = document.querySelector('.form-wrapper');

    const corners = [
        { class: 'top-left' },
        { class: 'top-right' },
        { class: 'bottom-left' },
        { class: 'bottom-right' }
    ];

    corners.forEach(corner => {
        const circle = document.createElement('div');
        circle.classList.add('corner-circle', corner.class);
        formWrapper.appendChild(circle);
    });
}

// Call the function after DOM is loaded
addCornerCircles();

  // Animating leaves and flowers
  function createLeaf() {
    const leaf = document.createElement("div");
    leaf.classList.add("leaf");
    document.body.appendChild(leaf);
  
    const startPosition = Math.random() * window.innerWidth;
    leaf.style.left = `${startPosition}px`;
  
    const animationDuration = Math.random() * 3 + 5; // Random duration between 5-8 seconds
    leaf.style.animationDuration = `${animationDuration}s`;
  
    leaf.addEventListener("animationend", () => {
      leaf.remove(); // Remove leaf after animation ends
    });
  }
  
  function createFlower() {
    const flower = document.createElement("div");
    flower.classList.add("flower");
    document.body.appendChild(flower);
  
    const startPosition = Math.random() * window.innerWidth;
    flower.style.left = `${startPosition}px`;
  
    const animationDuration = Math.random() * 3 + 5;
    flower.style.animationDuration = `${animationDuration}s`;
  
    flower.addEventListener("animationend", () => {
      flower.remove(); // Remove flower after animation ends
    });
  }
  
  // Add leaves and flowers at intervals
  setInterval(createLeaf, 2000); // Create a leaf every 2 seconds
  setInterval(createFlower, 3000); // Create a flower every 3 seconds
  

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