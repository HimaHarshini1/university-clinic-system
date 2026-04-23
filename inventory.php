<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inventory – Clinic System</title>
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
      <a href="inventory.php" class="nav-link active"><i class="bi bi-box-seam"></i> Inventory</a>
      <a href="departments.php" class="nav-link"><i class="bi bi-building"></i> Departments</a>
      <a href="suppliers.php" class="nav-link"><i class="bi bi-truck"></i> Suppliers</a>
      <a href="search.php" class="nav-link"><i class="bi bi-search"></i> Search</a>
      <a href="reports.php" class="nav-link"><i class="bi bi-bar-chart-line"></i> Reports</a>
    </nav>
  </div>
  <div class="col-md-10 main-content">
    <h2 class="page-title"><i class="bi bi-box-seam"></i> Inventory Management</h2>
    <?php
    require_once 'db_config.php';
    $pdo = getDBConnection();
    $msg = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if ($_POST['action'] === 'insert') {
        $st = $pdo->prepare("INSERT INTO Inventory (item_name,quantity,expiry_date,department_id,supplier_id) VALUES (?,?,?,?,?)");
        $st->execute([$_POST['item_name'],$_POST['qty'],$_POST['expiry'],$_POST['dept_id'],$_POST['sup_id']]);
        $msg = '<div class="alert alert-success">Item added.</div>';
      } elseif ($_POST['action'] === 'update') {
        $st = $pdo->prepare("UPDATE Inventory SET item_name=?,quantity=?,expiry_date=?,department_id=?,supplier_id=? WHERE item_id=?");
        $st->execute([$_POST['item_name'],$_POST['qty'],$_POST['expiry'],$_POST['dept_id'],$_POST['sup_id'],$_POST['id']]);
        $msg = '<div class="alert alert-info">Item updated.</div>';
      } elseif ($_POST['action'] === 'delete') {
        $pdo->prepare("DELETE FROM Inventory WHERE item_id=?")->execute([$_POST['id']]);
        $msg = '<div class="alert alert-warning">Item deleted.</div>';
      }
    }
    echo $msg;

    $editRow = null;
    if (isset($_GET['edit'])) {
      $q = $pdo->prepare("SELECT * FROM Inventory WHERE item_id=?");
      $q->execute([$_GET['edit']]);
      $editRow = $q->fetch();
    }

    $depts     = $pdo->query("SELECT department_id, department_name FROM Department ORDER BY department_name")->fetchAll();
    $suppliers = $pdo->query("SELECT supplier_id, supplier_name FROM Supplier ORDER BY supplier_name")->fetchAll();
    ?>

    <div class="card mb-4">
      <div class="card-header fw-bold"><?= $editRow ? 'Edit Item' : 'Add Inventory Item' ?></div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="action" value="<?= $editRow ? 'update' : 'insert' ?>">
          <?php if ($editRow): ?><input type="hidden" name="id" value="<?= $editRow['item_id'] ?>"><?php endif; ?>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Item Name</label>
              <input type="text" class="form-control" name="item_name" required value="<?= htmlspecialchars($editRow['item_name'] ?? '') ?>">
            </div>
            <div class="col-md-1">
              <label class="form-label">Qty</label>
              <input type="number" class="form-control" name="qty" min="0" required value="<?= $editRow['quantity'] ?? '' ?>">
            </div>
            <div class="col-md-2">
              <label class="form-label">Expiry Date</label>
              <input type="date" class="form-control" name="expiry" required value="<?= $editRow['expiry_date'] ?? '' ?>">
            </div>
            <div class="col-md-2">
              <label class="form-label">Department</label>
              <select class="form-select" name="dept_id" required>
                <?php foreach ($depts as $d): ?>
                  <option value="<?= $d['department_id'] ?>" <?= ($editRow['department_id'] ?? '') == $d['department_id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['department_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Supplier</label>
              <select class="form-select" name="sup_id" required>
                <?php foreach ($suppliers as $s): ?>
                  <option value="<?= $s['supplier_id'] ?>" <?= ($editRow['supplier_id'] ?? '') == $s['supplier_id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['supplier_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button class="btn btn-primary w-100"><?= $editRow ? 'Update' : 'Add Item' ?></button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-striped align-middle">
          <thead class="table-dark">
            <tr><th>ID</th><th>Item</th><th>Qty</th><th>Expiry</th><th>Department</th><th>Supplier</th><th>Actions</th></tr>
          </thead>
          <tbody>
          <?php
          $items = $pdo->query("
            SELECT i.*, d.department_name, s.supplier_name
            FROM Inventory i
            JOIN Department d ON i.department_id=d.department_id
            JOIN Supplier s ON i.supplier_id=s.supplier_id
            ORDER BY i.item_id")->fetchAll();
          foreach ($items as $it):
            $expired = strtotime($it['expiry_date']) < time();
          ?>
          <tr class="<?= $expired ? 'table-danger' : '' ?>">
            <td><?= $it['item_id'] ?></td>
            <td><?= htmlspecialchars($it['item_name']) ?> <?= $expired ? '<span class="badge bg-danger">Expired</span>' : '' ?></td>
            <td><?= $it['quantity'] ?></td>
            <td><?= $it['expiry_date'] ?></td>
            <td><?= htmlspecialchars($it['department_name']) ?></td>
            <td><?= htmlspecialchars($it['supplier_name']) ?></td>
            <td>
              <a href="?edit=<?= $it['item_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <form method="post" style="display:inline" onsubmit="return confirm('Delete?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $it['item_id'] ?>">
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
