<?php
include 'auth.php';
include 'config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Collect Data
    $member_id = $_POST['member_id'];
    $type      = $_POST['type'];
    $amount    = $_POST['amount'];
    $date      = $_POST['trans_date'];
    $desc      = $_POST['description'];

    // 2. Validate
    if (empty($member_id) || empty($amount)) {
        die("Error: Data tidak lengkap.");
    }

    // 3. DECISION: UPDATE vs INSERT
    if (isset($_POST['action']) && $_POST['action'] == 'update') {

        // ==========================
        //  UPDATE LOGIC (Fixed)
        // ==========================
        $id = $_POST['id']; 

        $stmt = $conn->prepare("UPDATE transactions SET member_id=?, type=?, amount=?, trans_date=?, description=? WHERE id=?");
        
        // FIXED LINE BELOW: changed "isisisi" to "isissi" (6 letters for 6 variables)
        // i=int, s=string, i=int, s=string, s=string, i=int
        $stmt->bind_param("isissi", $member_id, $type, $amount, $date, $desc, $id);

        if ($stmt->execute()) {
            header("Location: laporan.php?msg=updated"); 
            exit();
        } else {
            echo "Error Updating: " . $stmt->error;
        }

    } else {

        // ==========================
        //  INSERT LOGIC
        // ==========================
        $stmt = $conn->prepare("INSERT INTO transactions (member_id, type, amount, trans_date, description) VALUES (?, ?, ?, ?, ?)");
        
        // i=int, s=string, i=int, s=string, s=string
        $stmt->bind_param("isiss", $member_id, $type, $amount, $date, $desc);

        if ($stmt->execute()) {
            header("Location: index.php?status=success");
            exit();
        } else {
            echo "Error Inserting: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: index.php");
    exit();
}
?>