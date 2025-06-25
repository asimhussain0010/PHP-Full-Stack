<?php
$pageTitle = "Manage Employee Home";
include_once 'includes/header.php';

// Check if user is logged in and is HR
requireRole('hr');

// Initialize variables
$id = $username = $password = $fullname = $email = $department = $position = $hire_date = '';
$isEdit = false;

// Check if this is an edit operation
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id, username, fullname, email, department, position, hire_date FROM users WHERE id = ? AND role = 'employee'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $employee = $result->fetch_assoc();
        $username = $employee['username'];
        $fullname = $employee['fullname'];
        $email = $employee['email'];
        $department = $employee['department'];
        $position = $employee['position'];
        $hire_date = $employee['hire_date'];
        $isEdit = true;
        $pageTitle = "Edit Employee";
    } else {
        $_SESSION['error'] = "Employee not found";
        redirect("hr.php");
    }
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = cleanInput($_POST['username']);
    $fullname = cleanInput($_POST['fullname']);
    $email = cleanInput($_POST['email']);
    $department = cleanInput($_POST['department']);
    $position = cleanInput($_POST['position']);
    $hire_date = cleanInput($_POST['hire_date']);
    
    // Validate form data
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    
    if (empty($fullname)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // If this is a new employee, password is required
    if (!$isEdit) {
        $password = cleanInput($_POST['password']);
        if (empty($password)) {
            $errors[] = "Password is required for new employees";
        }
    }
    
    // If there are no errors, proceed with database operation
    if (empty($errors)) {
        if ($isEdit) {
            // Update existing employee
            $stmt = $conn->prepare("UPDATE users SET username = ?, fullname = ?, email = ?, department = ?, position = ?, hire_date = ? WHERE id = ? AND role = 'employee'");
            $stmt->bind_param("ssssssi", $username, $fullname, $email, $department, $position, $hire_date, $id);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Employee updated successfully";
                redirect("hr.php");
            } else {
                $_SESSION['error'] = "Failed to update employee: " . $conn->error;
            }
        } else {
            // Check if username already exists
            $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $checkStmt->bind_param("s", $username);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                $_SESSION['error'] = "Username already exists";
            } else {
                // Hash the password for security
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $role = 'employee';
                
                // Insert new employee
                $stmt = $conn->prepare("INSERT INTO users (username, password, role, fullname, email, department, position, hire_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssss", $username, $hashed_password, $role, $fullname, $email, $department, $position, $hire_date);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Employee added successfully";
                    redirect("hr.php");
                } else {
                    $_SESSION['error'] = "Failed to add employee: " . $conn->error;
                }
            }
        }
    } else {
        // Display errors
        $_SESSION['error'] = implode("<br>", $errors);
    }
}
?>

<div class="row justify-content-center my-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><?php echo $isEdit ? 'Edit Employee' : 'Add New Employee'; ?></h5>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . ($isEdit ? "?id=$id" : "")); ?>" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" required <?php echo $isEdit ? 'readonly' : ''; ?>>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php if (!$isEdit): ?>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo $fullname; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-select" id="department" name="department">
                                    <option value="" disabled <?php echo empty($department) ? 'selected' : ''; ?>>Select Department</option>
                                    <option value="Human Resources" <?php echo $department == 'Human Resources' ? 'selected' : ''; ?>>Human Resources</option>
                                    <option value="Engineering" <?php echo $department == 'Engineering' ? 'selected' : ''; ?>>Engineering</option>
                                    <option value="Marketing" <?php echo $department == 'Marketing' ? 'selected' : ''; ?>>Marketing</option>
                                    <option value="Finance" <?php echo $department == 'Finance' ? 'selected' : ''; ?>>Finance</option>
                                    <option value="Operations" <?php echo $department == 'Operations' ? 'selected' : ''; ?>>Operations</option>
                                    <option value="IT" <?php echo $department == 'IT' ? 'selected' : ''; ?>>IT</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" class="form-control" id="position" name="position" value="<?php echo $position; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hire_date" class="form-label">Hire Date</label>
                                <input type="date" class="form-control" id="hire_date" name="hire_date" value="<?php echo $hire_date; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="hr.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Update Employee' : 'Add Employee'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
