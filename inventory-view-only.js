// Set current year
document.getElementById('year').textContent = new Date().getFullYear();

// API endpoint
const API_URL = 'api.php';

// DOM Elements
const inventoryGrid = document.getElementById('inventoryGrid');
const emptyState = document.getElementById('emptyState');

// Initialize
function init() {
  loadInventory();
}

// Load inventory from API
function loadInventory() {
  fetch(API_URL)
    .then(response => response.json())
    .then(items => renderInventory(items))
    .catch(error => {
      console.error('Error loading inventory:', error);
      showEmptyState();
    });
}

// Render inventory grid (view-only, no edit/delete buttons)
function renderInventory(items) {
  inventoryGrid.innerHTML = '';

  if (!items || items.length === 0) {
    showEmptyState();
    return;
  }

  emptyState.style.display = 'none';
  inventoryGrid.style.display = 'grid';

  items.forEach(item => {
    const card = document.createElement('div');
    card.className = 'hardware-card';
    card.innerHTML = `
      <img src="${item.image}" alt="${item.name}" class="hardware-image" />
      <div class="hardware-body">
        <h3 class="hardware-name">${escapeHtml(item.name)}</h3>
        ${item.description ? `<p class="hardware-description">${escapeHtml(item.description)}</p>` : ''}
        <div class="quantity-control">
          <div class="qty-display-only">Quantity: ${item.quantity}</div>
        </div>
      </div>
    `;

    inventoryGrid.appendChild(card);
  });
}

// Show empty state
function showEmptyState() {
  emptyState.style.display = 'block';
  inventoryGrid.style.display = 'none';
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Initialize on page load
init();
