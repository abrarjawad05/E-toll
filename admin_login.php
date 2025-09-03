<?php
session_start();
include 'server.php';  // Include database connection

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded credentials for admin login
    if ($username == "Admin" && $password == "Admin") {
        $_SESSION['admin'] = true;
        header("Location: admin_panel.php");
        exit();
    } else {
        echo "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
</head>
<body>
    <header>
        <img src="etoll_logo.png" alt="E-Toll System Logo" class="logo"> <!-- Update logo source as needed -->
        <h1>Admin Login</h1>
    </header>
    <form action="" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
