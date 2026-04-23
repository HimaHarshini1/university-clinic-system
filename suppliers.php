<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Suppliers – Clinic System</title>
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
      <a href="departments.php"  class="nav-link"><i class="bi bi-building"></i> Departments</a>
      <a href="suppliers.php"    class="nav-link active"><i class="bi bi-truck"></i> Suppliers</a>
      <a href="search.php"       class="nav-link"><i class="bi bi-search"></i> Search</a>
      <a href="reports.php"      class="nav-link"><i class="bi bi-bar-chart-line"></i> Reports</a>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="col-md-10 main-content">
    <h2 class="page-title"><i class="bi bi-truck"></i> Suppliers</h2>

    <?php
    require_once 'db_config.php';
    $pdo = getDBConnection();
    $msg = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

      if ($_POST['action'] === 'insert') {
        $stmt = $pdo->prepare("INSERT INTO Supplier (supplier_name, contact, address) VALUES (?, ?, ?)");
        $stmt->execute([trim($_POST['supplier_name']), trim($_POST['contact']), trim($_POST['address'])]);
        $msg = '<div class="alert alert-success alert-dismissible fade show">
                  <i class="bi bi-check-circle"></i> Supplier added successfully.
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';

      } elseif ($_POST['action'] === 'update') {
        $stmt = $pdo->prepare("UPDATE Supplier SET supplier_name=?, contact=?, address=? WHERE supplier_id=?");
        $stmt->execute([trim($_POST['supplier_name']), trim($_POST['contact']), trim($_POST['address']), $_POST['id']]);
        $msg = '<div class="alert alert-info alert-dismissible fade show">
                  <i class="bi bi-pencil-check"></i> Supplier updated successfully.
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';

      } elseif ($_POST['action'] === 'delete') {
        try {
          $pdo->prepare("DELETE FROM Supplier WHERE supplier_id=?")->execute([$_POST['id']]);
          $msg = '<div class="alert alert-warning alert-dismissible fade show">
                    <i class="bi bi-trash"></i> Supplier deleted.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
        } catch (PDOException $e) {
          $msg = '<div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i> Cannot delete: this supplier has linked inventory items.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
        }
      }
    }

    echo $msg;

    // Fetch for edit
    $editRow = null;
    if (isset($_GET['edit'])) {
      $q = $pdo->prepare("SELECT * FROM Supplier WHERE supplier_id=?");
      $q->execute([$_GET['edit']]);
      $editRow = $q->fetch();
    }
    ?>

    <!-- Add / Edit Form -->
    <div class="card mb-4 shadow-sm">
      <div class="card-header bg-white fw-bold">
        <i class="bi bi-<?= $editRow ? 'pencil' : 'plus-circle' ?>"></i>
        <?= $editRow ? 'Edit Supplier' : 'Add New Supplier' ?>
      </div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="action" value="<?= $editRow ? 'update' : 'insert' ?>">
          <?php if ($editRow): ?>
            <input type="hidden" name="id" value="<?= $editRow['supplier_id'] ?>">
          <?php endif; ?>

          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label fw-semibold">Supplier Name</label>
              <input type="text" class="form-control" name="supplier_name" required
                     placeholder="e.g. McKesson Corporation"
                     value="<?= htmlspecialchars($editRow['supplier_name'] ?? '') ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Contact</label>
              <input type="text" class="form-control" name="contact" required
                     placeholder="e.g. 1-800-555-0000"
                     value="<?= htmlspecialchars($editRow['contact'] ?? '') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Address</label>
              <input type="text" class="form-control" name="address" required
                     placeholder="e.g. 123 Main St, Dallas, TX"
                     value="<?= htmlspecialchars($editRow['address'] ?? '') ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
              <button class="btn btn-primary w-100">
                <i class="bi bi-<?= $editRow ? 'save' : 'plus-lg' ?>"></i>
                <?= $editRow ? 'Update' : 'Add Supplier' ?>
              </button>
              <?php if ($editRow): ?>
                <a href="suppliers.php" class="btn btn-outline-secondary">
                  <i class="bi bi-x-circle"></i>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Suppliers Table -->
    <div class="card shadow-sm">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-table"></i> All Suppliers</span>
        <?php $total = $pdo->query("SELECT COUNT(*) FROM Supplier")->fetchColumn(); ?>
        <span class="badge bg-primary"><?= $total ?> total</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Supplier Name</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Inventory Items</th>
                <th>Total Stock</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php
            $suppliers = $pdo->query("
              SELECT s.*,
                     COUNT(i.item_id)  AS item_count,
                     COALESCE(SUM(i.quantity), 0) AS total_qty
              FROM Supplier s
              LEFT JOIN Inventory i ON s.supplier_id = i.supplier_id
              GROUP BY s.supplier_id, s.supplier_name, s.contact, s.address
              ORDER BY s.supplier_id
            ")->fetchAll();

            foreach ($suppliers as $s):
            ?>
            <tr>
              <td class="text-muted small"><?= $s['supplier_id'] ?></td>
              <td>
                <i class="bi bi-truck text-primary me-1"></i>
                <strong><?= htmlspecialchars($s['supplier_name']) ?></strong>
              </td>
              <td class="small"><?= htmlspecialchars($s['contact']) ?></td>
              <td class="small text-muted"><?= htmlspecialchars($s['address']) ?></td>
              <td>
                <span class="badge bg-info text-dark"><?= $s['item_count'] ?> item<?= $s['item_count'] != 1 ? 's' : '' ?></span>
              </td>
              <td>
                <span class="badge bg-secondary"><?= number_format($s['total_qty']) ?> units</span>
              </td>
              <td>
                <a href="?edit=<?= $s['supplier_id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                  <i class="bi bi-pencil"></i>
                </a>
                <form method="post" style="display:inline"
                      onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($s['supplier_name'])) ?>? This cannot be undone.')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $s['supplier_id'] ?>">
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