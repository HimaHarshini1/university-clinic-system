<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports – Clinic System</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body{background:#f0f4f8}.sidebar{background:#1a3c5e;min-height:100vh;padding:20px 0}
    .sidebar .brand{color:#fff;font-size:1.2rem;font-weight:700;padding:10px 20px 20px;border-bottom:1px solid #2d5986}
    .sidebar .nav-link{color:#a8c4e0;padding:10px 20px;transition:all .2s}
    .sidebar .nav-link:hover,.sidebar .nav-link.active{background:#2d5986;color:#fff;border-left:3px solid #4fc3f7}
    .sidebar .nav-link i{margin-right:8px}.main-content{padding:30px}
    .page-title{color:#1a3c5e;font-weight:700;margin-bottom:25px}
  </style>
</head>
<body>
<div class="container-fluid"><div class="row">
  <div class="col-md-2 sidebar">
    <div class="brand"><i class="bi bi-hospital"></i> Clinic System</div>
    <nav class="nav flex-column mt-3">
      <a href="index.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a href="students.php" class="nav-link"><i class="bi bi-people"></i> Students</a>
      <a href="providers.php" class="nav-link"><i class="bi bi-person-badge"></i> Providers</a>
      <a href="appointments.php" class="nav-link"><i class="bi bi-calendar-check"></i> Appointments</a>
      <a href="inventory.php" class="nav-link"><i class="bi bi-box-seam"></i> Inventory</a>
      <a href="departments.php" class="nav-link"><i class="bi bi-building"></i> Departments</a>
      <a href="suppliers.php" class="nav-link"><i class="bi bi-truck"></i> Suppliers</a>
      <a href="search.php" class="nav-link"><i class="bi bi-search"></i> Search</a>
      <a href="reports.php" class="nav-link active"><i class="bi bi-bar-chart-line"></i> Reports</a>
    </nav>
  </div>
  <div class="col-md-10 main-content">
    <h2 class="page-title"><i class="bi bi-bar-chart-line"></i> Aggregate Reports</h2>
    <?php
    require_once 'db_config.php';
    $pdo = getDBConnection();

    // 1. Appointments per Department
    $deptAppts = $pdo->query("
      SELECT d.department_name, COUNT(a.appointment_id) AS total,
             SUM(a.status='Completed') AS completed,
             SUM(a.status='Cancelled') AS cancelled
      FROM Department d
      LEFT JOIN Healthcare_Provider hp ON d.department_id=hp.department_id
      LEFT JOIN Appointment a ON hp.provider_id=a.provider_id
      GROUP BY d.department_id, d.department_name
      ORDER BY total DESC")->fetchAll();

    // 2. Inventory by Supplier
    $invSupplier = $pdo->query("
      SELECT s.supplier_name, COUNT(i.item_id) AS items, SUM(i.quantity) AS total_qty
      FROM Supplier s
      LEFT JOIN Inventory i ON s.supplier_id=i.supplier_id
      GROUP BY s.supplier_id, s.supplier_name
      ORDER BY total_qty DESC")->fetchAll();

    // 3. Top Students by Appointment Count
    $topStudents = $pdo->query("
      SELECT s.name, COUNT(a.appointment_id) AS appts
      FROM Student s
      JOIN Appointment a ON s.student_id=a.student_id
      GROUP BY s.student_id, s.name
      ORDER BY appts DESC
      LIMIT 10")->fetchAll();

    // 4. Monthly Appointment Trend
    $monthly = $pdo->query("
      SELECT DATE_FORMAT(appointment_date,'%Y-%m') AS month, COUNT(*) AS cnt
      FROM Appointment
      GROUP BY month
      ORDER BY month")->fetchAll();
    ?>

    <div class="row g-4">
      <!-- Appointments per Department -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header fw-bold"><i class="bi bi-building"></i> Appointments per Department</div>
          <div class="card-body table-responsive">
            <table class="table table-sm table-striped">
              <thead class="table-dark"><tr><th>Department</th><th>Total</th><th>Completed</th><th>Cancelled</th></tr></thead>
              <tbody>
              <?php foreach ($deptAppts as $r): ?>
              <tr>
                <td><?= htmlspecialchars($r['department_name']) ?></td>
                <td><strong><?= $r['total'] ?></strong></td>
                <td><span class="badge bg-success"><?= $r['completed'] ?></span></td>
                <td><span class="badge bg-danger"><?= $r['cancelled'] ?></span></td>
              </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Inventory by Supplier -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header fw-bold"><i class="bi bi-truck"></i> Inventory by Supplier</div>
          <div class="card-body table-responsive">
            <table class="table table-sm table-striped">
              <thead class="table-dark"><tr><th>Supplier</th><th>Items</th><th>Total Qty</th></tr></thead>
              <tbody>
              <?php foreach ($invSupplier as $r): ?>
              <tr>
                <td><?= htmlspecialchars($r['supplier_name']) ?></td>
                <td><?= $r['items'] ?></td>
                <td><strong><?= number_format($r['total_qty']) ?></strong></td>
              </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Top Students -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header fw-bold"><i class="bi bi-trophy"></i> Top 10 Students by Appointments</div>
          <div class="card-body">
            <?php foreach ($topStudents as $i => $r): ?>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span><?= $i+1 ?>. <?= htmlspecialchars($r['name']) ?></span>
              <div class="progress flex-grow-1 mx-3" style="height:18px">
                <div class="progress-bar" style="width:<?= ($r['appts']/max(array_column($topStudents,'appts')))*100 ?>%"><?= $r['appts'] ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Monthly Trend -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header fw-bold"><i class="bi bi-graph-up"></i> Monthly Appointment Trend</div>
          <div class="card-body table-responsive">
            <table class="table table-sm table-striped">
              <thead class="table-dark"><tr><th>Month</th><th>Appointments</th><th>Bar</th></tr></thead>
              <tbody>
              <?php $maxM = max(array_column($monthly,'cnt')); foreach ($monthly as $r): ?>
              <tr>
                <td><?= $r['month'] ?></td>
                <td><?= $r['cnt'] ?></td>
                <td><div class="progress" style="height:15px;width:120px"><div class="progress-bar bg-info" style="width:<?= ($r['cnt']/$maxM)*100 ?>%"></div></div></td>
              </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div></div>
</body>
</html>
