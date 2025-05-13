<?php
session_start();

if (!isset($_SESSION['admin'])){
  header('location:login.php');
}

try{
    $pdo = new PDO('mysql:host=localhost;dbname=ojt', "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
    echo 'Connection Failed' .$e->getMessage();
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
              <a class="nav-link active" href="index.php" style="color: white;"><i class="bi bi-pie-chart me-2"></i>Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="pur_req.php" style="color: white;"><i class="bi bi-card-checklist"></i> Purchase Request</a>
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

<!-- HEADER -->
<div class="container justify-content-center rounded">
<h3>DASHBOARD</h3>
<hr>

<!-- FETCH DATA -->
<?php
$done = 0;
$ong = 0;
$pen = 0;
$total = 0;

$stmt = $pdo->prepare("SELECT * from pur_req");
  $stmt->execute();
  if($stmt->rowCount() > 0){
    $result = $stmt->fetchAll();
    foreach($result as $row){
      $total += 1;
      if( $row['status'] == 'Done'){
        $done++;
      }
      elseif( $row['status'] == 'On-going'){
        $ong++;
      }
      elseif( $row['status'] == 'Pending'){
        $pen++;
      }
      else{
        continue;
      }
    }
}
else{
  $done = 0;
  $ong = 0;
  $pen = 0;
  $total = 0;
}

$pr_total = array($done, $ong, $pen);
?>

<!-- FETCH DATA -->
<div class="container">

  <div class="row g-3 m-1">
    <div class="col-md-3">
      <div class="p-3 rounded" style="background-color:#043047; color:#f0f0f0;">
        <h3><i class="bi bi-card-checklist"></i> <?= $total ?></h3>
        <p class="fs-5">Total Requests</p>
      </div><br>
      <div class="p-3 rounded" style="background-color:#FFD041;">
        <h3><i class="bi bi-dash-circle"></i> <?= $ong ?></h3>
        <p class="fs-5">On-going</p>
      </div>
    </div>

    <div class="col-md-3">
      <div class="p-3 rounded" style="background-color:#FFD041;">
        <h3><i class="bi bi-check-circle"></i> <?= $done ?></h3>
        <p class="fs-5">Done</p>
      </div><br>
      <div class="p-3 rounded" style="background-color:#043047; color:#f0f0f0;">
        <h3><i class="bi bi-exclamation-circle"> </i><?= $pen ?></h3>
        <p class="fs-5">Pending</p>
      </div>
    </div>

    <div class="col">
    <canvas class="p-3 rounded bg-white" id="myChart"></canvas>
    </div>
  </div><hr>

<!-- TABLE -->
<h6>RECENT REQUESTS <a href="pur_req.php"><i class="bi bi-arrow-right-circle"></i></a></h6>
<table id="myTable" class="table table-responsive table-hover table-bordered" style="border-color:#043047;">

<thead>
  <tr>
    <th scope="col" class="text-center">PR No.</th>
    <th scope="col" class="text-center">Province</th>
    <th scope="col" class="text-center">PR Date</th>
    <th scope="col" class="text-center">Status</th> 
  </tr>
</thead>

<tbody>
    <?php
    $count = 0;
    $pend_count = 0;
    $done_count = 0;
    $ong_count = 0;
    $pend_count = 0;
    $stmt = $pdo->prepare("SELECT * from pur_req WHERE status != 'RD' ORDER BY pr_no DESC LIMIT 5");
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
                <?php echo $status ?>
           </tr> 
    <?php
              }
            }
    ?>
</tbody>
</table>

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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- TABLE -->
<script>
  new DataTable('#myTable', {
    order: [[0, 'desc']],
    scrollCollapse: true,
    scrollY: '50vh',
    paging: false,
    searching: false,
    info: false,
    "language": {
            "lengthMenu": "Show _MENU_ Entries",
        }
  });
</script>

<!-- CHART -->
<script>
  const ctx = document.getElementById('myChart');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Done', 'On-going', 'Pending'],
      datasets: [{
        label: 'Purchase Requests',
        data: <?php echo json_encode($pr_total) ?>,
        borderWidth: 1,
        barPercentage: 0.5,
        backgroundColor: '#043047'
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
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