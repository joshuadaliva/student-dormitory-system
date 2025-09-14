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

$error = "";
$success = "";

$date = date('Y-m-d');
$month = date("m", strtotime($date));
$day = date("d", strtotime($date));
$year = date("Y", strtotime($date));

$stmt = $conn -> prepare("SELECT s.name , r.room_number, p.amount, date_format(p.payment_date, '%M %d, %Y') as date_payment ,p.status from payments p INNER JOIN students s USING(student_id) INNER JOIN bookings USING (booking_id) INNER JOIN rooms r USING(room_id) WHERE p.status = 'Approved' and month(p.payment_date) = ? and year(p.payment_date) = ?");
$stmt ->execute([$month,$year]);
$monthly_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);     



if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["date-submit"])){

    if(empty($_POST["date"])){
        $error = "date cannot be empty";
    }
    if(empty($error)){
        $sanitize_date = sanitizeInput($_POST["date"]);
        $month = date("m", strtotime($sanitize_date));
        $year = date("Y", strtotime($sanitize_date));

        $stmt = $conn -> prepare("SELECT s.name , r.room_number, p.amount, date_format(p.payment_date, '%M %d, %Y') as date_payment ,p.status from payments p INNER JOIN students s USING(student_id) INNER JOIN bookings USING (booking_id) INNER JOIN rooms r USING(room_id) WHERE p.status = 'Approved' and month(p.payment_date) = ? and year(p.payment_date) = ?");
        $stmt ->execute([$month,$year]);
        $monthly_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);     
    
    }
    
}






?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>payments</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/admin_reports.css">
</head>
<body>
    <main>
        <?php require_once "../component/sidebar.php" ?>
        <div class="container">
            <?php if (!empty($error)): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif ?>
            <?php if (!empty($success)): ?>
                <p class="success-message"><?= htmlspecialchars($success) ?></p>
            <?php endif ?>
            <h1>Reports</h1>
            <div class="recent card">
                <div style="display: flex; gap:20px; align-items:center">
                    <h1>All Payments</h1>
                    </form>
                    <form action="" method="POST">
                        <input type="date" name="date" id="date" value="<?= date("$year-$month-$day") ?>"><br>
                        <button type="submit" formaction="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" name="date-submit">get payments</button>
                        <button type="submit" formaction="./export_data.php" name="download-submit">Download CSV</button>
                    </form>
                </div>
                <?php if(empty($monthly_payments)): ?>
                    <p>No payments.</p>
                <?php endif ?>
                <?php if ($monthly_payments): ?>
                    <div class="container-table">
                        <table>
                            <tr>
                                <th>STUDENT NAME</th>
                                <th>AMOUNT</th>
                                <th>DATE</th>
                                <th>STATUS</th>
                            </tr>
                                <?php foreach ($monthly_payments as $monthly_payment): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($monthly_payment["name"]) ?></td>
                                        <td><?= htmlspecialchars($monthly_payment["amount"]) ?></td>
                                        <td><?= htmlspecialchars($monthly_payment["date_payment"]) ?></td>
                                        <td><p class="approved"> <?= htmlspecialchars($monthly_payment["status"]) ?></p></td>
                                    </tr>
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