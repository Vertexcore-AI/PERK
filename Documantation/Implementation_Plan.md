# Implementation Plan
## Auto Parts Inventory and Sales Management System
**Version 1.0**
**Prepared by: Claude Code Assistant**
**Date: September 13, 2025**
**Context References**:
- `@.claude\Developer_Documentation.markdown` - Technical specifications and database schema
- `@.claude\PRD_Document.markdown` - Business requirements and system scope
- `@.claude\Workflow_Documentation.markdown` - Operational workflows and processes

---

## 1. Overview

### 1.1 Purpose
This Implementation Plan provides step-by-step instructions for building the Laravel-based Auto Parts Inventory and Sales Management System. It follows a modular approach starting with simple CRUD operations and progressing to complex business logic modules.

### 1.2 Architecture Approach
- **Single-user system** (no authentication initially)
- **Laravel 12** with PHP 8.2+, SQLite database, Vite + Tailwind CSS 4
- **Service Layer Pattern** for complex business logic
- **Resource Controllers** for simple CRUD operations
- **Incremental development** from foundation to advanced features

### 1.3 Implementation Priority
1. **Foundation** (Database, Models, Basic CRUD)
2. **Inventory Management** (GRN, Stock Updates)
3. **Sales Processing** (POS, Basic Sales)
4. **Returns & Quotations**
5. **Advanced Features** (Serialization, Reporting)

---

## 2. Phase 0: UI Components and Theme Setup

### 2.0.1 Icon System Setup
Replace Feather icons with **Lucide icons** throughout the application:
```bash
# Install Lucide icons
npm install lucide
```

### 2.0.2 Basic Reusable Blade Components (Create First)
Create core UI components using **Tailwind CSS** design system:
```bash
# Basic UI Components - Create these first
php artisan make:component UI/Card
php artisan make:component UI/Button
php artisan make:component UI/Badge
php artisan make:component UI/Alert
php artisan make:component UI/Modal
php artisan make:component Forms/Input
php artisan make:component Forms/Select
php artisan make:component Forms/Textarea
php artisan make:component DataTable
```

### 2.0.3 Updated Navigation Structure
Update `resources/views/components/navbar.blade.php` with auto parts specific menu:
- **Inventory Management** (Package icon) - GRN Entry, Stock Overview, Items Registry
- **Sales & POS** (ShoppingCart icon) - Point of Sale, Sales History, Returns
- **Customers** (Users icon) - Customer List, Customer Types
- **Quotations** (FileText icon) - Create Quote, Quote Management, Convert to Sale
- **Reports** (BarChart icon) - Stock Reports, Sales Analytics, Financial Reports
- **Settings** (Settings icon) - Vendors, Categories, Stores & Bins

### 2.0.4 Component Features & Tailwind Theme
- **Tailwind CSS 4** - Modern utility-first CSS framework
- **Dark Mode Support** - Built-in dark mode using Tailwind's dark: modifier
- **Lucide Icons** - Consistent icon library throughout
- **Responsive Design** - Mobile-first approach with Tailwind breakpoints (sm:, md:, lg:, xl:)
- **Component Classes**:
  - Cards: `card` with `border-slate-200 dark:border-slate-700`
  - Buttons: `btn-primary`, `btn-secondary`, `btn-icon`
  - Badges: `badge badge-success`, `badge-warning`, etc.
  - Forms: Custom form components with Tailwind styling
  - Tables: `table-modern` with proper dark mode support
- **Color Palette**:
  - Primary: blue/indigo shades
  - Slate: grays for UI elements
  - Success: emerald
  - Danger: rose/red
  - Warning: amber/yellow

---

## 3. Phase 1: Foundation Setup

### 3.1 Database Migrations (Create in this order)

#### 3.1.1 Basic Entity Tables
```bash
php artisan make:migration create_vendors_table
php artisan make:migration create_categories_table
php artisan make:migration create_stores_table
php artisan make:migration create_bins_table
php artisan make:migration create_customers_table
```

#### 3.1.2 Core Item Tables
```bash
php artisan make:migration create_items_table
php artisan make:migration create_vendor_item_mappings_table
php artisan make:migration create_batches_table
php artisan make:migration create_serial_items_table
```

#### 2.1.3 System Tables
```bash
php artisan make:migration create_audit_logs_table
# Users table can be added later when authentication is needed
```

### 2.2 Eloquent Models (Create with relationships)

#### 2.2.1 Basic Models
- `Vendor.php` - hasMany(VendorItemMapping, GRN)
- `Category.php` - hasMany(Item)
- `Store.php` - hasMany(Bin, InventoryStock)
- `Bin.php` - belongsTo(Store), hasMany(InventoryStock)
- `Customer.php` - hasMany(Sale, Quotation)

#### 2.2.2 Core Item Models
- `Item.php` - belongsTo(Category), hasMany(VendorItemMapping, Batch, InventoryStock)
- `VendorItemMapping.php` - belongsTo(Vendor, Item)
- `Batch.php` - belongsTo(Item, Vendor), hasMany(SerialItem, InventoryStock)
- `SerialItem.php` - belongsTo(Batch)

#### 2.2.3 System Models
- `AuditLog.php` - for tracking all system changes

### 2.3 Simple CRUD Controllers (No Services Initially)

#### 2.3.1 Resource Controllers
```bash
php artisan make:controller VendorController --resource
php artisan make:controller CategoryController --resource
php artisan make:controller StoreController --resource
php artisan make:controller CustomerController --resource
php artisan make:controller ItemController --resource
```

#### 2.3.2 Form Requests
```bash
php artisan make:request StoreVendorRequest
php artisan make:request UpdateVendorRequest
php artisan make:request StoreCategoryRequest
php artisan make:request UpdateCategoryRequest
php artisan make:request StoreStoreRequest
php artisan make:request UpdateStoreRequest
php artisan make:request StoreCustomerRequest
php artisan make:request UpdateCustomerRequest
php artisan make:request StoreItemRequest
php artisan make:request UpdateItemRequest
```

### 2.4 Basic Views Structure

#### 2.4.1 Layout Files
- `resources/views/layouts/app.blade.php` - Main layout
- `resources/views/components/navbar.blade.php` - Navigation
- `resources/views/components/footer.blade.php` - Footer

#### 2.4.2 CRUD View Folders
```
resources/views/vendors/
├── index.blade.php
├── create.blade.php
└── edit.blade.php

resources/views/categories/
├── index.blade.php
├── create.blade.php
└── edit.blade.php

resources/views/stores/
├── index.blade.php
├── create.blade.php
└── edit.blade.php

resources/views/customers/
├── index.blade.php
├── create.blade.php
└── edit.blade.php

resources/views/items/
├── index.blade.php
├── create.blade.phpleft of phase 1 
└── edit.blade.php
```

### 2.5 Routes Configuration
```php
// web.php - No authentication middleware initially
Route::resource('vendors', VendorController::class);
Route::resource('categories', CategoryController::class);
Route::resource('stores', StoreController::class);
Route::resource('customers', CustomerController::class);
Route::resource('items', ItemController::class);
```

---

## 4. Phase 2: Inventory Management Module

### 4.1 Additional Database Migrations
```bash
php artisan make:migration create_grns_table
php artisan make:migration create_grn_items_table
php artisan make:migration create_inventory_stock_table
```

### 4.2 Inventory-Specific Components (Create When Needed)
```bash
# Create these components when implementing GRN and inventory features
php artisan make:component AutoParts/GRNItemRow
php artisan make:component AutoParts/StockCard
php artisan make:component AutoParts/BatchSelector
php artisan make:component AutoParts/VendorItemMapper
```

### 4.3 Inventory Models
- `GRN.php` - belongsTo(Vendor), hasMany(GRNItem)
- `GRNItem.php` - belongsTo(GRN, Item, Batch)
- `InventoryStock.php` - belongsTo(Item, Store, Bin, Batch)

### 3.3 Service Classes (Complex Business Logic)
```bash
php artisan make:class Services/GRNService
php artisan make:class Services/InventoryService
php artisan make:class Services/BatchService
php artisan make:class Services/AuditService
```

#### 3.3.1 GRNService Methods
- `processGRN($grnData)` - Main GRN processing workflow
- `resolveVendorItemMapping($vendorId, $vendorItemCode)` - Map vendor codes to items
- `createBatch($itemId, $vendorId, $cost, $quantity)` - Create new batch
- `calculateCosts($unitPrice, $discount, $vat, $quantity)` - Cost calculations
- `validateGRN($grnData)` - GRN data validation

#### 3.3.2 InventoryService Methods
- `updateStock($itemId, $storeId, $binId, $batchId, $quantity)` - Stock updates
- `checkStockAvailability($itemId, $quantity)` - Stock availability check
- `getStockByItem($itemId)` - Get current stock levels
- `transferStock($fromLocation, $toLocation, $itemId, $quantity)` - Stock transfers
- `generateLowStockAlert()` - Low stock notifications

### 3.4 Controllers with Services
```bash
php artisan make:controller GRNController
php artisan make:controller InventoryController
```

### 3.5 GRN Views
```
resources/views/grns/
├── index.blade.php - GRN listing
├── create.blade.php - GRN entry form
├── show.blade.php - GRN details
└── edit.blade.php - GRN editing
```

### 3.6 Inventory Views
```
resources/views/inventory/
├── index.blade.php - Stock overview
├── stock-by-item.blade.php - Item-wise stock
├── low-stock.blade.php - Low stock alerts
└── stock-transfer.blade.php - Stock transfer form
```

---

## 5. Phase 3: Sales Management Module

### 5.1 Sales Database Migrations
```bash
php artisan make:migration create_sales_table
php artisan make:migration create_sale_items_table
```

### 5.2 Sales-Specific Components (Create When Needed)
```bash
# Create these components when implementing POS and sales features
php artisan make:component AutoParts/POSCart
php artisan make:component AutoParts/CustomerSelector
php artisan make:component AutoParts/PaymentForm
php artisan make:component AutoParts/SalesReceipt
```

### 5.3 Sales Models
- `Sale.php` - belongsTo(Customer), hasMany(SaleItem), hasOne(Invoice)
- `SaleItem.php` - belongsTo(Sale, Item, Batch)

### 4.3 Sales Service Classes
```bash
php artisan make:class Services/SalesService
php artisan make:class Services/CustomerService
```

#### 4.3.1 SalesService Methods
- `processSale($saleData)` - Main sales processing workflow
- `selectBatchFIFO($itemId, $quantity)` - FIFO batch selection
- `calculateSaleTotal($items)` - Sales total calculation
- `deductStock($saleItems)` - Stock deduction after sale
- `validateSale($saleData)` - Sales data validation
- `generateReceipt($saleId)` - Receipt generation

#### 4.3.2 CustomerService Methods
- `createCustomer($customerData)` - Customer creation
- `searchCustomers($searchTerm)` - Customer search
- `getCustomerHistory($customerId)` - Customer transaction history
- `updateCustomerType($customerId, $type)` - Update customer type

### 4.4 Sales Controllers
```bash
php artisan make:controller SalesController
php artisan make:controller POSController
```

### 4.5 Sales Views
```
resources/views/sales/
├── index.blade.php - Sales listing
├── show.blade.php - Sale details
└── receipt.blade.php - Sale receipt

resources/views/pos/
├── index.blade.php - POS interface
├── cart.blade.php - Shopping cart component
└── payment.blade.php - Payment processing
```

---

## 6. Phase 4: Returns and Quotations Module

### 6.1 Returns Database Migrations
```bash
php artisan make:migration create_returns_table
php artisan make:migration create_return_items_table
```

### 6.2 Returns & Quotations Components (Create When Needed)
```bash
# Create these components when implementing returns and quotations
php artisan make:component AutoParts/ReturnForm
php artisan make:component AutoParts/QuotationBuilder
php artisan make:component AutoParts/QuoteItemRow
php artisan make:component AutoParts/QuoteConverter
```

### 6.3 Quotations Database Migrations
```bash
php artisan make:migration create_quotations_table
php artisan make:migration create_quote_items_table
```

### 5.3 Returns and Quotations Models
- `Return.php` - belongsTo(Sale), hasMany(ReturnItem)
- `ReturnItem.php` - belongsTo(Return, Item, Batch)
- `Quotation.php` - belongsTo(Customer), hasMany(QuoteItem)
- `QuoteItem.php` - belongsTo(Quotation, Item)

### 5.4 Service Classes
```bash
php artisan make:class Services/ReturnService
php artisan make:class Services/QuotationService
```

#### 5.4.1 ReturnService Methods
- `processReturn($returnData)` - Main return processing
- `restoreStock($returnItems)` - Stock restoration
- `calculateRefund($returnItems)` - Refund calculation
- `generateCreditNote($returnId)` - Credit note generation

#### 5.4.2 QuotationService Methods
- `createQuotation($quoteData)` - Quotation creation
- `convertToSale($quoteId)` - Convert quote to sale
- `calculateQuoteTotal($items)` - Quote total calculation
- `checkQuoteValidity($quoteId)` - Validity check

### 5.5 Controllers
```bash
php artisan make:controller ReturnController
php artisan make:controller QuotationController
```

---

## 7. Phase 5: Invoice and Document Management

### 7.1 Invoice Database Migration
```bash
php artisan make:migration create_invoices_table
```

### 7.2 Invoice Components (Create When Needed)
```bash
# Create these components when implementing invoicing
php artisan make:component AutoParts/InvoiceTemplate
php artisan make:component AutoParts/InvoiceBuilder
php artisan make:component AutoParts/PaymentTracker
```

### 7.3 Invoice Model and Service
- `Invoice.php` - belongsTo(Sale, Quotation)

```bash
php artisan make:class Services/InvoiceService
php artisan make:class Services/NotificationService
```

#### 6.2.1 InvoiceService Methods
- `generateInvoice($saleId, $quoteId = null)` - Invoice creation
- `calculateTaxes($invoiceItems)` - Tax calculations
- `generateInvoiceNumber()` - Invoice numbering
- `markAsPaid($invoiceId, $amount)` - Payment tracking

#### 6.2.2 NotificationService Methods
- `sendInvoiceEmail($invoiceId, $email)` - Email delivery
- `sendInvoiceSMS($invoiceId, $phone)` - SMS delivery
- `queueNotification($type, $data)` - Queue notifications

### 6.3 Invoice Controller and Views
```bash
php artisan make:controller InvoiceController
```

---

## 8. Phase 6: Advanced Features

### 8.1 Advanced Components (Create When Needed)
```bash
# Create these components for advanced features
php artisan make:component AutoParts/BarcodeScanner
php artisan make:component AutoParts/SerialTracker
php artisan make:component Reports/StockReport
php artisan make:component Reports/SalesChart
php artisan make:component Settings/StoreManager
```

### 8.2 Serialization Support
```bash
php artisan make:class Services/SerializationService
```

#### 8.2.1 SerializationService Methods
- `generateSerialNumbers($batchId, $quantity)` - Serial generation
- `updateSerialStatus($serialNo, $status)` - Status updates
- `validateBarcode($barcode)` - Barcode validation
- `searchBySerial($serialNo)` - Serial lookup

### 7.2 Reporting Module
```bash
php artisan make:class Services/ReportService
php artisan make:controller ReportController
```

#### 7.2.1 ReportService Methods
- `generateStockReport($filters)` - Stock reports
- `generateSalesReport($dateRange)` - Sales reports
- `generateFinancialReport($period)` - Financial reports
- `generateVendorReport($vendorId)` - Vendor reports

### 7.3 Multi-Store Support
```bash
php artisan make:class Services/StoreService
```

#### 7.3.1 StoreService Methods
- `transferBetweenStores($fromStore, $toStore, $items)` - Inter-store transfers
- `getStoreInventory($storeId)` - Store-specific inventory
- `allocateStock($storeId, $items)` - Stock allocation

---

## 9. Database Seeders

### 9.1 Create Seeders
```bash
php artisan make:seeder VendorSeeder
php artisan make:seeder CategorySeeder
php artisan make:seeder StoreSeeder
php artisan make:seeder ItemSeeder
php artisan make:seeder CustomerSeeder
```

### 9.2 Sample Data Requirements
- **10 Vendors** - Auto parts suppliers
- **20 Categories** - Engine parts, brake parts, electrical, etc.
- **3 Stores** with **15 Bins** each
- **50+ Items** - Various auto parts
- **25 Customers** - Mix of retail and insurance types

---

## 10. Testing Strategy

### 10.1 Feature Tests
```bash
php artisan make:test GRNProcessingTest
php artisan make:test SalesProcessingTest
php artisan make:test StockUpdateTest
php artisan make:test ReturnProcessingTest
php artisan make:test QuotationManagementTest
```

### 10.2 Unit Tests
```bash
php artisan make:test --unit GRNServiceTest
php artisan make:test --unit SalesServiceTest
php artisan make:test --unit InventoryServiceTest
php artisan make:test --unit BatchServiceTest
```

---

## 11. Implementation Commands Reference

### 11.1 Essential Laravel Commands
```bash
# Development server
php artisan serve

# Queue worker (for notifications)
php artisan queue:work

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Run tests
php artisan test
```

### 11.2 Code Quality Commands
```bash
# Format code
./vendor/bin/pint

# Build assets
npm run build

# Dev assets
npm run dev
```

---

## 12. Context Reference Guide for LLM

### 12.1 When to Reference Documentation Files

#### Reference `@.claude\Developer_Documentation.markdown` when:
- Creating database migrations (see Section 3.1 for exact SQL schema)
- Implementing model relationships (see Tables and Relationships)
- Understanding business logic requirements (see Section 4.2 Core Functions)
- Setting up indexes for performance (see Section 3.3 Indexes)

#### Reference `@.claude\PRD_Document.markdown` when:
- Understanding business requirements (see Section 2.1 Functional Requirements)
- Implementing specific features (GRN entry, POS, quotations)
- Understanding user stories and workflows (see Section 3 User Stories)
- Compliance requirements (GAAP/IFRS, GDPR/CCPA)

#### Reference `@.claude\Workflow_Documentation.markdown` when:
- Implementing complex business processes (see Section 2 Workflows)
- Understanding step-by-step operations (GRN processing, sales processing)
- Error handling scenarios (see Section 5 Error Handling)
- Integration requirements (barcode, email/SMS)

### 12.2 Key Business Rules to Remember
1. **Multi-vendor item mapping** via `Vendor_Item_Mappings` table
2. **FIFO batch selection** for stock deduction
3. **Stock validation** before sales (quantity >= sale_qty)
4. **Serial item tracking** for items with `is_serialized = TRUE`
5. **Audit logging** for all data changes
6. **Cost calculations** include discounts and VAT

### 12.3 Database Constraints to Implement
- `stored_qty <= received_qty` in GRN_Items
- `quantity >= 0` for all quantity fields
- Unique constraints on `Items.item_no` and vendor mappings
- Foreign key relationships as per schema

---

## 13. Development Checklist

### 13.1 Phase 1 Completion Criteria
- [ ] All basic migrations created and run successfully
- [ ] All models created with proper relationships
- [ ] CRUD controllers working for vendors, categories, stores, customers, items
- [ ] Basic views rendering correctly
- [ ] Sample data seeded

### 13.2 Phase 2 Completion Criteria
- [ ] GRN entry form functional
- [ ] Vendor item mapping working
- [ ] Batch creation and stock updates working
- [ ] Inventory stock tracking accurate
- [ ] Audit logging implemented

### 13.3 Phase 3 Completion Criteria
- [ ] POS interface functional
- [ ] Sales processing with stock deduction working
- [ ] FIFO batch selection implemented
- [ ] Receipt generation working
- [ ] Customer management complete

### 13.4 Testing Completion Criteria
- [ ] All feature tests passing
- [ ] Unit tests covering service methods
- [ ] Manual testing of complete workflows
- [ ] Performance testing with 1000+ items
- [ ] Error handling tested

---

**Note**: This implementation plan should be followed sequentially. Each phase builds upon the previous one. Always refer to the context documentation files for detailed specifications and business rules.

**Component Creation Strategy**:
- **Phase 0**: Create basic UI components (cards, buttons, forms, tables) using Tailwind CSS
- **Phase 2+**: Create auto-parts specific components only when implementing the related features
- **Lucide Icons**: Use Lucide icons consistently throughout the application
- **Tailwind Theme**: All components use Tailwind utility classes with dark mode support
- **Consistent Styling**: Follow the established pattern from vendor views:
  - Card containers with rounded corners and shadows
  - Responsive tables with hover states
  - Form inputs with proper focus states
  - Success/error alerts with appropriate colors