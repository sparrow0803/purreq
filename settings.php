<?php
session_start();

if (!isset($_SESSION['admin'])){
  header('location:login.php');
}

date_default_timezone_set("Asia/Manila");

try{
    $pdo = new PDO('mysql:host=localhost;dbname=ojt', "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
    echo 'Connection Failed' .$e->getMessage();
    }

if (isset($_POST['add'])){
  $username = $_POST["username"];
  $password = $_POST["password"];
  $cpassword = $_POST["cpassword"];
  $role = $_POST["role"];
      
  $stmt = $pdo->prepare("SELECT * from user where username='$username'");
  $stmt->execute();
    if($stmt->rowCount() > 0){
      $_SESSION['error'] = "Username Already Exists!";
    }
    else{
      if ($password === $cpassword){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("insert into user set username=?, password=?, user_type=?");
        $stmt->execute([$username, $hash, $role]);
        $_SESSION['success'] = "User Added Successfully!";
      }
      else{
        $_SESSION['error'] = "Password Does Not Match!";
      }
    }
}

if (isset($_POST['pass'])){
  $id = $_POST['id'];
  $password = $_POST["password"];
  $cpassword = $_POST["cpassword"];

  if ($password === $cpassword){
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE user set password='$hash' where id='$id'");
    $stmt->execute();
    $_SESSION['success'] = "Password Updated Successfully!";
  }
  else{
    $_SESSION['error'] = "Password Does Not Match!";
  }
}

if (isset($_POST['crole'])){
  $id = $_POST['id3'];

  $stmt = $pdo->prepare("SELECT * from user where id='$id'");
  if($stmt->execute()){
  $result = $stmt->fetchAll();
  foreach($result as $row){
    $role = $row['user_type'];
  }
    if($role == 'admin'){
    $stmt = $pdo->prepare("UPDATE user set user_type='user' where id='$id'");
    $stmt->execute();
    $_SESSION['success'] = "Role Updated Successfully!";
    }
    elseif($role == 'user') {
    $stmt = $pdo->prepare("UPDATE user set user_type='admin' where id='$id'");
    $stmt->execute();
    $_SESSION['success'] = "Role Updated Successfully!";
    }
    else{
    $_SESSION['error'] = "Update Error!";
    }
  }
  else{
    $_SESSION['error'] = "User Not Found!";
  }
}

if(isset($_POST['delete']))
  {
    $id = $_POST['id2'];

    $stmt = $pdo->prepare("DELETE from user where id='$id'");
    if($stmt->execute()){
      $_SESSION['success'] = " User Deleted Successfully!";
    }
    else{
      $_SESSION['error'] = "Delete Failed!";
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="refresh" content="900; url=logout.php">
  <title>RTWPB3</title>
  <link rel="icon" href="logo/logo.jpg" type="image/x-icon" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.5.4/css/dataTables.dateTime.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
</head>
<body style="background-color:#f0f0f0;">

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap');

* {
  font-family: 'Outfit', sans-serif;
}
</style>

<!-- NAVBAR -->
<nav class="navbar navbar-dark navbar-expand-lg" style="background-color: #043047;">
  <div class="container">
    <h2 style="color: white;">DOLE RTWPB3</h2>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
        <div class="collapse navbar-collapse flex-grow-0" id="navbarSupportedContent">
          <ul class="navbar-nav nav-fill nav-pills nav-tabs">
          <li class="nav-item">
              <a class="nav-link" href="index.php" style="color: white;"><i class="bi bi-pie-chart me-2"></i>Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="pur_req.php" style="color: white;"><i class="bi bi-card-checklist"></i> Purchase Request</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link active dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: white;"><i class="bi bi-person-circle"></i> Profile</a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item disabled" href="#"><?php echo $_SESSION['admin']; ?> (admin)</a></li>
                  <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                  <li><a class="dropdown-item" href="logout.php" style="color: red">Logout</a></li>
                </ul>
            </li>
          </ul>
        </div>
  </div>
</nav>
<br>

<!-- ADD MODAL -->
<div class="modal fade" id="addmodal" tabindex="-1" aria-labelledby="addmodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addmodalLabel">Add User</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="settings.php" method="POST">
      <div class="modal-body">

      <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">Username</label>
        <input type="text" name="username" class="form-control" required></input>
      </div>

      <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">Password</label>
        <input type="password" name="password" class="form-control" required></input>
      </div>

      <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">Confirm Password</label>
        <input type="password" name="cpassword" class="form-control" required></input>
      </div>

        <div class="input-group mb-3">
          <label class="col-sm-2 col-form-label">Role</label>
          <select class='form-select' name='role' required>
          <option value='user'>User</option>
          <option value='admin'>Admin</option>
          </select>
        </div>

        </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="add" class="btn btn-success">Add</button>
      </div>
      
      </form>

    </div>
  </div>
</div>

<!-- PASS MODAL -->
<div class="modal fade" id="passmodal" tabindex="-1" aria-labelledby="passmodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="passmodalLabel">Change Password</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="settings.php" method="POST">
      <div class="modal-body">

      <input type="hidden" id="id" name="id" class="form-control" required>

      <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">Username</label>
        <input type="text" id="username" name="username" class="form-control" readonly></input>
      </div>

      <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">Role</label>
        <input type="text" id="role" name="role" class="form-control" readonly></input>
      </div>

      <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label" style="color: red">Password</label>
        <input type="password" name="password" class="form-control" required></input>
      </div>

      <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label" style="color: red">Confirm Password</label>
        <input type="password" name="cpassword" class="form-control" required></input>
      </div>

        </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="pass" class="btn btn-success">Change Password</button>
      </div>
      
      </form>

    </div>
  </div>
</div>

<!-- ROLE MODAL -->
<div class="modal fade" id="rolemodal" tabindex="-1" aria-labelledby="rolemodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="rolemodalLabel">Change Role</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="settings.php" method="POST">
      <div class="modal-body">

      <input type="hidden" id="id3" name="id3" class="form-control" required>

      <div class="input-group mb-3">
        <h5 style="color: red" id="role2"></h5>
      </div>

        </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="crole" class="btn btn-success">Change Role</button>
      </div>
      
      </form>

    </div>
  </div>
</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="deletemodal" tabindex="-1" aria-labelledby="deletemodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="deletemodalLabel">Delete User?</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="settings.php" method="POST">
      <div class="modal-body">

      <input type="hidden" id="id2" name="id2" class="form-control" required>

        <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">Username</label>
          <input type="text" id="username3" class="form-control" readonly>
        </div>

        <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">Role</label>
          <input type="text" id="role3" class="form-control" readonly>
        </div>

        </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="delete" class="btn btn-danger">Delete</button>
      </div>
      
      </form>

    </div>
  </div>
</div>

<!-- HEADER -->
<div class="container justify-content-center rounded">
<div class="row">
<div class="col">
<h3>MANAGE USERS</h3>
</div>
<div class="col text-end">
<button type='button' id='add' class='btn btn-outline-success addbtn'>Add User</button>
</div>
</div>
<hr>

<!-- TABLE -->
<table id="myTable" class="table table-responsive table-hover table-bordered" style="border-color:#043047;">

    <thead>
      <tr>
        <th scope="col" class="text-center">User</th>
        <th scope="col" class="text-center">Role</th>
        <th scope="col" class="text-center">Action</th>
      </tr>
    </thead>

    <tbody>
        <?php
        $count = 0;
        $us_count = 0;
        $ad_count = 0;
        $stmt = $pdo->prepare("SELECT * from user");
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $result = $stmt->fetchAll();
            foreach($result as $row){
              $status = '';
                if ($row['user_type'] == 'user'){
                    $status .= "<td scope='col' style='color: white;' class='text-center bg-success'>user</td>";
                    $us_count += 1;
                }
                else if ($row['user_type'] == 'admin'){
                    $status .= "<td scope='col' style='color: white;' class='text-center bg-warning'>admin</td>";
                    $ad_count += 1;
                }
                else {
                  continue;
                }
            $count += 1;
        ?>
               <tr>
                    <td scope="col" class="text-center"><?= $row['username']; ?></td>
                    <?php echo $status ?>
                    <td scope="col" class="text-center">
                        <div class='btn-group' role='group' aria-label='Basic mixed styles example'>
                            <button type='button' id='<?php echo $row['id']; ?>' class='btn btn-outline-secondary passbtn' value='<?php echo $row['id']; ?>'><i class="bi bi-person-fill-lock"></i></button>
                            <button type='button' id='<?php echo $row['id']; ?>' class='btn btn-outline-warning rolebtn' value='<?php echo $row['id']; ?>'><i class="bi bi-person-gear"></i></button>
                            <button type='button' id='<?php echo $row['id']; ?>' class='btn btn-outline-danger deletebtn' value='<?php echo $row['id']; ?>'><i class="bi bi-trash3"></i></button>
                        </div>
                    </td>
               </tr> 
        <?php
                  }
                }
        ?>
    </tbody>
</table>
<hr>
<div class="text-center">
    <h6>Total: <?= $count ?> | User: <?= $us_count ?> | Admin: <?= $ad_count ?> </h6>
</div>
</div>

<!-- SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
<script src="https://cdn.datatables.net/datetime/1.5.4/js/dataTables.dateTime.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>


<!-- TABLE -->
<script>
  new DataTable('#myTable', {
    scrollCollapse: true,
    scrollY: '50vh',
    info: false,
    "language": {
            "lengthMenu": "Show _MENU_ Entries",
        }
  });
</script>


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

<!-- MODAL -->
<script>
  $(document).ready(function () {
    $('.addbtn').on('click', function() {
      $('#addmodal').modal('show');
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('#myTable').on('click', '.passbtn', function() {
      $('#passmodal').modal('show');
      var id = $(this).val();

        $tr = $(this).closest('tr');

        var data = $tr.children("td").map(function(){
        return $(this).text();
        }).get();

        console.log(data);

        $('#id').val(id);
        $('#username').val(data[0]);
        $('#role').val(data[1]);
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('#myTable').on('click', '.deletebtn', function() {
      $('#deletemodal').modal('show');
      var id = $(this).val();

        $tr = $(this).closest('tr');

        var data = $tr.children("td").map(function(){
        return $(this).text();
        }).get();

        console.log(data);

        $('#id2').val(id);
        $('#username3').val(data[0]);
        $('#role3').val(data[1]);
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('#myTable').on('click', '.rolebtn', function() {
      $('#rolemodal').modal('show');
      var id = $(this).val();

        $tr = $(this).closest('tr');

        var data = $tr.children("td").map(function(){
        return $(this).text();
        }).get();

        console.log(data);

        var userrole = data[1];
        $('#id3').val(id);

        if(userrole === 'admin'){
          $('#role2').val('Change the role of this user to "user"?');
        }
        else{
          $('#role2').val('Change the role of this user to "admin"?');
        }

        var role2Value = $('#role2').val();
        $('#role2').text(role2Value);

    });
  });
</script>

<!-- REFRESH -->
<script>
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }
</script>

</body>
</html>