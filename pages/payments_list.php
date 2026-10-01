<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

include "../db.php";

$result = mysqli_query($conn, "
    SELECT
        payments.payment_id,
        payments.booking_id,
        clients.full_name,
        services.service_name,
        payments.amount_paid,
        payments.method,
        payments.payment_date
    FROM payments
    INNER JOIN bookings
        ON payments.booking_id = bookings.booking_id
    INNER JOIN clients
        ON bookings.client_id = clients.client_id
    INNER JOIN services
        ON bookings.service_id = services.service_id
    ORDER BY payments.payment_date DESC
");
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment History</title>
</head>

<body>

<?php include "../nav.php"; ?>

<h2>Payment History</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Payment ID</th>
        <th>Booking</th>
        <th>Client</th>
        <th>Service</th>
        <th>Amount Paid</th>
        <th>Method</th>
        <th>Payment Date</th>
    </tr>

    <?php while ($payment = mysqli_fetch_assoc($result)): ?>

        <tr>
            <td>
                <?php echo $payment["payment_id"]; ?>
            </td>

            <td>
                Booking #<?php echo $payment["booking_id"]; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($payment["full_name"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($payment["service_name"]); ?>
            </td>

            <td>
                ₱<?php echo number_format($payment["amount_paid"], 2); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($payment["method"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($payment["payment_date"]); ?>
            </td>
        </tr>

    <?php endwhile; ?>

</table>

</body>
</html>