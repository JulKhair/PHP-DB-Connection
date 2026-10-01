<?php
session_start();
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["username"] = $user["username"];

            header("Location: index.php");
            exit();

        } else {
            $error = "Invalid username or password.";
        }

    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>

    <link rel="stylesheet" href="custom/style.css">
</head>

<body>

<main class="main-content">

    <div class="dashboard-container">

        <div class="page-header">
            <h2 class="page-title">Login Form</h2>
        </div>

        <?php if ($error != ""): ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="post">

            <div>
                <label>Username</label><br>
                <input type="text" name="username" required>
            </div>

            <br>

            <div>
                <label>Password</label><br>
                <input type="password" name="password" required>
            </div>

            <br>

            <button type="submit" class="btn btn-primary">
                Login
            </button>

        </form>

    </div>

</main>

</body>
</html>