# Software Requirements Specification

## Web-Based Room Rental Management System

**Version:** 1.0  
**Date:** October 2026  
**Technology:** Laravel, PHP, MySQL, Blade, Vite

## 1. Introduction

### 1.1 Purpose

This document defines the functional and non-functional requirements for a web-based
room rental management system. The system supports a small property business in
Siem Reap, Cambodia by centralizing rooms, tenants, rental contracts, utility
readings, invoices, and payments.

### 1.2 Scope

The system will allow authorized staff to:

- maintain room types, rooms, prices, and availability;
- register tenants and their contact information;
- create and manage rental contracts;
- record electricity and water meter readings;
- calculate monthly rent and utility charges;
- issue invoices and record payments; and
- view operational and financial information.

The first release is an internal management application. Online payment gateway
processing, accounting integration, multi-property tenancy, and a tenant mobile
application are outside the initial scope.

### 1.3 Definitions

| Term | Definition |
|---|---|
| Room type | A category of room with a base rent and utility rates. |
| Tenant | A person renting or applying to rent a room. |
| Contract | The agreement connecting a tenant to a room for a defined period. |
| Meter reading | Electricity and water meter values recorded for a room on a date. |
| Invoice | A monthly charge containing rent and utility amounts. |
| Payment | A recorded settlement, full or partial, of an invoice. |

## 2. Overall Description

### 2.1 Product perspective

The application is a Laravel web application using a relational MySQL database.
The user interface will be delivered through server-rendered Blade views and
Vite-managed assets. Eloquent models represent the domain relationships.

### 2.2 User classes

| User class | Responsibilities |
|---|---|
| Administrator | Manage all master data, users, contracts, invoices, and reports. |
| Rental staff | Manage tenants, rooms, readings, invoices, and payments. |
| Viewer | Read permitted operational and financial summaries. |

Authentication and authorization should be applied before production deployment.
The initial demo seed data is intended for local development only.

### 2.3 Operating environment

- PHP 8.3 or later
- Laravel 13
- MySQL 8 or compatible relational database
- Composer
- Node.js and npm for frontend assets
- Modern browser with JavaScript and responsive CSS support

### 2.4 Constraints and assumptions

- Monetary values are stored in US dollars with two decimal places.
- Electricity and water charges use the configured business rates.
- A room can have multiple historical contracts but should not have conflicting
  active contracts.
- Meter readings are unique for a room and reading date.
- The database is the source of truth for invoices and payment records.
- Time-zone and localization settings must be confirmed before production use.

## 3. Functional Requirements

### FR-01 Authentication and access

1. The system shall authenticate registered users.
2. The system shall restrict administrative actions to authorized roles.
3. The system shall protect passwords using Laravel's password hashing.
4. The system shall prevent unauthenticated users from accessing management screens.

### FR-02 Room type management

1. Staff shall create, view, update, and deactivate room types.
2. A room type shall contain a type name, base price, and description.
3. Room type names shall be unique.
4. The base price shall be a non-negative numeric value.

### FR-03 Room management

1. Staff shall create and update rooms.
2. Each room shall belong to one room type.
3. Each room shall have a unique room number and status.
4. The system shall support `available`, `occupied`, and `maintenance` statuses.
5. A room with an active contract shall not be presented as available.

### FR-04 Tenant management

1. Staff shall register tenants with full name, phone, national ID, and emergency
   contact information.
2. Staff shall search tenants by name, phone, or national ID.
3. Tenant records shall retain historical contracts after a tenancy ends.

### FR-05 Contract management

1. Staff shall create a contract for a tenant and room.
2. A contract shall record start date, optional end date, deposit amount, and
   status.
3. The system shall validate that the end date is not before the start date.
4. The system shall prevent overlapping active contracts for the same room.
5. Staff shall be able to renew, terminate, and view contract history.

### FR-06 Meter readings

1. Staff shall record electricity and water readings for a contract.
2. Each reading shall include old and new electricity and water values and a
   reading date.
3. Current values shall not be less than previous values.
4. The system shall prevent duplicate readings for the same contract and date.
5. Usage shall be calculated as current value minus previous value.

### FR-07 Invoice generation

1. Staff shall generate an invoice for an active contract and meter reading.
2. An invoice shall include contract, meter reading, invoice date, total amount,
   and payment status.
3. The system shall not create a duplicate invoice for the same contract,
   meter reading, and invoice date.
4. The system shall support `unpaid`, `partially_paid`, `paid`, and `overdue`
   statuses.

### FR-08 Payment management

1. Staff shall record payments against invoices.
2. A payment shall include date, amount paid, and payment method.
3. Payment amount shall be greater than zero.
4. The invoice status shall be updated from the total payments received.
5. The system shall retain an audit-friendly payment history.

### FR-09 Reporting and dashboard

1. The dashboard shall show room occupancy and availability.
2. Staff shall view unpaid and overdue invoices.
3. Staff shall view income totals for a selected period.
4. Staff shall view active contracts and upcoming contract end dates.
5. Reports shall be filterable by date, room, tenant, and status where relevant.

### FR-10 Validation and notifications

1. All create and update forms shall validate required fields and data types.
2. Validation failures shall identify the affected fields.
3. Successful create, update, and payment actions shall display a confirmation.
4. Failures shall be logged and shown as an actionable error rather than silently
   ignored.

## 4. Data Requirements

The initial schema contains the following core entities:

| Entity | Important attributes | Relationships |
|---|---|---|
| User | name, email, password, role | system account |
| RoomType | type name, base price, description | has many rooms |
| Room | number, type, status | belongs to room type; has contracts |
| Tenant | full name, phone, national ID, emergency contact | has contracts |
| Contract | tenant, room, dates, deposit, status | belongs to tenant and room; has readings and invoices |
| MeterReading | contract, date, old/new meter values | belongs to contract; has invoices |
| Invoice | contract, meter reading, date, total, payment status | belongs to contract and reading; has payments |
| Payment | date, amount paid, method | belongs to invoice |

Foreign keys shall enforce referential integrity. Deleting a record with
dependent operational history should be restricted or implemented as a controlled
deactivation rather than a destructive delete.

## 5. Business Rules

1. Base price cannot be negative.
2. Meter usage cannot be negative.
3. Invoice total equals the configured rent and utility charges for the contract period.
4. A payment cannot have a zero or negative amount.
5. A paid invoice must have a corresponding payment total at least equal to its
   invoice total.
6. Room availability is derived from room status and active contracts.
7. Dates displayed to users shall use the configured Cambodian business timezone.
8. Demo passwords and seed data must never be reused in production.

## 6. Non-Functional Requirements

### NFR-01 Security

- Secrets shall be supplied through environment configuration and never committed.
- Passwords shall use Laravel's hashing facilities.
- Forms that mutate state shall use CSRF protection.
- Authorization shall be checked server-side for every protected action.
- User input shall be validated and escaped before rendering.
- Production errors shall not expose stack traces or credentials.

### NFR-02 Performance

- Normal management pages should respond within two seconds under expected
  classroom/demo load.
- List pages shall paginate large result sets.
- Frequently filtered foreign keys and dates should be indexed.

### NFR-03 Reliability

- Database writes shall use transactions where an operation changes multiple
  related records.
- The application shall log unexpected failures without logging secrets.
- Backups should be scheduled for production data.

### NFR-04 Usability and accessibility

- Interfaces shall be responsive on desktop and mobile widths.
- Labels, validation messages, and status indicators shall be clear.
- Keyboard navigation and sufficient color contrast should be supported.
- Currency and dates shall be consistently formatted.

### NFR-05 Maintainability

- Domain behavior shall remain in models, services, policies, and controllers
  rather than duplicated in views.
- Migrations shall be version-controlled and reversible.
- Automated tests shall cover invoice calculations, contract rules, and payment
  status transitions.

## 7. External Interfaces

### 7.1 User interface

The application shall provide navigation for Dashboard, Room Types, Rooms,
Tenants, Contracts, Meter Readings, Invoices, Payments, and Reports.

### 7.2 Database interface

Laravel's database abstraction and Eloquent ORM shall be used. Credentials,
connection details, and environment-specific settings shall be read from `.env`.

### 7.3 Future integrations

The design may later support email notifications, ABA or other Cambodian payment
providers, PDF invoices, and export to CSV. These are not required for version 1.

## 8. Testing and Acceptance

Acceptance testing shall demonstrate that:

1. migrations create all eight domain tables successfully;
2. seeders create realistic Siem Reap demo data;
3. each model loads its documented relationships;
4. utility usage and invoice totals are correct;
5. duplicate room readings and invoice numbers are rejected;
6. payment recording changes invoice status correctly;
7. unauthorized users cannot perform protected operations; and
8. validation errors are displayed without losing entered safe values.

Recommended automated test groups are model relationship tests, feature tests for
CRUD flows, invoice calculation unit tests, authorization tests, and migration
tests.

## 9. Future Enhancements

- Tenant self-service portal
- Khmer and English localization
- PDF invoice generation
- SMS or email due-date reminders
- Multi-property support
- Online payment integration
- Maintenance requests and expense tracking
- Audit log and exportable financial reports