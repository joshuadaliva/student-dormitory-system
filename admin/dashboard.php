<?php

    require_once "../db/config.php";
    require_once "../functions/functions.php";
    session_start();


    isStudent("../student/dashboard.php");
    if(isset($_SESSION["user_type"])){
        if( $_SESSION["user_type"] !== "admin") {
            header("Location: " . "./login.php");
        }
    }
    else{
        header("Location: " . "./login.php");
    }


    $admin_id = $_SESSION["admin_id"];
    $student_count = countAllRows("SELECT COUNT(student_id) FROM students");
    $rooms_count = countAllRows("SELECT COUNT(room_id) FROM rooms");
    $pending_bookings_count = countWhereAllRows("SELECT COUNT(booking_id) FROM bookings where status = ?", "Pending");
    $pending_payments_count = countWhereAllRows("SELECT COUNT(payment_id) FROM payments where status = ?", "Pending");


    $stmt = $conn->prepare("SELECT s.name , r.room_number, d.booking_date, d.status from bookings d INNER join students s using (student_id) INNER JOIN rooms r using (room_id); ");
    $stmt->execute();
    $all_recent_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
</head>
<body>
    <?php require_once "../component/sidebar.php" ?>
    <div class="admin-dashboard">
        <?php if(isset($_SESSION["success_login"])): ?>
            <p class="success-login"><?= htmlspecialchars($_SESSION["success_login"]) ?></p>
        <?php endif ?>
        <h1>Admin Dashboard</h1>
        <div class="container">
            <div class="all-overview-section">
                <div class="card card-overview">
                    <div>
                        <i class="fas fa-user-graduate fa-2x" style="color: #1d4ed8; padding:10px; background-color:#dbeafe; border-radius:10px"></i>
                    </div>
                    <div>
                        <h1>Total Students</h1>
                        <p><?= htmlspecialchars($student_count) ?></p>
                    </div>
                </div>
                <div class="card card-overview">
                    <div>
                        <i class="fas fa-door-open fa-2x" style="color: #15803d; padding:10px; background-color:#dcfce7; border-radius:10px"></i>
                    </div>
                    <div>
                        <h1>Total Rooms</h1>
                        <p><?= htmlspecialchars($rooms_count) ?></p>
                    </div>
                </div>
                <div class="card card-overview">
                    <div>
                        <i class="fas fa-calendar-check fa-2x" style="color: #a16207; padding:10px; background-color:#fef9c3; border-radius:10px"></i>
                    </div>
                    <div>
                        <h1>Pending Bookings</h1>
                        <p><?= htmlspecialchars($pending_bookings_count) ?></p>
                    </div>
                </div>
                <div class="card card-overview">
                    <div>
                        <i class="fas fa-credit-card fa-2x" style="color:#b91c1c; padding:10px; background-color:#fee2e2; border-radius:10px"></i>
                    </div>
                    <div>
                        <h1>Pending Payments</h1>
                        <p><?= htmlspecialchars($pending_payments_count) ?></p>
                    </div>
                </div>
                
            </div>
            <div class="recent card">
                <h1>Recent Bookings</h1>
                <div class="container-table">
                    <table>
                        <tr>
                            <th>STUDENT</th>
                            <th>ROOM</th>
                            <th>DATE</th>
                            <th>STATUS</th>
                        </tr>
                        <?php if($all_recent_bookings): ?>
                            <?php foreach($all_recent_bookings as $recent_booking): ?>
                                <tr>
                                    <td><?= htmlspecialchars($recent_booking["name"]) ?></td>
                                    <td><?= htmlspecialchars($recent_booking["room_number"]) ?></td>
                                    <td><?= htmlspecialchars($recent_booking["booking_date"]) ?></td>
                                    <td><?= htmlspecialchars($recent_booking["status"]) ?></td>
                                </tr>
                            <?php endforeach?>
                        <?php endif ?>
                    </table>
                </div>
            </div>
            <div class="recent card">
                <h1>Recent Payments</h1>
                <div class="container-table">
                    <table>
                        <tr>
                            <th>STUDENT</th>
                            <th>AMOUNT</th>
                            <th>DATE</th>
                            <th>STATUS</th>
                        </tr>
                        <tr>
                            <td>JOSHUA DALIVA</td>
                            <td>1000</td>
                            <td>AUG 20 2025</td>
                            <td>Pendsadsdasdasdsding</td>
                        </tr>
                        <tr>
                            <td>JOSHUA DALIVA</td>
                            <td>31232</td>
                            <td>AUG 20 2025</td>
                            <td>Pending</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <footer>
            <p>© <?= date("Y") ?> Student Dormitory Management System. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>