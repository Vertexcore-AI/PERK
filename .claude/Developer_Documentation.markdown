# Developer Documentation  
## Auto Parts Inventory and Sales Management System  
**Version 1.0**  
**Prepared by: Vertexcore AI**  
**Date: September 13, 2025**  
**Contact: Phone: 070 314 3692 | 077 497 0885 | 077 803 6074 | Email: vertexcoreai@gmail.com | Website: https://vertexcoreai.vercel.app/**  

---

## 1. Overview

### 1.1 Purpose
This Developer Documentation provides technical guidance for building the **Auto Parts Inventory and Sales Management System**, a desktop application designed for a vehicle spare parts shop. It aligns with the Product Requirements Document (PRD) and the quotation dated September 11, 2025, outlining the system’s architecture, database schema, APIs, and implementation details.

### 1.2 Scope
The system includes modules for Inventory Management (GRN entry and stock updates), Sales Management, Sales Returns, Quotation Management, and Invoice Generation. It supports multi-vendor item mapping, batch tracking, and optional serialization for barcodes, with a local SQLite database and optional cloud backup (PostgreSQL) as per the LKR 68,000 initial payment option.

### 1.3 Target Audience
- Software developers implementing the system.
- QA engineers testing the application.
- System administrators managing deployment and backups.

### 1.4 Objectives
- Deliver a functional desktop app within 4-6 weeks.
- Ensure scalability for 1,000+ parts and multi-store support.
- Maintain compliance with GAAP/IFRS and GDPR/CCPA standards.
- Integrate with barcode scanners and email/SMS APIs.

---

## 2. System Architecture

### 2.1 High-Level Design
- **Frontend**: Desktop GUI using MVC framework (e.g., Python with Tkinter or C# with WinForms) for GRN entry, POS, quotes, etc.
- **Backend**: Business logic layer handling stock updates, cost calculations, and reporting.
- **Database**: SQLite for local storage; optional sync to PostgreSQL for cloud backup.
- **Integration**: Barcode scanner support via USB; email/SMS via APIs (e.g., SendGrid).

### 2.2 Component Breakdown
- **User Interface**: Forms for data entry (GRN, Sales, Returns, Quotes, Invoices) with validation.
- **Business Logic**: Modules for inventory, sales, and reporting with triggers for stock updates.
- **Data Layer**: SQL schema with tables for Items, Vendors, GRNs, etc.

---

## 3. Database Schema

### 3.1 Tables and Relationships
Below is the SQL schema for SQLite (adaptable to PostgreSQL). All tables include primary/foreign keys and constraints.

```sql
CREATE TABLE Vendors (
    vendor_id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    contact TEXT,
    address TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Categories (
    category_id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    description TEXT
);

CREATE TABLE Stores (
    store_id INTEGER PRIMARY KEY,
    store_name TEXT NOT NULL,
    store_location TEXT
);

CREATE TABLE Bins (
    bin_id INTEGER PRIMARY KEY,
    store_id INTEGER,
    bin_name TEXT NOT NULL,
    description TEXT,
    FOREIGN KEY (store_id) REFERENCES Stores(store_id)
);

CREATE TABLE Items (
    item_id INTEGER PRIMARY KEY,
    item_no TEXT UNIQUE NOT NULL,
    description TEXT NOT NULL,
    vat DECIMAL(5,2) DEFAULT 0,
    manufacturer_name TEXT,
    category_id INTEGER,
    unit_of_measure TEXT DEFAULT 'PCS',
    min_stock INTEGER DEFAULT 0,
    max_stock INTEGER,
    is_serialized BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES Categories(category_id)
);

CREATE TABLE Vendor_Item_Mappings (
    mapping_id INTEGER PRIMARY KEY,
    vendor_id INTEGER,
    vendor_item_code TEXT NOT NULL,
    item_id INTEGER,
    unit_cost DECIMAL(10,2),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (vendor_id, vendor_item_code),
    FOREIGN KEY (vendor_id) REFERENCES Vendors(vendor_id),
    FOREIGN KEY (item_id) REFERENCES Items(item_id)
);

CREATE TABLE Batches (
    batch_id INTEGER PRIMARY KEY,
    item_id INTEGER,
    vendor_id INTEGER,
    batch_number TEXT,
    cost DECIMAL(10,2),
    quantity INTEGER DEFAULT 0 CHECK (quantity >= 0),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES Items(item_id),
    FOREIGN KEY (vendor_id) REFERENCES Vendors(vendor_id)
);

CREATE TABLE Serial_Items (
    serial_id INTEGER PRIMARY KEY,
    batch_id INTEGER,
    serial_no TEXT UNIQUE NOT NULL,
    status INTEGER DEFAULT 0 CHECK (status IN (0, 1)),
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (batch_id) REFERENCES Batches(batch_id)
);

CREATE TABLE GRNs (
    grn_id INTEGER PRIMARY KEY,
    vendor_id INTEGER,
    inv_no TEXT NOT NULL,
    billing_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2),
    FOREIGN KEY (vendor_id) REFERENCES Vendors(vendor_id)
);

CREATE TABLE GRN_Items (
    grn_item_id INTEGER PRIMARY KEY,
    grn_id INTEGER,
    item_id INTEGER,
    batch_id INTEGER,
    vendor_item_code TEXT,
    received_qty INTEGER CHECK (received_qty >= 0),
    unit_price DECIMAL(10,2),
    discount DECIMAL(5,2) DEFAULT 0,
    unit_cost DECIMAL(10,2),
    vat DECIMAL(5,2) DEFAULT 0,
    total_cost DECIMAL(10,2),
    stored_qty INTEGER CHECK (stored_qty >= 0 AND stored_qty <= received_qty),
    notes TEXT,
    FOREIGN KEY (grn_id) REFERENCES GRNs(grn_id),
    FOREIGN KEY (item_id) REFERENCES Items(item_id),
    FOREIGN KEY (batch_id) REFERENCES Batches(batch_id)
);

CREATE TABLE Inventory_Stock (
    stock_id INTEGER PRIMARY KEY,
    item_id INTEGER,
    store_id INTEGER,
    bin_id INTEGER,
    batch_id INTEGER,
    quantity INTEGER DEFAULT 0 CHECK (quantity >= 0),
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES Items(item_id),
    FOREIGN KEY (store_id) REFERENCES Stores(store_id),
    FOREIGN KEY (bin_id) REFERENCES Bins(bin_id),
    FOREIGN KEY (batch_id) REFERENCES Batches(batch_id)
);

CREATE TABLE Customers (
    customer_id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    contact TEXT,
    address TEXT,
    type TEXT DEFAULT 'Retail',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Sales (
    sale_id INTEGER PRIMARY KEY,
    customer_id INTEGER,
    sale_date DATE,
    total_amount DECIMAL(10,2),
    status TEXT DEFAULT 'Completed',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES Customers(customer_id)
);

CREATE TABLE Sale_Items (
    sale_item_id INTEGER PRIMARY KEY,
    sale_id INTEGER,
    item_id INTEGER,
    batch_id INTEGER,
    quantity INTEGER CHECK (quantity > 0),
    unit_price DECIMAL(10,2),
    discount DECIMAL(5,2) DEFAULT 0,
    vat DECIMAL(5,2),
    total DECIMAL(10,2),
    FOREIGN KEY (sale_id) REFERENCES Sales(sale_id),
    FOREIGN KEY (item_id) REFERENCES Items(item_id),
    FOREIGN KEY (batch_id) REFERENCES Batches(batch_id)
);

CREATE TABLE Returns (
    return_id INTEGER PRIMARY KEY,
    sale_id INTEGER,
    return_date DATE,
    reason TEXT,
    total_refund DECIMAL(10,2),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES Sales(sale_id)
);

CREATE TABLE Return_Items (
    return_item_id INTEGER PRIMARY KEY,
    return_id INTEGER,
    item_id INTEGER,
    batch_id INTEGER,
    quantity INTEGER CHECK (quantity > 0),
    refund_amount DECIMAL(10,2),
    FOREIGN KEY (return_id) REFERENCES Returns(return_id),
    FOREIGN KEY (item_id) REFERENCES Items(item_id),
    FOREIGN KEY (batch_id) REFERENCES Batches(batch_id)
);

CREATE TABLE Quotations (
    quote_id INTEGER PRIMARY KEY,
    customer_id INTEGER,
    quote_date DATE,
    valid_until DATE,
    total_estimate DECIMAL(10,2),
    status TEXT DEFAULT 'Pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES Customers(customer_id)
);

CREATE TABLE Quote_Items (
    quote_item_id INTEGER PRIMARY KEY,
    quote_id INTEGER,
    item_id INTEGER,
    quantity INTEGER CHECK (quantity > 0),
    unit_price DECIMAL(10,2),
    discount DECIMAL(5,2) DEFAULT 0,
    vat DECIMAL(5,2),
    total DECIMAL(10,2),
    FOREIGN KEY (quote_id) REFERENCES Quotations(quote_id),
    FOREIGN KEY (item_id) REFERENCES Items(item_id)
);

CREATE TABLE Invoices (
    invoice_id INTEGER PRIMARY KEY,
    sale_id INTEGER,
    quote_id INTEGER,
    invoice_date DATE,
    total_amount DECIMAL(10,2),
    paid_amount DECIMAL(10,2) DEFAULT 0,
    status TEXT DEFAULT 'Unpaid',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES Sales(sale_id),
    FOREIGN KEY (quote_id) REFERENCES Quotations(quote_id)
);

CREATE TABLE Audit_Logs (
    log_id INTEGER PRIMARY KEY,
    user_id INTEGER,
    action TEXT,
    table_name TEXT,
    record_id INTEGER,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Users (
    user_id INTEGER PRIMARY KEY,
    username TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    role TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### 3.2 Key Constraints
- `CHECK` constraints ensure valid quantities (e.g., `stored_qty <= received_qty`).
- `UNIQUE` on `Items.item_no` and `Vendor_Item_Mappings.vendor_id, vendor_item_code`.
- Foreign key relationships enforce referential integrity.

### 3.3 Indexes
- `CREATE INDEX idx_items_item_no ON Items(item_no);`
- `CREATE INDEX idx_batches_item_id ON Batches(item_id);`
- Add as needed for performance (e.g., `GRN_Items.grn_id`).

---

## 4. Implementation Details

### 4.1 Setup Instructions
- **Environment**: Install Python 3.9+ or C# (.NET 6+), SQLite3, and optional PostgreSQL.
- **Dependencies**: Tkinter (Python) or WinForms (C#), pyodbc (for SQL), SendGrid API.
- **Database**: Initialize with the schema above; seed with sample data (50 items, 10 vendors).

### 4.2 Core Functions
- **GRN Processing**:
  - Function: `process_grn(vendor_id, inv_no, items)`.
  - Logic: Insert into `GRNs`, resolve `item_id` via `Vendor_Item_Mappings`, create `Batches`, update `Inventory_Stock`.
  - Trigger: On `GRN_Items` insert, update batch/stock if `stored_qty > 0`.
- **Sales Processing**:
  - Function: `process_sale(customer_id, items)`.
  - Logic: Insert into `Sales` and `Sale_Items`, deduct from `Inventory_Stock` (FIFO via `Batches.created_at`).
- **Returns Processing**:
  - Function: `process_return(sale_id, items)`.
  - Logic: Insert into `Returns` and `Return_Items`, add to `Inventory_Stock`.
- **Quotation Generation**:
  - Function: `generate_quote(customer_id, items)`.
  - Logic: Insert into `Quotations` and `Quote_Items`, calculate totals.
- **Invoice Generation**:
  - Function: `generate_invoice(sale_id_or_quote_id)`.
  - Logic: Insert into `Invoices`, populate from `Sales` or `Quotations`.

### 4.3 Serialization Handling
- If `Items.is_serialized = TRUE`, generate `Serial_Items` records during GRN (e.g., loop for `stored_qty` with unique `serial_no`).
- Update `status` (0 to 1) on sale; revert on return.

### 4.4 Cloud Backup
- Sync SQLite to PostgreSQL using a script (e.g., `sqlite3_to_postgres.py` with encryption).
- Trigger on data change to queue sync.

---

## 5. API Specifications
- **Local DB Access**: Use SQLite3 Python module or ADO.NET.
- **Email/SMS**: SendGrid API for invoice delivery (e.g., `send_email(to, subject, body)`).
- **Barcode**: Integrate with scanner input via keyboard emulation.

---

## 6. Testing Guidelines
- **Unit Tests**: Test GRN entry, stock updates, sales/returns logic.
- **Integration Tests**: Verify multi-vendor mapping, batch tracking, serialization.
- **Load Tests**: Simulate 1,000 parts, 100 transactions.
- **Security Tests**: Check data encryption, role access.

---

## 7. Deployment
- **Local**: Install on client machine(s); provide executable and DB file.
- **Cloud Backup**: Configure PostgreSQL connection (LKR 8,000/year option).
- **Maintenance**: Apply updates/security patches (LKR 5,000/year from Year 2).

---

## 8. Troubleshooting
- **Error**: Stock mismatch. *Solution*: Check `Inventory_Stock` triggers.
- **Error**: Mapping not found. *Solution*: Prompt user to add to `Vendor_Item_Mappings`.
- **Contact**: Email vertexcoreai@gmail.com for support.

---

## 9. Version History
- **1.0**: Initial release, September 13, 2025.