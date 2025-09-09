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

$room_list = fetchAllDetails("SELECT  description, room_id, room_number,roomType,status,rent_fee from rooms", "", $conn);

$error = "";
$success = "";
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST["submit"])) {

    if (empty($_POST["room_number"]) || empty($_POST["room_type"]) || empty($_POST["room_status"]) || empty($_POST["rent"]) || empty($_POST["description"])) {
        $error = "all inputs cannot be blank";
    } elseif ($_FILES["file"]["error"] === UPLOAD_ERR_NO_FILE) {
        $error = "image cannot be blank";
    }

    $room_number = sanitizeInput($_POST["room_number"]);
    $room_type = sanitizeInput($_POST["room_type"]);
    $room_status = sanitizeInput($_POST["room_status"]);
    $rent = sanitizeInput($_POST["rent"]);
    $description = sanitizeInput($_POST["description"]);


    if (empty($error)) {
        $file = uploadImage($_FILES);
        if (isset($file["error"])) {
            $error = $file["error"];
        } elseif (isset($file["success"])) {
            $stmt = $conn->prepare("SELECT room_number from rooms where room_number = ?");
            $stmt->execute([$room_number]);
            $is_room_number_exist = $stmt->fetch(PDO::FETCH_COLUMN);

            if ($is_room_number_exist) {
                $error = "room number exist, try another room number";
            } else {
                $stmt = $conn->prepare("INSERT INTO rooms(room_number,roomType,status,rent_fee,description,imagePath) values(?,?,?,?,?,?)");
                $stmt->execute([$room_number, $room_type, $room_status, $rent, $description, $file["file"]]);

                if ($stmt->rowCount() > 0) {
                    $success = "room added successfully";
                    $room_list = fetchAllDetails("SELECT description, room_id, room_number,roomType,status,rent_fee from rooms", null, $conn);
                } else {
                    $error = "room not added";
                }
            }
        }
    }
}

// edit form

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST["edit_form"])) {

    $room_id = sanitizeInput($_POST["room_id"]);
    $room_number = sanitizeInput($_POST["room_number"]);
    $room_type = sanitizeInput($_POST["room_type"]);
    $room_status = sanitizeInput($_POST["room_status"]);
    $rent = sanitizeInput($_POST["rent"]);
    $description = sanitizeInput($_POST["description"]);

    $is_occupied = fetchDetails("SELECT room_id from rooms where room_id = ? and status = 'Occupied'", $room_id, $conn);

    

    if (empty($_POST["room_number"]) || empty($_POST["room_type"]) || empty($_POST["room_status"]) || empty($_POST["rent"]) || empty($_POST["description"])) {
        $error = "all inputs cannot be blank";
    }elseif($is_occupied){
        $error = "room is occupied, nice try ";
    }

    
    if (empty($error)) {
        if ($_FILES["file"]["error"] === UPLOAD_ERR_NO_FILE) {
            $stmt = $conn->prepare("SELECT room_number from rooms where room_number = ?");
            $stmt->execute([$room_number]);
            $is_room_number_exist = $stmt->fetch(PDO::FETCH_COLUMN);
            if ($is_room_number_exist) {
                $error = "room number exist, try another room number" ;
            } else {
                $stmt = $conn->prepare("UPDATE rooms set room_number = ? , roomType = ?, description = ?, rent_fee = ?, status = ? where room_id = ?");
                $stmt->execute([$room_number, $room_type, $description, $rent, $room_status, $room_id]);
                if ($stmt->rowCount() > 0) {
                    $success = "room updated";
                    $room_list = fetchAllDetails("SELECT description, room_id, room_number,roomType,status,rent_fee from rooms", null, $conn);
                } else {
                    $error = "error updating the room";
                }
            }
        } else {
            $file = uploadImage($_FILES);
            if (isset($file["error"])) {
                $error = $file["error"];
            } elseif (isset($file["success"])) {
                $stmt = $conn->prepare("SELECT room_number from rooms where room_number = ?");
                $stmt->execute([$room_number]);
                $is_room_number_exist = $stmt->fetch(PDO::FETCH_COLUMN);
    
                if ($is_room_number_exist) {
                    $error = "room number exist, try another room number";
                } else {
                    $stmt = $conn->prepare("UPDATE rooms set room_number = ? , roomType = ?, status = ?, rent_fee = ?, description = ?, imagePath = ? where room_id = ?");
                    $stmt->execute([$room_number, $room_type, $room_status, $rent, $description, $file["file"]]);
    
                    if ($stmt->rowCount() > 0) {
                        $success = "room update";
                        $room_list = fetchAllDetails("SELECT description, room_id, room_number,roomType,status,rent_fee from rooms", null, $conn);
                    } else {
                        $error = "error updating the room";
                    }
                }
            }
            
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirm_delete"])) {
    $room_id = sanitizeInput($_POST["room_id"]);
    $is_room_occupied = fetchDetails("SELECT room_id from rooms WHERE room_id = ? and status = 'Occupied'", $room_id, $conn);
    if ($is_room_occupied) {
        $error = "cannot delete room, room occupied";
    } else {
        $stmt = $conn->prepare("UPDATE rooms set status = 'Deleted' where room_id = ?");
        $stmt->execute([$room_id]);
        $room_row_count = $stmt->rowCount();
        if ($room_row_count > 0) {
            $success = "room deleted";
            $room_list = fetchAllDetails("SELECT room_id, room_number,roomType,status,rent_fee from rooms", null, $conn);
        } else {
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
            <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
                <input type="hidden" name="room_id" class="room-id">
                <div style="display: flex; gap:10px">
                    <button type="button" name="close-modal-btn" class="close-modal-bookings">CLOSE MODAL</button>
                    <button name="confirm_delete" class="confirm-delete-btn"> DELETE ROOM </button>
                </div>
            </form>
        </div>
    </div>
    <div class="overlay-modal-edit">
        <div class="modal-edit">
            <h1>EDIT ROOM</h1>
            <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" enctype="multipart/form-data" method="POST">

                <input type="hidden" name="room_id" id="room-id-edit">
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
                    <label for="status" id="label-status">
                        Status: <br>
                        <select name="room_status" id="room_status">
                            <option value=""></option>
                            <option value="Available">Available</option>
                            <option value="Unavailable">Not Available</option>
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
                <div style="display: flex; gap:10px; justify-content:center">
                    <button type="button" name="close-modal-btn-edit" class="close-modal-edit">CLOSE</button>
                    <input type="submit" name="edit_form" class="submit" value="SAVE ROOM">
                </div>
            </form>
        </div>
    </div>
    <div class="rooms-panel">
        <h1>Manage Rooms</h1>
        <div class="container">
            <h1>Add New Room</h1>
            <?php if (!empty($error)): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif ?>
            <?php if (!empty($success)): ?>
                <p class="success-message"><?= htmlspecialchars($success) ?></p>
            <?php endif ?>
            <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" enctype="multipart/form-data" method="POST">
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
                            <option value="Unavailable">Not Available</option>
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
                    <?php foreach ($room_list as $room): ?>
                        <tr>
                            <td><?= htmlspecialchars($room["room_number"]) ?></td>
                            <td><?= htmlspecialchars($room["roomType"]) ?></td>
                            <td><?= htmlspecialchars($room["rent_fee"]) ?></td>
                            <?php if ($room["status"] === "Available" || $room["status"] === "Unavailable" || $room["status"] === "Occupied") : ?>
                                <td><?= htmlspecialchars($room["status"]) ?></td>
                            <?php endif ?>
                            <?php if ($room["status"] === "Deleted"): ?>
                                <td style="text-decoration: line-through;"><?= htmlspecialchars($room["status"]) ?></td>
                            <?php endif ?>
                            <?php if ($room["status"] != "Deleted"): ?>
                                <td>
                                    <button class="edit" data-description="<?= htmlspecialchars($room["description"]) ?>" data-id="<?= htmlspecialchars($room["room_id"]) ?>" data-status="<?= htmlspecialchars($room["status"]) ?>" data-rentfee="<?= htmlspecialchars($room["rent_fee"]) ?>" data-roomnumber="<?= htmlspecialchars($room["room_number"]) ?>" data-roomtype="<?= htmlspecialchars($room["roomType"]) ?>">EDIT</button>
                                    <button class="delete" data-id="<?= htmlspecialchars($room["room_id"]) ?>">DELETE</button>
                                </td>
                            <?php endif ?>

                        </tr>
                    <?php endforeach ?>
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
                room_id.value = roomID;
                overlayModal.classList.toggle("open-modal");
                closeModal.addEventListener("click", () => {
                    overlayModal.classList.remove("open-modal");
                })
            })
        })


        let editBtn = document.querySelectorAll(".edit");
        let overlayModalEdit = document.querySelector(".overlay-modal-edit");
        let closeModalEdit = document.querySelector(".close-modal-edit");
        let room_id_edit = document.getElementById("room-id-edit");
        let room_number = document.getElementById("room_number");
        let room_type = document.getElementById("room_type");
        let room_status = document.getElementById("room_status");
        let label_status = document.getElementById("label-status");
        let description = document.getElementById("description");
        let rent = document.getElementById("rent");

        editBtn.forEach(btn => {
            btn.addEventListener("click", (e) => {
                console.log(btn);
                if(e.target.dataset.status === "Occupied"){
                    room_status.style.display = "none";
                    label_status.style.display = "none";
                }
                else{
                    room_status.style.display = "block";
                    label_status.style.display = "block";
                }
                room_status.value = e.target.dataset.status;
                description.value = e.target.dataset.description;
                rent.value = e.target.dataset.rentfee;
                room_number.value = e.target.dataset.roomnumber;
                room_type.value = e.target.dataset.roomtype;
                room_id_edit.value = e.target.dataset.id;
                overlayModalEdit.classList.toggle("open-modal");
                closeModalEdit.addEventListener("click", () => {
                    overlayModalEdit.classList.remove("open-modal");
                })
            })
        })
    </script>
</body>

</html>