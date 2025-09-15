<?php 

    session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dormitory Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <!-- navigation bar -->
    <nav>
        <h1 class="nav-title"><a href="./index.php">DORMITORY SYSTEM</a></h1>
        <ul class="nav-links">
            <li><a href="#features">Features</a></li>
            <li><a href="#rooms">Rooms</a></li>
            <li><a href="">Testimonials</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="">Location</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        <div class="nav-btn">
            <?php if(!isset($_SESSION["user_type"])): ?>
                <button class="login"><a href="./student/login.php">Login</a></button>
                <button class="signup"><a href="./student/signup.php">Signup</a></button>
            <?php endif ?>
            <?php if(isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "student"): ?>
                <button class="signup"><a href="./student/dashboard.php">Dashboard</a></button>
            <?php endif ?>
            <?php if(isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "admin"): ?>
                <button class="signup"><a href="./student/dashboard.php">Dashboard</a></button>
            <?php endif ?>
        </div>
        <!-- <i class="fas fa-lg fa-bars hamburger"></i> -->
         <button class="hamburger">&#9776;</button>
    </nav>
    <div class="scrollable-nav">
        <ul>
            <li><a href="">Features</a></li>
            <li><a href="">Rooms</a></li>
            <li><a href="">Testimonials</a></li>
            <li><a href="">About</a></li>
            <li><a href="">Location</a></li>
            <li><a href="">Contact</a></li>
            <?php if(!isset($_SESSION["user_type"])): ?>
                <li class="active-signup"><a href="./student/signup.php">Signup</a></li>
                <li><a href="./student/login.php">Login</a></li>
            <?php endif ?>
            <?php if(isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "student"): ?>
                <li class="active-signup"><a href="./student/dashboard.php">Dashboard</a></li>
            <?php endif ?>
            <?php if(isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "admin"): ?>
                <li class="active-signup"><a href="./admin/dashboard.php">Dashboard</a></li>
            <?php endif ?>
        </ul>
    </div>
    <!-- header -->
    <header>
        <h1>Student Dormitory Management System</h1>
        <p>Manage rooms, bookings, and payments all in one intuitive platform. </p>
        <div>
            <button class="get-started">Get Started</button>
            <button class="learn-more">Learn More</button>
        </div>
    </header>
    <!-- section feautres -->
    <section class="features-section" id="features">
        <h1>Features That Make Dorm Life Easier </h1>
        <div class="container">
            <div class="card">
                <div class="font-icon-container font-icon-container-door">
                    <i class="fas fa-door-open"></i>
                </div>
                <h2>Room Allocation</h2>
                <p>Easily assign and manage rooms for  <br>students. </p>
            </div>
            <div class="card">
                <div class="font-icon-container font-icon-container-profile">
                    <i class="fas fa-users"></i>
                </div>
                <h2>Resident Profiles</h2>
                <p>Maintain detailed profiles for each resident<br> including contact info. </p>
            </div>
            <div class="card">
                <div class="font-icon-container font-icon-container-money">
                <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h2>Payment Tracking</h2>
                <p>Monitor rent payments and generate<br>invoices with ease. </p>
            </div>
        </div>
    </section>
    <section class="dorm-showcase-section" id="rooms">
        <h1>Explore Our Dorm Rooms</h1>
        <div class="container">
            <div class="card">
                <img src="https://storage.googleapis.com/a1aa/image/788ef91a-53dd-40a5-a318-6097d2e82d2a.jpg" alt="">
                <h2>Single Room</h2>
                <p>Cozy private room with study desk and ample storage. </p>
                <p class="room-availability">Available</p>
            </div>
            <div class="card">
                <img alt="Double dorm room with two beds, shared desk, and wardrobe" src="https://storage.googleapis.com/a1aa/image/05cb8872-7d4b-49d4-53f2-f249850c3734.jpg"/>
                <h2>Double Room</h2>
                <p>Cozy private room with study desk and ample storage. </p>
                <p class="room-availability">Available</p>
            </div>
            <div class="card">
                <img src="https://storage.googleapis.com/a1aa/image/56823748-e73f-487e-d859-d66b03d96006.jpg" alt="">
                <h2>Suite Room</h2>
                <p>Cozy private room with study desk and ample storage. </p>
                <p class="room-availability">Available</p>
            </div>
        </div>
    </section>
    <section class="about-section" id="about">
        <img src="https://storage.googleapis.com/a1aa/image/56823748-e73f-487e-d859-d66b03d96006.jpg" alt="">
        <div>
            <h1>About The Dormitory </h1>
            <p>this sytem is a comprehensive student dormitory management system designed to streamline the daily operations of dormitories. <br> From room assignments to maintenance tracking, our platform helps administrators and students stay connected and organized. </p>
            <p>Our mission is to create a seamless living experience for students while reducing administrative overhead for dormitory staff. </p>
        </div>
    </section>
    <section class="contact-us" id="contact">
        <h1>Contact Us</h1>
        <form>
            <label for="name">Name:</label><br>
            <input type="text" name="name" id="name" placeholder="Your Full Name"><br><br>
            <label for="name">email:</label><br>
            <input type="text" name="email" id="email" placeholder="Your Full email"><br><br>
            <label for="name">message:</label><br>
            <input type="text" name="message" id="message"  class="message" placeholder="Your Full message"><br><br>
            <input type="submit" class="submit" value="submit" id="submit">
        </form>
    </section>
    <footer>
        <h1>DormManage</h1>
        <p>@ <?= date("Y") ?> DormManage, All rights reserved</p>
        <div>
            <img src="" alt="logo">
            <img src="" alt="logo">
            <img src="" alt="logo">
            <img src="" alt="logo">
        </div>
    </footer>
    <script src="./js/index.js"></script>
</body>
</html>