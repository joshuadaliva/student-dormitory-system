<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>
    <style>
        :root {
        --gray: #6b7280;
        --indigo: #6366f1;
        --blue: #3b82f6;
        --red: #ef4444;
        --green: #22c55e;
        --purple: #a855f7;
        --yellow: #eab308;
        --pink: #ec4899;
        --dark: #0d121d;
        --bgdark: #101828;
        --cardark: #233042;
        --nav-shadow: rgb(0, 0, 0, 0.5);

        /* bg-pale */

    }

    * {
        box-sizing: border-box;
        font-family: "Inter", "san-serif";
        max-width: 100%;
    }

    body {
        height: 100vh;
        width: 100%;
        padding: 0;
        margin: 0;
        display: flex;
        background-color: #f9fafb;
        gap: 2px;
    }

    .sidebar {
        width: 250px;
        height: 100%;
        background-color: white;
        border-right: 1px solid #e6e8ec;
        position: fixed;
        top: 0;
        left: 0;


    }

    .sidebar h1 {
        font-size: 25px;
        color: #1d4ed8;
        text-align: center;
    }

    .sidebar .user {
        font-weight: bold;
        color: #374151;
        font-size: 15px;
        text-align: center;
        margin: 40px 0px;
    }

    .sidebar .bottom-links {
        display: flex;
        justify-content: space-between;
        flex-direction: column;
        padding: 0px 20px;
        width: 100%;
        height: 70%;
    }

    .sidebar .bottom-links .page-selection {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .sidebar .bottom-links .page-selection a {
        text-decoration: none;
        color: #1d4ed8;
        padding: 10px;
        font-weight: bold;
        border-radius: 10px;
    }

    .sidebar .bottom-links .page-selection a:hover {
        background-color: #dbeafe;
    }

    .sidebar .bottom-links button {
        width: 90%;
        background-color: var(--red);
        color: white;
        font-weight: bold;
        border: none;
        padding: 10px;
        border-radius: 10px;
        font-size: 15px;
        cursor: pointer;
        transition-duration: 0.5s;
    }

    .sidebar .bottom-links button:hover {
        transform: scale(0.9);
        background-color: red;
    }
    .sidebar .bottom-links a {
        text-decoration: none;
        color: white;
    }
    @media screen and (max-width : 967px) {
        .sidebar{
            display: none;
        }
        body{
            display: block;
            padding-left: 20px;
        }
    }
    </style>
</head>
<body>
    <div class="sidebar">
        <h1>dormitory system</h1>
        <hr color="#e6e8ec" size="1">
        <p class="user"><?= htmlspecialchars("Welcome! ". $_SESSION["name"]) ?></p>
        <div class="bottom-links">
            <?php if($_SESSION["user_type"] === "student"): ?>
                <div class="page-selection">
                    <a href="">Dashboard</a>
                    <a href="">My Bookings</a>
                    <a href="">My Payments</a>
                    <a href="">Profile</a>
                </div>
            <?php endif ?>
            <?php if($_SESSION["user_type"] === "admin"): ?>
                <div class="page-selection">
                    <a href="../admin/dashboard.php">Dashboard</a>
                    <a href="../admin/bookings.php">Bookings</a>
                    <a href="../admin/payments.php">Payments</a>
                    <a href="../admin/rooms.php">Rooms</a>
                    <a href="../admin/reports.php">Reports</a>
                </div>
            <?php endif ?>
            <button><a href="../process/logout.php">Logout</a></button>
        </div>
    </div>
</body>
</html>