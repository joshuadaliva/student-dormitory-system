
<?php

    $localhost = "localhost";
    $username = "root";
    $password = "";
    $db = "student_dorm_system";

    try{
        $conn = new PDO("mysql:host=$localhost", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "connected";
    }catch(PDOException $e){
        echo $e->getMessage();
    }


?>