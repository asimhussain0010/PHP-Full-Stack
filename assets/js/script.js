// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 1s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 1000);
        });
    }, 5000);

    // Enable tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Confirm delete action
    const deleteButtons = document.querySelectorAll('.btn-delete');
    if (deleteButtons) {
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this record?')) {
                    e.preventDefault();
                }
            });
        });
    }
});