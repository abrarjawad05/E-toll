<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection file
include('server.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");  // Redirect to login if not logged in
    exit();
}

$nid = $_SESSION['user']['nid']; // Get NID from session

// Get the user's vehicle type from the `users` table
$sql = "SELECT vehicle_category FROM users WHERE nid = '$nid'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_vehicle_type = $row['vehicle_category'];
} else {
    echo "Error fetching user vehicle type!";
}

// Fetch all available flyovers from the toll_chart table
$flyovers_query = "SELECT DISTINCT flyover FROM toll_chart";
$flyovers_result = $conn->query($flyovers_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['calculateToll'])) {
    // Get selected vehicle and flyover
    $selected_vehicle = $_POST['vehicleSelect'];
    $selected_flyover = $_POST['flyoverSelect'];

    // Check if the selected vehicle matches the user's vehicle type
    if ($selected_vehicle != $user_vehicle_type) {
        $error_message = "Invalid vehicle type selected!";
    } else {
        // Fetch toll amount from the toll_chart table
        $sql = "SELECT amount FROM toll_chart WHERE flyover = '$selected_flyover' AND vehicle_type = '$selected_vehicle'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $toll_amount = $row['amount'];
        } else {
            $error_message = "Error fetching toll amount!";
        }
    }
}

// Handle payment submission
if (isset($_POST['confirmPayment'])) {
    $amount_to_pay = $_POST['amount'];

    // Check if user has enough credits
    $user_credit_query = "SELECT credit FROM users WHERE nid = '$nid'";
    $user_credit_result = $conn->query($user_credit_query);
    $user_credit_row = $user_credit_result->fetch_assoc();
    $user_credit = $user_credit_row['credit'];

    if ($user_credit >= $amount_to_pay) {
        // Deduct amount from user's credit
        $new_credit = $user_credit - $amount_to_pay;
        $update_credit_query = "UPDATE users SET credit = '$new_credit' WHERE nid = '$nid'";
        if ($conn->query($update_credit_query)) {
            // Payment successful, insert into payments table
            $selected_vehicle = $_POST['vehicleSelect'];
            $selected_flyover = $_POST['flyoverSelect'];

            // Insert payment into the payments table
            $insert_payment_query = "INSERT INTO payments (nid, vehicle,flyover, amount, payment_date) 
                                     VALUES ('$nid', '$selected_vehicle', '$selected_flyover', '$amount_to_pay', NOW())";
            if ($conn->query($insert_payment_query)) {
                $payment_message = "Payment successful! Receipt has been recorded.";
            } else {
                $payment_message = "Error recording payment: " . $conn->error;
            }
        } else {
            $payment_message = "Error processing payment!";
        }
    } else {
        $payment_message = "Insufficient balance!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Payment - E-Toll System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>Make a Payment</h1>
    </header>

    <section>
        <h2>Select Vehicle and Flyover</h2>

        <form method="POST" action="">
            <!-- Vehicle Selection -->
            <label for="vehicleSelect">Select Your Vehicle:</label>
            <select id="vehicleSelect" name="vehicleSelect">
                <option value="car" <?= $user_vehicle_type == 'car' ? 'selected' : '' ?>>Car</option>
                <option value="bus" <?= $user_vehicle_type == 'bus' ? 'selected' : '' ?>>Bus</option>
                <option value="truck" <?= $user_vehicle_type == 'truck' ? 'selected' : '' ?>>Truck</option>
                <option value="motorcycle" <?= $user_vehicle_type == 'motorcycle' ? 'selected' : '' ?>>Motorcycle</option>
            </select>
            <br><br>

            <!-- Flyover Selection -->
            <label for="flyoverSelect">Select Flyover:</label>
            <select id="flyoverSelect" name="flyoverSelect">
                <?php while ($flyover = $flyovers_result->fetch_assoc()) { ?>
                    <option value="<?= htmlspecialchars($flyover['flyover']) ?>"><?= htmlspecialchars($flyover['flyover']) ?></option>
                <?php } ?>
            </select>
            <br><br>

            <button type="submit" name="calculateToll">Calculate Toll</button>
        </form>

        <!-- Show the toll amount and confirm payment button if calculated -->
        <?php if (isset($toll_amount)) { ?>
            <p>Amount to Pay: <?= htmlspecialchars($toll_amount) ?> credits</p>

            <form method="POST" action="">
                <input type="hidden" name="amount" value="<?= htmlspecialchars($toll_amount) ?>">
                <input type="hidden" name="vehicleSelect" value="<?= htmlspecialchars($selected_vehicle) ?>">
                <input type="hidden" name="flyoverSelect" value="<?= htmlspecialchars($selected_flyover) ?>">
                <button type="submit" name="confirmPayment">Confirm Payment</button>
            </form>
        <?php } ?>

        <!-- Display error or success messages -->
        <?php if (isset($error_message)) { ?>
            <p style="color:red;"><?= htmlspecialchars($error_message) ?></p>
        <?php } ?>

        <?php if (isset($payment_message)) { ?>
            <p style="color:green;"><?= htmlspecialchars($payment_message) ?></p>
        <?php } ?>

        <!-- Go to Dashboard Button -->
        <form action="dashboard.php" method="GET">
            <button type="submit">Go to Dashboard</button>
        </form>
    </section>

</body>
</html>
