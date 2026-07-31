# Migration Guide - Inventory Management System

This guide will help you migrate your existing files to the new structured architecture.

## 📋 Pre-Migration Checklist

- [x] Folder structure created
- [x] Documentation files created
- [ ] Files moved to appropriate modules
- [ ] Include paths updated
- [ ] Asset paths updated
- [ ] Testing completed

## 🔄 Step-by-Step Migration

### Step 1: Move Authentication Files

**Files to move:**
- `register.php` → `modules/auth/register.php`
- `logout.php` → `modules/auth/logout.php`

**Keep in root:**
- `index.php` (login page - entry point)

**Path updates needed:**
```php
// In modules/auth/register.php and modules/auth/logout.php
// Change:
include "config/database.php";
// To:
include "../../config/database.php";
```

### Step 2: Move Item Management Files

**Files to move:**
- `add_item.php` → `modules/items/add_item.php`
- `edit_item.php` → `modules/items/edit_item.php`
- `delete_item.php` → `modules/items/delete_item.php`
- `itemslist.php` → `modules/items/itemslist.php`
- `category.php` → `modules/items/category.php`

**Path updates needed:**
```php
// Change:
include "config/database.php";
include "sidebar.php";
// To:
include "../../config/database.php";
include "../../includes/sidebar.php";
```

### Step 3: Move Company Files

**Files to move:**
- `add_company.php` → `modules/companies/add_company.php`
- `edit_company.php` → `modules/companies/edit_company.php`
- `delete_company.php` → `modules/companies/delete_company.php`
- `companies.php` → `modules/companies/companies.php`
- `company_items.php` → `modules/companies/company_items.php`
- `add_company_item.php` → `modules/companies/add_company_item.php`
- `edit_company_item.php` → `modules/companies/edit_company_item.php`
- `delete_company_item.php` → `modules/companies/delete_company_item.php`
- `company_bills.php` → `modules/companies/company_bills.php`
- `add_company_bill.php` → `modules/companies/add_company_bill.php`
- `edit_company_bill.php` → `modules/companies/edit_company_bill.php`
- `delete_company_bill.php` → `modules/companies/delete_company_bill.php`

**Path updates needed:**
Same as Step 2 - update config and sidebar includes.

### Step 4: Move Customer Files

**Files to move:**
- `add_customer.php` → `modules/customers/add_customer.php`
- `edit_customer.php` → `modules/customers/edit_customer.php`
- `delete_customer.php` → `modules/customers/delete_customer.php`
- `customers.php` → `modules/customers/customers.php`

**Path updates needed:**
Same as Step 2.

### Step 5: Move Invoice Files

**Files to move:**
- `cart.php` → `modules/invoices/cart.php`
- `invoice.php` → `modules/invoices/invoice.php`
- `invoices.php` → `modules/invoices/invoices.php`
- `invoice_payment.php` → `modules/invoices/invoice_payment.php`

**Path updates needed:**
Same as Step 2.

### Step 6: Move Shared Components

**Files to move:**
- `sidebar.php` → `includes/sidebar.php`

**Path updates needed:**
Update all files that include sidebar.php:
```php
// Change:
include "sidebar.php";
// To:
include "../includes/sidebar.php";  // From root level files
include "../../includes/sidebar.php"; // From module files
```

### Step 7: Update Asset Paths

**Image paths:**
```php
// Change:
<img src="assets/naicon.png">
// To:
<img src="../assets/naicon.png">  // From modules
// Or move to public/images/ and update accordingly
```

### Step 8: Update Navigation Links

**In `includes/sidebar.php` and other navigation files:**

Update all href attributes to point to new locations:
```php
// Change:
<a href="dashboard.php">
<a href="itemslist.php">
<a href="companies.php">
// To:
<a href="../dashboard.php">  // If dashboard stays in root
<a href="modules/items/itemslist.php">  // Or use relative paths
```

## 🔧 Using Path Helpers (Recommended)

Instead of hardcoding paths, use the path helper:

```php
<?php
// At the top of each module file
require_once __DIR__ . '/../../app/helpers/paths.php';

// Then use:
includeDatabase();
includeSidebar();
```

## ✅ Post-Migration Testing

After migration, test the following:

1. **Authentication:**
   - [ ] Login works
   - [ ] Registration works
   - [ ] Logout works

2. **Dashboard:**
   - [ ] Dashboard loads correctly
   - [ ] Statistics display correctly

3. **Items:**
   - [ ] List items
   - [ ] Add item
   - [ ] Edit item
   - [ ] Delete item
   - [ ] Category management

4. **Companies:**
   - [ ] List companies
   - [ ] Add/edit/delete company
   - [ ] Company items
   - [ ] Company bills

5. **Customers:**
   - [ ] List customers
   - [ ] Add/edit/delete customer

6. **Invoices:**
   - [ ] Create invoice (cart)
   - [ ] View invoice
   - [ ] List invoices
   - [ ] Invoice payment

## 🐛 Common Issues

### Issue: "File not found" errors
**Solution:** Check that all include paths are updated correctly. Use relative paths from the file's location.

### Issue: Images not loading
**Solution:** Update image paths to use correct relative paths or move images to `public/images/`.

### Issue: Sidebar not displaying
**Solution:** Ensure sidebar.php is moved to `includes/` and all include paths are updated.

### Issue: Database connection errors
**Solution:** Verify `config/database.php` path is correct (should be `../../config/database.php` from modules).

## 📝 Notes

- Keep `index.php` and `dashboard.php` in root for easier access
- Consider creating a router in the future for cleaner URLs
- Empty folders `Company/` and `customer/` can be deleted
- Test thoroughly after each migration step

## 🚀 Quick Migration Script (Manual)

You can manually move files using your file manager or command line:

```powershell
# Example: Move register.php
Move-Item -Path "register.php" -Destination "modules/auth/register.php"
```

Then update the include paths in each moved file.

---

**Important:** Always backup your files before migration!
