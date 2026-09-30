<?php
include "../db.php";
$id = $_GET['id'];
 
$get = mysqli_query($conn, "SELECT * FROM services WHERE service_id = $id");
$service = mysqli_fetch_assoc($get);
 
if (isset($_POST['update'])) {
  $name = $_POST['service_name'];
  $desc = $_POST['description'];
  $rate = $_POST['hourly_rate'];
  $active = $_POST['is_active'];
 
  mysqli_query($conn, "UPDATE services
    SET service_name='$name', description='$desc', hourly_rate='$rate', is_active='$active'
    WHERE service_id=$id");
 
  header("Location: services_list.php");
  exit;
}
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Edit Service</title>

  <link rel="stylesheet" href="../custom/services.css">
  <link rel="stylesheet" href="../custom/style.css">
</head>

<body>

<?php include "../nav.php"; ?>

<main class="main-content">

  <div class="service-form-containerS2">

    <div class="page-header">
      <h2 class="page-title">Edit Service</h2>
    </div>

    <form class="service-formS2" method="post">

      <div class="form-group">

        <label for="service_name">Service Name</label>

        <input
          type="text"
          name="service_name"
          id="service_name"
          value="<?php echo $service['service_name']; ?>"
        >

      </div>


      <div class="form-group">

        <label for="description">Description</label>

        <textarea
          name="description"
          id="description"
          rows="4"
        ><?php echo $service['description']; ?></textarea>

      </div>


      <div class="form-group">

        <label for="hourly_rate">Hourly Rate</label>

        <input
          type="text"
          name="hourly_rate"
          id="hourly_rate"
          value="<?php echo $service['hourly_rate']; ?>"
        >

      </div>


      <div class="form-group">

        <label for="is_active">Active</label>

        <select name="is_active" id="is_active">

          <option value="1" <?php if($service['is_active']==1) echo "selected"; ?>>
            Yes
          </option>

          <option value="0" <?php if($service['is_active']==0) echo "selected"; ?>>
            No
          </option>

        </select>

      </div>


      <button type="submit" name="update" class="btn btn-primary">
        Update
      </button>

    </form>

  </div>

</main>

</body>
</html>