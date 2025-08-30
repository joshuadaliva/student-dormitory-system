

<?php


    function sanitizeInput($data){
        $data = strip_tags($data);
        $data = strip_tags($data);
        $data = trim($data);

        return $data;
    }



?>