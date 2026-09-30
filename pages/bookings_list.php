<?php
include "../db.php";
 
$sql = "
SELECT b.*, c.full_name AS client_name, s.service_name
FROM bookings b
JOIN clients c ON b.client_id = c.client_id
JOIN services s ON b.service_id = s.service_id
ORDER BY b.booking_id DESC
";
$result = mysqli_query($conn, $sql);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Bookings</title>

  <link rel="stylesheet" href="../custom/bookings.css">
  <link rel="stylesheet" href="../custom/style.css">
</head>

<body>

<?php include "../nav.php"; ?>

<main class="main-content">

  <div class="bookings-containerB2">

    <div class="page-header">
      <h2 class="page-title">Bookings</h2>
    </div>

    <div class="bookings-actionsB2">
      <a href="bookings_create.php" class="btn btn-primary">
        + Create Booking
      </a>
    </div>

    <div class="table-container">

      <table class="booking-tableB2">

        <thead>
          <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Service</th>
            <th>Date</th>
            <th>Hours</th>
            <th>Total</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>

          <?php while($b = mysqli_fetch_assoc($result)) { ?>

            <tr>
              <td><?php echo $b['booking_id']; ?></td>
              <td><?php echo $b['client_name']; ?></td>
              <td><?php echo $b['service_name']; ?></td>
              <td><?php echo $b['booking_date']; ?></td>
              <td><?php echo $b['hours']; ?></td>
              <td>₱<?php echo number_format($b['total_cost'],2); ?></td>
              <td><?php echo $b['status']; ?></td>

              <td>
                <a
                  href="payment_process.php?booking_id=<?php echo $b['booking_id']; ?>"
                  class="table-action"
                >
                  Process Payment
                </a>
              </td>
            </tr>

          <?php } ?>

        </tbody>

      </table>

    </div>

  </div>

</main>

</body>
</html>