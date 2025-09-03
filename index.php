<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Toll System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Logo -->
    <header>
        <img src="etoll logo.png" alt="E-Toll System Logo" class="logo">
        <h1>WELCOME to E-Toll System</h1>
    </header>

    <!-- Login Form -->
    <section>
        <h2>Login</h2>
        <form action="index.php" method="POST">
            <label for="nid">NID:</label>
            <input type="text" id="nid" name="nid" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign up here</a></p>

        <?php
        session_start(); // Start session for login

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            include('server.php');  // Correctly include database connection

            $nid = $_POST['nid'];
            $password = $_POST['password'];

            // Fetch user data based on NID
            $sql = "SELECT * FROM users WHERE nid = '$nid'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Store user info in session
                    $_SESSION['user'] = $user;
                    $_SESSION['nid'] = $user['nid']; // Store NID in session as well

                    // Redirect to dashboard
                    header("Location: dashboard.php");
                    exit();
                } else {
                    echo "<p style='color:red;'>Incorrect password. Please try again.</p>";
                }
            } else {
                echo "<p style='color:red;'>User not found. Please check your NID.</p>";
            }

            $conn->close();
        }
        ?>
    </section>
</body>
</html>
