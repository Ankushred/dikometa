<?php
include 'config.php';

// Check if the form was actually submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Get data from the form
    $member_id = $_POST['member_id'];
    $type      = $_POST['type'];
    $amount    = $_POST['amount'];
    $date      = $_POST['trans_date'];
    $desc      = $_POST['description'];

    // 2. Validate Input
    if (empty($member_id) || empty($amount)) {
        die("Error: Data tidak lengkap.");
    }

    // 3. Prepare SQL (Secure against SQL Injection)
    $stmt = $conn->prepare("INSERT INTO transactions (member_id, type, amount, trans_date, description) VALUES (?, ?, ?, ?, ?)");
    
    // 4. Bind parameters (i=int, s=string, d=decimal)
    $stmt->bind_param("isiss", $member_id, $type, $amount, $date, $desc);

    // 5. Execute
    if ($stmt->execute()) {
        // Redirect to Dashboard with success parameter
        header("Location: index.php?status=success");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // If someone tries to open this file directly without submitting form
    header("Location: transaksi_tambah.php");
    exit();
}
?>