<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>University Clinic System</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { background: #f0f4f8; }
    .sidebar { background: #1a3c5e; min-height: 100vh; padding: 20px 0; }
    .sidebar .brand { color: #fff; font-size: 1.2rem; font-weight: 700; padding: 10px 20px 20px; border-bottom: 1px solid #2d5986; }
    .sidebar .nav-link { color: #a8c4e0; padding: 10px 20px; transition: all .2s; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #2d5986; color: #fff; border-left: 3px solid #4fc3f7; }
    .sidebar .nav-link i { margin-right: 8px; }
    .stat-card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
    .main-content { padding: 30px; }
    .page-title { color: #1a3c5e; font-weight: 700; margin-bottom: 25px; }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-2 sidebar">
      <div class="brand"><i class="bi bi-hospital"></i> Clinic System</div>
      <nav class="nav flex-column mt-3">
        <a href="index.php" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="students.php" class="nav-link"><i class="bi bi-people"></i> Students</a>
        <a href="providers.php" class="nav-link"><i class="bi bi-person-badge"></i> Providers</a>
        <a href="appointments.php" class="nav-link"><i class="bi bi-calendar-check"></i> Appointments</a>
        <a href="inventory.php" class="nav-link"><i class="bi bi-box-seam"></i> Inventory</a>
        <a href="departments.php" class="nav-link"><i class="bi bi-building"></i> Departments</a>
        <a href="suppliers.php" class="nav-link"><i class="bi bi-truck"></i> Suppliers</a>
        <a href="search.php" class="nav-link"><i class="bi bi-search"></i> Search</a>
        <a href="reports.php" class="nav-link"><i class="bi bi-bar-chart-line"></i> Reports</a>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="col-md-10 main-content">
      <h2 class="page-title"><i class="bi bi-speedometer2"></i> Dashboard</h2>

      <?php
      require_once 'db_config.php';
      $pdo = getDBConnection();

      $stats = [
        ['Students',     'bi-people-fill',       'primary',   'SELECT COUNT(*) FROM Student'],
        ['Appointments', 'bi-calendar-check-fill','success',   'SELECT COUNT(*) FROM Appointment'],
        ['Providers',    'bi-person-badge-fill',  'info',      'SELECT COUNT(*) FROM Healthcare_Provider'],
        ['Inventory',    'bi-box-seam-fill',      'warning',   'SELECT COUNT(*) FROM Inventory'],
      ];
      ?>

      <div class="row g-3 mb-4">
        <?php foreach ($stats as [$label, $icon, $color, $sql]): ?>
          <?php $count = $pdo->query($sql)->fetchColumn(); ?>
          <div class="col-md-3">
            <div class="card stat-card text-white bg-<?= $color ?>">
              <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                  <div class="fs-4 fw-bold"><?= $count ?></div>
                  <div><?= $label ?></div>
                </div>
                <i class="bi <?= $icon ?> fs-1 opacity-50"></i>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Appointment Status Breakdown -->
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card stat-card">
            <div class="card-header fw-bold"><i class="bi bi-pie-chart"></i> Appointment Status</div>
            <div class="card-body">
              <?php
              $rows = $pdo->query("SELECT status, COUNT(*) as cnt FROM Appointment GROUP BY status")->fetchAll();
              foreach ($rows as $r):
                $color = match($r['status']) { 'Completed'=>'success','Scheduled'=>'primary','Cancelled'=>'danger', default=>'secondary' };
              ?>
              <div class="d-flex justify-content-between mb-1">
                <span><span class="badge bg-<?= $color ?>"><?= $r['status'] ?></span></span>
                <strong><?= $r['cnt'] ?></strong>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card stat-card">
            <div class="card-header fw-bold"><i class="bi bi-clock-history"></i> Upcoming Appointments (Next 7 Days)</div>
            <div class="card-body">
              <?php
              $upcoming = $pdo->query("
                SELECT a.appointment_date, s.name AS student, hp.name AS provider
                FROM Appointment a
                JOIN Student s ON a.student_id = s.student_id
                JOIN Healthcare_Provider hp ON a.provider_id = hp.provider_id
                WHERE a.appointment_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                  AND a.status = 'Scheduled'
                ORDER BY a.appointment_date LIMIT 5")->fetchAll();
              if (empty($upcoming)): ?>
                <p class="text-muted">No upcoming appointments in the next 7 days.</p>
              <?php else: foreach ($upcoming as $u): ?>
                <div class="d-flex justify-content-between border-bottom py-1">
                  <span><?= htmlspecialchars($u['student']) ?></span>
                  <span class="text-muted small"><?= $u['appointment_date'] ?></span>
                </div>
              <?php endforeach; endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
