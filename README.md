# Inventory Management App

A simple, modern, and responsive Inventory Management System built with **Native PHP** and **SQLite**.

## Features

-   **Dashboard**: Real-time overview of stock, suppliers, and recent transactions.
-   **Master Data Management**:
    -   **Products**: Manage product details (Name, Size, Brand, Location, Stock).
    -   **Suppliers**: Manage supplier contact information.
-   **Transactions**:
    -   **Incoming Goods**: Record stock entries with PO/FPB references.
    -   **Outgoing Goods**: Record stock exits with delivery notes (Surat Jalan) and destination.
    -   **Stock Opname**: Adjust physical stock levels and track discrepancies.
-   **Reporting**: Filterable transaction history (Incoming/Outgoing/All) by date range.
-   **User Management**: Role-based access control (Admin/Staff).
-   **Modern UI/UX**:
    -   Responsive Sidebar Layout.
    -   Clean and professional design using a custom Design System.
    -   Interactive components and mobile-friendly.

## Tech Stack

-   **Backend**: PHP 8.2+
-   **Database**: SQLite3
-   **Frontend**: HTML5, CSS3 (Custom Modern Design), JavaScript (Vanilla)
-   **Icons**: FontAwesome 6

## Installation

1.  **Clone the repository**:
    ```bash
    git clone https://github.com/zidane-sc/inventory-php.git
    cd inventory-php
    ```

2.  **Ensure PHP and SQLite are installed**:
    ```bash
    # Ubuntu/Debian
    sudo apt install php php-sqlite3
    ```

3.  **Start the Development Server**:
    ```bash
    php -S localhost:8000
    ```

4.  **Access the App**:
    Open your browser and visit `http://localhost:8000`.

## Default Credentials

-   **Username**: `admin`
-   **Password**: `admin123`

## Project Structure

```
inventory-php/
├── config.php          # Database connection & helper functions
├── schema.sql          # Database schema
├── inventory.db        # SQLite database file (auto-generated)
├── style.css           # Custom CSS styles
├── header.php          # Page header & sidebar
├── footer.php          # Page footer & scripts
├── index.php           # Dashboard
├── login.php           # Login page
├── products.php        # Product management
├── suppliers.php       # Supplier management
├── incoming.php        # Incoming transactions
├── outgoing.php        # Outgoing transactions
├── stock_opname.php    # Stock adjustment
├── history.php         # Transaction history
└── users.php           # User management
```

## License

This project is open-source and available under the MIT License.
