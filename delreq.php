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

if(isset($_POST['decline']))
  {
    $pr_no = $_POST['pr_no'];
    $last_ud = $_SESSION['admin'];

    $stmt = $pdo->prepare("UPDATE pur_req set status='Pending', last_ud='$last_ud' where pr_no='$pr_no'");
    if($stmt->execute()){
      $_SESSION['success'] = $pr_no. " Request Declined Successfully!";
    }
    else{
      $_SESSION['error'] = $pr_no. " Denying Request Failed!";
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

<!-- DECLINE MODAL -->
<div class="modal fade" id="declinemodal" tabindex="-1" aria-labelledby="declinemodalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="declinemodalLabel">Decline Deletion?</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="delreq.php" method="POST">
      <div class="modal-body">

        <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">PR No.</label>
          <input type="text" name="pr_no" id="pr_no" class="form-control" readonly>
        </div>

    </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="decline" class="btn btn-danger">Decline</button>
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

      <form action="delreq.php" method="POST">
      <div class="modal-body">

        <div class="input-group mb-3">
        <label class="col-sm-2 col-form-label">PR No.</label>
          <input type="text" name="pr_no2" id="pr_no2" class="form-control" readonly>
        </div>

        </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="delete" class="btn btn-success">Delete</button>
      </div>
      
      </form>

    </div>
  </div>
</div>

<!-- HEADER -->
<div class="container justify-content-center rounded">
<div class="row">
<div class="col">
<h3>DELETION REQUEST</h3>
</div>
<div class="col text-end">
</div>
</div>
<hr>

<div class="container text-center">
  <div class="row align-items-center">
    <div class="col">
    <div class="form-group mb-3">
      <label for="">From</label>
      <input type="text" name="min" id="min"  class="form-control">
    </div>
    </div>
    <div class="col">
    <div class="form-group mb-3">
      <label for="">To</label>
      <input type="text" name="max" id="max"  class="form-control">
    </div>
    </div>
  </div>
</div>
<hr>

<!-- TABLE -->
<table id="myTable" class="table table-responsive table-hover table-bordered" style="border-color:#043047;">

    <thead>
      <tr>
        <th scope="col" class="text-center">PR No.</th>
        <th scope="col" class="text-center">Province</th>
        <th scope="col" class="text-center">PR Date</th>
        <th scope="col" class="text-center">Purpose</th>
        <th scope="col" class="text-center">Remarks</th> 
        <th scope="col" class="text-center">Documents</th>
        <th scope="col" class="text-center">Request By</th> 
        <th scope="col" class="text-center">Action</th>
      </tr>
    </thead>

    <tbody>
        <?php
        $count = 0;
        $stmt = $pdo->prepare("SELECT * from pur_req WHERE status = 'RD'");
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $result = $stmt->fetchAll();
            foreach($result as $row){
            $count += 1;
        ?>
               <tr>
                    <td scope="col" class="text-center"><?= $row['pr_no']; ?></td>
                    <td scope="col" class="text-center"><?= $row['province']; ?></td>
                    <td scope="col" class="text-center"><?= $row['date']; ?></td>
                    <td scope="col" class="text-center"><?= $row['purpose']; ?></td>
                    <td scope="col" class="text-center"><?= $row['remarks']; ?></td>
                    <td scope="col" class="text-center"><a href="pur_req.php?file=<?php echo $row['documents']; ?>"><?php echo $row['documents']; ?></a></td>
                    <td scope="col" class="text-center"><?= $row['last_ud']; ?></td>
                    <td scope="col" class="text-center">
                        <div class='btn-group' role='group' aria-label='Basic mixed styles example'>
                            <button type='button' id='<?php echo $row['id']; ?>' class='btn btn-outline-danger declinebtn' value='<?php echo $row['id']; ?>'><i class="bi bi-x-circle"></i></button>
                            <button type='button' id='<?php echo $row['id']; ?>' class='btn btn-outline-success deletebtn' value='<?php echo $row['id']; ?>'><i class="bi bi-trash3"></i></button>
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
    <h6>Total: <?= $count ?></h6>
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
    columnDefs: [{ width: '15%', targets: [0, 2, 3, 4, 6] }],
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

<!-- DATE RANGE FILTER -->
<script>
let minDate, maxDate;
 
 DataTable.ext.search.push(function (settings, data, dataIndex) {
     let min = minDate.val();
     let max = maxDate.val();
     let date = new Date(data[2]);
  
     if (
         (min === null && max === null) ||
         (min === null && date <= max) ||
         (min <= date && max === null) ||
         (min <= date && date <= max)
     ) {
         return true;
     }
     return false;
 });
  
 minDate = new DateTime('#min', {
     format: 'MMMM Do YYYY'
 });
 maxDate = new DateTime('#max', {
     format: 'MMMM Do YYYY'
 });
  
 let table = new DataTable('#myTable');
  
 document.querySelectorAll('#min, #max').forEach((el) => {
     el.addEventListener('change', () => table.draw());
 });
</script>

<!-- MODAL -->

<script>
  $(document).ready(function () {
    $('#myTable').on('click', '.declinebtn', function() {
      $('#declinemodal').modal('show');

        $tr = $(this).closest('tr');

        var data = $tr.children("td").map(function(){
        return $(this).text();
        }).get();

        console.log(data);

        $('#pr_no').val(data[0]);
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

<!-- REFRESH -->
<script>
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }
</script>

</body>
</html>