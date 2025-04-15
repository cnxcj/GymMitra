<?php
// Connect to the database
include 'admin/dbcon.php';

// Add UNIQUE constraint to username field in members table
$sql = "ALTER TABLE members ADD UNIQUE (username)";

if (mysqli_query($con, $sql)) {
    echo "UNIQUE constraint added to username field successfully.";
} else {
    // If there's an error, it might be because the constraint already exists or there are duplicate usernames
    if (mysqli_errno($con) == 1061) {
        echo "UNIQUE constraint already exists on username field.";
    } elseif (mysqli_errno($con) == 1062) {
        echo "Error: There are duplicate usernames in the table. Please resolve duplicates before adding the constraint.";
        
        // Find duplicate usernames
        $find_duplicates = "SELECT username, COUNT(*) as count FROM members GROUP BY username HAVING count > 1";
        $result = mysqli_query($con, $find_duplicates);
        
        if (mysqli_num_rows($result) > 0) {
            echo "<h3>Duplicate Usernames:</h3>";
            echo "<ul>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<li>Username: " . $row['username'] . " (Count: " . $row['count'] . ")</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "Error adding UNIQUE constraint: " . mysqli_error($con);
    }
}

// Close the connection
mysqli_close($con);
?>
