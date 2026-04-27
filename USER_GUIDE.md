# May Food - User & Installation Guide

Welcome to May Food! This guide will help you navigate our online food ordering system and set it up on your own server.

## Table of Contents
1.  [Installation Guide](#installation-guide)
2.  [Home Page](#home-page)
3.  [Browsing the Menu](#browsing-the-menu)
4.  [Searching for Dishes](#searching-for-dishes)
5.  [Adding Items to Your Cart](#adding-items-to-your-cart)
6.  [Managing Your Cart](#managing-your-cart)
7.  [Placing an Order](#placing-an-order)
8.  [Creating an Account & Logging In](#creating-an-account--logging-in)
9.  [Viewing Your Profile and Order History](#viewing-your-profile-and-order-history)
10. [Making a Reservation](#making-a-reservation)
11. [Admin Panel](#admin-panel)

---

### 1. Installation Guide

Follow these steps to set up the project on your local machine or server.

#### Prerequisites
*   **Web Server:** Apache, Nginx, or any other web server that supports PHP.
*   **PHP:** Version 7.4 or higher.
*   **Database:** MySQL or MariaDB.
*   **Web Browser:** A modern web browser like Chrome, Firefox, or Edge.

#### Step 1: Database Setup
1.  **Create a Database:**
    *   Open your database management tool (like phpMyAdmin).
    *   Create a new database. You can name it `restaurant_db` or any other name you prefer.

2.  **Import the SQL File:**
    *   Select the newly created database.
    *   Find the `restaurant.sql` file in the project's root directory.
    *   Import this file into your database. This will create all the necessary tables and populate them with initial data.

3.  **Configure the Database Connection:**
    *   Open the `db_connection.php` file located in the project's root directory.
    *   Update the following variables with your database credentials:
        ```php
        $servername = "localhost";
        $username = "your_database_username";
        $password = "your_database_password";
        $dbname = "your_database_name"; 
        ```

#### Step 2: Project Setup
1.  **Download or Clone the Project:**
    *   Place all the project files and folders into your web server's root directory (e.g., `htdocs` for XAMPP, `www` for WAMP, or `/var/www/html` for a standard Apache server on Linux).

2.  **Start Your Web Server:**
    *   Ensure your Apache and MySQL services are running.

3.  **Access the Application:**
    *   Open your web browser and navigate to the project's URL (e.g., `http://localhost/Foodorderingsystem/`).

You should now see the May Food homepage. The application is ready to use!

---

### 2. Home Page
The home page is your starting point. From here, you can:
- See our featured food categories (Salads, Curries, Noodles, Snacks).
- Quickly navigate to the menu.
- Make a table reservation.
- Learn more about our restaurant.

### 3. Browsing the Menu
- Click on the **"Menu"** link in the navigation bar to see all our available dishes.
- The menu is organized by categories. You can also use the dropdown in the navigation bar to jump directly to a specific category like "Salads" or "Noodles".

### 4. Searching for Dishes
- On the menu page, you will find a **search bar**.
- Simply type the name of the dish you are looking for (e.g., "Mohinga").
- The menu will automatically filter to show you the matching results in real-time.

### 5. Adding Items to Your Cart
- To add an item to your cart, simply click the **"Add to cart"** button below the item's description.
- **Note:** You must be logged in to add items to your cart. If you are not logged in, a message will appear prompting you to log in or create an account.

### 6. Managing Your Cart
- You can view your cart at any time by clicking the **shopping cart icon** in the top-right corner of the page.
- In the cart, you can:
    - View all the items you've added.
    - Adjust the quantity of each item.
    - See the total price.
    - Remove items you no longer want.

### 7. Placing an Order
1.  Once you are happy with the items in your cart, click the **"Checkout"** button.
2.  You will be taken to the order review page to confirm your details.
3.  Fill in your delivery information (name, phone, address).
4.  Choose your payment method.
5.  Click **"Place Order"** to finalize your purchase.
6.  You will see an order confirmation page with all your order details.

### 8. Creating an Account & Logging In
- **Login:** Click the "Login" button in the navigation bar and enter your email and password.
- **Register:** If you don't have an account, click "Login" and then select the "Sign Up" option. Fill in your details to create your account.

### 9. Viewing Your Profile and Order History
- Once logged in, you can access your profile by clicking your name in the navigation bar and selecting **"My Profile"**.
- Here you can update your personal information.
- Select **"My Orders"** to see a complete history of your past and current orders, check their status, and submit reviews for completed orders.

### 10. Making a Reservation
- On the home page, you will find a **"Table Reservation"** section.
- Fill in your name, the number of guests, the date, and the time.
- Click **"Book A Table"** to submit your reservation request.

### 11. Admin Panel
The system also includes a comprehensive admin panel for restaurant staff to:
- Manage menu items and categories.
- Process and update customer orders.
- View and manage reservations.
- Manage user and staff accounts.
- Track earnings and site statistics.

---
Thank you for using May Food! We hope you enjoy your meal.
