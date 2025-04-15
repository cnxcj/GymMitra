<?php
// Check if username is available
if (isset($_POST['username'])) {
    $username = trim($_POST['username']);
    
    // Connect to database
    include 'dbcon.php';
    
    // Check if username exists in members table
    $query = "SELECT COUNT(*) as count FROM members WHERE username = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        echo 'taken';
    } else {
        echo 'available';
    }
    
    $stmt->close();
    $con->close();
} else {
    echo 'error';
}
?>
