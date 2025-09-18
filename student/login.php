<?php   
session_start();

    require_once "../functions/functions.php";
    require_once "../db/config.php";
    if(isset($_SESSION["signup_sucess"])){
        $signup_success = $_SESSION["signup_sucess"] || false;
    }

    isStudent("./dashboard.php");
    isAdmin("../admin/dashboard.php");

    $error = "";
    $success = "";
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $signup_success = null;
        unset($_SESSION["signup_sucess"]);
        $email = sanitizeInput($_POST["email"]);
        $password = sanitizeInput($_POST["password"]);

        if(empty($email) || empty($password)){
            $error = "email and password cannot be blank";
        }
        
        if(empty($error)){
            $query = "SELECT student_id, name , email, password from students where email = ? LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->execute([$email]);
            $student_info = $stmt->fetch(PDO::FETCH_ASSOC);

            if($student_info && password_verify($password, $student_info["password"])){
                $_SESSION["student_id"] = $student_info["student_id"];
                $_SESSION["name"] = $student_info["name"];
                $_SESSION["success_login"] = "login successful";
                $_SESSION["user_type"] = "student";

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
    <title>student login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/student_admin_login.css">
</head>
<body>
        <div class="left-panel">
            <h1>Login Student</h1>
        </div>
        <div class="right-panel">
            <h1>Student Sign In</h1>
            <?php if(isset($signup_success)): ?>
                <p class="success">Sign Up Success. You can login Now</p>
            <?php endif ?>
            <?php if(!empty($success)): ?>
                <p class="success"><?= $success ?></p>
            <?php endif ?>
            <?php if(!empty($error)) : ?>
                <p class="error"><?= $error ?></p>
            <?php endif ?>
            <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
                <label for="email">Email:</label><br>
                <input type="email" name="email" id="email"><br>
                <label for="password">Password:</label><br>
                <input type="password" name="password" id="password"><br>
                <input type="submit" value="Sign In" class="submit">
                
            </form>
        </div>
</body>
</html>