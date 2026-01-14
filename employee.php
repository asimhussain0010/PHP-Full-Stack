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
            <p><strong>Full Name:</strong> <?php echo $user['fullname']; ?></p>
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
                                <div class="stats-counter"><?php echo $rejectedLeaves; ?></div>
                                <div class="stats-label">Rejected</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card dashboard-card text-center mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Status</h5>
                        <?php
                        $todayAttendance = $conn->prepare("SELECT * FROM attendance WHERE user_id = ? AND date = ?");
                        $todayAttendance->bind_param("is", $_SESSION['user_id'], $today);
                        $todayAttendance->execute();
                        $todayResult = $todayAttendance->get_result();
                        
                        if ($todayResult->num_rows > 0) {
                            $todayRecord = $todayResult->fetch_assoc();
                            $status = ucfirst($todayRecord['status']);
                            $statusClass = ($status == 'Present') ? 'text-success' : (($status == 'Late') ? 'text-warning' : 'text-danger');
                            $timeIn = $todayRecord['time_in'] ? date('h:i A', strtotime($todayRecord['time_in'])) : 'N/A';
                            $timeOut = $todayRecord['time_out'] ? date('h:i A', strtotime($todayRecord['time_out'])) : 'N/A';
                        } else {
                            $status = 'Not Recorded';
                            $statusClass = 'text-muted';
                            $timeIn = 'N/A';
                            $timeOut = 'N/A';
                        }
                        ?>
                        <div class="stats-counter <?php echo $statusClass; ?>"><?php echo $status; ?></div>
                        <div class="row mt-3">
                            <div class="col">
                                <div class="stats-label">Time In</div>
                                <div><?php echo $timeIn; ?></div>
                            </div>
                            <div class="col">
                                <div class="stats-label">Time Out</div>
                                <div><?php echo $timeOut; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Leave History -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Leave Requests</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Dates</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($leaveRequests->num_rows > 0): ?>
                                <?php while ($leave = $leaveRequests->fetch_assoc()): ?>
                                    <?php 
                                        $statusClass = '';
                                        if ($leave['status'] == 'approved') {
                                            $statusClass = 'bg-success text-white';
                                        } elseif ($leave['status'] == 'pending') {
                                            $statusClass = 'bg-warning text-dark';
                                        } else {
                                            $statusClass = 'bg-danger text-white';
                                        }
                                    ?>
                                    <tr>
                                        <td><?php echo ucfirst($leave['leave_type']); ?></td>
                                        <td>
                                            <?php 
                                                $startDate = date('M d, Y', strtotime($leave['start_date']));
                                                $endDate = date('M d, Y', strtotime($leave['end_date']));
                                                
                                                if ($startDate == $endDate) {
                                                    echo $startDate;
                                                } else {
                                                    echo $startDate . ' to ' . $endDate;
                                                }
                                            ?>
                                        </td>
                                        <td><span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($leave['status']); ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">No leave requests found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance History -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Recent Attendance</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($attendance->num_rows > 0): ?>
                                <?php while ($record = $attendance->fetch_assoc()): ?>
                                    <?php 
                                        $statusClass = '';
                                        if ($record['status'] == 'present') {
                                            $statusClass = 'bg-success text-white';
                                        } elseif ($record['status'] == 'late') {
                                            $statusClass = 'bg-warning text-dark';
                                        } else {
                                            $statusClass = 'bg-danger text-white';
                                        }
                                    ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($record['date'])); ?></td>
                                        <td><?php echo $record['time_in'] ? date('h:i A', strtotime($record['time_in'])) : 'N/A'; ?></td>
                                        <td><?php echo $record['time_out'] ? date('h:i A', strtotime($record['time_out'])) : 'N/A'; ?></td>
                                        <td><span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($record['status']); ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No attendance records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>