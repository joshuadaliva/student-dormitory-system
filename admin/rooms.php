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



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
</head>
<style>
    .rooms-panel {
        margin-left: 260px;
        width: 100%;
        height: 100%;
    }

    .container {
        box-shadow: 0px 2px 5px rgb(0, 0, 0, 0.2);
        border-radius: 10px;
        background-color: white;
        margin: 0px 20px;
    }

    .container h1 {
        font-size: 20px;
        padding: 20px;
    }

    .container form {
        padding: 10px;
    }

    .container form .form-divide {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .container form input {
        padding: 10px;
        border: 1px solid #e5e7eb;
        width: 100%;
        border-radius: 10px;
    }

    .container form select {
        padding: 10px;
        width: 100%;
    }

    .description,
    .image {
        margin-top: 30px;
    }

    .container form .submit {
        background-color: #2563eb;
        color: white;
        cursor: pointer;
        transition-duration: 1s;
    }

    .container .form .submit:hover {
        transform: scale(2);
    }

    @media screen and (max-width:972px) {
        .rooms-panel {
            margin-left: 0;
        }
    }



    .rooms-panel {
        margin-bottom: 100px;
    }

    .rooms-panel .room-list {
        padding: 0px 20px;
        padding-bottom: 20px;
    }

    .rooms-panel .room-list h1 {
        padding: 20px 10px;
        margin-bottom: 0;
    }

    .rooms-panel .room-list table {
        width: 100%;
        text-align: left;
        border-radius: 20px;
        border: 1px solid #e6e8ec;
        border-collapse: collapse;
        padding: 10px;
    }

    .rooms-panel .room-list th {
        background-color: #f9fafb;
        border-bottom: 1px solid #e6e8ec;
        padding: 10px;
        color: #4b5563;
    }

    .rooms-panel .room-list td {
        padding: 10px;
        border-bottom: 1px solid #e6e8ec;
        color: #374151;

    }

    footer {
        background-color: #111827;
        padding: 20px;
        text-align: center;
        color: white;
        margin-top: 4rem;
    }
</style>

<body>
    <?php require_once "../component/sidebar.php" ?>
    <div class="rooms-panel">
        <h1>Manage Rooms</h1>
        <div class="container">
            <h1>Add New Room</h1>
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
                        <select name="status" id="status">
                            <option value=""></option>
                            <option value="">Available</option>
                            <option value="">Not Available</option>
                            <option value="">Maintenance</option>
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
                    <input type="file" name="image" id="image">
                </label><br><br>
                <input type="submit" class="submit" value="Add Room">
            </form>
        </div>
        <div class="room-list container">
            <h1>Room List</h1>
            <div>
                <table>
                    <tr>
                        <th>Room No.</th>
                        <th>Type</th>
                        <th>Rent Fee</th>
                        <th>STATUS</th>
                        <th>Actions</th>
                    </tr>
                    <tr>
                        <td>JOSHUA DALIVA</td>
                        <td>Bed Spacer</td>
                        <td>4234324</td>
                        <td>Available</td>
                        <th>
                            <button>EDIT</button>
                            <button>DELETE</button>
                        </th>
                    </tr>
                    <tr>
                        <td>JOSHUA DALIVA</td>
                        <td>Bed Spacer</td>
                        <td>4234324</td>
                        <td>Available</td>
                        <th>
                            <button>EDIT</button>
                            <button>DELETE</button>
                        </th>
                    </tr>
                </table>
            </div>
        </div>
        <footer>
            <p>© <?= date("Y") ?> Student Dormitory Management System. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>