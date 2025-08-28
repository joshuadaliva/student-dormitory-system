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

        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
            <label for="email:">Full Name<br>
            <input type="text" name="name" id="name"><br></label>
            
            <label for="email:">Email address<br>
            <input type="email" name="email" id="email"><br></label>

            <div class="other-inputs">
                <label for="Password:">Password<br>
                <input type="password" name="password" id="Password"><br></label>

                <label for="Confirm Password:">Confirm Password<br>
                <input type="password" name="confirm_pass" id="confirm_pass"><br></label>

                <label for="department:">department<br>
                <select name="department" id="department">
                    <option value=""></option>
                    <option value="ccs">css</option>
                </select></label>

                <label for="program:">program<br>
                <select name="program" id="program">
                    <option value=""></option>
                    <option value="bsit">BSIT</option>
                    <option value="bscs">BSCS</option>
                    <option value="bsis">BSIS</option>
                </select></label>

                <label for="Gender:">Gender<br>
                <select name="gender" id="Gender">
                    <option value=""></option>
                    <option value="male">male</option>
                    <option value="female">female</option>
                </select></label>

                <label for="contact number:">Contact Number<br>
                <input type="number" name="contact" id="contact"><br></label>
            </div>

            <label for="address:">address<br>
            <input type="text" name="address" id="address"><br></label>

            <input type="submit" value="Register" class="register">

        </form>

    </div>


</body>
</html>