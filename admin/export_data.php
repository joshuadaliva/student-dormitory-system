<?php
session_start();
require_once "../db/config.php";
require_once "../functions/functions.php";


isStudent("../student/dashboard.php");
if(isset($_SESSION["user_type"])){
    if( $_SESSION["user_type"] !== "admin") {
        header("Location: " . "./login.php");
    }
}
else{
    header("Location: " . "./login.php");
}

$sanitize_date = sanitizeInput($_POST["date"]);
$month = date("m", strtotime($sanitize_date));
$year = date("Y", strtotime($sanitize_date));
$stmt = $conn -> prepare("SELECT s.name , r.room_number, p.amount, date_format(p.payment_date, '%M %d, %Y') as date_payment ,p.status from payments p INNER JOIN students s USING(student_id) INNER JOIN bookings USING (booking_id) INNER JOIN rooms r USING(room_id) WHERE p.status = 'Approved' and month(p.payment_date) = ? and year(p.payment_date) = ?");
$stmt ->execute([$month,$year]);
$monthly_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);  


if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["download-submit"])){

    if($monthly_payments){
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Monthly_Report_' . $month . '_' . $year . '.csv');
    
        $output = fopen("php://output", "w");
        fputcsv($output, ['SQN', 'Student Name', 'Room Number', 'Payment Amount', 'Payment Date', 'Status']);
        
        $sqn = 1;
        $total = 0;
    
        foreach($monthly_payments as $row){
            fputcsv($output,[
                $sqn++,
                $row["name"],
                $row["room_number"],
                $row["amount"],
                $row["date_payment"],
                $row["status"]
            ]);
            $total += $row["amount"];
        }
        fputcsv($output, []);
        fputcsv($output, ['', '', 'Total Income', $total]);
        fclose($output);
    }else{
        header("Location:" . "./report.php");
    }

    
    
}