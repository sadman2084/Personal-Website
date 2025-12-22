// Set current year
document.getElementById('year').textContent = new Date().getFullYear();

// API endpoint
const API_URL = 'api.php';

// DOM Elements
const addItemBtn = document.getElementById('addItemBtn');
const addItemModal = document.getElementById('addItemModal');
const closeModalBtn = document.getElementById('closeModalBtn');
const cancelBtn = document.getElementById('cancelBtn');
const addForm = document.getElementById('addForm');
const inventoryGrid = document.getElementById('inventoryGrid');
const emptyState = document.getElementById('emptyState');
const imageUploadArea = document.getElementById('imageUploadArea');
const itemImage = document.getElementById('itemImage');
const imagePreview = document.getElementById('imagePreview');

// Edit Modal Elements
const editItemModal = document.getElementById('editItemModal');
const closeEditModalBtn = document.getElementById('closeEditModalBtn');
const cancelEditBtn = document.getElementById('cancelEditBtn');
const editForm = document.getElementById('editForm');
const editImageUploadArea = document.getElementById('editImageUploadArea');
const editItemImage = document.getElementById('editItemImage');
const editImagePreview = document.getElementById('editImagePreview');

// Sample data for first load
const sampleData = [
  {
    id: 1,
    name: 'Arduino Uno',
    quantity: 2,
    description: 'Microcontroller board with USB connection',
    image: 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="280" height="180"%3E%3Crect fill="%230b1220" width="280" height="180"/%3E%3Ctext x="50%25" y="50%25" font-size="16" fill="%2394a3b8" text-anchor="middle" dominant-baseline="middle"%3EArduino Uno%3C/text%3E%3C/svg%3E'
  },
  {
    id: 2,
    name: 'Breadboard',
    quantity: 3,
    description: 'Solderless breadboard for prototyping',
    image: 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="280" height="180"%3E%3Crect fill="%230b1220" width="280" height="180"/%3E%3Ctext x="50%25" y="50%25" font-size="16" fill="%2394a3b8" text-anchor="middle" dominant-baseline="middle"%3EBreadboard%3C/text%3E%3C/svg%3E'
  },
  {
    id: 3,
    name: 'LED Lights (Pack of 20)',
    quantity: 1,
    description: 'Assorted color LEDs',
    image: 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="280" height="180"%3E%3Crect fill="%230b1220" width="280" height="180"/%3E%3Ctext x="50%25" y="50%25" font-size="16" fill="%2394a3b8" text-anchor="middle" dominant-baseline="middle"%3ELED Pack%3C/text%3E%3C/svg%3E'
  }
];

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

// Render inventory grid
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
          <button class="qty-btn minus" data-id="${item.id}">−</button>
          <div class="qty-display" data-id="${item.id}">${item.quantity}</div>
          <button class="qty-btn plus" data-id="${item.id}">+</button>
        </div>
        <div class="card-actions">
          <button class="edit-btn" data-id="${item.id}" data-item='${JSON.stringify(item)}'>Edit</button>
          <button class="delete-btn" data-id="${item.id}">Delete</button>
        </div>
      </div>
    `;

    inventoryGrid.appendChild(card);
  });

  // Add event listeners
  document.querySelectorAll('.qty-btn.plus').forEach(btn => {
    btn.addEventListener('click', () => changeQuantity(parseInt(btn.dataset.id), 1));
  });

  document.querySelectorAll('.qty-btn.minus').forEach(btn => {
    btn.addEventListener('click', () => changeQuantity(parseInt(btn.dataset.id), -1));
  });

  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const item = JSON.parse(btn.dataset.item);
      openEditModal(item);
    });
  });

  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', () => deleteItem(parseInt(btn.dataset.id)));
  });
}

// Show empty state
function showEmptyState() {
  emptyState.style.display = 'block';
  inventoryGrid.style.display = 'none';
}

// Change quantity
function changeQuantity(id, delta) {
  fetch(API_URL, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      id: id,
      quantity: delta
    })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        loadInventory();
      } else {
        alert('Error updating quantity: ' + (data.error || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error updating quantity');
    });
}

// Delete item
function deleteItem(id) {
  if (confirm('Are you sure you want to delete this item?')) {
    fetch(API_URL, {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id })
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          loadInventory();
        } else {
          alert('Error deleting item: ' + (data.error || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error deleting item');
      });
  }
}

// Modal controls
addItemBtn.addEventListener('click', () => {
  addItemModal.classList.add('active');
  addForm.reset();
  imagePreview.style.display = 'none';
});

closeModalBtn.addEventListener('click', () => {
  addItemModal.classList.remove('active');
});

cancelBtn.addEventListener('click', () => {
  addItemModal.classList.remove('active');
});

addItemModal.addEventListener('click', e => {
  if (e.target === addItemModal) {
    addItemModal.classList.remove('active');
  }
});

// Edit Modal controls
closeEditModalBtn.addEventListener('click', () => {
  editItemModal.classList.remove('active');
});

cancelEditBtn.addEventListener('click', () => {
  editItemModal.classList.remove('active');
});

editItemModal.addEventListener('click', e => {
  if (e.target === editItemModal) {
    editItemModal.classList.remove('active');
  }
});

// Open edit modal with item data
function openEditModal(item) {
  document.getElementById('editItemId').value = item.id;
  document.getElementById('editItemName').value = item.name;
  document.getElementById('editItemQuantity').value = item.quantity;
  document.getElementById('editItemDescription').value = item.description || '';
  editImagePreview.style.display = 'none';
  editForm.reset();
  editItemImage.value = '';
  editItemModal.classList.add('active');
}

// Image upload preview
imageUploadArea.addEventListener('click', () => itemImage.click());

itemImage.addEventListener('change', e => {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      imagePreview.src = e.target.result;
      imagePreview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
});

// Edit Image upload preview
editImageUploadArea.addEventListener('click', () => editItemImage.click());

editItemImage.addEventListener('change', e => {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      editImagePreview.src = e.target.result;
      editImagePreview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
});

// Drag and drop for image
imageUploadArea.addEventListener('dragover', e => {
  e.preventDefault();
  imageUploadArea.style.borderColor = 'var(--accent)';
});

imageUploadArea.addEventListener('dragleave', () => {
  imageUploadArea.style.borderColor = 'rgba(124, 58, 237, 0.3)';
});

imageUploadArea.addEventListener('drop', e => {
  e.preventDefault();
  const files = e.dataTransfer.files;
  if (files.length > 0) {
    itemImage.files = files;
    const reader = new FileReader();
    reader.onload = e => {
      imagePreview.src = e.target.result;
      imagePreview.style.display = 'block';
    };
    reader.readAsDataURL(files[0]);
  }
});

// Drag and drop for edit image
editImageUploadArea.addEventListener('dragover', e => {
  e.preventDefault();
  editImageUploadArea.style.borderColor = 'var(--accent)';
});

editImageUploadArea.addEventListener('dragleave', () => {
  editImageUploadArea.style.borderColor = 'rgba(124, 58, 237, 0.3)';
});

editImageUploadArea.addEventListener('drop', e => {
  e.preventDefault();
  const files = e.dataTransfer.files;
  if (files.length > 0) {
    editItemImage.files = files;
    const reader = new FileReader();
    reader.onload = e => {
      editImagePreview.src = e.target.result;
      editImagePreview.style.display = 'block';
    };
    reader.readAsDataURL(files[0]);
  }
});

// Form submission
addForm.addEventListener('submit', e => {
  e.preventDefault();

  const name = document.getElementById('itemName').value.trim();
  const quantity = parseInt(document.getElementById('itemQuantity').value);
  const description = document.getElementById('itemDescription').value.trim();
  const imageFile = document.getElementById('itemImage').files[0];

  if (!imageFile) {
    alert('Please upload an image');
    return;
  }

  const reader = new FileReader();
  reader.onload = e => {
    const imageData = e.target.result; // base64 string

    const payload = {
      name: name,
      quantity: quantity,
      description: description,
      image: imageData
    };

    fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          addItemModal.classList.remove('active');
          addForm.reset();
          imagePreview.style.display = 'none';
          loadInventory();
          alert('Item added successfully!');
        } else {
          alert('Error adding item: ' + (data.error || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error adding item');
      });
  };

  reader.readAsDataURL(imageFile);
});

// Edit form submission
editForm.addEventListener('submit', e => {
  e.preventDefault();

  const id = document.getElementById('editItemId').value;
  const name = document.getElementById('editItemName').value.trim();
  const quantity = parseInt(document.getElementById('editItemQuantity').value);
  const description = document.getElementById('editItemDescription').value.trim();
  const imageFile = document.getElementById('editItemImage').files[0];

  const payload = {
    id: parseInt(id),
    name: name,
    quantity: quantity,
    description: description
  };

  // If a new image is selected, add it to payload
  if (imageFile) {
    const reader = new FileReader();
    reader.onload = e => {
      payload.image = e.target.result;
      submitEditForm(payload);
    };
    reader.readAsDataURL(imageFile);
  } else {
    submitEditForm(payload);
  }
});

function submitEditForm(payload) {
  fetch(API_URL, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        editItemModal.classList.remove('active');
        editForm.reset();
        editImagePreview.style.display = 'none';
        loadInventory();
        alert('Item updated successfully!');
      } else {
        alert('Error updating item: ' + (data.error || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error updating item');
    });
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return text.replace(/[&<>"']/g, m => map[m]);
}

// Initialize on load
init();
