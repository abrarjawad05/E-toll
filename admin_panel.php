<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "toll_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update Toll Chart (already working)
if (isset($_POST['update_toll'])) {
    $vehicle_type = $_POST['vehicle_type'];
    $flyover = $_POST['flyover'];
    $toll_amount = $_POST['amount'];

    if (empty($flyover) || empty($vehicle_type) || empty($toll_amount)) {
        echo "All fields are required!";
        exit();
    }

    $sql = "INSERT INTO toll_chart (flyover, vehicle_type, amount) VALUES ('$flyover', '$vehicle_type', '$toll_amount')
            ON DUPLICATE KEY UPDATE amount = '$toll_amount'";

    if ($conn->query($sql) === TRUE) {
        echo "Toll chart updated!";
    } else {
        echo "Error updating toll chart: " . $conn->error;
    }
}

// Search by NID and Recharge Credits
if (isset($_POST['search_user'])) {
    $nid = $_POST['nid'];

    // Fetch user details by NID
    $sql = "SELECT nid, name, credit FROM users WHERE nid = '$nid'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_found = true; // Flag to show recharge section
    } else {
        $error_message = "User not found!";
    }
}

// Handle Credit Recharge
if (isset($_POST['recharge_credits'])) {
    $nid = $_POST['nid'];
    $recharge_amount = $_POST['credit_amount'];

    // Fetch the current credit of the user
    $sql = "SELECT credit FROM users WHERE nid = '$nid'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $new_credit = $user['credit'] + $recharge_amount;

        // Update user credits
        $update_sql = "UPDATE users SET credit = '$new_credit' WHERE nid = '$nid'";
        if ($conn->query($update_sql) === TRUE) {
            $success_message = "Recharge successful!";
        } else {
            $error_message = "Error updating credits!";
        }
    } else {
        $error_message = "User not found!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - E-Toll System</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
    </header>

    <!-- Update Toll Chart -->
    <section>
        <h2>Update Toll Chart</h2>
        <form method="POST">
            <label for="flyover">Flyover:</label>
            <select name="flyover" id="flyover" required>
                <option value="">Select Flyover</option>
                <option value="Flyover 1">Flyover 1</option>
                <option value="Flyover 2">Flyover 2</option>
                <option value="Flyover 3">Flyover 3</option>
                <option value="Flyover 4">Flyover 4</option>
                <option value="Flyover 5">Flyover 5</option>
                <option value="Flyover 6">Flyover 6</option>
            </select><br><br>

            <label for="vehicle_type">Vehicle Type:</label>
            <select name="vehicle_type" id="vehicle_type" required>
                <option value="">Select Vehicle Type</option>
                <option value="car">Car</option>
                <option value="bus">Bus</option>
                <option value="truck">Truck</option>
                <option value="motorcycle">Motorcycle</option>
            </select><br><br>

            <label for="amount">Toll Amount:</label>
            <input type="number" name="amount" required><br><br>

            <button type="submit" name="update_toll">Update Toll</button>
        </form>
    </section>

    <!-- Credit Recharge Section -->
    <section>
        <h2>Recharge User Credits</h2>
        <form method="POST">
            <label for="nid">Search by NID:</label>
            <input type="text" name="nid" required><br><br>
            <button type="submit" name="search_user">Search User</button>
        </form>

        <!-- Display user details if found -->
        <?php if (isset($user_found) && $user_found) { ?>
            <h3>User Details</h3>
            <p><strong>NID:</strong> <?= htmlspecialchars($user['nid']) ?></p>
            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Current Credit:</strong> <?= htmlspecialchars($user['credit']) ?></p>

            <!-- Recharge Form -->
            <form method="POST">
                <input type="hidden" name="nid" value="<?= htmlspecialchars($user['nid']) ?>">
                <label for="credit_amount">Enter Recharge Amount:</label>
                <input type="number" name="credit_amount" required><br><br>
                <button type="submit" name="recharge_credits">Recharge Credits</button>
            </form>
        <?php } ?>

        <!-- Error or Success Message -->
        <?php if (isset($error_message)) { ?>
            <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
        <?php } ?>

        <?php if (isset($success_message)) { ?>
            <p style="color: green;"><?= htmlspecialchars($success_message) ?></p>
        <?php } ?>
    </section>

    <!-- Logout -->
    <form action="logout.php" method="POST">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
