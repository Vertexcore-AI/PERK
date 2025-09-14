# Product Requirements Document (PRD)  
## Auto Parts Inventory and Sales Management System  
**Version 1.0**  
**Prepared by: Vertexcore AI**  
**Date: September 13, 2025**  
**Contact: Phone: 070 314 3692 | 077 497 0885 | 077 803 6074 | Email: vertexcoreai@gmail.com | Website: https://vertexcoreai.vercel.app/**  

---

## 1. Overview

### 1.1 Purpose
This Product Requirements Document (PRD) outlines the specifications for a desktop-based **Auto Parts Inventory and Sales Management System** designed for a vehicle spare parts shop. The system aims to streamline inventory management, sales processing, customer returns, quotation generation for insurance companies/agents, and invoice creation, enhancing operational efficiency and accuracy in the automotive aftermarket industry.

### 1.2 Scope
The system will serve as a specialized Retail Management System (RMS) with the following core functionalities:
- Inventory tracking and updates via Goods Received Notes (GRNs).
- Point-of-sale (POS) sales processing.
- Management of returns and refunds.
- Generation of quotations for insurance agents.
- Automated invoice creation.
The initial scope excludes mobile apps, e-commerce integration, or custom hardware (e.g., POS terminals), with additional features charged extra as per the quotation.

### 1.3 Target Audience
- Shop owners managing vehicle spare parts inventory and sales.
- Staff handling daily transactions (sales, returns, quotes).
- Insurance agents/customers receiving quotations.

### 1.4 Objectives
- Reduce manual errors in stock and financial tracking.
- Support multi-vendor part sourcing with unified item management.
- Provide a scalable solution for up to 1,000+ parts initially.
- Ensure compliance with accounting (GAAP/IFRS) and data privacy (GDPR/CCPA) standards.
- Deliver within 4-6 weeks from initial payment, aligning with the quotation dated September 11, 2025.

---

## 2. System Requirements

### 2.1 Functional Requirements

#### 2.1.1 Inventory Management
- **GRN Entry**: Allow entry of GRNs with fields: vendor, inv_no, billing_date, created_at, received_qty, unit_price, discount, unit_cost, total_cost, grn_id, item_id, stored_qty, vat.
- **Item Registry**: Map vendor-specific item codes to a unique internal item_no via a Vendor_Item_Mappings table, enabling multi-vendor support.
- **Stock Updates**: Transfer stored_qty from GRN to Inventory_Stock per batch and store/bin after user approval.
- **Batch Tracking**: Track batches with batch_id, cost, and quantity for quality control and FIFO stock management.
- **Serialization (Optional)**: Support barcode/serial number tracking for individual items via Serial_Items (status 0: Available, 1: Sold).

#### 2.1.2 Sales Management
- **POS Interface**: Enable sales entry with customer details, cart management, discounts, taxes, and payment processing (cash/card).
- **Stock Deduction**: Automatically reduce Inventory_Stock quantity per batch on sale completion.
- **Batch Selection**: Use FIFO logic to select batches for sales.

#### 2.1.3 Sales Returns
- **Return Processing**: Handle returns with reason, original sale reference, and refund/credit note generation.
- **Stock Restoration**: Add returned quantity back to Inventory_Stock per batch.

#### 2.1.4 Quotation Management
- **Quote Generation**: Create quotes for insurance agents with parts, quantities, and validity periods (e.g., 30 days).
- **Conversion**: Convert accepted quotes to sales/invoices.

#### 2.1.5 Invoice Generation
- **Billing**: Generate printable/digital invoices with line items, tax calculations, and email/SMS delivery.
- **History**: Maintain searchable invoice records.

### 2.2 Non-Functional Requirements
- **Performance**: Handle 1,000+ parts with sub-second query response times.
- **Scalability**: Support multi-store expansion with Bins table.
- **Security**: Encrypt customer data; role-based access (Admin/Staff) via Users table.
- **Reliability**: ACID-compliant database (e.g., SQLite); optional cloud backup via PostgreSQL.
- **Usability**: Responsive desktop UI with barcode scanning support.
- **Compliance**: GAAP/IFRS for accounting; GDPR/CCPA for data privacy.

### 2.3 Technical Requirements
- **Platform**: Desktop application (Windows/Mac compatible).
- **Database**: SQLite for local use; optional cloud sync to PostgreSQL for backup (LKR 8,000/year).
- **Framework**: MVC architecture (e.g., Python with Tkinter or C# with WinForms).
- **Integration**: Support for barcode scanners; email/SMS APIs (e.g., SendGrid).

---

## 3. User Stories

- **As a shop owner**, I want to enter GRNs to update stock, so I can track incoming parts from multiple vendors.
- **As a sales staff**, I want a POS system to process sales, so I can serve customers efficiently.
- **As a manager**, I want to generate quotes for insurance agents, so I can secure repair contracts.
- **As an accountant**, I want invoices and returns tracked, so I can maintain accurate financial records.
- **As a user**, I want batch tracking, so I can manage stock quality and recalls.

---

## 4. System Architecture

- **Frontend**: Desktop GUI with forms for GRN entry, POS, quotes, etc.
- **Backend**: MVC with business logic for stock updates, cost calculations, and reporting.
- **Database**: Relational schema with tables for Items, Vendors, GRNs, Inventory_Stock, Batches, Serial_Items, etc.
- **Deployment**: Local installation with optional cloud backup hosting.

---

## 5. Milestones and Timeline
- **Planning & Design**: September 13 - September 20, 2025 (1 week).
- **Development**: September 21 - October 25, 2025 (5 weeks).
- **Testing & Training**: October 26 - November 1, 2025 (1 week).
- **Delivery**: November 2, 2025.
- **Total**: 4-6 weeks from initial payment (50% upfront, 50% on delivery).

---

## 6. Cost and Pricing
Based on the quotation dated September 11, 2025:
- **Initial Payment**: LKR 60,000 (Development Fee only) or LKR 68,000 (includes Backup Database Hosting Fee).
- **Maintenance Fee**: LKR 5,000/year (from Year 2) for updates, security, optimization.
- **Recurring Annual Cost (Year 2 onwards)**:
  - Hosting Renewal: LKR 8,000
  - Maintenance Fee: LKR 5,000
  - Total Yearly: LKR 13,000
- **Additional Features**: Charged extra (e.g., LKR 5,000/module).

---

## 7. Risks and Mitigation
- **Risk**: Multi-vendor code mapping errors. *Mitigation*: User validation during GRN entry.
- **Risk**: Performance with large stock. *Mitigation*: Indexing and batch processing.
- **Risk**: Data loss. *Mitigation*: Cloud backup (optional) and local backups.
- **Risk**: Delayed delivery. *Mitigation*: Weekly progress reviews.

---

## 8. Acceptance Criteria
- GRN entry updates Inventory_Stock correctly.
- Sales deduct stock; returns restore it.
- Quotes and invoices generate accurately.
- System handles 50 sample parts and 10 vendors without errors.
- User training completed with no major issues reported.

---

## 9. Appendix
- **References**: Quotation dated September 11, 2025.
- **Next Steps**: Client to confirm requirements; initiate payment process.