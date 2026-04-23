<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Providers – Clinic System</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body{background:#f0f4f8}
    .sidebar{background:#1a3c5e;min-height:100vh;padding:20px 0}
    .sidebar .brand{color:#fff;font-size:1.2rem;font-weight:700;padding:10px 20px 20px;border-bottom:1px solid #2d5986}
    .sidebar .nav-link{color:#a8c4e0;padding:10px 20px;transition:all .2s}
    .sidebar .nav-link:hover,.sidebar .nav-link.active{background:#2d5986;color:#fff;border-left:3px solid #4fc3f7}
    .sidebar .nav-link i{margin-right:8px}
    .main-content{padding:30px}
    .page-title{color:#1a3c5e;font-weight:700;margin-bottom:25px}
  </style>
</head>
<body>
<div class="container-fluid">
<div class="row">
 
  <!-- Sidebar -->
  <div class="col-md-2 sidebar">
    <div class="brand"><i class="bi bi-hospital"></i> Clinic System</div>
    <nav class="nav flex-column mt-3">
      <a href="index.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a href="students.php" class="nav-link"><i class="bi bi-people"></i> Students</a>
      <a href="providers.php" class="nav-link active"><i class="bi bi-person-badge"></i> Providers</a>
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
    <h2 class="page-title"><i class="bi bi-person-badge"></i> Healthcare Providers</h2>
 
    <?php
    require_once 'db_config.php';
    $pdo = getDBConnection();
    $msg = '';
 
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
 
      if ($_POST['action'] === 'insert') {
        $stmt = $pdo->prepare(
          "INSERT INTO Healthcare_Provider (name, specialization, contact, department_id)
           VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
          $_POST['name'],
          $_POST['specialization'],
          $_POST['contact'],
          $_POST['department_id']
        ]);
        $msg = '<div class="alert alert-success alert-dismissible fade show">
                  <i class="bi bi-check-circle"></i> Provider added successfully.
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
 
      } elseif ($_POST['action'] === 'update') {
        $stmt = $pdo->prepare(
          "UPDATE Healthcare_Provider
           SET name=?, specialization=?, contact=?, department_id=?
           WHERE provider_id=?"
        );
        $stmt->execute([
          $_POST['name'],
          $_POST['specialization'],
          $_POST['contact'],
          $_POST['department_id'],
          $_POST['id']
        ]);
        $msg = '<div class="alert alert-info alert-dismissible fade show">
                  <i class="bi bi-pencil-check"></i> Provider updated successfully.
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
 
      } elseif ($_POST['action'] === 'delete') {
        try {
          $pdo->prepare("DELETE FROM Healthcare_Provider WHERE provider_id=?")
              ->execute([$_POST['id']]);
          $msg = '<div class="alert alert-warning alert-dismissible fade show">
                    <i class="bi bi-trash"></i> Provider deleted.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
        } catch (PDOException $e) {
          $msg = '<div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i> Cannot delete: this provider has existing appointments.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
        }
      }
    }
 
    echo $msg;
 
    // Fetch for edit
    $editRow = null;
    if (isset($_GET['edit'])) {
      $q = $pdo->prepare("SELECT * FROM Healthcare_Provider WHERE provider_id=?");
      $q->execute([$_GET['edit']]);
      $editRow = $q->fetch();
    }
 
    // Get departments for dropdown
    $departments = $pdo->query("SELECT department_id, department_name FROM Department ORDER BY department_name")->fetchAll();
    ?>
 
    <!-- Add / Edit Form -->
    <div class="card mb-4 shadow-sm">
      <div class="card-header bg-white fw-bold">
        <i class="bi bi-<?= $editRow ? 'pencil' : 'plus-circle' ?>"></i>
        <?= $editRow ? 'Edit Provider' : 'Add New Provider' ?>
      </div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="action" value="<?= $editRow ? 'update' : 'insert' ?>">
          <?php if ($editRow): ?>
            <input type="hidden" name="id" value="<?= $editRow['provider_id'] ?>">
          <?php endif; ?>
 
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label fw-semibold">Full Name</label>
              <input type="text" class="form-control" name="name" required
                     placeholder="e.g. Dr. Jane Smith"
                     value="<?= htmlspecialchars($editRow['name'] ?? '') ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Specialization</label>
              <input type="text" class="form-control" name="specialization" required
                     placeholder="e.g. Physician, Counselor"
                     value="<?= htmlspecialchars($editRow['specialization'] ?? '') ?>">
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">Contact (Email)</label>
              <input type="text" class="form-control" name="contact" required
                     placeholder="email@clinic.edu"
                     value="<?= htmlspecialchars($editRow['contact'] ?? '') ?>">
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold">Department</label>
              <select class="form-select" name="department_id" required>
                <option value="">-- Select Department --</option>
                <?php foreach ($departments as $d): ?>
                  <option value="<?= $d['department_id'] ?>"
                    <?= ($editRow['department_id'] ?? '') == $d['department_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['department_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button class="btn btn-primary w-100">
                <i class="bi bi-<?= $editRow ? 'save' : 'plus-lg' ?>"></i>
                <?= $editRow ? 'Update' : 'Add Provider' ?>
              </button>
            </div>
          </div>
 
          <?php if ($editRow): ?>
            <div class="mt-2">
              <a href="providers.php" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Cancel Edit
              </a>
            </div>
          <?php endif; ?>
        </form>
      </div>
    </div>
 
    <!-- Providers Table -->
    <div class="card shadow-sm">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-table"></i> All Providers</span>
        <?php
          $total = $pdo->query("SELECT COUNT(*) FROM Healthcare_Provider")->fetchColumn();
        ?>
        <span class="badge bg-primary"><?= $total ?> total</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Specialization</th>
                <th>Contact</th>
                <th>Department</th>
                <th>Appointments</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php
            $providers = $pdo->query("
              SELECT hp.*, d.department_name,
                     COUNT(a.appointment_id) AS appt_count
              FROM Healthcare_Provider hp
              JOIN Department d ON hp.department_id = d.department_id
              LEFT JOIN Appointment a ON hp.provider_id = a.provider_id
              GROUP BY hp.provider_id, hp.name, hp.specialization, hp.contact, hp.department_id, d.department_name
              ORDER BY hp.provider_id
            ")->fetchAll();
 
            foreach ($providers as $p):
            ?>
            <tr>
              <td class="text-muted small"><?= $p['provider_id'] ?></td>
              <td>
                <i class="bi bi-person-circle text-primary me-1"></i>
                <strong><?= htmlspecialchars($p['name']) ?></strong>
              </td>
              <td>
                <span class="badge bg-info text-dark"><?= htmlspecialchars($p['specialization']) ?></span>
              </td>
              <td class="small"><?= htmlspecialchars($p['contact']) ?></td>
              <td>
                <i class="bi bi-building text-secondary me-1"></i>
                <?= htmlspecialchars($p['department_name']) ?>
              </td>
              <td>
                <span class="badge bg-secondary"><?= $p['appt_count'] ?> appts</span>
              </td>
              <td>
                <a href="?edit=<?= $p['provider_id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                  <i class="bi bi-pencil"></i>
                </a>
                <form method="post" style="display:inline"
                      onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($p['name'])) ?>? This cannot be undone.')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $p['provider_id'] ?>">
                  <button class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
 
  </div><!-- end main-content -->
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
 