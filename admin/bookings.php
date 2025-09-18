<?php
session_start();
require_once "../db/config.php";
require_once "../functions/functions.php";


isStudent("../student/dashboard.php");
if (isset($_SESSION["user_type"])) {
    if ($_SESSION["user_type"] !== "admin") {
        header("Location: " . "./login.php");
    }
} else {
    header("Location: " . "./login.php");
}


$all_pending_bookings = fetchAllDetails("SELECT b.booking_id,s.name,r.room_number, r.room_id, date_format(b.booking_date, '%M %d, %Y') as booking_date from bookings b INNER JOIN students s USING(student_id) INNER JOIN rooms r USING(room_id) where b.status =  ? order by b.booking_date desc", 'Pending', $conn);
$all_bookings = fetchAllDetails("SELECT b.booking_id,s.name, r.room_id , r.room_number,date_format(b.booking_date, '%M %d, %Y') as booking_date, b.status from bookings b INNER JOIN students s USING(student_id) INNER JOIN rooms r USING(room_id) order by b.booking_date desc", '', $conn);

$error = "";
$success = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["approve"])) {
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Approved' WHERE booking_id = ?");
    $stmt->execute([$_POST["booking_id"]]);
    $booking_rowCount = $stmt->rowCount();
    $stmt = $conn->prepare("UPDATE rooms SET status = 'Occupied' WHERE room_id = ?");
    $stmt->execute([$_POST["room_id"]]);
    $room_rowCount = $stmt->rowCount();
    if ($booking_rowCount > 0 && $room_rowCount > 0) {
        $success = "booking approved";
        $all_bookings = fetchAllDetails("SELECT b.booking_id,s.name, r.room_id, r.room_number,date_format(b.booking_date, '%M %d, %Y') as booking_date, b.status from bookings b INNER JOIN students s USING(student_id) INNER JOIN rooms r USING(room_id) order by b.booking_date desc", '', $conn);
        $all_pending_bookings = fetchAllDetails("SELECT b.booking_id,s.name,r.room_number, r.room_id,date_format(b.booking_date, '%M %d, %Y') as booking_date from bookings b INNER JOIN students s USING(student_id) INNER JOIN rooms r USING(room_id) where b.status =  ? order by b.booking_date desc", 'Pending', $conn);
    } else {
        $error = "there was an error approving the booking";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reject"])) {
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Rejected' WHERE booking_id = ?");
    $stmt->execute([$_POST["booking_id"]]);
    if ($stmt->rowCount() > 0) {
        $success = "booking Rejected";
        $all_bookings = fetchAllDetails("SELECT b.booking_id,s.name,  r.room_id, r.room_number,date_format(b.booking_date, '%M %d, %Y') as booking_date, b.status from bookings b INNER JOIN students s USING(student_id) INNER JOIN rooms r USING(room_id) order by b.booking_date desc", '', $conn);
        $all_pending_bookings = fetchAllDetails("SELECT b.booking_id,s.name,r.room_number,date_format(b.booking_date, '%M %d, %Y') as booking_date from bookings b INNER JOIN students s USING(student_id) INNER JOIN rooms r USING(room_id) where b.status =  ? order by b.booking_date desc", 'Pending', $conn);
    } else {
        $error = "there was an error rejecting the booking";
    }
}



if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirm_checkout"])) {
    $booking_id = sanitizeInput($_POST["booking_id"]);
    $room_id = sanitizeInput($_POST["room_id"]);
    $is_student_pending_payment = fetchDetails("SELECT payment_id from payments WHERE booking_id = ? and status = 'Pending'", $booking_id, $conn);
    if($is_student_pending_payment){
        $error = "cannot checkout student, student have pending payment";
    }
    else{
        $stmt = $conn->prepare("UPDATE bookings set status = 'Checkout' where booking_id = ?");
        $stmt -> execute([$booking_id]);
        $booking_row_count = $stmt->rowCount();
        $stmt = $conn->prepare("UPDATE rooms set status = 'Available' where room_id = ?");
        $stmt -> execute([$room_id]);
        $room_row_count = $stmt->rowCount();
        if($booking_row_count > 0 && $room_row_count > 0){
            $success = "checkout success";
            $all_bookings = fetchAllDetails("SELECT b.booking_id,s.name,r.room_id, r.room_number,date_format(b.booking_date, '%M %d, %Y') as booking_date, b.status from bookings b INNER JOIN students s USING(student_id) INNER JOIN rooms r USING(room_id) order by b.booking_date desc", '', $conn);
        }
        else{
            $error = "cannot checkout student, please try again later";
            $all_bookings = fetchAllDetails("SELECT b.booking_id,s.name,r.room_id,r.room_number,date_format(b.booking_date, '%M %d, %Y') as booking_date, b.status from bookings b INNER JOIN students s USING(student_id) INNER JOIN rooms r USING(room_id) order by b.booking_date desc", '', $conn);

        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bookings</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/admin_bookings.css">
</head>

<body>
    <main>
        <?php require_once "../component/sidebar.php" ?>
        <div class="overlay-modal">
            <div class="modal">
                <h1>DO YOU WANT TO CHECKOUT THIS STUDENT</h1>
                <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
                    <input type="hidden" name="booking_id" class="booking-id">
                    <input type="hidden" name="room_id" class="room-id">
                    <div style="display: flex; gap:10px">
                        <button class="close-modal-bookings">CLOSE MODAL</button>
                        <button name="confirm_checkout" class="confirm-checkout-btn"> CHECKOUT STUDENT </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="container">
            <?php if (!empty($error)): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif ?>
            <?php if (!empty($success)): ?>
                <p class="success-message"><?= htmlspecialchars($success) ?></p>
            <?php endif ?>
            <h1>Manage Bookings</h1>
            <div class="recent card">
                <h1>Pending Bookings</h1>
                <?php if(empty($all_pending_bookings)): ?>
                    <p>No pending bookings.</p>
                <?php endif ?>
                <?php if ($all_pending_bookings): ?>
                    <div class="container-table">
                        <table>
                            <tr>
                                <th>BOOKING ID</th>
                                <th>STUDENT</th>
                                <th>ROOM</th>
                                <th>DATE</th>
                                <th>ACTIONS</th>
                            </tr>

                            
                                <?php foreach ($all_pending_bookings as $pending_booking): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($pending_booking["booking_id"]) ?></td>
                                        <td><?= htmlspecialchars($pending_booking["name"]) ?></td>
                                        <td><?= htmlspecialchars($pending_booking["room_number"]) ?></td>
                                        <td><?= htmlspecialchars($pending_booking["booking_date"]) ?></td>
                                        <td>
                                            <div class="action">
                                                <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
                                                    <input type="hidden" name="booking_id" value="<?= htmlspecialchars($pending_booking["booking_id"]) ?>">
                                                    <input type="hidden" name="room_id" value="<?= htmlspecialchars($pending_booking["room_id"]) ?>">
                                                    <button class="approve" name="approve">Approve</button>
                                                </form>
                                                <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
                                                    <input type="hidden" name="booking_id" value="<?= htmlspecialchars($pending_booking["booking_id"]) ?>">
                                                    <button class="reject-btn" name="reject">Reject</button>
                                                </form>
                                            </div>
                                        </td>
                                    <tr>
                                    <?php endforeach ?>
                                    </tr>
                        </table>
                    </div>
                <?php endif ?>
            </div>
            <div class="recent card all-booking-card">
                <h1>All Bookings</h1>
                <?php if(empty($all_bookings)): ?>
                    <p>No bookings.</p>
                <?php endif ?>
                <?php if ($all_bookings): ?>
                    <div class="container-table">
                        <table>
                            <tr>
                                <th>BOOKING ID</th>
                                <th>STUDENT</th>
                                <th>ROOM</th>
                                <th>DATE</th>
                                <th>STATUS</th>
                                <th>CHECKOUT</th>
                            </tr>
                                <?php foreach ($all_bookings as $all_booking): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($all_booking["booking_id"]) ?></td>
                                        <td><?= htmlspecialchars($all_booking["name"]) ?></td>
                                        <td><?= htmlspecialchars($all_booking["room_number"]) ?></td>
                                        <td><?= htmlspecialchars($all_booking["booking_date"]) ?></td>
                                        <?php if ($all_booking["status"] == "Approved"): ?>
                                            <td>
                                                <p class="approved"> <?= htmlspecialchars($all_booking["status"]) ?></p>
                                            </td>
                                        <?php endif ?>
                                        <?php if ($all_booking["status"] == "Pending"): ?>
                                            <td>
                                                <p class="pending"> <?= htmlspecialchars($all_booking["status"]) ?></p>
                                            </td>
                                        <?php endif ?>
                                        <?php if ($all_booking["status"] == "Rejected"): ?>
                                            <td>
                                                <p class="rejected"> <?= htmlspecialchars($all_booking["status"]) ?></p>
                                            </td>
                                        <?php endif ?>
                                        <?php if ($all_booking["status"] == "Checkout"): ?>
                                            <td>
                                                <p class="checkout"> <?= htmlspecialchars($all_booking["status"]) ?></p>
                                            </td>
                                        <?php endif ?>
                                        <?php if ($all_booking["status"] != "Checkout" && $all_booking["status"] != "Rejected" && $all_booking["status"] != "Pending"): ?>
                                            <td>
                                                <button name="checkout_student" data-roomid="<?= htmlspecialchars($all_booking["room_id"]) ?>" data-bookingid="<?= htmlspecialchars($all_booking["booking_id"]) ?>" class="checkout-student"> checkout </button>
                                            </td>
                                        <?php endif ?>
                                        
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
        let checkoutBtn = document.querySelectorAll(".checkout-student");
        let overlayModal = document.querySelector(".overlay-modal");
        let booking_id = document.querySelector(".booking-id");
        let room_id = document.querySelector(".room-id");
        let closeModal = document.querySelector(".close-modal-bookings");
        checkoutBtn.forEach(btn => {
            btn.addEventListener("click", (e) => {
                const roomID = e.target.dataset.roomid;
                const bookingID = e.target.dataset.bookingid;
                booking_id.value = bookingID;
                room_id.value = roomID;
                overlayModal.classList.toggle("open-modal");
                closeModal.addEventListener("click", () => {
                    overlayModal.classList.remove("open-modal");
                })
            })
        })
        
    </script>
</body>

</html>