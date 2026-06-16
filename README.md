# Inventory Management System

## App Description
This Inventory Management System runs in a browser. User accounts are designed for company users only. Only admins can create accounts for system users. The dashboard displays data such as monthly revenue, inventory status, top-selling items, stock movement, and recent transactions. Users can manage purchase orders, sales orders, items, suppliers, customers, and item attributes. Admins can manage everything, including all user accounts. The system also has settings for customizing low-stock and out-of-stock color indicators and updating user accounts. The tech stack used is Laravel, Vue.js, and Tailwind CSS.

## Installation
1. Clone the repository or download the zip file and extract it
2. Open the project in VS Code (or anything alike)
3. Install Node dependencies
    - Change directory to frontend folder
    - Run: `npm install`
4. Install PHP dependencies
    - Change directory to backend folder
    - Run: `composer install`
5. Environment Setup
    - Backend: Copy `backend/.env.example` to `backend/.env`
    - Frontend: Copy `frontend/.env.example` to `frontend/.env`
6. Generate application key: `php artisan key:generate`
7. Run database migration `php artisan migrate` (make sure xampp server (or anything alike) is running)
8. Run seeders: `php artisan db:seed` (required for ColorSeeder and other default data)

## Usage

Run these two commands in separate terminals:
- Frontend: `npm run dev`
- Backend: `php artisan serve`
