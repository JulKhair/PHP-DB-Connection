<?php
include "../db.php";
 
$id = $_GET['id'];
 
$get = mysqli_query($conn, "SELECT * FROM clients WHERE client_id = $id");
$client = mysqli_fetch_assoc($get);
 
$message = "";
 
if (isset($_POST['update'])) {
  $full_name = $_POST['full_name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $address = $_POST['address'];
 
  if ($full_name == "" || $email == "") {
    $message = "Name and Email are required!";
  } else {
    $sql = "UPDATE clients
            SET full_name='$full_name', email='$email', phone='$phone', address='$address'
            WHERE client_id=$id";
    mysqli_query($conn, $sql);
    header("Location: clients_list.php");
    exit;
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Client</title>

  <link rel="stylesheet" href="../custom/clients.css">
  <link rel="stylesheet" href="../custom/style.css">
</head>

<body>

<?php include "../nav.php"; ?>

<main class="main-content">
  <div class="form-containerC2">

    <div class="page-header">
      <h2 class="page-title">Edit Client</h2>
    </div>

    <p class="form-messageC2">
      <?php echo $message; ?>
    </p>

    <form class="client-formC2" method="post">

      <div class="form-groupC2">
        <label for="full_name">Full Name*</label>
        <input
          type="text"
          name="full_name"
          id="full_name"
          value="<?php echo $client['full_name']; ?>">
      </div>

      <div class="form-groupC2">
        <label for="email">Email*</label>
        <input
          type="text"
          name="email"
          id="email"
          value="<?php echo $client['email']; ?>">
      </div>

      <div class="form-groupC2">
        <label for="phone">Phone</label>
        <input
          type="text"
          name="phone"
          id="phone"
          value="<?php echo $client['phone']; ?>">
      </div>

      <div class="form-groupC2">
        <label for="address">Address</label>
        <input
          type="text"
          name="address"
          id="address"
          value="<?php echo $client['address']; ?>">
      </div>

      <button type="submit" name="update" class="btn btn-primary">
        Update
      </button>

    </form>

  </div>
</main>

</body>
</html>