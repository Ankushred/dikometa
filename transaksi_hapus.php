<?php
include 'auth.php'; // Protect this file
include 'config.php';

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete query
    $stmt = $conn->prepare("DELETE FROM transactions WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Success: Go back to Report
        header("Location: laporan.php?msg=deleted");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $stmt->close();
}
?>