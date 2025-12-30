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
    
    // Get Current User Info for 'created_by'
    $user_id   = $_SESSION['user_id']; 
    $role      = $_SESSION['role'];

    // 2. Validate
    if (empty($member_id) || empty($amount)) {
        die("Error: Data tidak lengkap.");
    }

    // 3. DECISION: UPDATE vs INSERT
    if (isset($_POST['action']) && $_POST['action'] == 'update') {

        // ==========================
        //  UPDATE LOGIC
        // ==========================
        $id = $_POST['id']; 
        
        // Capture Status from the Edit Form
        // If status is not sent (e.g. slight edit), default to approved or keep existing.
        // But our Edit form sends it, so we capture it.
        $status = $_POST['status']; 

        $stmt = $conn->prepare("UPDATE transactions SET member_id=?, type=?, amount=?, trans_date=?, description=?, status=? WHERE id=?");
        
        // Types: i=int, s=string, d=double(amount), s=string, s=string, s=string, i=int
        $stmt->bind_param("isdsssi", $member_id, $type, $amount, $date, $desc, $status, $id);

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
        
        // MAKER-CHECKER LOGIC:
        // If Admin adds it -> Auto Approved
        // If Member adds it -> Pending
        $status = ($role == 'admin') ? 'approved' : 'pending';

        $stmt = $conn->prepare("INSERT INTO transactions (member_id, type, amount, trans_date, description, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        // Types: i=int, s=string, d=double, s=string, s=string, s=string, i=int
        $stmt->bind_param("isdsssi", $member_id, $type, $amount, $date, $desc, $status, $user_id);

        if ($stmt->execute()) {
            if($role == 'admin') {
                header("Location: index.php?status=success");
            } else {
                // If member, send back to member panel
                header("Location: member_panel.php?status=sent");
            }
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