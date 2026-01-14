<?php
/**
 * Display alert messages from session
 */

// Function to display alerts
function displayAlert() {
    // Display success message
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo $_SESSION['success'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['success']);
    }
    
    // Display error message
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        echo $_SESSION['error'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['error']);
    }
    
    // Display warning message
    if (isset($_SESSION['warning'])) {
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">';
        echo $_SESSION['warning'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['warning']);
    }
    
    // Display info message
    if (isset($_SESSION['info'])) {
        echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
        echo $_SESSION['info'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['info']);
    }
}
?>