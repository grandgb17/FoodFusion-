# FoodFusion

A full-stack recipe web application for discovering, saving, and sharing recipes from cuisines around the world.

---

## What It Does

FoodFusion is a PHP and MySQL web application where users can register, browse a large collection of recipes, save favourites to their personal cookbook, like recipes, and download recipe cards. It includes a full authentication system, user profiles, culinary resources, and an educational section.

---

## Features

- **User Authentication** — Register, login, logout, and forgot password flow
- **Recipe Browser** — Browse a large collection of recipes with images
- **Recipe Detail Pages** — Full ingredient and instruction pages per recipe
- **Personal Cookbook** — Save and manage your favourite recipes
- **Like System** — Toggle likes on recipes
- **Download Recipes** — Download individual or all recipes
- **User Profiles** — View and update your profile
- **Culinary Resources** — Educational content about cooking techniques
- **Contact Page** — Contact form for user enquiries
- **Privacy Policy Page**
- **Responsive Design** — Works across different screen sizes

---

## Project Structure

```
FoodFusion/
├── db.php                        # Database connection
├── Home.php                      # Homepage
├── Home.js                       # Homepage JavaScript
├── Home1.css                     # Homepage styles
├── Login.php                     # Login page
├── login1.css                    # Login styles
├── register.php                  # Registration page
├── forgot_password.php           # Password reset
├── logout.php                    # Session logout
├── Recipe.php                    # Recipe listing page
├── Recipe1.js                    # Recipe page JavaScript
├── Recipes.css                   # Recipe listing styles
├── recipe_detail.php             # Individual recipe page
├── recipe_details.css            # Recipe detail styles
├── COOKBOOK.php                  # Personal cookbook page
├── cookbook.css                  # Cookbook styles
├── cookbook1.js                  # Cookbook JavaScript
├── save_recipe.php               # Save recipe to cookbook
├── toggle_save.php               # Toggle saved status
├── toggle_like.php               # Toggle like on recipe
├── download_recipe.php           # Download single recipe
├── download_all_recipes.php      # Download all recipes
├── Profile.php                   # User profile page
├── profile.css                   # Profile styles
├── update_profile.php            # Update profile handler
├── culinary_resources.php        # Culinary resources page
├── culinary_resources1.css       # Culinary resources styles
├── educational_resources.php     # Educational content page
├── educational_resources.css     # Educational styles
├── about.php                     # About page
├── about.css                     # About styles
├── contact.php                   # Contact page
├── privacy_policy.php            # Privacy policy
├── save_tip.php                  # Save cooking tip
├── video_proxy.php               # Video proxy handler
├── imgs/                         # General site images
├── imgs_recipe/                  # Recipe images (90+ recipes)
├── foodfusion.sql                # Full database schema
├── insert_recipes_final.sql      # Recipe seed data
├── insert_community.sql          # Community data
└── foodfusion_update.sql         # Database updates
```

---

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- A local server environment — [XAMPP](https://www.apachefriends.org/) is recommended for Windows

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/grandgb17/FoodFusion-.git
```

### 2. Set up a local server

Download and install **XAMPP** from apachefriends.org. Start both **Apache** and **MySQL** from the XAMPP control panel.

### 3. Copy the project files

Copy the cloned folder into:
```
C:/xampp/htdocs/FoodFusion
```

### 4. Create the database

1. Open your browser and go to `http://localhost/phpmyadmin`
2. Click **New** → name the database `foodfusion` → click **Create**
3. Click on the `foodfusion` database → click the **Import** tab
4. Import `foodfusion.sql` first
5. Then import `insert_recipes_final.sql`
6. Then import `insert_community.sql`
7. Then import `foodfusion_update.sql`

### 5. Configure the database connection

Open `db.php` — the default settings work with XAMPP out of the box:

```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "foodfusion";
```

If your MySQL has a password set, update `$pass` accordingly.

### 6. Run the app

Open your browser and go to:
```
http://localhost/FoodFusion/Home.php
```

---

## Tech Stack

| Technology | Purpose |
|---|---|
| PHP | Backend logic and server-side rendering |
| MySQL | Database |
| HTML / CSS | Frontend structure and styling |
| JavaScript | Dynamic interactions |
| XAMPP | Local development server |

---

## Notes

- The database credentials in `db.php` are for local development only (`root` with no password is the XAMPP default)
- Recipe images are included in the `imgs_recipe/` folder
- This project was built as part of a B.Sc. Cybersecurity degree to demonstrate full-stack web development skills

---

## Author

**Granthik Bhor**  
B.Sc. Cybersecurity — Vidyalankar Institute of International Education  
[github.com/grandgb17](https://github.com/grandgb17)
