<?php
session_start(); // Start session for authentication and status messages
require_once "../db/config.php"; 
require_once "../functions/functions.php"; 

// Redirect students away from this page
isStudent("../student/dashboard.php");

// Ensure only admin users can access
if (isset($_SESSION["user_type"])) {
    if ($_SESSION["user_type"] !== "admin") {
        header("Location: ./login.php"); // Non-admins are redirected
    }
} else {
    header("Location: ./login.php"); // If no user_type set, redirect to login
}

// Fetch all pending bookings
$all_pending_bookings = fetchAllDetails(
    "SELECT b.booking_id, s.name, r.room_number, r.room_id, DATE_FORMAT(b.booking_date, '%M %d, %Y') as booking_date 
     FROM bookings b 
     INNER JOIN students s USING(student_id) 
     INNER JOIN rooms r USING(room_id) 
     WHERE b.status = ? 
     ORDER BY b.booking_date DESC",
    'Pending',
    $conn
);

// Fetch all bookings
$all_bookings = fetchAllDetails(
    "SELECT b.booking_id, s.name, r.room_id, r.room_number, DATE_FORMAT(b.booking_date, '%M %d, %Y') as booking_date, b.status 
     FROM bookings b 
     INNER JOIN students s USING(student_id) 
     INNER JOIN rooms r USING(room_id) 
     ORDER BY b.booking_date DESC",
    '',
    $conn
);


// Handle approving bookings
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["approve"])) {
    // Update booking to Approved
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Approved' WHERE booking_id = ?");
    $stmt->execute([$_POST["booking_id"]]);
    $booking_rowCount = $stmt->rowCount();

    // Update room status to Occupied
    $stmt = $conn->prepare("UPDATE rooms SET status = 'Occupied' WHERE room_id = ?");
    $stmt->execute([$_POST["room_id"]]);
    $room_rowCount = $stmt->rowCount();

    // Check if both updates succeeded
    if ($booking_rowCount > 0 && $room_rowCount > 0) {
        // Refresh bookings list
        $all_bookings = fetchAllDetails(
            "SELECT b.booking_id, s.name, r.room_id, r.room_number, DATE_FORMAT(b.booking_date, '%M %d, %Y') as booking_date, b.status 
             FROM bookings b 
             INNER JOIN students s USING(student_id) 
             INNER JOIN rooms r USING(room_id) 
             ORDER BY b.booking_date DESC",
            '',
            $conn
        );
        $all_pending_bookings = fetchAllDetails(
            "SELECT b.booking_id, s.name, r.room_number, r.room_id, DATE_FORMAT(b.booking_date, '%M %d, %Y') as booking_date 
             FROM bookings b 
             INNER JOIN students s USING(student_id) 
             INNER JOIN rooms r USING(room_id) 
             WHERE b.status = ? 
             ORDER BY b.booking_date DESC",
            'Pending',
            $conn
        );

        $_SESSION["success"] = "booking approved";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $_SESSION["error"] = "there was an error approving the booking";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle rejecting bookings
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reject"])) {
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Rejected' WHERE booking_id = ?");
    $stmt->execute([$_POST["booking_id"]]);

    if ($stmt->rowCount() > 0) {
        // Refresh data
        $all_bookings = fetchAllDetails(
            "SELECT b.booking_id, s.name, r.room_id, r.room_number, DATE_FORMAT(b.booking_date, '%M %d, %Y') as booking_date, b.status 
             FROM bookings b 
             INNER JOIN students s USING(student_id) 
             INNER JOIN rooms r USING(room_id) 
             ORDER BY b.booking_date DESC",
            '',
            $conn
        );
        $all_pending_bookings = fetchAllDetails(
            "SELECT b.booking_id, s.name, r.room_number, DATE_FORMAT(b.booking_date, '%M %d, %Y') as booking_date 
             FROM bookings b 
             INNER JOIN students s USING(student_id) 
             INNER JOIN rooms r USING(room_id) 
             WHERE b.status = ? 
             ORDER BY b.booking_date DESC",
            'Pending',
            $conn
        );

        $_SESSION["success"] = "booking Rejected";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $_SESSION["error"] = "there was an error rejecting the booking";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle confirming checkout
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirm_checkout"])) {
    $booking_id = sanitizeInput($_POST["booking_id"]);
    $room_id = sanitizeInput($_POST["room_id"]);

    // Check if student still has pending payments
    $is_student_pending_payment = fetchDetails(
        "SELECT payment_id FROM payments WHERE booking_id = ? AND status = 'Pending'",
        $booking_id,
        $conn
    );

    if ($is_student_pending_payment) {
        $_SESSION["error"] = "cannot checkout student, student have pending payment";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        // Update booking status to Checkout
        $stmt = $conn->prepare("UPDATE bookings SET status = 'Checkout' WHERE booking_id = ?");
        $stmt->execute([$booking_id]);
        $booking_row_count = $stmt->rowCount();

        // Update room status to Available
        $stmt = $conn->prepare("UPDATE rooms SET status = 'Available' WHERE room_id = ?");
        $stmt->execute([$room_id]);
        $room_row_count = $stmt->rowCount();

        if ($booking_row_count > 0 && $room_row_count > 0) {
            $all_bookings = fetchAllDetails(
                "SELECT b.booking_id, s.name, r.room_id, r.room_number, DATE_FORMAT(b.booking_date, '%M %d, %Y') as booking_date, b.status 
                 FROM bookings b 
                 INNER JOIN students s USING(student_id) 
                 INNER JOIN rooms r USING(room_id) 
                 ORDER BY b.booking_date DESC",
                '',
                $conn
            );

            $_SESSION["success"] = "checkout success";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $all_bookings = fetchAllDetails(
                "SELECT b.booking_id, s.name, r.room_id, r.room_number, DATE_FORMAT(b.booking_date, '%M %d, %Y') as booking_date, b.status 
                 FROM bookings b 
                 INNER JOIN students s USING(student_id) 
                 INNER JOIN rooms r USING(room_id) 
                 ORDER BY b.booking_date DESC",
                '',
                $conn
            );

            $_SESSION["error"] = "cannot checkout student, please try again later";
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
            <?php if (!empty($_SESSION["error"])): ?>
                <p class="error-message"><?= htmlspecialchars($_SESSION["error"]) ?></p>
                <?php unset($_SESSION["error"]); ?>
            <?php endif ?>
            <?php if (!empty($_SESSION["success"])): ?>
                <p class="success-message"><?= htmlspecialchars($_SESSION["success"]) ?></p>
                <?php unset($_SESSION["success"]); ?>
            <?php endif ?>
            <h1>Manage Bookings</h1>
            <div class="recent card">
                <h1>Pending Bookings</h1>
                <?php if (empty($all_pending_bookings)): ?>
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
                <?php if (empty($all_bookings)): ?>
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