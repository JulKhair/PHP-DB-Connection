<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

include "../db.php";
$result = mysqli_query($conn, "SELECT * FROM services ORDER BY service_id DESC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Services</title>

  <link rel="stylesheet" href="../custom/services.css">
  <link rel="stylesheet" href="../custom/style.css">
</head>

<body>

<?php include "../nav.php"; ?>

<main class="main-content">

  <div class="services-containerS1">

    <div class="page-header">
      <h2 class="page-title">Services</h2>
    </div>

    <div class="table-container">

      <table class="service-tableS1">

        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Rate</th>
            <th>Active</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>

          <?php while($row = mysqli_fetch_assoc($result)) { ?>

            <tr>
              <td><?php echo $row['service_id']; ?></td>
              <td><?php echo $row['service_name']; ?></td>
              <td>₱<?php echo number_format($row['hourly_rate'],2); ?></td>
              <td><?php echo $row['is_active'] ? "Yes" : "No"; ?></td>

              <td>
                <a
                  href="services_edit.php?id=<?php echo $row['service_id']; ?>"
                  class="table-action"
                >
                  Edit
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