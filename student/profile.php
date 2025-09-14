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
    $student_info = fetchDetails("SELECT student_id, name, email,department,program,gender, address,contact,status from students where student_id = ?", $student_id, $conn);


    $error = "";
    $success = "";
    $accepted_gender = ["Male", "Female"];
    $accepted_program = ["bsit", "bscs","bsis"];
    $accepted_department = ["ccs"];
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])){
        $name = sanitizeInput($_POST["name"]);
        $gender = sanitizeInput($_POST["gender"]);
        $email = sanitizeInput($_POST["email"]);
        $department = sanitizeInput($_POST["department"]);
        $program = sanitizeInput($_POST["program"]);
        $address = sanitizeInput($_POST["address"]);
        $contact = sanitizeInput($_POST["contact"]);

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $error = "email not valid";
        }
        else if(empty($name) || empty($email) || empty($department) || empty($program) || empty($gender) || empty($contact) || empty($address)){
            $error = "all fields are required";
        }
        else if($student_info["name"] === $name && $student_info["gender"] === $gender &&
        $student_info["email"] === $email && $student_info["department"] === $department &&
        $student_info["program"] === $program && $student_info["address"] === $address &&
        $student_info["contact"] === $contact 
        ){
            $error = "info not change, try changing the name";
        }
        else if(!in_array($gender,$accepted_gender)){
            $error = "invalid gender";
        }
        else if(!in_array($program,$accepted_program)){
            $error = "invalid program";
        }        
        else if(!in_array($department,$accepted_department)){
            $error = "invalid department";
        }        


        if(empty($error)){
            $stmt = $conn ->prepare("UPDATE students set name = ?, gender = ?, email = ?, department = ?, program = ?, address = ?, contact = ? where student_id = ?");
            $stmt->execute([$name,$gender,$email,$department,$program,$address,$contact,$student_id]);
            if($stmt->rowCount() > 0){
                $success = "profile updated successfully";
                $student_info = fetchDetails("SELECT student_id, name, email,department,program,gender, address,contact,status from students where student_id = ?", $student_id, $conn);

            }
            else{
                $error = "there's an error updating profile";
                $student_info = fetchDetails("SELECT student_id, name, email,department,program,gender, address,contact,status from students where student_id = ?", $student_id, $conn);
            }
        }
        

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/student_profile.css">
</head>
<body>
    <main>
        <?php require_once "../component/sidebar.php" ?>
        <div class="student-profile">
            <h1>Student Profile</h1>
            <div class="container">
                <div class="info">
                    <h1><i class="fas fa-user-circle" style="color: var(--blue);"></i>  My Information</h1>
                    <div class="container-info">
                        <div>
                            <p class="label">Student Id:</p>
                            <?= htmlspecialchars($student_info["student_id"]) ?>
                        </div>
                        <div>
                            <p class="label">Name:</p>
                            <?= htmlspecialchars($student_info["name"]) ?>
                        </div>
                        <div>
                            <p class="label">Gender:</p>
                            <?= htmlspecialchars($student_info["gender"]) ?>
                        </div>
                        <div>
                            <p class="label">Email:</p>
                            <?= htmlspecialchars($student_info["email"]) ?>
                        </div>
                        <div>
                            <p class="label">Department:</p>
                            <?= htmlspecialchars($student_info["department"]) ?>
                        </div>
                        <div>
                            <p class="label">Program:</p>
                            <?= htmlspecialchars($student_info["program"]) ?>
                        </div>
                        <div>
                            <p class="label">Address:</p>
                            <?= htmlspecialchars($student_info["address"]) ?>
                        </div>
                        <div>
                            <p class="label">Contact:</p>
                            <?= htmlspecialchars($student_info["contact"]) ?>
                        </div>
                        <div>
                            <p class="label">Account Status:</p>
                            <?= htmlspecialchars($student_info["status"]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="info">
                    <h1><i class="fas fa-user-circle" style="color: var(--blue);"></i>  Update Information</h1>
                    <?php if (!empty($error)): ?>
                        <p class="error-message"><?= htmlspecialchars($error) ?></p>
                    <?php endif ?>
                    <?php if (!empty($success)): ?>
                        <p class="success-message"><?= htmlspecialchars($success) ?></p>
                    <?php endif ?>
                    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
                        <div class="container-info">
                            <div>
                                <p class="label">Student Id:</p>
                                <?= htmlspecialchars($student_info["student_id"]) ?>
                            </div>
                            <div>
                                <p class="label">Name:</p>
                                <input type="text" name="name" value="<?= htmlspecialchars($student_info["name"]) ?>" id="">
                            </div>
                            <div>
                                <p class="label">Gender:</p>
                                <select name="gender" id="gender">
                                    <option value="<?= htmlspecialchars($student_info["gender"]) ?>"><?= htmlspecialchars($student_info["gender"]) ?></option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div>
                                <p class="label">Email:</p>
                                <input type="email" name="email" value="<?= htmlspecialchars($student_info["email"]) ?>" id="">
                            </div>
                            <div>
                                <p class="label">Department:</p>
                                <select name="department" id="department">
                                    <option value="<?= htmlspecialchars($student_info["department"]) ?>"><?= htmlspecialchars($student_info["department"]) ?></option>
                                    <option value="ccs">CCS</option>
                                </select>
                            </div>
                            <div>
                                <p class="label">Program:</p>
                                <select name="program" id="program">
                                    <option value="<?= htmlspecialchars($student_info["program"]) ?>"><?= htmlspecialchars($student_info["program"]) ?></option>
                                    <option value="bsit">bsit</option>
                                    <option value="bscs">bscs</option>
                                    <option value="bsis">bsis</option>
                                </select>
                            </div>
                            <div>
                                <p class="label">Address:</p>
                                <input type="text" name="address" value="<?= htmlspecialchars($student_info["address"]) ?>" id="">
                            </div>
                            <div>
                                <p class="label">Contact:</p>
                                <input type="number" name="contact" value="<?= htmlspecialchars($student_info["contact"]) ?>" id="">
                                
                            </div>
                            <div>
                                <p class="label">Account Status:</p>
                                <?= htmlspecialchars($student_info["status"]) ?>
                            </div>
                            
                            <button type="submit" name="submit">UPDATE PROFILE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <p>© <?= date("Y") ?> Student Dormitory Management System. All rights reserved.</p>
    </footer>
</body>
</html>