<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search – Clinic System</title>
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
      <a href="search.php" class="nav-link active"><i class="bi bi-search"></i> Search</a>
      <a href="reports.php" class="nav-link"><i class="bi bi-bar-chart-line"></i> Reports</a>
    </nav>
  </div>
  <div class="col-md-10 main-content">
    <h2 class="page-title"><i class="bi bi-search"></i> Search &amp; Join Queries</h2>

    <?php
    require_once 'db_config.php';
    $pdo = getDBConnection();
    $results = [];
    $query_type = $_GET['qtype'] ?? '';
    $keyword = trim($_GET['keyword'] ?? '');
    ?>

    <!-- Search Form -->
    <div class="card mb-4">
      <div class="card-header fw-bold">Search Appointments (Student + Provider JOIN)</div>
      <div class="card-body">
        <form method="get" class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Search Type</label>
            <select name="qtype" class="form-select">
              <option value="student"   <?= $query_type==='student'   ? 'selected':'' ?>>By Student Name</option>
              <option value="provider"  <?= $query_type==='provider'  ? 'selected':'' ?>>By Provider Name</option>
              <option value="status"    <?= $query_type==='status'    ? 'selected':'' ?>>By Status</option>
              <option value="date"      <?= $query_type==='date'      ? 'selected':'' ?>>By Date (YYYY-MM-DD)</option>
            </select>
          </div>
          <div class="col-md-5">
            <label class="form-label">Keyword / Value</label>
            <input type="text" class="form-control" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Enter search term...">
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary w-100"><i class="bi bi-search"></i> Search</button>
          </div>
        </form>
      </div>
    </div>

    <?php if ($keyword !== ''): ?>
    <?php
    // JOIN query: Student + Appointment + Healthcare_Provider
    $col = match($query_type) {
      'provider' => 'hp.name',
      'status'   => 'a.status',
      'date'     => 'a.appointment_date',
      default    => 's.name',
    };
    $stmt = $pdo->prepare("
      SELECT a.appointment_id, a.appointment_date, a.status,
             s.student_id, s.name AS student_name, s.email,
             hp.provider_id, hp.name AS provider_name, hp.specialization,
             d.department_name
      FROM Appointment a
      JOIN Student s            ON a.student_id  = s.student_id
      JOIN Healthcare_Provider hp ON a.provider_id = hp.provider_id
      JOIN Department d         ON hp.department_id = d.department_id
      WHERE $col LIKE ?
      ORDER BY a.appointment_date DESC");
    $stmt->execute(["%$keyword%"]);
    $results = $stmt->fetchAll();
    ?>
    <div class="card">
      <div class="card-header fw-bold">Results (<?= count($results) ?> records)</div>
      <div class="card-body table-responsive">
        <?php if (empty($results)): ?>
          <p class="text-muted">No results found.</p>
        <?php else: ?>
        <table class="table table-striped align-middle">
          <thead class="table-dark">
            <tr><th>Appt ID</th><th>Student</th><th>Email</th><th>Provider</th><th>Specialization</th><th>Department</th><th>Date</th><th>Status</th></tr>
          </thead>
          <tbody>
          <?php foreach ($results as $r):
            $badge = match($r['status']){ 'Completed'=>'success','Scheduled'=>'primary','Cancelled'=>'danger', default=>'secondary' };
          ?>
          <tr>
            <td><?= $r['appointment_id'] ?></td>
            <td><?= htmlspecialchars($r['student_name']) ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td><?= htmlspecialchars($r['provider_name']) ?></td>
            <td><?= htmlspecialchars($r['specialization']) ?></td>
            <td><?= htmlspecialchars($r['department_name']) ?></td>
            <td><?= $r['appointment_date'] ?></td>
            <td><span class="badge bg-<?= $badge ?>"><?= $r['status'] ?></span></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div></div>
</body>
</html>
