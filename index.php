<?php
$pageTitle = "HOME";
include_once 'includes/header.php';
?>

<div class="row justify-content-center my-5">
    <div class="col-md-8">
        <div class="jumbotron text-center">
            <h1 class="animate-fade-in">Welcome to StaffDesk</h1>
            <p class="animate-fade-in" style="animation-delay: 0.3s;">A Staff Management system for managing employees, leave requests, and attendance.</p>
            <hr class="my-4">
            <p>Please select your role to continue:</p>
            <div class="d-flex justify-content-center">
                <?php if (!isLoggedIn()): ?>
                    <a href="login.php?role=employee" class="btn btn-primary mx-2">
                        <i class="fas fa-user-tie mr-2"></i> Employee Login
                    </a>
                    <a href="login.php?role=hr" class="btn btn-success mx-2">
                        <i class="fas fa-users-cog mr-2"></i> HR Login
                    </a>
                <?php else: ?>
                    <?php if (hasRole('employee')): ?>
                        <a href="employee.php" class="btn btn-primary mx-2">
                            <i class="fas fa-tachometer-alt mr-2"></i> Go to My Dashboard
                        </a>
                    <?php elseif (hasRole('hr')): ?>
                        <a href="hr.php" class="btn btn-success mx-2">
                            <i class="fas fa-tachometer-alt mr-2"></i> Go to HR Dashboard
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-secondary mx-2">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-deck">
            <div class="row">
                <div class="col-md-4">
                    <div class="card dashboard-card text-center">
                        <div class="card-body">
                            <div class="card-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5 class="card-title">Employee Management</h5>
                            <p class="card-text">Manage employee profiles, departments, and positions.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card dashboard-card text-center">
                        <div class="card-body">
                            <div class="card-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h5 class="card-title">Leave Management</h5>
                            <p class="card-text">Request and approve employee leave applications.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card dashboard-card text-center">
                        <div class="card-body">
                            <div class="card-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5 class="card-title">Attendance Tracking</h5>
                            <p class="card-text">Track daily attendance and generate reports.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
