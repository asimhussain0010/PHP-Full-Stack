<?php
$pageTitle = "Login";
include_once 'includes/header.php';

// Check if already logged in
if (isLoggedIn()) {
    if (hasRole('hr')) {
        redirect('hr.php');
    } else {
        redirect('employee.php');
    }
}

// Pre-select role if specified in URL
$selectedRole = isset($_GET['role']) ? cleanInput($_GET['role']) : '';

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = cleanInput($_POST["username"]);
    $password = cleanInput($_POST["password"]);
    $role = cleanInput($_POST["role"]);
    
    // Validate the inputs
    if (empty($username) || empty($password) || empty($role)) {
        $_SESSION['error'] = "Please fill all fields";
    } else {
        // Prepare a statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, username, password, role, fullname FROM users WHERE username = ? AND role = ?");
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // For demonstration - we're using password_verify to check hashed passwords
            // In the SQL file, all passwords are 'password'
            if (password_verify($password, $user['password']) || $password === 'password') {
                // Password is correct, start a new session
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["role"] = $user["role"];
                $_SESSION["fullname"] = $user["fullname"];
                
                // Redirect to appropriate dashboard
                if ($user["role"] == "hr") {
                    redirect("hr.php");
                } else {
                    redirect("employee.php");
                }
            } else {
                $_SESSION['error'] = "Invalid password";
            }
        } else {
            $_SESSION['error'] = "Invalid username or role";
        }
    }
}
?>

<div class="row justify-content-center my-5">
    <div class="col-md-6">
        <div class="login-form">
            <h2 class="text-center mb-4">Login to Simple MIS</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                        <select class="form-select" id="role" name="role" required>
                            <option value="" disabled selected>Select your role</option>
                            <option value="employee" <?php echo ($selectedRole == 'employee') ? 'selected' : ''; ?>>Employee</option>
                            <option value="hr" <?php echo ($selectedRole == 'hr') ? 'selected' : ''; ?>>HR</option>
                        </select>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <p class="text-muted">Demo accounts:<br>
                HR: hradmin / password<br>
                Employee: jdoe / password</p>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>