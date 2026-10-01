<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

include "../db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $booking_id = (int) $_POST["booking_id"];
    $tool_id = (int) $_POST["tool_id"];
    $qty_used = (int) $_POST["qty_used"];

    if ($booking_id <= 0 || $tool_id <= 0 || $qty_used <= 0) {
        die("Invalid assignment information.");
    }

    mysqli_begin_transaction($conn);

    try {

        // Check the selected tool's available quantity
        $stmt = mysqli_prepare($conn, "
            SELECT quantity_available
            FROM tools
            WHERE tool_id = ?
            FOR UPDATE
        ");

        mysqli_stmt_bind_param($stmt, "i", $tool_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $tool = mysqli_fetch_assoc($result);

        if (!$tool) {
            throw new Exception("Tool not found.");
        }

        if ($qty_used > $tool["quantity_available"]) {
            throw new Exception("Not enough tools available.");
        }

        // Record the assignment
        $stmt = mysqli_prepare($conn, "
            INSERT INTO booking_tools
                (booking_id, tool_id, qty_used)
            VALUES
                (?, ?, ?)
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "iii",
            $booking_id,
            $tool_id,
            $qty_used
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to assign tool.");
        }

        // Reduce available quantity
        $stmt = mysqli_prepare($conn, "
            UPDATE tools
            SET quantity_available = quantity_available - ?
            WHERE tool_id = ?
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "ii",
            $qty_used,
            $tool_id
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to update inventory.");
        }

        mysqli_commit($conn);

        header("Location: tools_inventory.php?success=1");
        exit();

    } catch (Exception $e) {

        mysqli_rollback($conn);

        die("Assignment failed: " . htmlspecialchars($e->getMessage()));
    }
}

$result = mysqli_query($conn, "SELECT * FROM tools ORDER BY tool_id ASC");
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tools Inventory</title>
    <link rel="stylesheet" href="../custom/style.css">
</head>

<body>

<?php include "../nav.php"; ?>

<main class="main-content">

    <div class="dashboard-container">

        <div class="page-header">
            <h2 class="page-title">Tools Inventory</h2>
        </div>

        <?php if (isset($_GET["success"])): ?>

    <p>
        Tool assigned successfully.
    </p>

<?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Tool ID</th>
                    <th>Tool Name</th>
                    <th>Total Quantity</th>
                    <th>Available Quantity</th>
                </tr>
            </thead>

            <tbody>

                <?php while ($tool = mysqli_fetch_assoc($result)): ?>

                    <tr>
                        <td><?php echo $tool["tool_id"]; ?></td>

                        <td>
                            <?php echo htmlspecialchars($tool["tool_name"]); ?>
                        </td>

                        <td>
                            <?php echo $tool["quantity_total"]; ?>
                        </td>

                        <td>
                            <?php echo $tool["quantity_available"]; ?>
                        </td>
                    </tr>

                <?php endwhile; ?>

            </tbody>
        </table>

        <h3 class="section-title">Assigned Tools</h3>

        <?php
$assignedResult = mysqli_query($conn, "
    SELECT
        booking_tools.booking_tool_id,
        booking_tools.booking_id,
        clients.full_name,
        tools.tool_name,
        booking_tools.qty_used,
        booking_tools.created_at
    FROM booking_tools
    INNER JOIN bookings
        ON booking_tools.booking_id = bookings.booking_id
    INNER JOIN clients
        ON bookings.client_id = clients.client_id
    INNER JOIN tools
        ON booking_tools.tool_id = tools.tool_id
    ORDER BY booking_tools.booking_tool_id DESC
");
?>

<table>
    <thead>
        <tr>
            <th>Booking</th>
            <th>Client</th>
            <th>Tool</th>
            <th>Quantity</th>
            <th>Assigned At</th>
        </tr>
    </thead>

    <tbody>

        <?php while ($assignment = mysqli_fetch_assoc($assignedResult)): ?>

            <tr>

                <td>
                    Booking #<?php echo $assignment["booking_id"]; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($assignment["full_name"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($assignment["tool_name"]); ?>
                </td>

                <td>
                    <?php echo $assignment["qty_used"]; ?>
                </td>

                <td>
                    <?php echo $assignment["created_at"]; ?>
                </td>

            </tr>

        <?php endwhile; ?>

    </tbody>
</table>

        <h3 class="section-title">Assign Tool to Booking</h3>

<form method="post">

    <label for="booking_id">Booking</label><br>

    <select name="booking_id" id="booking_id" required>

        <option value="">Select Booking</option>

        <?php
        $bookingResult = mysqli_query($conn, "
            SELECT 
                bookings.booking_id,
                clients.full_name
            FROM bookings
            INNER JOIN clients
                ON bookings.client_id = clients.client_id
            ORDER BY bookings.booking_id ASC
        ");

        while ($booking = mysqli_fetch_assoc($bookingResult)):
        ?>

            <option value="<?php echo $booking["booking_id"]; ?>">
                Booking #<?php echo $booking["booking_id"]; ?>
                - <?php echo htmlspecialchars($booking["full_name"]); ?>
            </option>

        <?php endwhile; ?>

    </select>

    <br><br>

    <label for="tool_id">Tool</label><br>

<select name="tool_id" id="tool_id" required>

    <option value="">Select Tool</option>

    <?php
    $toolResult = mysqli_query($conn, "
        SELECT tool_id, tool_name, quantity_available
        FROM tools
        ORDER BY tool_name ASC
    ");

    while ($tool = mysqli_fetch_assoc($toolResult)):
    ?>

        <option value="<?php echo $tool["tool_id"]; ?>">
            <?php echo htmlspecialchars($tool["tool_name"]); ?>
            - <?php echo $tool["quantity_available"]; ?> available
        </option>

    <?php endwhile; ?>

</select>

<br><br>


<label for="qty_used">Quantity</label><br>

<input 
    type="number" 
    name="qty_used" 
    id="qty_used"
    min="1"
    value="1"
    required
>

<br><br>

<button type="submit">Assign Tool</button>

</form>

</div>

</main>

</body>
</html>