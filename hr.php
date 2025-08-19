

// Check if user is logged in and is HR
requireRole('hr');

// Get all employees
$stmt = $conn->query("SELECT id, username, role, fullname, email, department, position, created_at FROM users WHERE role = 'employee' ORDER BY fullname");
$employees = $stmt->fetch_all(MYSQLI_ASSOC);

// Process delete employee if requested
if (isset($_GET['delete']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'employee'");
    $deleteStmt->bind_param("i", $id);
    
    if ($deleteStmt->execute()) {
        $_SESSION['success'] = "Employee deleted successfully";
    } else {
        $_SESSION['error'] = "Failed to delete employee: " . $conn->error;
    }
    
    redirect("hr.php");
}

// Count statistics
$totalEmployees = countUsers($conn);
$pendingLeaves = countPendingLeaves($conn);
$todayAbsences = countTodayAbsences($conn);
?>

<div class="dashboard-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1>HR Dashboard</h1>
            <p class="lead">Welcome back, <?php echo $_SESSION['fullname']; ?>!</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="manage_employee.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Add New Employee
            </a>
        </div>
    </div>
</div>

<!-- Statistics Overview -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card dashboard-card text-center">
            <div class="card-body">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h5 class="card-title">Total Employees</h5>
                <div class="stats-counter"><?php echo $totalEmployees; ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card text-center">
            <div class="card-body">
                <div class="card-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h5 class="card-title">Pending Leave Requests</h5>
                <div class="stats-counter"><?php echo $pendingLeaves; ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card text-center">
            <div class="card-body">
                <div class="card-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <h5 class="card-title">Today's Absences</h5>
                <div class="stats-counter"><?php echo $todayAbsences; ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Employee List -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">Employee Management</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($employees) > 0): ?>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td><?php echo $employee['id']; ?></td>
                                <td><?php echo $employee['fullname']; ?></td>
                                <td><?php echo $employee['email']; ?></td>
                                <td><?php echo $employee['department']; ?></td>
                                <td><?php echo $employee['position']; ?></td>
                                <td>
                                    <a href="manage_employee.php?id=<?php echo $employee['id']; ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="hr.php?delete=1&id=<?php echo $employee['id']; ?>" class="btn btn-sm btn-danger btn-delete" data-bs-toggle="tooltip" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No employees found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
