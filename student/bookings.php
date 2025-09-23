    <?php
    session_start();

    require_once "../db/config.php";
    require_once "../functions/functions.php";


    isAdmin("../admin/dashboard.php");
    if (isset($_SESSION["user_type"])) {
        if ($_SESSION["user_type"] !== "student") {
            header("Location: " . "./login.php");
        }
    } else {
        header("Location: " . "./login.php");
    }


    // FETCH ALL ROOMS
    $stmt = $conn->prepare("SELECT * from rooms where status = 'Available' order by created_at desc");
    $stmt->execute();
    $allRooms = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // FETCH BOOKINGS DETAILS
    $booking_details = fetchAllDetails("SELECT r.room_number , r.roomType,date_format(b.booking_date, '%M %d, %Y') as booking_date, b.status, r.description, r.imagePath, r.rent_fee FROM rooms r INNER JOIN bookings b USING(room_id) INNER JOIN students USING(student_id) WHERE student_id = ? order by b.booking_date desc", $_SESSION["student_id"], $conn);


    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_bookings"])) {
        $is_student_book_room = countWhereAllRows("SELECT booking_id from bookings where student_id = ? and status != 'Rejected' and status != 'Checkout'", $_SESSION["student_id"]);
        if (empty($is_student_book_room)) {
            $stmt = $conn->prepare("INSERT INTO bookings(student_id,room_id,status) values(?,?, 'Pending');");
            $stmt->execute([$_SESSION["student_id"], $_POST["room_id"]]);
            $rowCount = $stmt->rowCount();
            if ($rowCount > 0) {
                $_SESSION["success"] = "room booked , status pending";
                $booking_details = fetchAllDetails("SELECT r.room_number , r.roomType, b.booking_date, b.status, r.description, r.imagePath, r.rent_fee FROM rooms r INNER JOIN bookings b USING(room_id) INNER JOIN students USING(student_id) WHERE student_id = ? order by b.booking_date desc", $_SESSION["student_id"], $conn);
                header("Location: ". $_SERVER["PHP_SELF"]);
                exit;
            }
        } else {
            $_SESSION["error"] = "you currently booked a room";
            header("Location: ". $_SERVER["PHP_SELF"]);
            exit;
        }
    }

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="../css/student_bookings.css">
    </head>

    <body>
        <main>
            <?php require_once "../component/sidebar.php" ?>
            <div class="student-bookings">
                <?php if (!empty($_SESSION["error"])): ?>
                    <p class="error-message"><?= htmlspecialchars($_SESSION["error"]) ?></p>
                    <?php unset($_SESSION["error"]); ?>
                <?php endif ?>
                <?php if (!empty($_SESSION["success"])): ?>
                    <p class="success-message"><?= htmlspecialchars($_SESSION["success"]) ?></p>
                    <?php unset($_SESSION["success"]); ?>
                <?php endif ?>
                <h1>Bookings</h1>
                <div class="container1">
                    <div class="rooms-container">
                        <h1><i class="fas fa-door-open" style="color: blue; font-size:20px; margin-right:12px"></i> Available Rooms</h1>
                        <?php if(empty($allRooms)): ?>
                            <p>No rooms yet.</p>
                        <?php endif ?>
                        <div class="container-card">
                            <?php if ($allRooms): ?>
                                <?php foreach ($allRooms as $rooms): ?>
                                    <div class="card">
                                        <img src="<?= htmlspecialchars($rooms["imagePath"]) ?>" alt="">
                                        <h1><?= htmlspecialchars($rooms["room_number"]) . " " . htmlspecialchars($rooms["roomType"]) ?></h1>
                                        <p class="description"><?= htmlspecialchars($rooms["description"]) ?></p>
                                        <div class="books-rent-btn">
                                            <p class="pay-isavaible"><?= htmlspecialchars($rooms["rent_fee"])  . " /month <br> " . htmlspecialchars($rooms["status"])  ?> </p>
                                            <?php if($rooms["status"] === "Available"): ?>
                                                <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
                                                    <input type="hidden" name="room_id" value="<?= $rooms["room_id"] ?>">
                                                    <button type="submit" name="add_bookings">Book Now</button>
                                                </form>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            <?php endif ?>
                        </div>
                    </div>
        
                    <div class="room-list container">
                        <h1><i class="fas fa-calendar-check " style="font-size:20px; margin-right:12px; color:#16a34a"></i>My Bookings</h1>
                        <?php if(empty($booking_details)): ?>
                            <p>No bookings.</p>
                        <?php endif ?>
                        <?php if ($booking_details): ?>
                            <?php foreach ($booking_details as $booking_detail) : ?>
                                <div class="container-table">
                                    <h1><?= "Room: " . htmlspecialchars($booking_detail["room_number"]) . " " . htmlspecialchars($booking_detail["roomType"]) ?></h1>
                                    <p><?= "Booked on " . htmlspecialchars($booking_detail["booking_date"]) ?></p>
                                    <h1><?= "Monthly Rent: " . htmlspecialchars($booking_detail["rent_fee"]) ?></h1>
                                    <?php if ($booking_detail["status"] == "Approved"): ?>
                                        <p class="status approved"> <?= htmlspecialchars($booking_detail["status"]) ?></p>
                                        <button><a href="payment.php">Pay Now</a></button>
                                    <?php endif ?>
                                    <?php if ($booking_detail["status"] == "Pending"): ?>
                                        <p class="status pending"> <?= htmlspecialchars($booking_detail["status"]) ?></p>
                                    <?php endif ?>
                                    <?php if ($booking_detail["status"] == "Rejected"): ?>
                                        <p class="status rejected"> <?= htmlspecialchars($booking_detail["status"]) ?></p>
                                    <?php endif ?>
                                    <?php if ($booking_detail["status"] == "Checkout"): ?>
                                        <p class="status checkout"> <?= htmlspecialchars($booking_detail["status"]) ?></p>
                                    <?php endif ?>
                                </div>
                            <?php endforeach ?>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </main>
        <footer>
            <p>© <?= date("Y") ?> Student Dormitory Management System. All rights reserved.</p>
        </footer>
    </body>

    </html>