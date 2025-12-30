<?php
include 'auth.php';
include 'config.php';

// SECURITY: Only Admins are allowed to delete data.
// This prevents members from accessing this URL directly.
checkAdmin(); 

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize input to integer for safety

    // Delete query
    $stmt = $conn->prepare("DELETE FROM transactions WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Success: Go back to Report with a success message
        header("Location: laporan.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $stmt->close();
} else {
    // If no ID is provided, just go back
    header("Location: laporan.php");
    exit();
}
?>