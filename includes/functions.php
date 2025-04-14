<?php
// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user has specific role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] == $role;
}

// Redirect to another page
function redirect($url) {
    header("Location: $url");
    exit;
}

// Protect page from unauthorized access
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "Please login to access this page";
        redirect("login.php");
    }
}

// Check if user has required role, else redirect
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        $_SESSION['error'] = "You don't have permission to access this page";
        redirect("index.php");
    }
}

// Clean user inputs
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Display flash message
function displayAlert() {
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
}

// Get user data by ID
function getUserById($conn, $id) {
    $stmt = $conn->prepare("SELECT id, username, role, fullname, email, department, position, hire_date FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    return false;
}

// Count total users
function countUsers($conn) {
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'employee'");
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Count pending leave requests
function countPendingLeaves($conn) {
    $result = $conn->query("SELECT COUNT(*) as total FROM leave_requests WHERE status = 'pending'");
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Count today's absences
function countTodayAbsences($conn) {
    $today = date('Y-m-d');
    $result = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE date = '$today' AND status = 'absent'");
    $row = $result->fetch_assoc();
    return $row['total'];
}