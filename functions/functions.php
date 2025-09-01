

<?php



    function sanitizeInput($data){
        $data = strip_tags($data);
        $data = strip_tags($data);
        $data = trim($data);

        return $data;
    }


    function isAdmin($location){
        if(isset($_SESSION["user_type"])){
            if( $_SESSION["user_type"] === "admin") {
                header("Location: " . $location);
            }
        }
    }


    function isStudent($location){
        if(isset($_SESSION["user_type"])){
            if( $_SESSION["user_type"] === "student") {
                header("Location: " . $location);
            }
        }
    }


    // admin functions
    function countAllRows($query){
        include "../db/config.php";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $count = $stmt->fetch(PDO::FETCH_COLUMN);
        return $count;
    }

    function countWhereAllRows($query, $status){
        include "../db/config.php";
        $stmt = $conn->prepare("SELECT COUNT(booking_id) FROM bookings where status = ?");
        $stmt->execute([$status]);
        $count = $stmt->fetch(PDO::FETCH_COLUMN);
        return $count;
    }


?>