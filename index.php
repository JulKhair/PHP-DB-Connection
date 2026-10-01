<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "db.php";
 

$clients = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM clients"))['c'];
$services = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM services"))['c'];
$bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM bookings"))['c'];
 
$revRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(amount_paid),0) AS s FROM payments"));
$revenue = $revRow['s'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="custom/style.css">
</head>
<body>
<?php include "nav.php"; ?>

<main class="main-content">

    <div class="dashboard-container">

        <div class="page-header">
            <h2 class="page-title">Dashboard</h2>
        </div>

   
        <div class="dashboard-stats">

            <div class="stat-card">
                <h3 class="stat-label">Total Clients</h3>
                <p class="stat-value"><?php echo $clients; ?></p>
            </div>

            <div class="stat-card">
                <h3 class="stat-label">Total Services</h3>
                <p class="stat-value"><?php echo $services; ?></p>
            </div>

            <div class="stat-card">
                <h3 class="stat-label">Total Bookings</h3>
                <p class="stat-value"><?php echo $bookings; ?></p>
            </div>

            <div class="stat-card">
                <h3 class="stat-label">Total Revenue</h3>
                <p class="stat-value">₱<?php echo number_format($revenue,2); ?></p>
            </div>

        </div>


        <div class="quick-actions">

            <h3 class="section-title">Quick Actions</h3>

            <div class="action-buttons">

                <a class="btn btn-primary"
                   href="/PHP-DB-Connection/pages/clients_add.php">
                    Add Client
                </a>

                <a class="btn btn-secondary"
                   href="/PHP-DB-Connection/pages/bookings_create.php">
                    Create Booking
                </a>

            </div>

        </div>

    </div>

</main>
 
</body>
</html>