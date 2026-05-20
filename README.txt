QUICKBITE UPGRADED PROJECT

1. Extract this folder into:
   C:\xampp\htdocs\quickbite

2. Start Apache and MySQL from XAMPP.

3. Open phpMyAdmin:
   http://localhost/phpmyadmin

4. Create a database named:
   quickbite_db

5. Import:
   database/quickbite.sql

6. Default admin login:
   Email: admin@quickbite.com
   Password: admin123

7. Open the project:
   http://localhost/quickbite/

NOTES
- Uploaded food images are stored in assets/images/foods/
- Seed menu items expect local image files:
  burger.jpg
  pizza.jpg
  chicken.jpg
  shawarma.jpg
  drink.jpg
  french-fries.jpg

If you do not have those images yet, upload replacement meals from the admin menu page.


New behavior:
- normal users login to dashboard.php
- admins login to admin/dashboard.php
- admin nav includes Add Dish button
