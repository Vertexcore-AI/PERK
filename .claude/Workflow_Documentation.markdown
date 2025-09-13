# Workflow Documentation  
## Auto Parts Inventory and Sales Management System  
**Version 1.0**  
**Prepared by: Vertexcore AI**  
**Date: September 13, 2025**  
**Contact: Phone: 070 314 3692 | 077 497 0885 | 077 803 6074 | Email: vertexcoreai@gmail.com | Website: https://vertexcoreai.vercel.app/**  

---

## 1. Overview

### 1.1 Purpose
This document outlines the complete workflows for the **Auto Parts Inventory and Sales Management System**, a desktop application designed for a vehicle spare parts shop. It details the step-by-step processes for key operations—Goods Received Note (GRN) entry, inventory updates, sales, returns, quotations, and invoice generation—aligned with the quotation dated September 11, 2025, and the system’s database schema.

### 1.2 Scope
The workflows cover all core modules: Inventory Management, Sales Management, Sales Returns, Quotation Management, and Invoice Generation. The system supports multi-vendor item mapping, batch tracking, and optional serialization, with local SQLite storage and optional cloud backup (PostgreSQL) as per the LKR 68,000 initial payment option.

### 1.3 Objectives
- Provide clear, repeatable processes for shop operations.
- Ensure data consistency across modules (e.g., stock updates, financial records).
- Support scalability and compliance with accounting (GAAP/IFRS) and privacy (GDPR/CCPA) standards.

---

## 2. Workflows

### 2.1 Goods Received Note (GRN) Entry and Inventory Update
**Objective**: Record incoming parts from vendors and update inventory stock.  
**Actors**: Shop owner, staff.  
**Steps**:
1. **Initiate GRN**: User opens the GRN entry form and selects a vendor from the `Vendors` table.
2. **Enter Details**: Input GRN fields: inv_no, billing_date, and line items (vendor_item_code, received_qty, unit_price, discount, vat).
3. **Resolve Item Mapping**: System queries `Vendor_Item_Mappings` to match vendor_item_code to internal item_id. If unmatched, prompt user to map to an existing `Items.item_no` or create a new item.
4. **Calculate Costs**: Compute unit_cost (unit_price - discount), total_cost (unit_cost * received_qty), and apply vat. Update `GRNs` and `GRN_Items` tables.
5. **Create Batch**: Generate a new `Batches` record with batch_number, cost, and initial quantity.
6. **Decide Stored Quantity**: User specifies stored_qty (≤ received_qty) per item. If `Items.is_serialized = TRUE`, generate `Serial_Items` records with unique serial_no for each unit.
7. **Update Inventory**: Insert/update `Inventory_Stock` with stored_qty per store/bin/batch via trigger.
8. **Save and Audit**: Save transaction; log action in `Audit_Logs`.

**Output**: Updated stock in `Inventory_Stock`, new batch records, and GRN history.

### 2.2 Sales Processing
**Objective**: Process customer purchases and update stock.  
**Actors**: Sales staff.  
**Steps**:
1. **Start Sale**: Open POS interface and select customer from `Customers` or add new.
2. **Add Items**: Scan barcode (if serialized) or search `Items.item_no`/`description`. Select batch (FIFO via `Batches.created_at`) and quantity.
3. **Calculate Totals**: Apply unit_price (from last batch cost or `Vendor_Item_Mappings`), discount, and vat. Compute total for `Sale_Items`.
4. **Process Payment**: Enter payment method (cash/card) and amount. Update `Sales.total_amount`.
5. **Update Stock**: Deduct quantity from `Inventory_Stock` per batch. If serialized, update `Serial_Items.status` to 1.
6. **Generate Invoice**: Trigger `generate_invoice()` to create `Invoices` record.
7. **Save and Audit**: Save transaction; log in `Audit_Logs`.

**Output**: Completed sale, updated stock, and invoice.

### 2.3 Sales Returns Processing
**Objective**: Handle returned items and restore stock.  
**Actors**: Sales staff.  
**Steps**:
1. **Initiate Return**: Open return form and select original sale from `Sales` using sale_id.
2. **Select Items**: Choose returned items and quantities from `Sale_Items`. Enter reason.
3. **Verify Condition**: User confirms item condition (resalable or not); adjust refund_amount.
4. **Update Stock**: Add quantity back to `Inventory_Stock` per batch. If serialized, revert `Serial_Items.status` to 0.
5. **Process Refund**: Issue credit note or refund; update `Returns.total_refund`.
6. **Save and Audit**: Save transaction; log in `Audit_Logs`.

**Output**: Restored stock, return record, and refund/credit note.

### 2.4 Quotation Generation
**Objective**: Provide estimates for insurance agents/customers.  
**Actors**: Manager, staff.  
**Steps**:
1. **Start Quote**: Open quote form and select customer from `Customers` (e.g., insurance type).
2. **Add Items**: Search `Items` by item_no/description, select quantities, and set unit_price (from batch cost).
3. **Calculate Totals**: Apply discount and vat; compute `Quote_Items.total` and `Quotations.total_estimate`.
4. **Set Validity**: Define valid_until date (e.g., 30 days).
5. **Save Quote**: Insert into `Quotations` and `Quote_Items`. Status defaults to "Pending".
6. **Save and Audit**: Log action in `Audit_Logs`.

**Output**: Saved quotation document, convertible to sale/invoice.

### 2.5 Invoice Generation
**Objective**: Create billing documents from sales or accepted quotes.  
**Actors**: Staff, accountant.  
**Steps**:
1. **Trigger Invoice**: Select sale_id (from `Sales`) or quote_id (from `Quotations`) post-acceptance.
2. **Populate Details**: Fetch line items from `Sale_Items` or `Quote_Items`, including quantities, prices, vat, and totals.
3. **Generate Document**: Create `Invoices` record with invoice_date, total_amount, and status ("Unpaid" by default).
4. **Delivery**: Offer print or email/SMS via SendGrid API.
5. **Save and Audit**: Log in `Audit_Logs`.

**Output**: Invoice document, updated `Invoices` table.

---

## 3. Data Flow Diagram
- **GRN Entry → GRNs/GRN_Items → Batches/Serial_Items → Inventory_Stock**.
- **Sales → Sales/Sale_Items → Inventory_Stock → Invoices**.
- **Returns → Returns/Return_Items → Inventory_Stock**.
- **Quotes → Quotations/Quote_Items → (Optional) Sales/Invoices**.
- **Audit Logs**: Captures all changes across tables.

---

## 4. Integration Points
- **Barcode Scanners**: Input serial_no to `Serial_Items` during GRN or sales.
- **Email/SMS**: SendGrid API for invoice delivery.
- **Cloud Backup**: Sync SQLite to PostgreSQL (optional, LKR 8,000/year).

---

## 5. Error Handling
- **Stock Mismatch**: Validate `Inventory_Stock.quantity >= sale_qty` before sales.
- **Mapping Error**: Prompt user to update `Vendor_Item_Mappings` if vendor_item_code not found.
- **Payment Failure**: Roll back sale if payment fails; log in `Audit_Logs`.

---

## 6. Maintenance Workflow
**Objective**: Ensure system uptime and security (LKR 5,000/year from Year 2).  
**Actors**: Developer, admin.  
**Steps**:
1. **Update Check**: Monthly review for software patches.
2. **Security Scan**: Run checks on database/user access.
3. **Optimization**: Clear old logs, rebuild indexes.
4. **Backup Sync**: Verify cloud sync (if enabled).

**Output**: Updated, secure system.

---

## 7. Alignment with Quotation
- **Initial Cost**: LKR 60,000 (development) or LKR 68,000 (with hosting).
- **Recurring Cost**: LKR 14,000/year (LKR 8,000 hosting + LKR 5,000 maintenance + LKR 1,000 buffer) from Year 2.
- **Timeline**: 4-6 weeks from 50% payment (by September 20, 2025, for start).

---

## 8. Appendix
- **References**: Quotation dated September 11, 2025; PRD v1.0.
- **Next Steps**: Train staff on workflows; monitor initial use.