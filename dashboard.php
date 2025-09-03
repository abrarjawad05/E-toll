<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - E-Toll System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            background-color: rgba(0, 0, 0, 0.5); /* Black w/ opacity */
        }

        .modal-content {
            background-color: #000;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 40%; /* Could be more or less, depending on screen size */
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Logo -->
    <header>
        <img src="etoll logo.png" alt="E-Toll System Logo" class="logo">
        <h1>Welcome, 
            <?php 
                session_start(); 
                include 'server.php';  // Include database connection

                if (isset($_SESSION['user'])) {
                    echo htmlspecialchars($_SESSION['user']['name']);  // Display user's name
                } else {
                    header("Location: index.php");  // Redirect to login if not logged in
                    exit();
                }
            ?>
        </h1>
    </header>

    <!-- Dashboard Buttons -->
    <section>
        <h2>Dashboard</h2>
        <button id="userInfoBtn">View User Info</button>
        <button id="creditBalanceBtn">Check Credit Balance</button>
        <button onclick="window.location.href='payment.php'">Make a Payment</button>
        <button onclick="window.location.href='receipts.php'">View Receipts</button>
    </section>

    <!-- User Info Modal -->
    <div id="userInfoModal" class="modal">
        <div class="modal-content">
            <span class="close" id="userInfoClose">&times;</span>
            <h2>User Info</h2>
            <?php
            // Fetch user info from database
            $nid = $_SESSION['user']['nid'];
            $sql = "SELECT name, nid, vehicle_name, vehicle_number, vehicle_category FROM users WHERE nid = '$nid'";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                echo "<p>Name: " . htmlspecialchars($user['name']) . "</p>";
                echo "<p>NID: " . htmlspecialchars($user['nid']) . "</p>";
                echo "<p>Vehicle Name: " . htmlspecialchars($user['vehicle_name']) . "</p>";
                echo "<p>Vehicle Number: " . htmlspecialchars($user['vehicle_number']) . "</p>";
                echo "<p>Vehicle Category: " . htmlspecialchars($user['vehicle_category']) . "</p>";
            } else {
                echo "<p>No user info found.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Credit Balance Modal -->
    <div id="creditBalanceModal" class="modal">
        <div class="modal-content">
            <span class="close" id="creditBalanceClose">&times;</span>
            <h2>Credit Balance</h2>
            <?php
            // Fetch user credit balance from database
            $sql = "SELECT credit FROM users WHERE nid = '$nid'";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                echo "<p>Current Balance: ৳" . htmlspecialchars($user['credit']) . "</p>";
            } else {
                echo "<p>No credit info found.</p>";
            }
            ?>
        </div>
    </div>

    <!-- JavaScript to handle modals -->
    <script>
        // User Info Modal
        var userInfoModal = document.getElementById("userInfoModal");
        var userInfoBtn = document.getElementById("userInfoBtn");
        var userInfoClose = document.getElementById("userInfoClose");

        userInfoBtn.onclick = function() {
            userInfoModal.style.display = "block";
        }

        userInfoClose.onclick = function() {
            userInfoModal.style.display = "none";
        }

        // Credit Balance Modal
        var creditBalanceModal = document.getElementById("creditBalanceModal");
        var creditBalanceBtn = document.getElementById("creditBalanceBtn");
        var creditBalanceClose = document.getElementById("creditBalanceClose");

        creditBalanceBtn.onclick = function() {
            creditBalanceModal.style.display = "block";
        }

        creditBalanceClose.onclick = function() {
            creditBalanceModal.style.display = "none";
        }

        // Close modals if clicking outside the modal content
        window.onclick = function(event) {
            if (event.target == userInfoModal) {
                userInfoModal.style.display = "none";
            }
            if (event.target == creditBalanceModal) {
                creditBalanceModal.style.display = "none";
            }
        }
    </script>
</body>
</html>
