<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect already logged-in users
if (isLoggedIn()) {
    if (hasRole('hr')) {
        redirect('hr.php'); exit();
    } else {
        redirect('employee.php'); exit();
    }
}
    
// Pre-select role from URL
$selectedRole = isset($_GET['role']) ? cleanInput($_GET['role']) : '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = cleanInput($_POST["username"]);
    $password = cleanInput($_POST["password"]);
    $role = cleanInput($_POST["role"]);

    if (empty($username) || empty($password) || empty($role)) {
        $_SESSION['error'] = "Please fill all fields";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, role, fullname FROM users WHERE username = ? AND role = ?");
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Direct comparison of plain-text password
            if ($password === $user['password']) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["role"] = $user["role"];
                $_SESSION["fullname"] = $user["fullname"];

                file_put_contents('debug.log', "Redirecting user {$user['username']} to dashboard\n", FILE_APPEND);

                if ($user["role"] == "hr") {
                    redirect("hr.php"); exit();
                } else {
                    redirect("employee.php"); exit();
                }
            } else {
                $_SESSION['error'] = "Invalid password";
            }
        } else {
            $_SESSION['error'] = "Invalid username or role";
        }
    }
}

$pageTitle = "Login";
include_once 'includes/header.php';
?>

<div class="row justify-content-center my-5">
    <div class="col-md-6">
        <div class="login-form">
            <h2 class="text-center mb-4">Login to Simple MIS</h2>
            <?php displayAlert(); ?>
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
