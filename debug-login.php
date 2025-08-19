<?php
// Start session

// Turn on all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include_once 'includes/db.php';

echo "<h2>Login Debugging Information</h2>";

// Check if database connection is working
if ($conn->connect_error) {
    echo "<p style='color:red'>Database connection failed: " . $conn->connect_error . "</p>";
} else {
    echo "<p style='color:green'>Database connection successful</p>";
    
    // Check if users table exists and has records
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "<p style='color:green'>Users table exists</p>";
        
        // Count users
        $result = $conn->query("SELECT COUNT(*) as total FROM users");
        $row = $result->fetch_assoc();
        echo "<p>Total users in database: " . $row['total'] . "</p>";
        
        // Check for HR users
        $result = $conn->query("SELECT username FROM users WHERE role = 'hr'");
        echo "<p>HR users found: " . $result->num_rows . "</p>";
        
        // Check for Employee users
        $result = $conn->query("SELECT username FROM users WHERE role = 'employee'");
        echo "<p>Employee users found: " . $result->num_rows . "</p>";
        
    } else {
        echo "<p style='color:red'>Users table does not exist!</p>";
    }
}

// Check session status
echo "<h3>Session Information:</h3>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session status: " . (session_status() == PHP_SESSION_ACTIVE ? "Active" : "Not active") . "</p>";

// Test login functionality
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    echo "<h3>Login Test:</h3>";
    echo "<p>Attempting to login with username: $username</p>";
    
    // Query database for user
    $stmt = $conn->prepare("SELECT id, username, password, role, fullname FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        echo "<p style='color:green'>User found in database</p>";
        echo "<p>Stored role: " . $user['role'] . "</p>";
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            echo "<p style='color:green'>Password is correct</p>";
            echo "<p>This user should be redirected to: " . ($user['role'] == 'hr' ? 'hr.php' : 'employee.php') . "</p>";
        } else {
            echo "<p style='color:red'>Password verification failed!</p>";
            echo "<p>Note: If you manually added users to the database, the passwords need to be hashed properly.</p>";
        }
    } else {
        echo "<p style='color:red'>User not found in database</p>";
    }
}
?>

<h3>Test Login Form</h3>
<form method="post" action="">
    <div>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
    </div>
    <div style="margin-top: 10px;">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
    </div>
    <div style="margin-top: 10px;">
        <button type="submit">Test Login</button>
    </div>
</form>

<h3>Session Contents:</h3>
<pre>
<?php print_r($_SESSION); ?>
</pre>
