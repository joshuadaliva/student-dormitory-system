<?php

require_once "../db/config.php";
require_once "../functions/functions.php";

session_start();

isAdmin("../admin/dashboard.php");
if (isset($_SESSION["user_type"])) {
    if ($_SESSION["user_type"] !== "student") {
        header("Location: " . "./login.php");
    }
} else {
    header("Location: " . "./login.php");
}


// FETCH ALL ROOMS
$stmt = $conn->prepare("SELECT * from rooms where status = 'Available'");
$stmt->execute();
$allRooms = $stmt->fetchAll(PDO::FETCH_ASSOC);


// FETCH BOOKINGS DETAILS
$booking_details = fetchDetails("SELECT r.room_number, r.roomType, b.booking_id, r.rent_fee from rooms r INNER JOIN bookings b USING(room_id) INNER JOIN students s USING(student_id) WHERE b.status = 'Approved' and s.student_id = ?", $_SESSION["student_id"], $conn);

$error = "";
$success = "";


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["pay_now"])) {
    $is_student_book_room = countWhereAllRows("SELECT booking_id from bookings where student_id = ? and status != 'Rejected'", $_SESSION["student_id"]);
    if (empty($is_student_book_room)) {
        $stmt = $conn->prepare("INSERT INTO bookings(student_id,room_id,status) values(?,?, 'Pending');");
        $stmt->execute([$_SESSION["student_id"], $_POST["room_id"]]);
        $rowCount = $stmt->rowCount();
        if ($rowCount > 0) {
            $success = "room booked , status pending";
            $booking_details = fetchAllDetails("SELECT r.room_number , r.roomType, b.booking_date, b.status, r.description, r.imagePath, r.rent_fee FROM rooms r INNER JOIN bookings b USING(room_id) INNER JOIN students USING(student_id) WHERE student_id = ?", $_SESSION["student_id"], $conn);
        }
    } else {
        $error = "you currently booked a room";
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
    <!-- <link rel="stylesheet" href="../css/student_bookings.css"> -->
</head>

<style>
    .student-payments {
        display: flex;
        flex-direction: column;
        margin-left: 265px;
        width: 100%;
    }

    .student-payments h1 {
        margin-bottom: 0;
        padding: 0;
        color: #1f2937;
    }

    .room-list {
        padding: 0px 20px;
        padding-bottom: 20px;
    }

    .room-list h1 {
        font-size: 20px;
    }

    .container {
        box-shadow: 0px 2px 5px rgb(0, 0, 0, 0.2);
        border-radius: 10px;
        background-color: white;
        margin-top: 50px;
        margin-right: 20px;
    }

    .container-table {
        padding: 5px 20px;
        border-radius: 10px;
        position: relative;
        padding-bottom: 10px;
        margin-top: 10px;
        background-color: lightblue;
    }

    .container-table h1 {
        font-size: 15px;
        margin-bottom: 0;
        margin-top: 20px;
    }

    .container-table p {
        margin-bottom: 0;
        color: #6b7280;
        margin-top: 5px;
        font-size: 12px;
    }

    .submit {
        padding: 10px 20px;
        background-color: #3b82f6;
        border: none;
        color: white;
        border-radius: 10px;
        margin: 20px 0px;
        cursor: pointer;
        width: 100%;
    }

    footer {
        background-color: #111827;
        padding: 20px;
        text-align: center;
        color: white;
        margin-top: 4rem;
    }
</style>

<body>
    <?php require_once "../component/sidebar.php" ?>
    <div class="student-payments">
        <h1>My Payments</h1>
        <div class="room-list container">
            <h1>Make Payments</h1>
            <?php if ($booking_details): ?>
                <div class="container-table">
                    <h1><?= "Room: " . htmlspecialchars($booking_details["room_number"]) . " " . htmlspecialchars($booking_details["roomType"]) ?></h1>
                    <h1><?= "Monthly Rent: " . htmlspecialchars($booking_details["rent_fee"]) ?></h1>
                </div>
                <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
                    <input type="hidden" name="booking_details" value="<?= $booking_details["booking_id"] ?>">
                    <button type="submit" class="submit" name="pay_now">Pay Now</button>
                </form>
            <?php endif ?>
        </div>
        <footer>
            <p>© <?= date("Y") ?> Student Dormitory Management System. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>