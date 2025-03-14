<?php
session_start();

if (isset($_SESSION['admin'])){
  header('location:index.php');
}

if (isset($_SESSION['user'])){
    header('location:home.php');
}

$conn = mysqli_connect('localhost', 'root', '', 'ojt');

if (isset($_POST['login'])){
    $username = $_POST['username'];
    $query = "SELECT * from user where username='$username'";
    $result = mysqli_query($conn, $query);

    if($result){
        if(mysqli_num_rows($result) == 1){
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $pass = $row['password'];

            if(password_verify($_POST['password'], $pass)){
                if($row['user_type'] == 'admin'){
                $_SESSION['admin'] = $username;
                $_SESSION['admin_id'] = $row['id'];
                header('location:index.php');
                }
                else{
                $_SESSION['user'] = $username;
                $_SESSION['user_id'] = $row['id'];
                header('location:home.php');
                }
            }
            else{
                $_SESSION['error'] = "Invalid Password";
            }
        }
        else{
            $_SESSION['error'] = "Invalid Credentials";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>RTWPB3</title>
    <link rel="icon" href="logo/logo.jpg" type="image/x-icon" />
</head>
<body style="background-color:#043047;">

<div class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="card p-3 w-100" style="background-color: #fff; max-width: 400px;">
      <div class="card-header text-center">
        <img src="logo/logo.png" class="card-img-top" style="max-width: 100px; margin: 0 auto; display: block;">
        <h1 class="text-center mt-2">Login</h1>
        <h6 style="color: gray;">A Purchase Request System for RTWPB3</h6>
      </div>

      <div class="card-body">
        <form action="login.php" method="POST">
          <div class="mb-3">
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" autocomplete="off" required>
          </div>

          <div class="mb-3">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
          </div>

          <div class="text-center"> 
            <button type="submit" class="btn" name="login" style="background-color: #043047; color: white;">Log In</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<!-- SCRIPT -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- MESSAGE -->
<?php
  if(isset($_SESSION['success'])) { ?>
  <script>
    Swal.fire({
    title: "<?php echo $_SESSION['success']; ?>",
    icon: "success",
    confirmButtonText: 'OK',
    confirmButtonColor: "#043047",
    });
  </script>
<?php unset($_SESSION['success']); } ?>

<?php
  if(isset($_SESSION['error'])) { ?>
  <script>
    Swal.fire({
    title: "<?php echo $_SESSION['error']; ?>",
    icon: "error",
    confirmButtonText: 'OK',
    confirmButtonColor: "#043047",
    });
  </script>
<?php unset($_SESSION['error']); } ?>

<!-- REFRESH -->
<script>
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }
</script>

</body>
</html>