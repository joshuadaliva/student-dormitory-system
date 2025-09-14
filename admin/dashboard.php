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
    

    $stmt = $conn->prepare("SELECT s.name , r.room_number, date_format(b.booking_date, '%M %d, %Y') as booking_date, b.status from bookings b INNER join students s using (student_id) INNER JOIN rooms r using (room_id) order by b.booking_date desc limit 5");
    $stmt->execute();
    $all_recent_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $all_payments = fetchAllDetails("SELECT p.payment_id, s.name, p.amount, date_format(p.payment_date, '%M %d, %Y') as date_payment, p.status, p.notes FROM payments p INNER JOIN students s USING (student_id) INNER JOIN bookings USING (booking_id) INNER JOIN rooms r USING (room_id) order by p.payment_date desc limit 5", null , $conn);

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
    <main>
        <?php require_once "../component/sidebar.php" ?>
        <div class="admin-dashboard">
            <?php if(isset($_SESSION["success_login"])): ?>
                <p class="success-login"><?= htmlspecialchars($_SESSION["success_login"]) ?></p>
                <?php unset($_SESSION["success_login"]); ?>
            <?php endif ?>
            <h1>Admin Dashboard</h1>
            <div class="container">
                <div class="all-overview-section">
                    <div class="card card-overview" style="background-color: #22c55e; color:white">
                        <div>
                            <i class="fas fa-user-graduate fa-2x" style="color: #1d4ed8; padding:10px; background-color:#dbeafe; border-radius:10px"></i>
                        </div>
                        <div>
                            <h1 style="color:white">Total Students</h1>
                            <p><?= htmlspecialchars($student_count) ?></p>
                        </div>
                    </div>
                    <div class="card card-overview" style="background-color: #eab308; color:white">
                        <div>
                            <i class="fas fa-door-open fa-2x" style="color: #15803d; padding:10px; background-color:#dcfce7; border-radius:10px"></i>
                        </div>
                        <div>
                            <h1 style="color: white;" >Total Rooms</h1>
                            <p><?= htmlspecialchars($rooms_count) ?></p>
                        </div>
                    </div>
                    <div class="card card-overview" style="background-color:  #3b82f6; color:white">
                        <div>
                            <i class="fas fa-calendar-check fa-2x" style="color: #a16207; padding:10px; background-color:#fef9c3; border-radius:10px"></i>
                        </div>
                        <div>
                            <h1 style="color: white;">Pending Bookings</h1>
                            <p><?= htmlspecialchars($pending_bookings_count) ?></p>
                        </div>
                    </div>
                    <div class="card card-overview" style="background-color: #ef4444; color:white">
                        <div>
                            <i class="fas fa-credit-card fa-2x" style="color:#b91c1c; padding:10px; background-color:#fee2e2; border-radius:10px"></i>
                        </div>
                        <div>
                            <h1 style="color: white;">Pending Payments</h1>
                            <p><?= htmlspecialchars($pending_payments_count) ?></p>
                        </div>
                    </div>
                    
                </div>
                <div class="recent card">
                    <h1><i class="fas fa-calendar-alt" style="color:#2563eb; margin-right:12px"></i>Recent Bookings</h1>
                    <?php if(empty($all_recent_bookings)): ?>
                        <p>No bookings.</p>
                    <?php endif ?>
                    <?php if($all_recent_bookings): ?>
                        <div class="container-table">
                            <table>
                                <tr>
                                    <th>STUDENT</th>
                                    <th>ROOM</th>
                                    <th>DATE</th>
                                    <th>STATUS</th>
                                </tr>
                                
                                    <?php foreach($all_recent_bookings as $recent_booking): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($recent_booking["name"]) ?></td>
                                            <td><?= htmlspecialchars($recent_booking["room_number"]) ?></td>
                                            <td><?= htmlspecialchars($recent_booking["booking_date"]) ?></td>
                                            <?php if ($recent_booking["status"] == "Approved"): ?>
                                                <td>
                                                    <p class="approved"> <?= htmlspecialchars($recent_booking["status"]) ?></p>
                                                </td>
                                            <?php endif ?>
                                            <?php if ($recent_booking["status"] == "Pending"): ?>
                                                <td>
                                                    <p class="pending"> <?= htmlspecialchars($recent_booking["status"]) ?></p>
                                                </td>
                                            <?php endif ?>
                                            <?php if ($recent_booking["status"] == "Rejected"): ?>
                                                <td>
                                                    <p class="rejected"> <?= htmlspecialchars($recent_booking["status"]) ?></p>
                                                </td>
                                            <?php endif ?>
                                            <?php if ($recent_booking["status"] == "Checkout"): ?>
                                                <td>
                                                    <p class="checkout"> <?= htmlspecialchars($recent_booking["status"]) ?></p>
                                                </td>
                                            <?php endif ?>
                                        </tr>
                                    <?php endforeach?>
                            </table>
                        </div>
                    <?php endif ?>
                </div>
                <div class="recent card">
                    <h1><i class="fas fa-dollar-sign text-green-600" style="color:#2bab5a;margin-right:12px"></i>Recent Payments</h1>
                    <?php if(empty($all_payments)): ?>
                    <p>No payments.</p>
                <?php endif ?>
                <?php if ($all_payments): ?>
                    <div class="container-table">
                        <table>
                            <tr>
                                <th>Payment ID</th>
                                <th>STUDENT</th>
                                <th>AMOUNT</th>
                                <th>DATE</th>
                                <th>STATUS</th>
                                <th>NOTES</th>
                            </tr>
                                <?php foreach ($all_payments as $all_payment): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($all_payment["payment_id"]) ?></td>
                                        <td><?= htmlspecialchars($all_payment["name"]) ?></td>
                                        <td><?= htmlspecialchars($all_payment["amount"]) ?></td>
                                        <td><?= htmlspecialchars($all_payment["date_payment"]) ?></td>
                                        
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
                                    </tr>
                                <?php endforeach ?>
                            </tr>
                        </table>
                    </div>
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