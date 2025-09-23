<?php
session_start(); // Start session to manage authentication 

require_once "../db/config.php"; 
require_once "../functions/functions.php"; 

// Redirect student users away from this page
isStudent("../student/dashboard.php");

// Ensure user is logged in and is an admin
if (isset($_SESSION["user_type"])) {
    if ($_SESSION["user_type"] !== "admin") {
        header("Location: ./login.php"); // Non-admin users redirected to login
    }
} else {
    header("Location: ./login.php"); // If not logged in, redirect to login
}

// Fetch all pending payments
$all_pending_payments = fetchAllDetails(
    "SELECT p.payment_id, s.name, r.room_number, p.amount, DATE_FORMAT(p.payment_date, '%M %d, %Y') as date_payment, p.status, p.notes 
     FROM payments p 
     INNER JOIN students s USING (student_id) 
     INNER JOIN bookings USING (booking_id) 
     INNER JOIN rooms r USING (room_id) 
     WHERE p.status = ? 
     ORDER BY p.payment_date DESC",
    'Pending',
    $conn
);

// Fetch all payments
$all_payments = fetchAllDetails(
    "SELECT p.payment_id, s.name, p.amount, DATE_FORMAT(p.payment_date, '%M %d, %Y') as date_payment, p.status, p.notes 
     FROM payments p 
     INNER JOIN students s USING (student_id) 
     INNER JOIN bookings USING (booking_id) 
     INNER JOIN rooms r USING (room_id) 
     ORDER BY p.payment_date DESC",
    "",
    $conn
);

// Approve a payment
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["approve"])) {
    $stmt = $conn->prepare("UPDATE payments SET status = 'Approved' WHERE payment_id = ?");
    $stmt->execute([$_POST["payment_id"]]);

    if ($stmt->rowCount() > 0) {
        // Refresh payment lists
        $all_pending_payments = fetchAllDetails(
            "SELECT p.payment_id, s.name, p.amount, r.room_number, DATE_FORMAT(p.payment_date, '%M %d, %Y') as date_payment 
             FROM payments p 
             INNER JOIN students s USING (student_id) 
             INNER JOIN bookings USING (booking_id) 
             INNER JOIN rooms r USING (room_id) 
             WHERE p.status = ? 
             ORDER BY p.payment_date DESC",
            'Pending',
            $conn
        );
        $all_payments = fetchAllDetails(
            "SELECT p.payment_id, s.name, p.amount, DATE_FORMAT(p.payment_date, '%M %d, %Y') as date_payment, p.status, p.notes 
             FROM payments p 
             INNER JOIN students s USING (student_id) 
             INNER JOIN bookings USING (booking_id) 
             INNER JOIN rooms r USING (room_id) 
             ORDER BY p.payment_date DESC",
            "",
            $conn
        );

        $_SESSION["success"] = "payment approved";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $_SESSION["error"] = "there was an error approving the payment";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Reject a payment
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirm_payment_rejection"])) {
    if (empty($_POST["notes"])) {
        // Rejection requires notes
        $_SESSION["error"] = "notes cannot be empty";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $notes = sanitizeInput($_POST["notes"]);
        $stmt = $conn->prepare("UPDATE payments SET status = 'Rejected', notes = ? WHERE payment_id = ?");
        $stmt->execute([$notes, $_POST["payment_id"]]);

        if ($stmt->rowCount() > 0) {
            // Refresh data
            $all_payments = fetchAllDetails(
                "SELECT p.payment_id, s.name, p.amount, DATE_FORMAT(p.payment_date, '%M %d, %Y') as date_payment, p.status, p.notes 
                 FROM payments p 
                 INNER JOIN students s USING (student_id) 
                 INNER JOIN bookings USING (booking_id) 
                 INNER JOIN rooms r USING (room_id) 
                 ORDER BY p.payment_date DESC",
                "",
                $conn
            );
            $all_pending_payments = fetchAllDetails(
                "SELECT p.payment_id, s.name, p.amount, r.room_number, DATE_FORMAT(p.payment_date, '%M %d, %Y') as date_payment 
                 FROM payments p 
                 INNER JOIN students s USING (student_id) 
                 INNER JOIN bookings USING (booking_id) 
                 INNER JOIN rooms r USING (room_id) 
                 WHERE p.status = ? 
                 ORDER BY p.payment_date DESC",
                'Pending',
                $conn
            );

            $_SESSION["success"] = "payment Rejected";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $_SESSION["error"] = "there was an error rejecting the payment";
            header("Location: " . $_SERVER['PHP_SELF']);
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
    <title>payments</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/admin_bookings.css">
</head>
<body>
    <main>
        <?php require_once "../component/sidebar.php" ?>
        <div class="overlay-modal">
            <div class="modal">
                <button class="close-modal">&#88;</button>
                <h1>REASON OF REJECTING PAYMENT</h1>
                <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
                    <input type="hidden" name="payment_id" class="payment-id">
                    <textarea name="notes" class="reason-input" id="reason" placeholder="enter your reason for rejecting payment"></textarea><br><br>
                    <button name="confirm_payment_rejection" class="confirm-reject-btn" value="confirm rejection"> CONFIRM REJECTION </button>
                </form>
            </div>
        </div>
        <div class="container">
            <?php if (!empty($_SESSION["error"])): ?>
                <p class="error-message"><?= htmlspecialchars($_SESSION["error"]) ?></p>
                <?php unset($_SESSION["error"]); ?>
            <?php endif ?>
            <?php if (!empty($_SESSION["success"])): ?>
                <p class="success-message"><?= htmlspecialchars($_SESSION["success"]) ?></p>
                <?php unset($_SESSION["success"]); ?>
            <?php endif ?>
            <h1>Manage Payments</h1>
            <div class="recent card">
                <h1>Pending Payments</h1>
                <?php if(empty($all_pending_payments)): ?>
                    <p>No pending payments.</p>
                <?php endif ?>
                <?php if ($all_pending_payments): ?>
                    <div class="container-table">
                        <table>
                            <tr>
                                <th>PAYMENT ID</th>
                                <th>STUDENT NAME</th>
                                <th>AMOUNT</th>
                                <th>ROOM NUMBER</th>
                                <th>DATE</th>
                                <th>ACTIONS</th>
                            </tr>

                            
                                <?php foreach ($all_pending_payments as $pending_payment): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($pending_payment["payment_id"]) ?></td>
                                        <td><?= htmlspecialchars($pending_payment["name"]) ?></td>
                                        <td><?= htmlspecialchars($pending_payment["amount"]) ?></td>
                                        <td><?= htmlspecialchars($pending_payment["room_number"]) ?></td>
                                        <td><?= htmlspecialchars($pending_payment["date_payment"]) ?></td>
                                        <td>
                                            <div class="action">
                                                <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
                                                    <input type="hidden" name="payment_id" value="<?= htmlspecialchars($pending_payment["payment_id"]) ?>">
                                                    <button class="approve" name="approve">Approve</button>
                                                </form>
                                                <button class="reject-btn" name="reject-btn" id="reject-btn" data-id="<?= $pending_payment['payment_id'] ?>">Reject</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tr>
                        </table>
                    </div>
                <?php endif ?>
            </div>
            <div class="recent card">
                <h1>All Payments</h1>
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
    </main>
    <footer>
        <p>© <?= date("Y") ?> Student Dormitory Management System. All rights reserved.</p>
    </footer>
    
    <script>
        let rejectBtn = document.querySelectorAll(".reject-btn");
        let overlayModal = document.querySelector(".overlay-modal");
        let rejectInputId = document.querySelector(".payment-id");
        let closeModal = document.querySelector(".close-modal");
        rejectBtn.forEach(btn => {
            btn.addEventListener("click", (e) => {
                const id = e.target.dataset.id;
                rejectInputId.value = id;
                overlayModal.classList.toggle("open-modal");
                closeModal.addEventListener("click", () => {
                    overlayModal.classList.remove("open-modal");
                })
            })
        })
        
    </script>
</body>
</html>