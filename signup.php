<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - E-Toll System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Logo -->
    <header>
        <img src="etoll logo.png" alt="E-Toll System Logo" class="logo">
        <h1>Sign Up for E-Toll System</h1>
    </header>

    <!-- Signup Form -->
    <section>
        <form action="" method="POST"> <!-- Changed to POST for sign-up -->
            <label for="name">Username:</label>
            <input type="text" id="name" name="name" required>

            <label for="nid">NID:</label>
            <input type="text" id="nid" name="nid" required>

            <label for="vehicle-name">Vehicle Name:</label>
            <input type="text" id="vehicle-name" name="vehicle-name" required>

            <label for="vehicle-number">Vehicle Number:</label>
            <input type="text" id="vehicle-number" name="vehicle-number" required>

            <label for="vehicle-category">Vehicle Category:</label>
            <select id="vehicle-category" name="vehicle-category" required>
                <option value="car">Car</option>
                <option value="bus">Bus</option>
                <option value="truck">Truck</option>
                <option value="motorcycle">Motorcycle</option>
            </select>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Sign Up</button>
        </form>

        <p>Already have an account? <a href="index.php">Login here</a></p>
    </section>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        include 'server.php'; // Include the database connection

        // Collecting form data
        $name = $_POST['name'];
        $nid = $_POST['nid'];
        $vehicle_name = $_POST['vehicle-name'];
        $vehicle_number = $_POST['vehicle-number'];
        $vehicle_category = $_POST['vehicle-category'];
        $password = $_POST['password'];

        // Check if user exists
        $checkUser = "SELECT * FROM users WHERE nid='$nid'";
        $result = $conn->query($checkUser);

        if ($result->num_rows > 0) {
            echo "<p>User already exists. Please <a href='index.php'>login here</a>.</p>";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert data into the database
            $sql = "INSERT INTO users (nid, name, vehicle_category, vehicle_name, vehicle_number, password, credit)
                    VALUES ('$nid', '$name', '$vehicle_category', '$vehicle_name', '$vehicle_number', '$hashed_password', 100)";

            if ($conn->query($sql) === TRUE) {
                echo "<p>Sign up successful! <a href='index.php'>Login here</a></p>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        // Close the database connection
        $conn->close();
    }
    ?>
</body>
</html>
