<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="main-nav">
  <div class="nav-container">

    <a class="nav-link" href="/PHP-DB-Connection/index.php">
        Dashboard
    </a>

    <a class="nav-link" href="/PHP-DB-Connection/pages/clients_list.php">
        Clients
    </a>

    <a class="nav-link" href="/PHP-DB-Connection/pages/services_list.php">
        Services
    </a>

    <a class="nav-link" href="/PHP-DB-Connection/pages/bookings_list.php">
        Bookings
    </a>

    <a class="nav-link" href="/PHP-DB-Connection/pages/tools_inventory.php">
        Tools
    </a>

    <a class="nav-link" href="/PHP-DB-Connection/pages/payments_list.php">
        Payments
    </a>

    <span class="nav-link">
        User: <?php echo htmlspecialchars($_SESSION["username"]); ?>
    </span>

    <a class="nav-link" href="/PHP-DB-Connection/logout.php">
        Logout
    </a>

  </div>
</nav>