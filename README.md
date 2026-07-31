# Inventory Management System

A PHP-based inventory management system for tracking items, companies, customers, and invoices.

## 📋 Project Structure

This project follows a modular architecture for better organization and maintainability.

### Key Directories

- **`app/`** - Application core files (controllers, models, helpers)
- **`public/`** - Publicly accessible assets (CSS, JS, images)
- **`includes/`** - Reusable PHP components
- **`modules/`** - Feature-based modules organized by domain
- **`config/`** - Configuration files (database, etc.)

### Module Organization

- **`modules/auth/`** - Authentication (login, register, logout)
- **`modules/items/`** - Inventory items management
- **`modules/companies/`** - Companies/vendors management
- **`modules/customers/`** - Customer management
- **`modules/invoices/`** - Invoice and sales management

## 🚀 Getting Started

### Prerequisites

- PHP 7.0 or higher
- MySQL/MariaDB database
- Apache web server (XAMPP recommended)
- mod_rewrite enabled (for clean URLs)

### Installation

1. Clone or download this repository to your web server directory
   ```
   C:\xampp\htdocs\inventory\
   ```

2. Create a MySQL database named `inventory`

3. Import your database schema (if you have a SQL file)

4. Configure database connection in `config/database.php`:
   ```php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "inventory";
   ```

5. Access the application:
   ```
   http://localhost/inventory/
   ```

## 📁 Current File Organization

### Root Level Files
- `index.php` - Login page (entry point)
- `dashboard.php` - Main dashboard after login
- `error.php` - Error handling page

### Configuration
- `config/database.php` - Database connection settings

### Assets
- `assets/` - Static assets (images, etc.)

## 🔄 Migration to New Structure

The new folder structure has been created. To complete the migration:

1. **Move files to appropriate modules:**
   - Authentication files → `modules/auth/`
   - Item management files → `modules/items/`
   - Company files → `modules/companies/`
   - Customer files → `modules/customers/`
   - Invoice files → `modules/invoices/`

2. **Update include paths:**
   - Change `include "config/database.php"` to `include "../config/database.php"` (from modules)
   - Change `include "sidebar.php"` to `include "../includes/sidebar.php"`

3. **Move shared components:**
   - Move `sidebar.php` to `includes/sidebar.php`

4. **Update asset paths:**
   - Update image paths to point to `public/images/` or `assets/`

## 🛠️ Development

### Using Path Helpers

The `app/helpers/paths.php` file provides helper functions for managing paths:

```php
<?php
require_once '../app/helpers/paths.php';

// Include database
includeDatabase();

// Include sidebar
includeSidebar();
```

### Adding New Features

1. Create new files in the appropriate module directory
2. Use the path helpers for includes
3. Follow the existing code patterns
4. Update the sidebar navigation if needed

## 🔒 Security Notes

- Database credentials should be secured (consider environment variables)
- Input validation is recommended for all user inputs
- Use prepared statements for database queries
- Implement CSRF protection for forms
- Regular security audits recommended

## 📝 Features

- ✅ User authentication (login/register)
- ✅ Dashboard with statistics
- ✅ Inventory items management
- ✅ Category management
- ✅ Company/vendor management
- ✅ Customer management
- ✅ Invoice creation and management
- ✅ Stock tracking
- ✅ Role-based access control

## 📚 Documentation

- `ARCHITECTURE.md` - Detailed architecture documentation
- `STRUCTURE.txt` - Visual folder structure

## 🤝 Contributing

When contributing:
1. Follow the existing folder structure
2. Use the path helpers for includes
3. Maintain code consistency
4. Test thoroughly before submitting

## 📄 License

[Specify your license here]

## 👤 Author

[Your name/team]

---

**Note:** This is a structured version of the inventory management system. Files are currently in the root directory and should be migrated to the new module structure as described in the migration plan.
