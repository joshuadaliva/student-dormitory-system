

<?php


    function sanitizeInput($data){
        $data = strip_tags($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

        return $data;
    }



?>