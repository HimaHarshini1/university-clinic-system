<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Appointments – Clinic System</title>
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
      <a href="appointments.php" class="nav-link active"><i class="bi bi-calendar-check"></i> Appointments</a>
      <a href="inventory.php" class="nav-link"><i class="bi bi-box-seam"></i> Inventory</a>
      <a href="departments.php" class="nav-link"><i class="bi bi-building"></i> Departments</a>
      <a href="suppliers.php" class="nav-link"><i class="bi bi-truck"></i> Suppliers</a>
      <a href="search.php" class="nav-link"><i class="bi bi-search"></i> Search</a>
      <a href="reports.php" class="nav-link"><i class="bi bi-bar-chart-line"></i> Reports</a>
    </nav>
  </div>
  <div class="col-md-10 main-content">
    <h2 class="page-title"><i class="bi bi-calendar-check"></i> Appointments</h2>
    <?php
    require_once 'db_config.php';
    $pdo = getDBConnection();
    $msg = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if ($_POST['action'] === 'insert') {
        $st = $pdo->prepare("INSERT INTO Appointment (appointment_date,status,student_id,provider_id) VALUES (?,?,?,?)");
        $st->execute([$_POST['appt_date'],$_POST['status'],$_POST['student_id'],$_POST['provider_id']]);
        $msg = '<div class="alert alert-success">Appointment created.</div>';
      } elseif ($_POST['action'] === 'update') {
        $st = $pdo->prepare("UPDATE Appointment SET appointment_date=?,status=?,student_id=?,provider_id=? WHERE appointment_id=?");
        $st->execute([$_POST['appt_date'],$_POST['status'],$_POST['student_id'],$_POST['provider_id'],$_POST['id']]);
        $msg = '<div class="alert alert-info">Appointment updated.</div>';
      } elseif ($_POST['action'] === 'delete') {
        $pdo->prepare("DELETE FROM Appointment WHERE appointment_id=?")->execute([$_POST['id']]);
        $msg = '<div class="alert alert-warning">Appointment deleted.</div>';
      }
    }
    echo $msg;

    $editRow = null;
    if (isset($_GET['edit'])) {
      $q = $pdo->prepare("SELECT * FROM Appointment WHERE appointment_id=?");
      $q->execute([$_GET['edit']]);
      $editRow = $q->fetch();
    }

    $students  = $pdo->query("SELECT student_id, name FROM Student ORDER BY name")->fetchAll();
    $providers = $pdo->query("SELECT provider_id, name FROM Healthcare_Provider ORDER BY name")->fetchAll();
    ?>

    <div class="card mb-4">
      <div class="card-header fw-bold"><?= $editRow ? 'Edit Appointment' : 'New Appointment' ?></div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="action" value="<?= $editRow ? 'update' : 'insert' ?>">
          <?php if ($editRow): ?><input type="hidden" name="id" value="<?= $editRow['appointment_id'] ?>"><?php endif; ?>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Student</label>
              <select class="form-select" name="student_id" required>
                <option value="">-- Select --</option>
                <?php foreach ($students as $s): ?>
                  <option value="<?= $s['student_id'] ?>" <?= ($editRow['student_id'] ?? '') == $s['student_id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Provider</label>
              <select class="form-select" name="provider_id" required>
                <option value="">-- Select --</option>
                <?php foreach ($providers as $p): ?>
                  <option value="<?= $p['provider_id'] ?>" <?= ($editRow['provider_id'] ?? '') == $p['provider_id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" name="appt_date" required value="<?= $editRow['appointment_date'] ?? '' ?>">
            </div>
            <div class="col-md-2">
              <label class="form-label">Status</label>
              <select class="form-select" name="status" required>
                <?php foreach (['Scheduled','Completed','Cancelled'] as $s): ?>
                  <option <?= ($editRow['status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button class="btn btn-primary w-100"><?= $editRow ? 'Update' : 'Book' ?></button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-striped align-middle">
          <thead class="table-dark">
            <tr><th>ID</th><th>Student</th><th>Provider</th><th>Date</th><th>Status</th><th>Actions</th></tr>
          </thead>
          <tbody>
          <?php
          $rows = $pdo->query("
            SELECT a.appointment_id, a.appointment_date, a.status,
                   s.name AS student_name, hp.name AS provider_name
            FROM Appointment a
            JOIN Student s ON a.student_id=s.student_id
            JOIN Healthcare_Provider hp ON a.provider_id=hp.provider_id
            ORDER BY a.appointment_date DESC")->fetchAll();
          foreach ($rows as $r):
            $badge = match($r['status']){ 'Completed'=>'success','Scheduled'=>'primary','Cancelled'=>'danger', default=>'secondary' };
          ?>
          <tr>
            <td><?= $r['appointment_id'] ?></td>
            <td><?= htmlspecialchars($r['student_name']) ?></td>
            <td><?= htmlspecialchars($r['provider_name']) ?></td>
            <td><?= $r['appointment_date'] ?></td>
            <td><span class="badge bg-<?= $badge ?>"><?= $r['status'] ?></span></td>
            <td>
              <a href="?edit=<?= $r['appointment_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <form method="post" style="display:inline" onsubmit="return confirm('Delete?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $r['appointment_id'] ?>">
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div></div>
</body>
</html>
