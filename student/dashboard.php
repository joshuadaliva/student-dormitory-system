<?php

    require_once "../db/config.php";
    require_once "../functions/functions.php";

    session_start();

    isAdmin("../admin/dashboard.php");
    if(isset($_SESSION["user_type"])){
        if( $_SESSION["user_type"] !== "student") {
            header("Location: " . "./login.php");
        }
    }
    else{
        header("Location: " . "./login.php");
    }


    

    

    $student_id = $_SESSION["student_id"];
    $query = "SELECT name,email,program,contact  from students where student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt -> execute([$student_id]);
    $student_info = $stmt->fetch(PDO::FETCH_ASSOC);


    $room_info = fetchDetails("SELECT r.room_number, r.roomType, r.rent_fee, date_format(b.booking_date, '%M %d, %Y') as booking_date from rooms r INNER JOIN bookings b USING (room_id) where b.status = 'Approved' and student_id = ?", $_SESSION["student_id"], $conn);
    $all_payments = fetchAllDetails("SELECT p.payment_id, r.room_id, p.amount, date_format(p.payment_date, '%M,%d %Y') as payment_date , p.notes, p.status FROM payments p INNER JOIN bookings b USING(booking_id) INNER JOIN rooms r USING(room_id) WHERE p.student_id = ? order by p.payment_date desc limit 2", $_SESSION["student_id"], $conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/student_dashboard.css">
</head>
<body>
    <?php require_once "../component/sidebar.php" ?>
    <div class="student-dashboard">
        <?php if(isset($_SESSION["success_login"])): ?>
            <p class="success-login"><?= htmlspecialchars($_SESSION["success_login"]) ?></p>
            <?php unset($_SESSION["success_login"]); ?>
        <?php endif ?>
        <h1>Student Dashboard</h1>
        <div class="container">
            <div class="info">
                <h1><i class="fas fa-user-circle" style="color: var(--blue);"></i>  My Information</h1>
                <div class="container-info">
                    <div>
                        <p class="label">Name:</p>
                        <?= htmlspecialchars($student_info["name"]) ?>
                    </div>
                    <div>
                        <p class="label">Email:</p>
                        <?= htmlspecialchars($student_info["email"]) ?>
                    </div>
                    <div>
                        <p class="label">Program:</p>
                        <?= htmlspecialchars($student_info["program"]) ?>
                    </div>
                    <div>
                        <p class="label">Contact:</p>
                        <?= htmlspecialchars($student_info["contact"]) ?>
                    </div>
                </div>
            </div>
            <div class="room">
                <h1><i class="fas fa-bed" style="color: green;"></i>  My Room</h1>
                <?php  if(empty($room_info)): ?>
                    <div class="room-container-none">
                        <i class="fas fa-door-closed" style="color: #d1d5db; font-size:2.8rem"></i>
                        <p style="text-align: center;">You don't have an approved room <br> booking yet.</p>
                        <button><a href="bookings.php">Book Now</a></button>
                    </div>
                <?php endif ?>
                <?php  if($room_info): ?>
                    <div class="container-info">
                        <div>
                            <p class="label">Room number:</p>
                            <?= htmlspecialchars($room_info["room_number"]) ?>
                        </div>
                        <div>
                            <p class="label">Room Type:</p>
                            <?= htmlspecialchars($room_info["roomType"]) ?>
                        </div>
                        <div>
                            <p class="label">Rent fee:</p>
                            <?= htmlspecialchars($room_info["rent_fee"]) ?>
                        </div>
                        <div>
                            <p class="label">Booking date:</p>
                            <?= htmlspecialchars($room_info["booking_date"]) ?>
                        </div>
                    </div>
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
        <footer>
            <p>© <?= date("Y") ?> Student Dormitory Management System. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>