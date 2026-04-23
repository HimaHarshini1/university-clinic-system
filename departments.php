<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Departments – Clinic System</title>
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
      <a href="index.php"        class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a href="students.php"     class="nav-link"><i class="bi bi-people"></i> Students</a>
      <a href="providers.php"    class="nav-link"><i class="bi bi-person-badge"></i> Providers</a>
      <a href="appointments.php" class="nav-link"><i class="bi bi-calendar-check"></i> Appointments</a>
      <a href="inventory.php"    class="nav-link"><i class="bi bi-box-seam"></i> Inventory</a>
      <a href="departments.php"  class="nav-link active"><i class="bi bi-building"></i> Departments</a>
      <a href="suppliers.php"    class="nav-link"><i class="bi bi-truck"></i> Suppliers</a>
      <a href="search.php"       class="nav-link"><i class="bi bi-search"></i> Search</a>
      <a href="reports.php"      class="nav-link"><i class="bi bi-bar-chart-line"></i> Reports</a>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="col-md-10 main-content">
    <h2 class="page-title"><i class="bi bi-building"></i> Departments</h2>

    <?php
    require_once 'db_config.php';
    $pdo = getDBConnection();
    $msg = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

      if ($_POST['action'] === 'insert') {
        $stmt = $pdo->prepare("INSERT INTO Department (department_name, location) VALUES (?, ?)");
        $stmt->execute([trim($_POST['department_name']), trim($_POST['location'])]);
        $msg = '<div class="alert alert-success alert-dismissible fade show">
                  <i class="bi bi-check-circle"></i> Department added successfully.
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';

      } elseif ($_POST['action'] === 'update') {
        $stmt = $pdo->prepare("UPDATE Department SET department_name=?, location=? WHERE department_id=?");
        $stmt->execute([trim($_POST['department_name']), trim($_POST['location']), $_POST['id']]);
        $msg = '<div class="alert alert-info alert-dismissible fade show">
                  <i class="bi bi-pencil-check"></i> Department updated successfully.
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';

      } elseif ($_POST['action'] === 'delete') {
        try {
          $pdo->prepare("DELETE FROM Department WHERE department_id=?")->execute([$_POST['id']]);
          $msg = '<div class="alert alert-warning alert-dismissible fade show">
                    <i class="bi bi-trash"></i> Department deleted.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
        } catch (PDOException $e) {
          $msg = '<div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i> Cannot delete: this department has linked providers or inventory items.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
        }
      }
    }

    echo $msg;

    // Fetch for edit
    $editRow = null;
    if (isset($_GET['edit'])) {
      $q = $pdo->prepare("SELECT * FROM Department WHERE department_id=?");
      $q->execute([$_GET['edit']]);
      $editRow = $q->fetch();
    }
    ?>

    <!-- Add / Edit Form -->
    <div class="card mb-4 shadow-sm">
      <div class="card-header bg-white fw-bold">
        <i class="bi bi-<?= $editRow ? 'pencil' : 'plus-circle' ?>"></i>
        <?= $editRow ? 'Edit Department' : 'Add New Department' ?>
      </div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="action" value="<?= $editRow ? 'update' : 'insert' ?>">
          <?php if ($editRow): ?>
            <input type="hidden" name="id" value="<?= $editRow['department_id'] ?>">
          <?php endif; ?>

          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Department Name</label>
              <input type="text" class="form-control" name="department_name" required
                     placeholder="e.g. General Medicine"
                     value="<?= htmlspecialchars($editRow['department_name'] ?? '') ?>">
            </div>
            <div class="col-md-5">
              <label class="form-label fw-semibold">Location</label>
              <input type="text" class="form-control" name="location" required
                     placeholder="e.g. Building A, Room 101"
                     value="<?= htmlspecialchars($editRow['location'] ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
              <button class="btn btn-primary w-100">
                <i class="bi bi-<?= $editRow ? 'save' : 'plus-lg' ?>"></i>
                <?= $editRow ? 'Update' : 'Add Department' ?>
              </button>
              <?php if ($editRow): ?>
                <a href="departments.php" class="btn btn-outline-secondary">
                  <i class="bi bi-x-circle"></i> Cancel
                </a>
              <?php endif; ?>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Departments Table -->
    <div class="card shadow-sm">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-table"></i> All Departments</span>
        <?php $total = $pdo->query("SELECT COUNT(*) FROM Department")->fetchColumn(); ?>
        <span class="badge bg-primary"><?= $total ?> total</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Department Name</th>
                <th>Location</th>
                <th>Providers</th>
                <th>Inventory Items</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php
            $departments = $pdo->query("
              SELECT d.*,
                     COUNT(DISTINCT hp.provider_id) AS provider_count,
                     COUNT(DISTINCT i.item_id)      AS item_count
              FROM Department d
              LEFT JOIN Healthcare_Provider hp ON d.department_id = hp.department_id
              LEFT JOIN Inventory i            ON d.department_id = i.department_id
              GROUP BY d.department_id, d.department_name, d.location
              ORDER BY d.department_id
            ")->fetchAll();

            foreach ($departments as $d):
            ?>
            <tr>
              <td class="text-muted small"><?= $d['department_id'] ?></td>
              <td>
                <i class="bi bi-building text-primary me-1"></i>
                <strong><?= htmlspecialchars($d['department_name']) ?></strong>
              </td>
              <td>
                <i class="bi bi-geo-alt text-secondary me-1"></i>
                <?= htmlspecialchars($d['location']) ?>
              </td>
              <td>
                <span class="badge bg-info text-dark"><?= $d['provider_count'] ?> provider<?= $d['provider_count'] != 1 ? 's' : '' ?></span>
              </td>
              <td>
                <span class="badge bg-secondary"><?= $d['item_count'] ?> item<?= $d['item_count'] != 1 ? 's' : '' ?></span>
              </td>
              <td>
                <a href="?edit=<?= $d['department_id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                  <i class="bi bi-pencil"></i>
                </a>
                <form method="post" style="display:inline"
                      onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($d['department_name'])) ?>? This cannot be undone.')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $d['department_id'] ?>">
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