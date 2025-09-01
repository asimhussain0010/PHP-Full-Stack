<?php
$pageTitle = "Employee Management";
include_once 'includes/header.php';

// Check if user is logged in and is an employee
requireRole('employee');

// Get employee details
$user = getUserById($conn, $_SESSION['user_id']);

// Get leave requests for this employee
$leaveStmt = $conn->prepare("SELECT * FROM leave_requests WHERE user_id = ? ORDER BY created_at DESC");
$leaveStmt->bind_param("i", $_SESSION['user_id']);
$leaveStmt->execute();
$leaveRequests = $leaveStmt->get_result();

// Get attendance records for this employee
$today = date('Y-m-d');
$attendanceStmt = $conn->prepare("SELECT * FROM attendance WHERE user_id = ? ORDER BY date DESC LIMIT 7");
$attendanceStmt->bind_param("i", $_SESSION['user_id']);
$attendanceStmt->execute();
$attendance = $attendanceStmt->get_result();

// Count statistics
$totalLeaves = 0;
$pendingLeaves = 0;
$approvedLeaves = 0;
$rejectedLeaves = 0;

$leaveCountStmt = $conn->prepare("SELECT status, COUNT(*) as count FROM leave_requests WHERE user_id = ? GROUP BY status");
$leaveCountStmt->bind_param("i", $_SESSION['user_id']);
$leaveCountStmt->execute();
$leaveCounts = $leaveCountStmt->get_result();

while ($row = $leaveCounts->fetch_assoc()) {
    $totalLeaves += $row['count'];
    if ($row['status'] == 'pending') {
        $pendingLeaves = $row['count'];
    } elseif ($row['status'] == 'approved') {
        $approvedLeaves = $row['count'];
    } elseif ($row['status'] == 'rejected') {
        $rejectedLeaves = $row['count'];
    }
}
?>

<div class="dashboard-header">
    <h1>Employee Dashboard</h1>
    <p class="lead">Welcome back, <?php echo $user['fullname']; ?>!</p>
</div>

<div class="row">
    <!-- Employee Profile -->
    <div class="col-md-4">
        <div class="profile-section">
            <h4>My Profile</h4>
            <hr>
            <p><strong>Full Name:</strong> <?php echo $user['Fullname']; ?></p>
            <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
            <p><strong>Department:</strong> <?php echo $user['department']; ?></p>
            <p><strong>Position:</strong> <?php echo $user['position']; ?></p>
            <p><strong>Hire Date:</strong> <?php echo $user['hire_date'] ? date('F d, Y', strtotime($user['hire_date'])) : 'Not set'; ?></p>
        </div>
    </div>

    <!-- Statistics -->
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-6">
                <div class="card dashboard-card text-center mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Leave Balance</h5>
                        <div class="row">
                            <div class="col">
                                <div class="stats-counter"><?php echo $approvedLeaves; ?></div>
                                <div class="stats-label">Approved</div>
                            </div>
                            <div class="col">
                                <div class="stats-counter"><?php echo $pendingLeaves; ?></div>
                                <div class="stats-label">Pending</div>
                            </div>
                            <div class="col">
                                <div
