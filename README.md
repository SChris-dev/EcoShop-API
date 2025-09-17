# EcoShop - Eco-Friendly E-Commerce API

EcoShop is a RESTful API for an eco-friendly e-commerce platform built with Laravel. This API provides comprehensive functionality for managing products, orders, and reviews with proper authentication and authorization.

## Features

### Product Management
- **Admin**: Full CRUD operations (Create, Read, Update, Delete)
- **Users**: Read-only access (List and view product details)
- **Guests**: Read-only access (List and view product details)
- Product stock management with automatic reduction on orders
- Average rating calculation based on reviews

### Order System
- **Authenticated Users**: Create orders with multiple products
- **Stock Validation**: Prevents orders when insufficient stock
- **Automatic Stock Reduction**: Updates product stock after successful orders
- **Authorization**: Users can only view their own orders, admins can view all
- **Database Transactions**: Ensures data consistency during order creation

### Review System
- **Purchase Validation**: Only users who bought a product can review it
- **One Review Per Product**: Users can only submit one review per product
- **Rating System**: 1-5 star rating with optional comment
- **Public Access**: Anyone can view reviews

### Authentication & Authorization
- **Laravel Sanctum**: Token-based API authentication
- **Role-based Access**: Admin and User roles
- **Protected Routes**: Proper middleware protection
- **API Versioning**: `/api/v1/` prefix for future compatibility

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd EcoShop
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   Update your `.env` file with MySQL database settings:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ecoshop
   DB_USERNAME=root
   DB_PASSWORD=your-password
   ```

5. **Create Database**
   ```bash
   mysql -u root -p -e "CREATE DATABASE ecoshop;"
   ```

6. **Run Migrations & Seeders**
   ```bash
   php artisan migrate
   php artisan db:seed --class=EcoShopSeeder
   ```

7. **Start the Development Server**
   ```bash
   php artisan serve
   ```

## API Documentation

### Base URL
```
http://localhost:8000/api/v1
```

### Authentication

#### Register User
```http
POST /api/v1/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "user" // optional, defaults to "user"
}
```

#### Login
```http
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

#### Logout
```http
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

### Products

#### Get All Products (Public)
```http
GET /api/v1/products
```

#### Get Single Product (Public)
```http
GET /api/v1/products/{id}
```

#### Create Product (Admin Only)
```http
POST /api/v1/admin/products
Authorization: Bearer {admin-token}
Content-Type: application/json

{
    "name": "Eco Product",
    "description": "Environmentally friendly product",
    "price": 19.99,
    "stock": 50
}
```

#### Update Product (Admin Only)
```http
PUT /api/v1/admin/products/{id}
Authorization: Bearer {admin-token}
Content-Type: application/json

{
    "name": "Updated Product Name",
    "price": 24.99
}
```

#### Delete Product (Admin Only)
```http
DELETE /api/v1/admin/products/{id}
Authorization: Bearer {admin-token}
```

### Orders

#### Get User Orders
```http
GET /api/v1/user/orders
Authorization: Bearer {token}
```

#### Create Order
```http
POST /api/v1/user/orders
Authorization: Bearer {token}
Content-Type: application/json

{
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        },
        {
            "product_id": 3,
            "quantity": 1
        }
    ]
}
```

#### Get Single Order
```http
GET /api/v1/user/orders/{id}
Authorization: Bearer {token}
```

#### Update Order Status (Admin Only)
```http
PUT /api/v1/admin/orders/{id}
Authorization: Bearer {admin-token}
Content-Type: application/json

{
    "status": "completed"
}
```

### Reviews

#### Get Product Reviews (Public)
```http
GET /api/v1/products/{product_id}/reviews
```

#### Create Review (Authenticated, Must Have Purchased)
```http
POST /api/v1/user/products/{product_id}/reviews
Authorization: Bearer {token}
Content-Type: application/json

{
    "rating": 5,
    "comment": "Great eco-friendly product!"
}
```

#### Update Review
```http
PUT /api/v1/user/reviews/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "rating": 4,
    "comment": "Updated review"
}
```

#### Delete Review
```http
DELETE /api/v1/user/reviews/{id}
Authorization: Bearer {token}
```

## Default Test Accounts

After running the seeder, you can use these test accounts:

### Admin Account
- **Email**: admin@ecoshop.com
- **Password**: password
- **Role**: admin

### User Account
- **Email**: user@ecoshop.com
- **Password**: password
- **Role**: user

## Database Schema

### Users
- `id`, `name`, `email`, `password`, `role` (admin/user), `timestamps`

### Products
- `id`, `name`, `description`, `price`, `stock`, `timestamps`

### Orders
- `id`, `user_id`, `total_amount`, `status`, `timestamps`

### Order Items
- `id`, `order_id`, `product_id`, `quantity`, `price`, `timestamps`

### Reviews
- `id`, `user_id`, `product_id`, `rating`, `comment`, `timestamps`
- **Unique constraint**: (user_id, product_id)

## Key Business Logic

### Order Creation Process
1. Validate request data and stock availability
2. Start database transaction
3. Create order record
4. Create order items
5. Reduce product stock
6. Commit transaction or rollback on error

### Review Restrictions
- Users can only review products they have purchased
- One review per user per product
- Reviews are public but only owners (or admins) can modify/delete

### Authorization Rules
- **Guests**: Can view products and reviews
- **Users**: Can create orders, view own orders, create/edit own reviews
- **Admins**: Full access to all resources

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
