# Hardware Inventory System - Setup Guide

## ✅ Database Integration Complete

Your inventory system is **fully integrated with the database**! All add, edit, and delete operations are automatically saved to MySQL.

## 📁 System Architecture

### **Database Table: `hardware_inventory`**
Located in: `database-setup.sql`

```sql
CREATE TABLE hardware_inventory (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  quantity INT NOT NULL DEFAULT 1,
  image LONGBLOB NOT NULL,
  image_type VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### **Backend API: `api.php`**
Handles all database operations:
- ✅ **GET** - Fetch all inventory items from database
- ✅ **POST** - Add new item to database
- ✅ **PUT** - Update quantity in database
- ✅ **PATCH** - Edit item details in database
- ✅ **DELETE** - Remove item from database

### **Frontend Pages**

#### 1. **User Page: `inventory.html`**
- 👁️ **View-only** for public visitors
- Uses: `inventory-view-only.js`
- Shows: Items from database (read-only)

#### 2. **Admin Page: `admin_inventory.html`**
- 🔐 **Full management** for administrators
- Uses: `inventory-script.js`
- Features:
  - ➕ Add new items → Saved to database
  - ✏️ Edit items → Updates database
  - 🗑️ Delete items → Removes from database
  - 🔢 Change quantities → Updates database

## 🚀 How It Works

### Adding New Item (Admin)
1. Admin clicks "Add New Item" button
2. Fills form (name, quantity, description, image)
3. Submits form
4. JavaScript sends **POST** request to `api.php`
5. `api.php` saves to database
6. Success! Item appears on both admin and user pages

### Editing Item (Admin)
1. Admin clicks "Edit" button on item card
2. Updates form fields
3. Submits changes
4. JavaScript sends **PATCH** request to `api.php`
5. `api.php` updates database
6. Success! Changes visible on both pages

### Deleting Item (Admin)
1. Admin clicks "Delete" button
2. Confirms deletion
3. JavaScript sends **DELETE** request to `api.php`
4. `api.php` removes from database
5. Success! Item removed from both pages

### Changing Quantity (Admin)
1. Admin clicks +/- buttons
2. JavaScript sends **PUT** request to `api.php`
3. `api.php` updates quantity in database
4. Success! New quantity displayed

## 📊 Database Setup

### First Time Setup
1. Make sure XAMPP is running (Apache + MySQL)
2. Import the database schema:
   ```bash
   mysql -u root -p < database-setup.sql
   ```
   Or use phpMyAdmin to import `database-setup.sql`

3. Verify table exists:
   ```sql
   USE personal;
   SHOW TABLES;
   DESCRIBE hardware_inventory;
   ```

### Database Configuration
Check `config.php` for database connection settings:
```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "personal";
```

## 🔍 Testing

1. **Test Admin Functions:**
   - Go to `admin_inventory.html`
   - Add a test item
   - Check database: `SELECT * FROM hardware_inventory;`
   - Edit the item
   - Check database again
   - Delete the item
   - Verify it's removed from database

2. **Test User View:**
   - Go to `inventory.html`
   - Should see same items as admin (read-only)
   - No add/edit/delete buttons

## 📝 Notes

- Images are stored as **LONGBLOB** (base64 encoded)
- All changes are **immediately saved** to database
- Both pages fetch **live data** from database
- Admin page requires authentication (via admin dashboard)

## 🎯 Quick Reference

| Action | Admin Page | User Page | Database |
|--------|-----------|-----------|----------|
| View Items | ✅ | ✅ | SELECT |
| Add Item | ✅ | ❌ | INSERT |
| Edit Item | ✅ | ❌ | UPDATE |
| Delete Item | ✅ | ❌ | DELETE |
| Change Qty | ✅ | ❌ | UPDATE |

---

**All operations in the admin panel automatically update the database!** 🎉
