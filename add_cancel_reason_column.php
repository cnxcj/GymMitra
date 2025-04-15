<?php
// Connect to the database
include "customer/pages/dbcon.php";

// Check if the cancel_reason column exists
$check_column_query = "SHOW COLUMNS FROM member_packages LIKE 'cancel_reason'";
$check_column_result = mysqli_query($con, $check_column_query);

if (mysqli_num_rows($check_column_result) == 0) {
    // The column doesn't exist, so add it
    $add_column_query = "ALTER TABLE member_packages ADD COLUMN cancel_reason TEXT NULL AFTER status";
    $add_column_result = mysqli_query($con, $add_column_query);
    
    if ($add_column_result) {
        echo "Successfully added cancel_reason column to member_packages table.<br>";
    } else {
        echo "Error adding cancel_reason column: " . mysqli_error($con) . "<br>";
    }
} else {
    echo "cancel_reason column already exists in member_packages table.<br>";
}

// Check if the cancel_date column exists
$check_column_query = "SHOW COLUMNS FROM member_packages LIKE 'cancel_date'";
$check_column_result = mysqli_query($con, $check_column_query);

if (mysqli_num_rows($check_column_result) == 0) {
    // The column doesn't exist, so add it
    $add_column_query = "ALTER TABLE member_packages ADD COLUMN cancel_date DATETIME NULL AFTER cancel_reason";
    $add_column_result = mysqli_query($con, $add_column_query);
    
    if ($add_column_result) {
        echo "Successfully added cancel_date column to member_packages table.<br>";
    } else {
        echo "Error adding cancel_date column: " . mysqli_error($con) . "<br>";
    }
} else {
    echo "cancel_date column already exists in member_packages table.<br>";
}

echo "Done.";
?>
