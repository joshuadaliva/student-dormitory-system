<?php

    require_once "../db/config.php";
    require_once "../functions/functions.php";
    session_start();

    $error = "";

    isStudent("./dashboard.php");
    isAdmin("../admin/dashboard.php");

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

        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $error = "email not valid";
        }
        else if(empty($name) || empty($email) || empty($password) || 
        empty($confirm_pass) || empty($department) || empty($program ||
        empty($gender) || empty($contact) || empty($address)
        )){
            $error = "all fields are required";
        }
        else if(strlen($password) < 8 || !preg_match("/[0-9]/", $password) || !preg_match("/[\W]/", $password)){
            $error = "password must be 8 charaters long, contains atleast one number and contains atleast one symbol ";
        }

        else if($password !== $confirm_pass){
            $error = "password not matched";
        }

    
        
       
        if(empty($error)){
            $query  = "SELECT email from students where email = ?";
            $stmt = $conn->prepare($query);
            $stmt ->execute([$email]);
            $is_email_exist = $stmt->fetch(PDO::FETCH_ASSOC);

            if($is_email_exist){
                $error = "email already exist";
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
        <h1>Sign Up Now</h1>
        <br>
        <div>
            <p><i class="fa fa-check check-icon" aria-hidden="true"></i> Access all room</p>
            <p><i class="fa fa-check check-icon" aria-hidden="true"></i> Track your payment progress</p>
            <p><i class="fa fa-check check-icon" aria-hidden="true"></i> Connect with the admin</p>
        </div>
    </div>
    <div class="right-panel">
        <h1>Create your account</h1>
        <p class="text-to-login">or <a href="login.php">login to your existing account</a></p>
        <?php if(!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif ?>
        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
            <label for="email:">Full Name<br>
            <input type="text" name="name" id="name" required><br></label>
            
            <label for="email:">Email address<br>
            <input type="text" name="email" id="email" required><br></label>

            <div class="other-inputs">
                <label for="Password:">Password<br>
                <input type="password" name="password" id="Password" required><br></label>

                <label for="Confirm Password:">Confirm Password<br>
                <input type="password" name="confirm_pass" id="confirm_pass" required><br></label>

                <label for="department:">department<br>
                <select name="department" id="department" required>
                    <option value=""></option>
                    <option value="ccs">css</option>
                </select></label>

                <label for="program:">program<br>
                <select name="program" id="program" required>
                    <option value=""></option>
                    <option value="bsit">BSIT</option>
                    <option value="bscs">BSCS</option>
                    <option value="bsis">BSIS</option>
                </select></label>

                <label for="Gender:">Gender<br>
                <select name="gender" id="Gender" required>
                    <option value=""></option>
                    <option value="Male">male</option>
                    <option value="Female">female</option>
                </select></label>

                <label for="contact number:">Contact Number<br>
                <input type="number" name="contact" id="contact" required><br></label>
            </div>

            <label for="address:">address<br>
            <input type="text" name="address" id="address" required><br></label>

            <input type="submit" value="Register" class="register">

        </form>

    </div>


</body>
</html>