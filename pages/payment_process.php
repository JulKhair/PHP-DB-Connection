<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

include "../db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $booking_id = (int) $_POST["booking_id"];
    $amount_paid = (float) $_POST["amount_paid"];
    $method = $_POST["method"];

    if ($booking_id <= 0) {
        die("Invalid booking.");
    }

    if ($amount_paid <= 0) {
        die("Payment amount must be greater than zero.");
    }

    if (empty($method)) {
        die("Please select a payment method.");
    }

    $stmt = mysqli_prepare($conn, "
        SELECT total_cost
        FROM bookings
        WHERE booking_id = ?
    ");

    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $bookingData = mysqli_fetch_assoc($result);

    if (!$bookingData) {
        die("Booking not found.");
    }

    $stmt = mysqli_prepare($conn, "
        SELECT COALESCE(SUM(amount_paid), 0) AS total_paid
        FROM payments
        WHERE booking_id = ?
    ");

    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $paymentData = mysqli_fetch_assoc($result);

    $totalPaid = (float) $paymentData["total_paid"];

    $remainingBalance =
        (float) $bookingData["total_cost"] - $totalPaid;

    if ($amount_paid > $remainingBalance) {
        die("Payment exceeds the remaining balance.");
    }

    $stmt = mysqli_prepare($conn, "
        INSERT INTO payments
            (booking_id, amount_paid, method)
        VALUES
            (?, ?, ?)
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "ids",
        $booking_id,
        $amount_paid,
        $method
    );

    if (!mysqli_stmt_execute($stmt)) {
        die("Failed to process payment.");
    }

    header(
        "Location: payment_process.php?booking_id="
        . $booking_id
        . "&success=1"
    );

    exit();
}

if (!isset($_GET["booking_id"])) {
    die("No booking selected.");
}

$booking_id = (int) $_GET["booking_id"];



$stmt = mysqli_prepare($conn, "
    SELECT
        bookings.booking_id,
        clients.full_name,
        services.service_name,
        bookings.booking_date,
        bookings.hours,
        bookings.hourly_rate_snapshot,
        bookings.total_cost,
        bookings.status
    FROM bookings
    INNER JOIN clients
        ON bookings.client_id = clients.client_id
    INNER JOIN services
        ON bookings.service_id = services.service_id
    WHERE bookings.booking_id = ?
");

mysqli_stmt_bind_param($stmt, "i", $booking_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    die("Booking not found.");
}

$paymentResult = mysqli_query($conn, "
    SELECT
        payment_id,
        amount_paid,
        method,
        payment_date
    FROM payments
    WHERE booking_id = $booking_id
    ORDER BY payment_date DESC
");

$totalPaid = 0;

while ($payment = mysqli_fetch_assoc($paymentResult)) {
    $totalPaid += $payment["amount_paid"];
}

$remainingBalance = $booking["total_cost"] - $totalPaid;
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Process Payment</title>
    <link rel="stylesheet" href="../custom/style.css">
</head>

<body>

<?php include "../nav.php"; ?>

<main class="main-content">

    <div class="dashboard-container">

        <div class="page-header">
            <h2 class="page-title">Process Payment</h2>
        </div>

        <h3>Booking Information</h3>

        <p>
            <strong>Booking:</strong>
            #<?php echo $booking["booking_id"]; ?>
        </p>

        <p>
            <strong>Client:</strong>
            <?php echo htmlspecialchars($booking["full_name"]); ?>
        </p>

        <p>
            <strong>Service:</strong>
            <?php echo htmlspecialchars($booking["service_name"]); ?>
        </p>

        <p>
            <strong>Booking Date:</strong>
            <?php echo $booking["booking_date"]; ?>
        </p>

        <p>
            <strong>Hours:</strong>
            <?php echo $booking["hours"]; ?>
        </p>

        <p>
            <strong>Total Cost:</strong>
            ₱<?php echo number_format($booking["total_cost"], 2); ?>
        </p>

        <p>
            <strong>Status:</strong>
            <?php echo htmlspecialchars($booking["status"]); ?>
        </p>

        <hr>

<h3>Payment Summary</h3>

<p>
    <strong>Total Cost:</strong>
    ₱<?php echo number_format($booking["total_cost"], 2); ?>
</p>

<p>
    <strong>Total Paid:</strong>
    ₱<?php echo number_format($totalPaid, 2); ?>
</p>

<p>
    <strong>Remaining Balance:</strong>
    ₱<?php echo number_format($remainingBalance, 2); ?>
</p>

<hr>

<h3>Make Payment</h3>

<form method="post">
    
    <input 
       type="hidden" 
       name="booking_id" 
       value="<?php echo $booking["booking_id"]; ?>"
    >

    <label for="amount_paid">Amount Paid</label><br>

    <input
        type="number"
        name="amount_paid"
        id="amount_paid"
        min="0.01"
        step="0.01"
        required
    >

    <br><br>

    <label for="method">Payment Method</label><br>

    <select name="method" id="method" required>

        <option value="">Select Payment Method</option>
        <option value="CASH">Cash</option>
        <option value="GCASH">GCash</option>
        <option value="BANK">Bank Transfer</option>

    </select>

    <br><br>

    <button type="submit">
        Save Payment
    </button>

</form>

    </div>

</main>

</body>
</html>