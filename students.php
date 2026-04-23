<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Students – Clinic System</title>
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
  <div class="col-md-2 sidebar">
    <div class="brand"><i class="bi bi-hospital"></i> Clinic System</div>
    <nav class="nav flex-column mt-3">
      <a href="index.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a href="students.php" class="nav-link active"><i class="bi bi-people"></i> Students</a>
      <a href="providers.php" class="nav-link"><i class="bi bi-person-badge"></i> Providers</a>
      <a href="appointments.php" class="nav-link"><i class="bi bi-calendar-check"></i> Appointments</a>
      <a href="inventory.php" class="nav-link"><i class="bi bi-box-seam"></i> Inventory</a>
      <a href="departments.php" class="nav-link"><i class="bi bi-building"></i> Departments</a>
      <a href="suppliers.php" class="nav-link"><i class="bi bi-truck"></i> Suppliers</a>
      <a href="search.php" class="nav-link"><i class="bi bi-search"></i> Search</a>
      <a href="reports.php" class="nav-link"><i class="bi bi-bar-chart-line"></i> Reports</a>
    </nav>
  </div>

  <div class="col-md-10 main-content">
    <h2 class="page-title"><i class="bi bi-people"></i> Students</h2>

    <?php
    require_once 'db_config.php';
    $pdo = getDBConnection();
    $msg = '';

    // INSERT
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
      if ($_POST['action'] === 'insert') {
        $stmt = $pdo->prepare("INSERT INTO Student (name,email,date_of_birth,phone) VALUES (?,?,?,?)");
        $stmt->execute([$_POST['name'],$_POST['email'],$_POST['dob'],$_POST['phone']]);
        $msg = '<div class="alert alert-success">Student added successfully.</div>';
      } elseif ($_POST['action'] === 'update') {
        $stmt = $pdo->prepare("UPDATE Student SET name=?,email=?,date_of_birth=?,phone=? WHERE student_id=?");
        $stmt->execute([$_POST['name'],$_POST['email'],$_POST['dob'],$_POST['phone'],$_POST['id']]);
        $msg = '<div class="alert alert-info">Student updated successfully.</div>';
      } elseif ($_POST['action'] === 'delete') {
        try {
          $stmt = $pdo->prepare("DELETE FROM Student WHERE student_id=?");
          $stmt->execute([$_POST['id']]);
          $msg = '<div class="alert alert-warning">Student deleted.</div>';
        } catch (PDOException $e) {
          $msg = '<div class="alert alert-danger">Cannot delete: student has associated appointments.</div>';
        }
      }
    }

    echo $msg;

    // Fetch edit record
    $editRow = null;
    if (isset($_GET['edit'])) {
      $editRow = $pdo->prepare("SELECT * FROM Student WHERE student_id=?");
      $editRow->execute([$_GET['edit']]);
      $editRow = $editRow->fetch();
    }
    ?>

    <!-- Add / Edit Form -->
    <div class="card mb-4">
      <div class="card-header fw-bold"><?= $editRow ? 'Edit Student' : 'Add New Student' ?></div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="action" value="<?= $editRow ? 'update' : 'insert' ?>">
          <?php if ($editRow): ?>
            <input type="hidden" name="id" value="<?= $editRow['student_id'] ?>">
          <?php endif; ?>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" name="name" required value="<?= htmlspecialchars($editRow['name'] ?? '') ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" required value="<?= htmlspecialchars($editRow['email'] ?? '') ?>">
            </div>
            <div class="col-md-2">
              <label class="form-label">Date of Birth</label>
              <input type="date" class="form-control" name="dob" required value="<?= $editRow['date_of_birth'] ?? '' ?>">
            </div>
            <div class="col-md-2">
              <label class="form-label">Phone</label>
              <input type="text" class="form-control" name="phone" required value="<?= htmlspecialchars($editRow['phone'] ?? '') ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button class="btn btn-primary w-100"><?= $editRow ? 'Update' : 'Add Student' ?></button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Table -->
    <div class="card">
      <div class="card-body">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr><th>#</th><th>Name</th><th>Email</th><th>DOB</th><th>Phone</th><th>Actions</th></tr>
          </thead>
          <tbody>
          <?php
          $students = $pdo->query("SELECT * FROM Student ORDER BY student_id")->fetchAll();
          foreach ($students as $s): ?>
          <tr>
            <td><?= $s['student_id'] ?></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= htmlspecialchars($s['email']) ?></td>
            <td><?= $s['date_of_birth'] ?></td>
            <td><?= htmlspecialchars($s['phone']) ?></td>
            <td>
              <a href="?edit=<?= $s['student_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <form method="post" style="display:inline" onsubmit="return confirm('Delete this student?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $s['student_id'] ?>">
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
</div>
</div>
</body>
</html>
