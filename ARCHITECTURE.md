# Inventory Management System - Architecture Documentation

## 📁 Folder Structure

```
inventory/
│
├── app/                          # Application core files
│   ├── controllers/              # Business logic controllers (future)
│   ├── models/                   # Data models (future)
│   └── helpers/                  # Helper functions and utilities
│
├── public/                       # Publicly accessible files
│   ├── css/                      # Stylesheets (extracted from inline styles)
│   ├── js/                       # JavaScript files
│   └── images/                   # Image assets
│
├── includes/                     # Reusable PHP includes
│   └── sidebar.php              # Navigation sidebar component
│
├── modules/                      # Feature modules organized by domain
│   ├── auth/                     # Authentication module
│   │   ├── login.php
│   │   ├── register.php
│   │   └── logout.php
│   │
│   ├── items/                    # Inventory items management
│   │   ├── add_item.php
│   │   ├── edit_item.php
│   │   ├── delete_item.php
│   │   ├── itemslist.php
│   │   └── category.php
│   │
│   ├── companies/                # Companies/Vendors management
│   │   ├── add_company.php
│   │   ├── edit_company.php
│   │   ├── delete_company.php
│   │   ├── companies.php
│   │   ├── company_items.php
│   │   ├── add_company_item.php
│   │   ├── edit_company_item.php
│   │   ├── delete_company_item.php
│   │   ├── company_bills.php
│   │   ├── add_company_bill.php
│   │   ├── edit_company_bill.php
│   │   └── delete_company_bill.php
│   │
│   ├── customers/                 # Customer management
│   │   ├── add_customer.php
│   │   ├── edit_customer.php
│   │   ├── delete_customer.php
│   │   └── customers.php
│   │
│   └── invoices/                  # Invoice and sales management
│       ├── cart.php
│       ├── invoice.php
│       ├── invoices.php
│       └── invoice_payment.php
│
├── config/                       # Configuration files
│   └── database.php              # Database connection configuration
│
├── assets/                       # Static assets (legacy - consider moving to public/)
│   └── naicon.png
│
├── index.php                     # Main entry point (login page)
├── dashboard.php                 # Dashboard (main page after login)
├── sidebar.php                   # Sidebar navigation (legacy - move to includes/)
├── error.php                     # Error handling page
├── index2.html                   # Alternative entry point (if needed)
│
└── ARCHITECTURE.md               # This file

```

## 🎯 Architecture Principles

### 1. **Separation of Concerns**
   - **Public files**: Only files that need to be directly accessed via URL
   - **Modules**: Feature-based organization for better maintainability
   - **Includes**: Reusable components shared across modules
   - **Config**: Centralized configuration management

### 2. **Module Organization**
   Each module contains all related files:
   - CRUD operations (Create, Read, Update, Delete)
   - List/View pages
   - Related functionality

### 3. **Security Considerations**
   - Database credentials in `config/database.php` (consider environment variables)
   - Session management for authentication
   - Input validation and prepared statements (to be enhanced)

## 📋 Migration Plan

### Phase 1: Current Structure (Completed)
- ✅ Created folder structure
- ✅ Documented architecture

### Phase 2: File Organization (Recommended)
1. Move authentication files to `modules/auth/`
2. Move item management files to `modules/items/`
3. Move company files to `modules/companies/`
4. Move customer files to `modules/customers/`
5. Move invoice files to `modules/invoices/`
6. Move `sidebar.php` to `includes/sidebar.php`
7. Move assets to `public/images/`

### Phase 3: Code Refactoring (Future)
1. Extract inline CSS to `public/css/`
2. Extract inline JavaScript to `public/js/`
3. Create helper functions in `app/helpers/`
4. Implement MVC pattern with controllers and models
5. Add routing system

## 🔧 Configuration

### Database Configuration
Located in: `config/database.php`
- Update database credentials as needed
- Consider using environment variables for production

### Path Updates Required
After moving files, update all `include` and `require` statements:
- `include "config/database.php"` → `include "../config/database.php"` (from modules)
- `include "sidebar.php"` → `include "../includes/sidebar.php"`

## 📝 Notes

- The `Company/` and `customer/` folders in root are empty and can be removed
- `index.php` serves as the login page (entry point)
- `dashboard.php` is the main application page after authentication
- All modules should include proper authentication checks
- Consider implementing a base controller for common functionality

## 🚀 Next Steps

1. **Update file paths**: Modify all include/require statements to match new structure
2. **Test functionality**: Ensure all features work after reorganization
3. **Extract assets**: Move CSS/JS to public folders
4. **Implement routing**: Consider using a router for cleaner URLs
5. **Add error handling**: Centralize error handling logic
6. **Security enhancements**: Add input validation, CSRF protection, etc.
