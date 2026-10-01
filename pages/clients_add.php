<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

include "../db.php";
 
$message = "";
 
if (isset($_POST['save'])) {
  $full_name = $_POST['full_name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $address = $_POST['address'];
 
  if ($full_name == "" || $email == "") {
    $message = "Name and Email are required!";
  } else {
    $sql = "INSERT INTO clients (full_name, email, phone, address)
            VALUES ('$full_name', '$email', '$phone', '$address')";
    mysqli_query($conn, $sql);
    header("Location: clients_list.php");
    exit;
  }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Add Client</title></head>
<link rel="stylesheet" href="../custom/clients.css">
<link rel="stylesheet" href="../custom/style.css">
<body>
<?php include "../nav.php"; ?>
 
<main class="container py-4">

<main class="main-content">
  <div class="form-containerC1">

    <div class="page-headerC1">
      <h2 class="page-title">Add Client</h2>
    </div>

    <p class="form-messageC1">
      <?php echo $message; ?>
    </p>

    <form class="client-formC1" method="post">

      <div class="form-groupC1">
        <label for="full_name">Full Name*</label>
        <input type="text" name="full_name" id="full_name">
      </div>

      <div class="form-groupC1">
        <label for="email">Email*</label>
        <input type="text" name="email" id="email">
      </div>

      <div class="form-groupC1">
        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone">
      </div>

      <div class="form-groupC1">
        <label for="address">Address</label>
        <input type="text" name="address" id="address">
      </div>

      <button type="submit" name="save" class="btn btn-primaryC1">
        Save
      </button>

    </form>

  </div>
</main>

</body>
</html>