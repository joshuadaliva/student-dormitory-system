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
        flex-direction: column;
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
        transition-duration: 1s;
        z-index: 999;

    }

    .sidebar .header h1 {
        font-size: 25px;
        color: #1d4ed8;
        text-align: center;
        width: 100%;
    }
    .sidebar .close-btn{
        display: none;
    }

    .sidebar .user {
        font-weight: bold;
        color: #374151;
        font-size: 15px;
        text-align: center;
        margin: 40px 0px;
    }
    .sidebar .header{
        display: flex;
        text-align: center;
        width: 100%;
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

    nav{
        display: none;
        gap: 20px;
        align-items: center;
        box-shadow: 0px 0px 5px rgb(0, 0, 0,  0.5);
        margin: 0;
        padding: 5px 30px;
        position: fixed;
        width: 100%;
        top: 0;
        left: 0;
        z-index: 1;
        background-color: white;
        
    }
    .nav-title{
        font-size: 1.2rem;
        background: linear-gradient(to right, #6366f1 , #a855f7);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: bold;

    }
    .nav-title a{
        text-decoration: none;
    }
    @media screen and (max-width : 967px) {
        .sidebar{
            transform: translate(-100%);
        }
        .sidebar-open{
            transform: translate(0%);
            
        }
        .sidebar .close-btn{
            display: block;
            font-size: 15px;
            position: absolute;
            top: 0;
            right: 10px;
        }
        nav{
            display: flex;
        }
        .container{
            padding-left: 20px;
        }
    }
    </style>
</head>
<body>
    
    <div class="sidebar">
        <div class="header">
            <h1 class="nav-title"><a href="../index.php">Dormitory System</a></h1>
            <button class="close-btn">&#88;</button>
        </div>
        <hr color="#e6e8ec" size="1">
        <p class="user"><?= htmlspecialchars("Welcome! ". $_SESSION["name"]) ?></p>
        <div class="bottom-links">
            <?php if($_SESSION["user_type"] === "student"): ?>
                <div class="page-selection">
                    <a href="../student/dashboard.php">Dashboard</a>
                    <a href="../student/bookings.php">My Bookings</a>
                    <a href="../student/payment.php">My Payments</a>
                    <a href="../student/profile.php">Profile</a>
                </div>
            <?php endif ?>
            <?php if($_SESSION["user_type"] === "admin"): ?>
                <div class="page-selection">
                    <a href="../admin/dashboard.php">Dashboard</a>
                    <a href="../admin/bookings.php">Bookings</a>
                    <a href="../admin/payments.php">Payments</a>
                    <a href="../admin/rooms.php">Rooms</a>
                    <a href="../admin/report.php">Reports</a>
                </div>
            <?php endif ?>
            <button><a href="../process/logout.php">Logout</a></button>
        </div>
    </div>
    <nav>
        <button class="hamburger">&#9776;</button>
        <h1 class="nav-title"><a href="../index.php">DORMITORY SYSTEM</a></h1>
    </nav>


    <script>
        let btn = document.querySelector(".hamburger");
        let sidebar = document.querySelector(".sidebar");
        let closeBtn = document.querySelector(".close-btn");
        btn.addEventListener("click", () => {
            sidebar.classList.toggle("sidebar-open");
        })
        closeBtn.addEventListener("click", () => {
            sidebar.classList.toggle("sidebar-open");
        })

    </script>
</body>
</html>