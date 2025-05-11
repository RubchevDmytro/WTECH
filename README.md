# WTECH
RougeSmare (dmitryr757@gmail.com)-Dmytro Rubchev
RubchevDmytro(xrubchev@stuba.sk)-Dmytro Rubchev
RiabichenkoMaksim(xriabichenko@stuba.sk)- Riabichenko Maksim
WTECH Project
Welcome to the WTECH project repository! This project is a web application designed to manage products, categories, and shopping cart functionality. It is built using Laravel, PHP, and Blade templating, with a focus on e-commerce features.
Table of Contents

Description
Features
Prerequisites
Installation
Running the Project
Usage
Contributing
License
Contact

Description
WTECH is an e-commerce platform that allows users to browse products, add them to a cart, and manage inventory (for admins). The project includes a user-friendly interface, admin panel, and basic shopping cart functionality. It uses PostgreSQL as the database.
Features

User authentication and authorization
Product management (create, edit, delete)
Shopping cart with quantity control
Image upload for products (stored as base64 in the database)
Admin panel for managing categories and products
Responsive design

Prerequisites
Before you begin, ensure you have the following installed:

PHP 8.1 or higher
Composer (https://getcomposer.org/)
Node.js and NPM (for frontend assets)
Git (https://git-scm.com/)
Access to the PostgreSQL database server (host, port, database name, username, password)

Installation

Clone the repository:
```
git clone https://github.com/RubchevDmytro/WTECH.git
cd WTECH/backend
```


Install PHP dependencies:
```
composer install
```


Install JavaScript dependencies:
```
npm install
```


Compile frontend assets:
```
npm run dev
```



Running the Project

Start the Laravel development server:
```
php artisan serve
```


By default, the app will be available at http://localhost:8000.


Open your browser and navigate to http://localhost:8000 to see the application.


Usage

Register or log in as a user to access the shopping cart.
Admins can log in to the admin panel (e.g., /admin) to manage products and categories.
Admin login "admin@example.com" and password "password"
Explore the product list, add items to the cart, and proceed to checkout.

License
This project is licensed under the MIT License. See the LICENSE file for details.
Contact
For questions or support, contact the project maintainer:

GitHub: RubchevDmytro
Email: [xriabichenko@stuba.sk] and [xrubchev@stuba.sk]

