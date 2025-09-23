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




// FETCH BOOKINGS DETAILS
$payment_details = fetchDetails("SELECT r.room_number, r.roomType, b.booking_id, r.rent_fee from rooms r INNER JOIN bookings b USING(room_id) INNER JOIN students s USING(student_id) WHERE b.status = 'Approved' and s.student_id = ?", $_SESSION["student_id"], $conn);
$all_payments = fetchAllDetails("SELECT p.payment_id, r.room_id, p.amount, date_format(p.payment_date, '%M,%d %Y') as payment_date , p.notes, p.status FROM payments p INNER JOIN bookings b USING(booking_id) INNER JOIN rooms r USING(room_id) WHERE p.student_id = ? order by p.payment_date desc", $_SESSION["student_id"], $conn);


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["pay_now"])) {
    $is_student_payment_exist = countWhereAllRows("SELECT p.payment_id FROM payments p WHERE p.student_id = ? and p.status = 'Pending'", $_SESSION["student_id"]);
    $is_student_paid_this_month_query = "SELECT COUNT(*) as total FROM payments WHERE student_id = ? and month(payment_date) and year(payment_date) and status = 'Approved' and booking_id = ?";
    $stmt = $conn->prepare($is_student_paid_this_month_query);
    $stmt->execute([$_SESSION["student_id"] , $_POST["booking_id"]]);
    $count = $stmt->fetch(PDO::FETCH_COLUMN);
    if($count){
        $_SESSION["error"] = "you already paid this month";
        header("Location:". $_SERVER["PHP_SELF"]);
        exit;
    }
    else{
        if (empty($is_student_payment_exist)) {
            $stmt = $conn->prepare("INSERT INTO payments(student_id,booking_id, amount ,status) values(?,? ,?,'Pending');");
            $stmt->execute([$_SESSION["student_id"], $_POST["booking_id"], $_POST["rent_fee"]]);
            $rowCount = $stmt->rowCount();
            if ($rowCount > 0) {
                $payment_details = fetchDetails("SELECT r.room_number, r.roomType, b.booking_id, r.rent_fee from rooms r INNER JOIN bookings b USING(room_id) INNER JOIN students s USING(student_id) WHERE b.status = 'Approved' and s.student_id = ?", $_SESSION["student_id"], $conn);
                $all_payments = fetchAllDetails("SELECT p.payment_id, r.room_id, p.amount, date_format(p.payment_date, '%M,%d %Y') as payment_date , p.notes, p.status FROM payments p INNER JOIN bookings b USING(booking_id) INNER JOIN rooms r USING(room_id) WHERE p.student_id = ? order by p.payment_date desc", $_SESSION["student_id"], $conn);
                $_SESSION["success"] = "payment created , status pending";
                header("Location:". $_SERVER["PHP_SELF"]);
                exit;
            }
        } else {
            $_SESSION["error"] = "you currently have pending payment";
            header("Location:". $_SERVER["PHP_SELF"]);
            exit;
        }
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
    <link rel="stylesheet" href="../css/student_payments.css">
</head>
<body>
    <main>
        <?php require_once "../component/sidebar.php" ?>
        <div class="student-payments">
            <h1>My Payments</h1>
            <div class="room-list container">
                <h1>Make Payments</h1>
                <?php if (!empty($_SESSION["error"])): ?>
                    <p class="error-message"><?= htmlspecialchars($_SESSION["error"]) ?></p>
                    <?php unset($_SESSION["error"]); ?>
                <?php endif ?>
                <?php if (!empty($_SESSION["success"])): ?>
                    <p class="success-message"><?= htmlspecialchars($_SESSION["success"]) ?></p>
                    <?php unset($_SESSION["success"]); ?>
                <?php endif ?>
                <?php if(empty($payment_details)): ?>
                        <p>No payment.</p>
                    <?php endif ?>
                <?php if ($payment_details): ?>
                    <div class="container-table">
                        <h1><?= "Room: " . htmlspecialchars($payment_details["room_number"]) . " " . htmlspecialchars($payment_details["roomType"]) ?></h1>
                        <h1><?= "Monthly Rent: " . htmlspecialchars($payment_details["rent_fee"]) ?></h1>
                    </div>
                    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
                        <input type="hidden" name="booking_id" value="<?= $payment_details["booking_id"] ?>">
                        <input type="hidden" name="rent_fee" value="<?= $payment_details["rent_fee"] ?>">
                        <button type="submit" class="submit" name="pay_now">Pay Now</button>
                    </form>
                <?php endif ?>
            </div>
            <div class="recent card">
                <h1>All payments</h1>
                <?php if(empty($all_payments)): ?>
                    <p>No payment.</p>
                <?php endif ?>
                <?php if ($all_payments): ?>
                    <div class="table-container">
                        <table>
                            <tr>
                                <th>PAYMENT ID</th>
                                <th>ROOM NO</th>
                                <th>AMOUNT</th>
                                <th>DATE</th>
                                <th>STATUS</th>
                                <th>NOTE</th>
                            </tr>
    
                            
                                <?php foreach ($all_payments as $all_payment): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($all_payment["payment_id"]) ?></td>
                                        <td><?= htmlspecialchars($all_payment["room_id"]) ?></td>
                                        <td><?= htmlspecialchars($all_payment["amount"]) ?></td>
                                        <td><?= htmlspecialchars($all_payment["payment_date"]) ?></td>
                                        <?php if ($all_payment["status"] == "Approved"): ?>
                                            <td>
                                                <p class="approved"> <?= htmlspecialchars($all_payment["status"]) ?></p>
                                            </td>
                                        <?php endif ?>
                                        <?php if ($all_payment["status"] == "Pending"): ?>
                                            <td>
                                                <p class="pending"> <?= htmlspecialchars($all_payment["status"]) ?></p>
                                            </td>
                                        <?php endif ?>
                                        <?php if ($all_payment["status"] == "Rejected"): ?>
                                            <td>
                                                <p class="rejected"> <?= htmlspecialchars($all_payment["status"]) ?></p>
                                            </td>
                                        <?php endif ?>
                                        <td><?= htmlspecialchars($all_payment["notes"]) ?></td>
                                    <tr>
                                    <?php endforeach ?>
                                
                                    </tr>
                        </table>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </main>
    <footer>
        <p>© <?= date("Y") ?> Student Dormitory Management System. All rights reserved.</p>
    </footer>
</body>

</html>