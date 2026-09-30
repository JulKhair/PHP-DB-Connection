<?php
include "../db.php";
 
$clients = mysqli_query($conn, "SELECT * FROM clients ORDER BY full_name ASC");
$services = mysqli_query($conn, "SELECT * FROM services WHERE is_active=1 ORDER BY service_name ASC");
 
if (isset($_POST['create'])) {
  $client_id = $_POST['client_id'];
  $service_id = $_POST['service_id'];
  $booking_date = $_POST['booking_date'];
  $hours = $_POST['hours'];
 
  // get service hourly rate
  $s = mysqli_fetch_assoc(mysqli_query($conn, "SELECT hourly_rate FROM services WHERE service_id=$service_id"));
  $rate = $s['hourly_rate'];
 
  $total = $rate * $hours;
 
  mysqli_query($conn, "INSERT INTO bookings (client_id, service_id, booking_date, hours, hourly_rate_snapshot, total_cost, status)
    VALUES ($client_id, $service_id, '$booking_date', $hours, $rate, $total, 'PENDING')");
 
  header("Location: bookings_list.php");
  exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Create Booking</title>

  <link rel="stylesheet" href="../custom/bookings.css">
  <link rel="stylesheet" href="../custom/style.css">
</head>

<body>

<?php include "../nav.php"; ?>

<main class="main-content">
  <div class="booking-form-containerB1">

    <div class="page-header">
      <h2 class="page-title">Create Booking</h2>
    </div>

    <form class="booking-formB1" method="post">

      <div class="form-group">
        <label for="client_id">Client</label>

        <select name="client_id" id="client_id">
          <?php while($c = mysqli_fetch_assoc($clients)) { ?>
            <option value="<?php echo $c['client_id']; ?>">
              <?php echo $c['full_name']; ?>
            </option>
          <?php } ?>
        </select>
      </div>

      <div class="form-group">
        <label for="service_id">Service</label>

        <select name="service_id" id="service_id">
          <?php while($s = mysqli_fetch_assoc($services)) { ?>
            <option value="<?php echo $s['service_id']; ?>">
              <?php echo $s['service_name']; ?>
              (₱<?php echo number_format($s['hourly_rate'],2); ?>/hr)
            </option>
          <?php } ?>
        </select>
      </div>

      <div class="form-group">
        <label for="booking_date">Date</label>

        <input
          type="date"
          name="booking_date"
          id="booking_date">
      </div>

      <div class="form-group">
        <label for="hours">Hours</label>

        <input
          type="number"
          name="hours"
          id="hours"
          min="1"
          value="1">
      </div>

      <button type="submit" name="create" class="btn btn-primary">
        Create Booking
      </button>

    </form>

  </div>
</main>

</body>
</html>