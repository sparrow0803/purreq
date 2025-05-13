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

if(isset($_GET['filter']))
  {
    $_SESSION['min'] = $_GET['min'];
    $_SESSION['max'] = $_GET['max'];
    $_SESSION['sta'] = $_GET['sta'];
  }

if (isset($_GET['reset'])) 
  {
    unset($_SESSION['min']);
    unset($_SESSION['max']);
    unset($_SESSION['sta']);
    header("Location: pur_req.php");
    exit;
  }

if(isset($_POST['create']))
  {
    $stmt = $pdo->prepare("SELECT * from pur_req");
    $stmt->execute();
    if($stmt->rowCount() == 0){
      $pr_id = '001';
      $pr_id = sprintf('%03d',$pr_id);
      $pr_id2 = sprintf('%03d',$pr_id);
      $pr_no = date('Y-m').'-'.$pr_id2;
    }
    else{
      $result = $stmt->fetchAll();
      foreach($result as $row){
        $pr_id = $row['pr_id'] + 001;
        $pr_id = sprintf('%03d',$pr_id);
        $pr_id2 = sprintf('%03d',$pr_id);
        $pr_no = date('Y-m').'-'.$pr_id2;
      }
    }

    if(isset($_FILES['choosefile']) && $_FILES['choosefile']['error'] == 0){
      $filename = $_FILES['choosefile']['name'];
      $tempfile = $_FILES['choosefile']['tmp_name'];
      $temp = explode(".", $_FILES["choosefile"]["name"]);
      $newfilename = $pr_no.'.'.end($temp);
      $folder = "documents/".$newfilename;
      move_uploaded_file($tempfile, $folder);
    }
    else{
      $newfilename = '';
    }

    $province = $_POST['province'];
    $remarks = $_POST['remarks'];
    $date = date('Y-m-d');
    $purpose = $_POST['purpose'];
    $last_ud = $_SESSION['admin'];
    $stmt = $pdo->prepare("INSERT into pur_req(pr_id, pr_no, province, date, purpose, status, remarks, documents, last_ud)
    values ('$pr_id', '$pr_no', '$province', '$date', '$purpose', 'Pending', '$remarks', :filename, '$last_ud')");
    $stmt->bindParam(':filename', $newfilename);
    if($stmt->execute()){
      $_SESSION['success'] = "Request Added Successfully!";
    }
    else{
      $_SESSION['error'] = "Failed to Add Request!";
    }
  }

if(isset($_POST['update']))
  {
    $pr_no = $_POST['pr_no'];
    $province = $_POST['province'];
    $date = $_POST['pr_date'];
    $purpose = $_POST['purpose'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];
    $last_ud = $_SESSION['admin'];

    $stmt = $pdo->prepare("UPDATE pur_req set status='$status', remarks='$remarks', date='$date', purpose='$purpose', province='$province', last_ud='$last_ud' where pr_no='$pr_no'");
    if($stmt->execute()){
      $_SESSION['success'] = $pr_no. " Updated Successfully!";
    }
    else{
      $_SESSION['error'] = $pr_no. " Update Failed!";
    }
  }

if(isset($_POST['upload']))
  {
    $pr_no = $_POST['pr_no3'];
    $filename = $_FILES['choosefile']['name'];
    $tempfile = $_FILES['choosefile']['tmp_name'];
    $temp = explode(".", $_FILES["choosefile"]["name"]);
    $newfilename = $pr_no.'.'.end($temp);
    $folder = "documents/".$newfilename;
    $last_ud = $_SESSION['admin'];

    $stmt = $pdo->prepare("UPDATE pur_req SET documents=:filename, last_ud='$last_ud' where pr_no=:pr_no");
    $stmt->bindParam(':pr_no', $pr_no);
    $stmt->bindParam(':filename', $newfilename);
    if($stmt->execute()){
      $_SESSION['success'] = "Uploaded Successfully!";
      move_uploaded_file($tempfile, $folder);
    }
    else{
      $_SESSION['error'] = "Upload Failed!";
    }
  }

if(!empty($_GET['file']))
  {
    $file_name = basename(($_GET['file']));
    $file_path = "documents/".$file_name;

    if(!empty($file_name) && file_exists($file_path)){

      header("Cache-Control: public");
      header("Content-Description: File Transfer");
      header("Content-Disposition: attachment; filename=$file_name");
      header("Content-Type: application/zip");
      header("Content-Transfer-Encoding: binary");

      readfile($file_path);
      exit;
    }

    else{
      $_SESSION['error'] = "File Does Not Exist!";
    }
  }

if(isset($_POST['delete']))
  {
    $pr_no = $_POST['pr_no2'];

    $stmt = $pdo->prepare("DELETE from pur_req where pr_no='$pr_no'");
    if($stmt->execute()){
      $_SESSION['success'] = $pr_no. " Deleted Successfully!";
    }
    else{
      $_SESSION['error'] = $pr_no. " Delete Failed!";
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
              <a class="nav-link active" href="pur_req.php" style="color: white;"><i class="bi bi-card-checklist"></i> Purchase Request</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: white;"><i class="bi bi-person-circle"></i> Profile</a>
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

<!-- CREATE MODAL -->
<div class="modal fade" id="createmodal" tabindex="-1" aria-labelledby="createmodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="createmodalLabel">Create New Request</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="pur_req.php" method="POST" enctype="multipart/form-data">
      <div class="modal-body">


        <div class="input-group mb-3">
          <label class="col-sm-2 col-form-label">Province</label>
          <select class='form-select' name='province' id='province' required>
          <option value='Aurora'>Aurora</option>
          <option value='Bataan'>Bataan</option>
          <option value='Bulacan'>Bulacan</option>
          <option value='Nueva Ecija'>Nueva Ecija</option>
          <option value='Pampanga'>Pampanga</option>
          <option value='Region-wide'>Region-wide</option>
          <option value='Tarlac'>Tarlac</option>
          <option value='Zambales'>Zambales</option>
          </select>
        </div>

        <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">Purpose</label>
          <textarea name="purpose" class="form-control" required></textarea>
        </div>

        <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">Remarks</label>
          <input type="text" name="remarks" class="form-control" required></input>
        </div>

        <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">Document</label>
          <input type="file" name="choosefile" class="form-control" accept="application/pdf">
        </div>

        </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="create" class="btn btn-success">Create</button>
      </div>
      
      </form>

    </div>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editmodal" tabindex="-1" aria-labelledby="editmodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editmodalLabel">Update Entry</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="pur_req.php" method="POST">
      <div class="modal-body">

        <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label" style="color: red;">PR No.</label>
          <input type="text" name="pr_no" id="pr_no" class="form-control" readonly>
        </div>

        <div class="input-group mb-3">
          <label class="col-sm-2 col-form-label">Province</label>
          <select class='form-select' name='province' id='province' required>
          <option value='Aurora'>Aurora</option>
          <option value='Bataan'>Bataan</option>
          <option value='Bulacan'>Bulacan</option>
          <option value='Nueva Ecija'>Nueva Ecija</option>
          <option value='Pampanga'>Pampanga</option>
          <option value='Region-wide'>Region-wide</option>
          <option value='Tarlac'>Tarlac</option>
          <option value='Zambales'>Zambales</option>
          </select>
        </div>

        <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">PR Date</label>
          <input type="date" name="pr_date" id="pr_date" class="form-control" required>
        </div>

        <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">Purpose</label>
          <textarea name="purpose" id="purpose" class="form-control" required></textarea>
        </div>

        <div class="input-group mb-3">
          <label class="col-sm-2 col-form-label">Status</label>
          <select class='form-select' name='status' id='status'>
          <option value='Done'>Done</option>
          <option value='On-going'>On-going</option>
          <option value='Pending'>Pending</option>
          </select>
        </div>

        <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">Remarks</label>
          <textarea name="remarks" id="remarks" class="form-control" required></textarea>
        </div>

        </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="update" class="btn btn-success">Update</button>
      </div>
      
      </form>

    </div>
  </div>
</div>

<!-- UPLOAD MODAL -->
<div class="modal fade" id="uploadmodal" tabindex="-1" aria-labelledby="uploadmodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="uploadmodalLabel">Upload Document</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="pur_req.php" method="POST" enctype="multipart/form-data">
      <div class="modal-body">

      <input type="hidden" name="pr_no3" id="pr_no3">

        <div class="input-group mb-3">
          <input type="file" name="choosefile" class="form-control" accept="application/pdf" required>
        </div>

        </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="upload" class="btn btn-warning">Upload</button>
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
        <h1 class="modal-title fs-5" id="deletemodalLabel">Confirm Delete</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="pur_req.php" method="POST">
      <div class="modal-body">

        <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">PR No.</label>
          <input type="text" name="pr_no2" id="pr_no2" class="form-control" readonly>
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
<h3>PURCHASE REQUEST</h3>
</div>
<div class="col text-end">
<button type='button' id='delreq' onclick="window.location.href='delreq.php';" class='btn btn-outline-danger'>Deletion Request</button>
<button type='button' id='exportBtn' class='btn btn-outline-warning'>Export</button>
<button type='button' id='create' class='btn btn-outline-success createbtn'>Create New +</button>
</div>
</div>
<hr>

<form action="pur_req.php" method="GET">
<div class="container text-center">
  <div class="row align-items-center">
    <div class="col">
    <div class="form-group mb-3">
      <label for="">From</label>
      <input type="date" name="min" id="min" class="form-control" value="<?= isset($_SESSION['min']) ? $_SESSION['min'] : '' ?>" required>
    </div>
    </div>
    <div class="col">
    <div class="form-group mb-3">
      <label for="">To</label>
      <input type="date" name="max" id="max" class="form-control" value="<?= isset($_SESSION['max']) ? $_SESSION['max'] : '' ?>" required>
    </div>
    </div>
    <div class="col">
    <div class="form-group mb-3">
      <label for="">Status</label>
      <select name="sta" id="sta" class="form-select" required>
        <option value=""></option>
        <option value="All" <?= (isset($_SESSION['sta']) && $_SESSION['sta'] == 'All') ? 'selected' : '' ?>>All</option>
        <option value="Done" <?= (isset($_SESSION['sta']) && $_SESSION['sta'] == 'Done') ? 'selected' : '' ?>>Done</option>
        <option value="On-going" <?= (isset($_SESSION['sta']) && $_SESSION['sta'] == 'On-going') ? 'selected' : '' ?>>On-going</option>
        <option value="Pending" <?= (isset($_SESSION['sta']) && $_SESSION['sta'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
      </select>
    </div>
    </div>
    <div class="col">
    <div class="form-group mb-3">
    <label>Filter</label>
    <div class="row g-2">
      <div class="col-6">
        <button type="submit" class="btn btn-outline-success w-100" name="filter">Apply</button>
      </div>
      <div class="col-6">
        <button type="submit" class="btn btn-outline-danger w-100" name="reset">Reset</button>
      </div>
    </div>
    </div>
    </div>
  </div>
</div>
</form>
<hr>

<!-- TABLE -->
<table id="myTable" class="table table-responsive table-hover table-bordered" style="border-color:#043047;">

    <thead>
      <tr>
        <th scope="col" class="text-center">PR No.</th>
        <th scope="col" class="text-center">Province</th>
        <th scope="col" class="text-center">PR Date</th>
        <th scope="col" class="text-center">Purpose</th>
        <th scope="col" class="text-center">Status</th> 
        <th scope="col" class="text-center">Remarks</th> 
        <th scope="col" class="text-center">Documents</th>
        <th scope="col" class="text-center">Last Updated</th> 
        <th scope="col" class="text-center">Action</th>
      </tr>
    </thead>

    <tbody>
        <?php
        $count = 0;
        $pend_count = 0;
        $done_count = 0;
        $ong_count = 0;
        $pend_count = 0;
        if(isset($_SESSION['min']) && isset($_SESSION['max']) && isset($_SESSION['sta']) && $_SESSION['sta'] != 'All'){
        $min = $_SESSION['min'];
        $max = $_SESSION['max'];
        $sta = $_SESSION['sta'];
        $stmt = $pdo->prepare("SELECT * from pur_req WHERE status != 'RD' and '$min' <= date and date <= '$max' and status = '$sta'");
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $result = $stmt->fetchAll();
            foreach($result as $row){
                $status = '';
                if ($row['status'] == 'Done'){
                    $status .= "<td scope='col' style='color: white;' class='text-center bg-success'>Done</td>";
                    $done_count += 1;
                }
                else if ($row['status'] == 'On-going'){
                    $status .= "<td scope='col' style='color: white;' class='text-center bg-warning'>On-going</td>";
                    $ong_count += 1;
                }
                else if ($row['status'] == 'Pending'){
                  $status .= "<td scope='col' style='color: white;' class='text-center bg-secondary'>Pending</td>";
                  $pend_count += 1;
                }
                else {
                  continue;
                }
            $count += 1;
        ?>
               <tr>
                    <td scope="col" class="text-center"><?= $row['pr_no']; ?></td>
                    <td scope="col" class="text-center"><?= $row['province']; ?></td>
                    <td scope="col" class="text-center"><?= $row['date']; ?></td>
                    <td scope="col" class="text-center"><?= $row['purpose']; ?></td>
                    <?php echo $status ?>
                    <td scope="col" class="text-center"><?= $row['remarks']; ?></td>
                    <td scope="col" class="text-center"><a href="pur_req.php?file=<?php echo $row['documents']; ?>"><?php echo $row['documents']; ?></a></td>
                    <td scope="col" class="text-center"><?= $row['last_ud']; ?></td>
                    <td scope="col" class="text-center">
                        <div class='btn-group' role='group' aria-label='Basic mixed styles example'>
                            <button type='button' id='<?php echo $row['id']; ?>' class='btn btn-outline-secondary editbtn' value='<?php echo $row['id']; ?>'><i class="bi bi-pencil-square"></i></button>
                            <button type='button' id='<?php echo $row['id']; ?>' class='btn btn-outline-warning uploadbtn' value='<?php echo $row['id']; ?>'><i class="bi bi-file-earmark-arrow-up"></i></button>
                            <button type='button' id='<?php echo $row['id']; ?>' class='btn btn-outline-danger deletebtn' value='<?php echo $row['id']; ?>'><i class="bi bi-trash3"></i></button>
                        </div>
                    </td>
               </tr> 
        <?php
                  }
                }
              }
        elseif(isset($_SESSION['min']) && isset($_SESSION['max']) && isset($_SESSION['sta']) && $_SESSION['sta'] == 'All'){
        $min = $_SESSION['min'];
        $max = $_SESSION['max'];
        $stmt = $pdo->prepare("SELECT * from pur_req WHERE status != 'RD' and '$min' <= date and date <= '$max'");
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $result = $stmt->fetchAll();
            foreach($result as $row){
                $status = '';
                if ($row['status'] == 'Done'){
                    $status .= "<td scope='col' style='color: white;' class='text-center bg-success'>Done</td>";
                    $done_count += 1;
                }
                else if ($row['status'] == 'On-going'){
                    $status .= "<td scope='col' style='color: white;' class='text-center bg-warning'>On-going</td>";
                    $ong_count += 1;
                }
                else if ($row['status'] == 'Pending'){
                  $status .= "<td scope='col' style='color: white;' class='text-center bg-secondary'>Pending</td>";
                  $pend_count += 1;
                }
                else {
                  continue;
                }
                $count += 1;
          ?>
                 <tr>
                      <td scope="col" class="text-center"><?= $row['pr_no']; ?></td>
                      <td scope="col" class="text-center"><?= $row['province']; ?></td>
                      <td scope="col" class="text-center"><?= $row['date']; ?></td>
                      <td scope="col" class="text-center"><?= $row['purpose']; ?></td>
                      <?php echo $status ?>
                      <td scope="col" class="text-center"><?= $row['remarks']; ?></td>
                      <td scope="col" class="text-center"><a href="pur_req.php?file=<?php echo $row['documents']; ?>"><?php echo $row['documents']; ?></a></td>
                      <td scope="col" class="text-center"><?= $row['last_ud']; ?></td>
                      <td scope="col" class="text-center">
                          <div class='btn-group' role='group' aria-label='Basic mixed styles example'>
                              <button type='button' id='<?php echo $row['id']; ?>' class='btn btn-outline-secondary editbtn' value='<?php echo $row['id']; ?>'><i class="bi bi-pencil-square"></i></button>
                              <button type='button' id='<?php echo $row['id']; ?>' class='btn btn-outline-warning uploadbtn' value='<?php echo $row['id']; ?>'><i class="bi bi-file-earmark-arrow-up"></i></button>
                              <button type='button' id='<?php echo $row['id']; ?>' class='btn btn-outline-danger deletebtn' value='<?php echo $row['id']; ?>'><i class="bi bi-trash3"></i></button>
                          </div>
                      </td>
                 </tr> 
          <?php
                    }
                  }
        }
        else{
        $stmt = $pdo->prepare("SELECT * from pur_req WHERE status != 'RD'");
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $result = $stmt->fetchAll();
            foreach($result as $row){
                $status = '';
                if ($row['status'] == 'Done'){
                    $status .= "<td scope='col' style='color: white;' class='text-center bg-success'>Done</td>";
                    $done_count += 1;
                }
                else if ($row['status'] == 'On-going'){
                    $status .= "<td scope='col' style='color: white;' class='text-center bg-warning'>On-going</td>";
                    $ong_count += 1;
                }
                else if ($row['status'] == 'Pending'){
                  $status .= "<td scope='col' style='color: white;' class='text-center bg-secondary'>Pending</td>";
                  $pend_count += 1;
                }
                else {
                  continue;
                }
            $count += 1;
        ?>
               <tr>
                    <td scope="col" class="text-center"><?= $row['pr_no']; ?></td>
                    <td scope="col" class="text-center"><?= $row['province']; ?></td>
                    <td scope="col" class="text-center"><?= $row['date']; ?></td>
                    <td scope="col" class="text-center"><?= $row['purpose']; ?></td>
                    <?php echo $status ?>
                    <td scope="col" class="text-center"><?= $row['remarks']; ?></td>
                    <td scope="col" class="text-center"><a href="pur_req.php?file=<?php echo $row['documents']; ?>"><?php echo $row['documents']; ?></a></td>
                    <td scope="col" class="text-center"><?= $row['last_ud']; ?></td>
                    <td scope="col" class="text-center">
                        <div class='btn-group' role='group' aria-label='Basic mixed styles example'>
                            <button type='button' id='<?php echo $row['id']; ?>' class='btn btn-outline-secondary editbtn' value='<?php echo $row['id']; ?>'><i class="bi bi-pencil-square"></i></button>
                            <button type='button' id='<?php echo $row['id']; ?>' class='btn btn-outline-warning uploadbtn' value='<?php echo $row['id']; ?>'><i class="bi bi-file-earmark-arrow-up"></i></button>
                            <button type='button' id='<?php echo $row['id']; ?>' class='btn btn-outline-danger deletebtn' value='<?php echo $row['id']; ?>'><i class="bi bi-trash3"></i></button>
                        </div>
                    </td>
               </tr> 
        <?php
                  }
                }
              }
        ?>
    </tbody>
</table>
<hr>
<div class="text-center">
    <h6>Total: <?= $count ?> | Pending: <?= $pend_count ?> | Done: <?= $done_count ?> | On-going: <?= $ong_count ?></h6>
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
    order: [[0, 'desc']],
    scrollCollapse: true,
    scrollY: '50vh',
    info: false,
    columnDefs: [{ width: '10%', targets: [0] }],
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
    "language": {
            "lengthMenu": "Show _MENU_ Entries",
        }
  });
</script>

<!-- EXCEL -->
<script>
document.getElementById("exportBtn").addEventListener("click", function() {
    var table = document.getElementById("myTable");

    // Get the table rows and delete the last column (the actions column)
    var rows = table.rows;

    // Remove the last column from the header
    var headerRow = rows[0];
    headerRow.deleteCell(headerRow.cells.length - 1);

    // Remove the last cell (actions) from each body row
    for (var i = 1; i < rows.length; i++) {
        rows[i].deleteCell(rows[i].cells.length - 1);
    }

    // Create a new array to store the data in the proper format
    var data = [];
    for (var i = 0; i < rows.length; i++) {
        var rowData = [];
        for (var j = 0; j < rows[i].cells.length; j++) {
            // Ensure PR No. is treated as a string
            var cellValue = rows[i].cells[j].innerText || rows[i].cells[j].textContent;
            // If it's the PR No. column, treat it as text
            if (j === 0) { // Assuming PR No. is in the first column (index 0)
                cellValue = cellValue; // Add a leading apostrophe to treat it as text in Excel
            }
            rowData.push(cellValue);
        }
        data.push(rowData);
    }

    // Create the Excel worksheet from the data array
    var ws = XLSX.utils.aoa_to_sheet(data);

    // Create a workbook and download the Excel file
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Sheet 1");
    XLSX.writeFile(wb, "pur_req_table.xlsx");
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
    $('.createbtn').on('click', function() {
      $('#createmodal').modal('show');
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('.pendingbtn').on('click', function() {
      $('#pendingmodal').modal('show');
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('#myTable').on('click', '.editbtn', function() {
      $('#editmodal').modal('show');

        $tr = $(this).closest('tr');

        var data = $tr.children("td").map(function(){
        return $(this).text();
        }).get();

        console.log(data);

        $('#pr_no').val(data[0]);
        $('#province').val(data[1]);
        $('#pr_date').val(data[2]);
        $('#purpose').val(data[3]);
        $('#status').val(data[4]);
        $('#remarks').val(data[5]);
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('#myTable').on('click', '.deletebtn', function() {
      $('#deletemodal').modal('show');

        $tr = $(this).closest('tr');

        var data = $tr.children("td").map(function(){
        return $(this).text();
        }).get();

        console.log(data);

        $('#pr_no2').val(data[0]);
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('#myTable').on('click', '.uploadbtn', function() {
      $('#uploadmodal').modal('show');

        $tr = $(this).closest('tr');

        var data = $tr.children("td").map(function(){
        return $(this).text();
        }).get();

        console.log(data);

        $('#pr_no3').val(data[0]);
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