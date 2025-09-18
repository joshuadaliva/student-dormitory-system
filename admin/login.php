<?php   
    session_start();
    require_once "../functions/functions.php";
    require_once "../db/config.php";
    

    isStudent("../student/dashboard.php");
    isAdmin("./dashboard.php");
    

    $error = "";
    $success = "";
    $email = "";
    $password= "";
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $signup_success = null;
        unset($_SESSION["signup_sucess"]);
        $email = sanitizeInput($_POST["email"]);
        $password = sanitizeInput($_POST["password"]);

        if(empty($email) || empty($password)){
            $error = "email and password cannot be blank";
        }
        
        if(empty($error)){
            $query = "SELECT admin_id, name , email, password from admin where email = ? LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->execute([$email]);
            $admin_info = $stmt->fetch(PDO::FETCH_ASSOC);

            if($admin_info && password_verify($password, $admin_info["password"])){
                $_SESSION["admin_id"] = $admin_info["admin_id"];
                $_SESSION["name"] = $admin_info["name"];
                $_SESSION["user_type"] = "admin";
                $_SESSION["success_login"] = "login successful";
                header("Location: " . "./dashboard.php");
            }
            else{
                $error = "wrong username or password";
            }
        }
    }



?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/student_admin_login.css">
</head>
<body>
        <div class="left-panel">
            <h1>Admin Login</h1>
            <br>
            <div>
                <p><i class="fa fa-check check-icon" aria-hidden="true"></i> Access all room</p>
                <p><i class="fa fa-check check-icon" aria-hidden="true"></i> Track your payment progress</p>
                <p><i class="fa fa-check check-icon" aria-hidden="true"></i> Connect with the admin</p>
            </div>
        </div>
        <div class="right-panel">
            <h1>Admin</h1>
            <?php if(!empty($success)): ?>
                <p class="success"><?= $success ?></p>
            <?php endif ?>
            <?php if(!empty($error)) : ?>
                <p class="error"><?= $error ?></p>
            <?php endif ?>
            <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
                <label for="email">Email:</label><br>
                <input type="email" name="email" id="email" value="<?=  htmlspecialchars($email)?>"><br>
                <label for="password">Password:</label><br>
                <input type="password" name="password" id="password" value="<?=  htmlspecialchars($password)?>"><br>
                <input type="submit" value="Sign In" class="submit">
                
            </form>
        </div>
</body>
</html>