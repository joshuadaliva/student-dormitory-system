<?php
session_start();

    require_once "../db/config.php";
    require_once "../functions/functions.php";

    isStudent("./dashboard.php");
    isAdmin("../admin/dashboard.php");

    $name = $email = $department = $program = $gender = $contact = $address = "";
    $password = $confirm_pass = "";

    if($_SERVER["REQUEST_METHOD"] === "POST"){

        $name = sanitizeInput($_POST["name"]);
        $email = sanitizeInput($_POST["email"]);
        $password = sanitizeInput($_POST["password"]);
        $confirm_pass = sanitizeInput($_POST["confirm_pass"]);
        $department = sanitizeInput($_POST["department"]);
        $program = sanitizeInput($_POST["program"]);
        $gender = sanitizeInput($_POST["gender"]);
        $contact = sanitizeInput($_POST["contact"]);
        $address = sanitizeInput($_POST["address"]);
        $accepted_gender = ["Male", "Female"];
        $accepted_program = ["bsit", "bscs","bsis"];
        $accepted_department = ["ccs"];
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $_SESSION["error"] = "email not valid";
            header("Location:". $_SERVER['PHP_SELF']);
            exit;
        }
        else if(empty($name) || empty($email) || empty($password) || 
        empty($confirm_pass) || empty($department) || empty($program ||
        empty($gender) || empty($contact) || empty($address)
        )){
            $_SESSION["error"] =  "all fields are required";
            header("Location:". $_SERVER['PHP_SELF']);
            exit;
        }
        else if(strlen($password) < 8 || !preg_match("/[0-9]/", $password) || !preg_match("/[\W]/", $password)){
            $_SESSION["error"] =  "password must be 8 charaters long, contains atleast one number and contains atleast one symbol ";
            header("Location:". $_SERVER['PHP_SELF']);
            exit;
        }

        else if($password !== $confirm_pass){
            $_SESSION["error"] = "password not matched";  
            header("Location:". $_SERVER['PHP_SELF']);
            exit;
        }
        else if(!in_array($gender,$accepted_gender)){
            $_SESSION["error"] = "invalid gender";  
            header("Location:". $_SERVER['PHP_SELF']);
            exit;
        }
        else if(!in_array($program,$accepted_program)){
            $_SESSION["error"] = "invalid program";
            header("Location:". $_SERVER['PHP_SELF']);
            exit;
        }        
        else if(!in_array($department,$accepted_department)){
            $_SESSION["error"] = "invalid department";
            header("Location:". $_SERVER['PHP_SELF']);
            exit;
        }        
    
        
       
        if(empty($error)){
            $query  = "SELECT email from students where email = ?";
            $stmt = $conn->prepare($query);
            $stmt ->execute([$email]);
            $is_email_exist = $stmt->fetch(PDO::FETCH_ASSOC);

            if($is_email_exist){
                $_SESSION["error"] = "email already exist";
                header("Location:". $_SERVER['PHP_SELF']);
                exit;
            }
            else{
                $password = password_hash($password, PASSWORD_BCRYPT);
                $query = "INSERT INTO students(name, email, password, department, program,gender,address,contact) VALUES(?,?,?,?,?,?,?,?)";
                $stmt = $conn->prepare($query);
                $stmt->execute([$name, $email, $password, $department, $program, $gender, $address,$contact]);
                $rowCount = $stmt->rowCount();
                if($rowCount > 0){
                    $success = true;
                    header("Location:" . "../student/login.php");
                    $_SESSION["signup_sucess"] = true;
                }
            }
            
        }


    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/student_signup.css">
</head>
<body>
    
    <div class="left-panel">
        <h1>Sign Up Student</h1>
        <br>
    </div>
    <div class="right-panel">
        <h1>Create your account</h1>
        <p class="text-to-login">or <a href="login.php">login to your existing account</a></p>
        <?php if(!empty($_SESSION["error"])): ?>
            <p class="error"><?= htmlspecialchars($_SESSION["error"]) ?></p>
            <?php unset($_SESSION["error"]); ?>
        <?php endif ?>
        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
            <label for="email:">Full Name<br>
            <input type="text" name="name" id="name" value="<?=  htmlspecialchars($name)?>" required><br></label>
            
            <label for="email:">Email address<br>
            <input type="text" name="email" id="email" value="<?=  htmlspecialchars($email)?>" required><br></label>

            <div class="other-inputs">
                <label for="Password:">Password<br>
                <input type="password" name="password" id="Password" value="<?=  htmlspecialchars($password)?>" required><br></label>

                <label for="Confirm Password:">Confirm Password<br>
                <input type="password" name="confirm_pass" id="confirm_pass" value="<?=  htmlspecialchars($confirm_pass)?>"  required><br></label>

                <label for="department:">department<br>
                <select name="department" id="department" required>
                    <option value="<?= htmlspecialchars($department)?>"></option>
                    <option value="ccs">css</option>
                </select></label>

                <label for="program:">program<br>
                <select name="program" id="program" required>
                    <option value="<?=  htmlspecialchars($program)?>"></option>
                    <option value="bsit">BSIT</option>
                    <option value="bscs">BSCS</option>
                    <option value="bsis">BSIS</option>
                </select></label>

                <label for="Gender:">Gender<br>
                <select name="gender" id="Gender" required>
                    <option value="<?=  htmlspecialchars($gender)?>"></option>
                    <option value="Male">male</option>
                    <option value="Female">female</option>
                </select></label>

                <label for="contact number:">Contact Number<br>
                <input type="number" name="contact" id="contact" value="<?=  htmlspecialchars($contact)?>" required><br></label>
            </div>

            <label for="address:">address<br>
            <input type="text" name="address" id="address" value="<?=  htmlspecialchars($address)?>" required><br></label>

            <input type="submit" value="Register" class="register">

        </form>

    </div>


</body>
</html>