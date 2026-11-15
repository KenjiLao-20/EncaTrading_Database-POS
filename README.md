<div align="center">

# 🏪 Enca Trading
### POS & Inventory Management System

<br>

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-323330?style=for-the-badge&logo=javascript&logoColor=F7DF1E)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

<br>

> **A web-based Point of Sale (POS) and Inventory Management System** developed as a final project for the **System Integration** course.
> 
> Our client is **Enca Trading**, a construction supply store in the Philippines. This system modernizes their daily operations by replacing manual logbooks with digital checkout, real-time stock tracking, and intelligent sales analytics.

</div>

<br>
<hr>
<br>

## ✨ Key Features

* **🛒 Point of Sale (POS):** Fast checkout interface supporting multiple payment methods (Cash, GCash, Maya, Bank Transfer, Card) with automatic change calculation.
* **📦 Inventory Tracking:** Real-time stock updates and manual inventory adjustment tools (for damages, new deliveries, returns, etc.).
* **🔔 Stock Alerts:** Automatic visual warnings for low-stock and out-of-stock construction materials.
* **📈 Analytics & Forecasting:** Beautiful data visualizations for daily sales, top-selling items, staff performance, and a 7-day future sales forecast using linear regression.
* **🔐 Role-Based Access:** Secure login with Admin accounts (full system access) and Staff/Cashier accounts (restricted access).
* **🧾 Printable Receipts:** Generates digital receipts with barcode and QR code generation for scanning.
* **📄 CSV Export/Import:** Easily export sales reports or bulk-import products via CSV files.

<br>
<hr>
<br>

## 🚀 How to Run Locally (Using XAMPP)

Follow these steps to set up and run the system on your local machine.

### 1. Prerequisites
* Download and install [XAMPP](https://www.apachefriends.org/index.html).
* A modern web browser (Chrome, Edge, Firefox, etc.).

### 2. Set up the Project
1. Clone this repository or download it as a `.zip` file.
2. Extract the folder and move it inside your XAMPP `htdocs` directory. 
   *(Example path: `C:\xampp\htdocs\encatrading`)*

### 3. Start XAMPP & Import Database
1. Open the **XAMPP Control Panel**.
2. Click **Start** next to **Apache** and **MySQL**.
3. Open your browser and go to `http://localhost/phpmyadmin/`.
4. On the top menu, click on the **SQL** tab.
5. Open the `sql data.txt` (or `.sql`) file from this repository.
6. Copy ALL of its contents, paste it into the SQL text box in phpMyAdmin, and click **Go**.
   *(Note: This will automatically create the `encatrading_db` database, build the tables, and insert sample construction materials, suppliers, and users.)*

### 4. Configure Database Connection (If needed)
If your XAMPP uses a different MySQL port (like 3307 or 3308), you need to update the configuration in `src/config/database.php` to match your XAMPP setup:
- `$host = '127.0.0.1';`                   
- `$port = '3306';` *(Change this if your XAMPP MySQL runs on a different port)*
- `$dbname = 'encatrading_db';`
- `$username = 'root';`
- `$password = '';` *(Default XAMPP password is empty)*

### 5. Access the System
Open your browser and go to:
`http://localhost/encatrading/src/`

*(Note: If you named the folder differently inside `htdocs`, change `encatrading` to your actual folder name).*

<br>
<hr>
<br>

## 🔐 Default Login Accounts

Use the following sample accounts to test the system:

| Role | Username | Password | Access Level |
| :--- | :--- | :--- | :--- |
| **Admin** | `hau` | `hau123456789` | Full Access |
| **Admin** | `admin` | `admin123` | Full Access |
| **Staff** | `staff` | `admin123` | POS & Inventory Only |

<br>
<hr>
<br>

<div align="center">
    
## 👥 The Team

**[Your Name Here]** - *Lead Developer* <br>
**[Teammate Name]** - *UI/UX & Documentation* <br>
**[Teammate Name]** - *Database & Backend* <br>

**Course:** System Integration <br>
**Institution:** [Your School/University]

<br>

---
*This project is for educational purposes as part of the System Integration coursework.*
</div>
