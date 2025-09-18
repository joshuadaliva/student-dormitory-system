<?php

    $localhost = "localhost";
    $username = "root";
    $password = "";
    $db = "dorm_system";

    try{
        $conn = new PDO("mysql:host=$localhost;dbname=$db", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }catch(PDOException $e){
        echo $e->getMessage();
    }


