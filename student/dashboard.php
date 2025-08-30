<?php
    session_start();    

    if(!isset($_SESSION["name"]) && !isset($_SESSION["student_id"])){
        header("Location: " . "./login.php");
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    sample dashboard
</body>
</html>