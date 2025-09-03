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
                <div class="room-container-none">
                    <i class="fas fa-door-closed" style="color: #d1d5db; font-size:2.8rem"></i>
                    <p style="text-align: center;">You don't have an approved room <br> booking yet.</p>
                    <button>book a room</button>
                </div>
            </div>
            <div class="payment">
                <h1><i class="fas fa-receipt" style="color: indigo;"></i></i>  My Recent Payment</h1>
                <div class="payment-container-none">
                    <i class="fas fa-wallet" style="color: #d1d5db; font-size:2.8rem"></i>
                    <p style="text-align: center;">No payment records found.</p>
                </div>
            </div>
        </div>
        <footer>
            <p>© <?= date("Y") ?> Student Dormitory Management System. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>