<?php

require_once "../db/config.php";
require_once "../functions/functions.php";
session_start();


isStudent("../student/dashboard.php");
if (isset($_SESSION["user_type"])) {
    if ($_SESSION["user_type"] !== "admin") {
        header("Location: " . "./login.php");
    }
} else {
    header("Location: " . "./login.php");
}

$room_list = fetchAllDetails("SELECT room_id, room_number,roomType,status,rent_fee from rooms", "", $conn);

$error = "";
$success = "";
if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST["submit"])){

    if(empty($_POST["room_number"]) || empty($_POST["room_type"]) || empty($_POST["room_status"]) || empty($_POST["rent"]) ||empty($_POST["description"])){
        $error = "all inputs cannot be blank";
    }
    elseif($_FILES["file"]["error"] === UPLOAD_ERR_NO_FILE){
        $error = "image cannot be blank";
    }

    $room_number = sanitizeInput($_POST["room_number"]);
    $room_type = sanitizeInput($_POST["room_type"]);
    $room_status = sanitizeInput($_POST["room_status"]);
    $rent = sanitizeInput($_POST["rent"]);
    $description = sanitizeInput($_POST["description"]);

    
    if(empty($error)){
        $file = uploadImage($_FILES);
        if(isset($file["error"])){
            $error = $file["error"];
        }
        elseif(isset($file["success"])){
            $stmt = $conn->prepare("SELECT room_number from rooms where room_number = ?");
            $stmt->execute([$room_number]);
            $is_room_number_exist = $stmt->fetch(PDO::FETCH_COLUMN);

            if($is_room_number_exist){
                $error = "room number exist, try another room number";
            }
            else{
                $stmt = $conn->prepare("INSERT INTO rooms(room_number,roomType,status,rent_fee,description,imagePath) values(?,?,?,?,?,?)");
                $stmt->execute([$room_number, $room_type,$room_status,$rent,$description,$file["file"]]);
                
                if($stmt->rowCount() > 0){
                    $success = "room added successfully";
                    $room_list = fetchAllDetails("SELECT room_id, room_number,roomType,status,rent_fee from rooms", null, $conn);
                }
                else{
                    $error = "room not added";
                }

            }
        }


    }


}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirm_delete"])) {
    $room_id = sanitizeInput($_POST["room_id"]);
    $is_student_pending_payment = fetchDetails("SELECT room_id from rooms WHERE room_id = ? and status = 'Unavailable'", $room_id, $conn);
    if($is_student_pending_payment){
        $error = "cannot delete room, room occupied";
    }
    else{
        $stmt = $conn->prepare("DELETE from rooms where room_id = ?");
        $stmt -> execute([$room_id]);
        $room_row_count = $stmt->rowCount();
        if($room_row_count > 0){
            $success = "room deleted";
            $room_list = fetchAllDetails("SELECT room_id, room_number,roomType,status,rent_fee from rooms", null, $conn);
        }
        else{
            $error = "cannot delete room, please try again later";
            $room_list = fetchAllDetails("SELECT room_id, room_number,roomType,status,rent_fee from rooms", null, $conn);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/admin_room.css">
</head>


<body>
    <?php require_once "../component/sidebar.php" ?>
        <div class="overlay-modal">
            <div class="modal">
                <h1>DO YOU WANT TO DELETE THIS ROOM?</h1>
                <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
                    <input type="hidden" name="room_id" class="room-id">
                    <div style="display: flex; gap:10px">
                        <button class="close-modal-bookings">CLOSE MODAL</button>
                        <button name="confirm_delete" class="confirm-delete-btn"> DELETE ROOM </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="rooms-panel">
        <h1>Manage Rooms</h1>
        <div class="container">
            <h1>Add New Room</h1>
            <?php if(!empty($error)): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif ?>
            <?php if(!empty($success)): ?>
                <p class="success-message"><?= htmlspecialchars($success) ?></p>
            <?php endif ?>
            <form action="<?php htmlspecialchars($_SERVER["REQUEST_METHOD"] === "POST") ?>" enctype="multipart/form-data" method="POST">
                <div class="form-divide">
                    <label for="room-number">
                        Room Number:<br>
                        <input type="number" name="room_number" id="room_number">
                    </label>
                    <label for="room-type">
                        Room Type: <br>
                        <select name="room_type" id="room_type">
                            <option value=""></option>
                            <option value="Bed Spacer">Bed Spacer</option>
                            <option value="Single Room">Single Room</option>
                            <option value="Double Room">Double Room</option>
                        </select>
                    </label>
                    <label for="status">
                        Status: <br>
                        <select name="room_status" id="room_status">
                            <option value=""></option>
                            <option value="Available">Available</option>
                            <option value="Unavailabe">Not Available</option>
                        </select>
                    </label>
                    <label for="Rent Fee">
                        Rent Fee:<br>
                        <input type="number" name="rent" id="rent">
                    </label>
                </div><br>
                <label for="description" class="description">
                    description:
                    <input type="text" name="description" id="description">
                </label><br><br>
                <label for="image" class="image">
                    Pick image:
                    <input type="file" name="file" id="image">
                </label><br><br>
                <input type="submit" name="submit" class="submit" value="Add Room">
            </form>
        </div>
        <div class="room-list container">
            <h1>Room List</h1>
            <div class="container-table">
                <table>
                    <tr>
                        <th>Room No.</th>
                        <th>Type</th>
                        <th>Rent Fee</th>
                        <th>STATUS</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach($room_list as $room): ?>
                        <tr>
                            <td><?= htmlspecialchars($room["room_number"]) ?></td>
                            <td><?= htmlspecialchars($room["roomType"]) ?></td>
                            <td><?= htmlspecialchars($room["rent_fee"]) ?></td>
                            <td><?= htmlspecialchars($room["status"]) ?></td>
                            <td>
                                <button class="edit">EDIT</button>
                                <button class="delete" data-id="<?= htmlspecialchars($room["room_id"]) ?>">DELETE</button>
                            </td>
                        </tr>
                    <?php endforeach?>
                </table>
            </div>
        </div>
        <footer>
            <p>© <?= date("Y") ?> Student Dormitory Management System. All rights reserved.</p>
        </footer>
    </div>
    <script>
        let deleteBtn = document.querySelectorAll(".delete");
        let overlayModal = document.querySelector(".overlay-modal");
        let room_id = document.querySelector(".room-id");
        let closeModal = document.querySelector(".close-modal-bookings");
        deleteBtn.forEach(btn => {
            btn.addEventListener("click", (e) => {
                const roomID = e.target.dataset.id;
                console.log(roomID);
                room_id.value = roomID;
                overlayModal.classList.toggle("open-modal");
                closeModal.addEventListener("click", () => {
                    overlayModal.classList.remove("open-modal");
                })
            })
        })
        
    </script>
</body>

</html>