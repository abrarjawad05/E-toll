<?php
// Include the database connection file
include('server.php');
include('phpqrcode/qrlib.php'); // Include the QR code library
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get the current user's NID from the session
if (!isset($_SESSION['user'])) {
    echo "User not logged in.";
    exit();
}
$nid = $_SESSION['user']['nid'];

// Retrieve the latest payment for the current user
$query = "SELECT * FROM payments WHERE nid = '$nid' ORDER BY payment_date DESC LIMIT 1";
$result = mysqli_query($conn, $query);
if (!$result) {
    echo "Error executing query: " . mysqli_error($conn);
    exit();
}

// Retrieve the last 5 payments for the current user
$history_query = "SELECT * FROM payments WHERE nid = '$nid' ORDER BY payment_date DESC LIMIT 5";
$history_result = mysqli_query($conn, $history_query);

$latest_payment = mysqli_fetch_assoc($result); // Fetch latest payment for QR code generation
if ($latest_payment) {
    $payment_info = "NID: " . htmlspecialchars($latest_payment['nid']) . 
                    ", Flyover: " . htmlspecialchars($latest_payment['flyover']) . 
                    ", Amount: " . htmlspecialchars($latest_payment['amount']);
    $qr_code_path = __DIR__ . '/qr_code.png';
    if (QRcode::png($payment_info, $qr_code_path, QR_ECLEVEL_L, 4)) {
        echo "QR Code generated successfully.";
    } else {
        echo "Failed to generate QR Code.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipts - E-Toll System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <img src="etoll_logo.png" alt="E-Toll System Logo" class="logo">
        <h1>Receipts</h1>
    </header>

    <section>
        <h2>Latest Payment</h2>
        <ul>
            <?php
            if ($latest_payment) {
                echo "<li>NID: " . htmlspecialchars($latest_payment['nid']) . "</li>";
                echo "<li>Flyover: " . htmlspecialchars($latest_payment['flyover']) . "</li>";
                echo "<li>Vehicle: " . htmlspecialchars($latest_payment['vehicle']) . "</li>";
                echo "<li>Amount Paid: ৳" . htmlspecialchars($latest_payment['amount']) . "</li>";
                echo "<li>Payment Date: " . htmlspecialchars($latest_payment['payment_date']) . "</li>";
            } else {
                echo "<li>No payment record found.</li>";
            }
            ?>
        </ul>

        <!-- Display QR Code -->
        <?php if ($latest_payment): ?>
            <h2>Your QR Code</h2>
            <img src="qr_code.png" alt="QR Code">
        <?php endif; ?>

        <!-- Last 5 Payments -->
        <h2>Last 5 Payments</h2>
        <ul>
            <?php
            if (mysqli_num_rows($history_result) > 0) {
                while ($row = mysqli_fetch_assoc($history_result)) {
                    echo "<li>Flyover: " . htmlspecialchars($row['flyover']) . " - " . 
                         "Amount: ৳" . htmlspecialchars($row['amount']) . " - " . 
                         "Vehicle: " . htmlspecialchars($row['vehicle']) . " - " . 
                         "Payment Date: " . htmlspecialchars($row['payment_date']) . "</li>";
                }
            } else {
                echo "<li>No payment history available.</li>";
            }
            ?>
        </ul>

        <!-- Dummy Print Receipt Button -->
        <button type="button" onclick="alert('This is a dummy print button.')">Print Receipt</button>
        <br><br>

        <!-- Go to Dashboard Button -->
        <button onclick="window.location.href='dashboard.php'">Go to Dashboard</button>
    </section>
</body>
</html>
