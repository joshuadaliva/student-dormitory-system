

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

    function countWhereAllRows($query, $value){
        include "../db/config.php";
        $stmt = $conn->prepare($query);
        $stmt->execute([$value]);
        $count = $stmt->fetch(PDO::FETCH_COLUMN);
        return $count;
    }


    function uploadImage($file){
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($file["file"]["name"]);
        $check = getimagesize($file["file"]["tmp_name"]);
        if($check === false){
            return [
                "error" => "file is not an image" 
            ];
        }
            
        if(move_uploaded_file($file["file"]["tmp_name"], $target_file)){
            return [
                "success" => "the file " . htmlspecialchars(basename($file["file"]["name"])) . " is uploaded",
                "file" => $target_file
            ];
        }
        else{
            return [
                "error" => "There was an error uploading your file" 
            ];
        }   
    }


    function fetchDetails($query, $value, $conn){
        $stmt =  $conn->prepare($query);
        $stmt->execute([$value]);
        $details = $stmt->fetch(PDO::FETCH_ASSOC);
        return $details;
    }

    function fetchAllDetails($query, $value, $conn){
        if($value == false){
            $stmt =  $conn->prepare($query);
            $stmt->execute();
            $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $details;
        }
        else{
            $stmt =  $conn->prepare($query);
            $stmt->execute([$value]);
            $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $details;
        }
    }

    



?>