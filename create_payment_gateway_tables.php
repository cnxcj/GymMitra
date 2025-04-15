<?php
// Connect to the database
include 'admin/dbcon.php';

// Create payment_transactions table
$sql = "CREATE TABLE IF NOT EXISTS payment_transactions (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    member_id INT(11) NOT NULL,
    member_package_id INT(11) NULL,
    order_id VARCHAR(255) NOT NULL,
    payment_id VARCHAR(255) NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'INR',
    payment_method VARCHAR(50) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    response_data TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(user_id) ON DELETE CASCADE,
    FOREIGN KEY (member_package_id) REFERENCES member_packages(id) ON DELETE SET NULL
)";

if (mysqli_query($con, $sql)) {
    echo "Table payment_transactions created successfully<br>";
} else {
    echo "Error creating table: " . mysqli_error($con) . "<br>";
}

// Add payment_transaction_id column to payments table if it doesn't exist
$check_column = "SHOW COLUMNS FROM payments LIKE 'payment_transaction_id'";
$result = mysqli_query($con, $check_column);

if (mysqli_num_rows($result) == 0) {
    $alter_sql = "ALTER TABLE payments ADD COLUMN payment_transaction_id INT(11) NULL AFTER receipt_number, 
                 ADD FOREIGN KEY (payment_transaction_id) REFERENCES payment_transactions(id) ON DELETE SET NULL";
    
    if (mysqli_query($con, $alter_sql)) {
        echo "Column payment_transaction_id added to payments table successfully<br>";
    } else {
        echo "Error adding column: " . mysqli_error($con) . "<br>";
    }
}

// Add payment_gateway column to payments table if it doesn't exist
$check_column = "SHOW COLUMNS FROM payments LIKE 'payment_gateway'";
$result = mysqli_query($con, $check_column);

if (mysqli_num_rows($result) == 0) {
    $alter_sql = "ALTER TABLE payments ADD COLUMN payment_gateway VARCHAR(50) NULL AFTER payment_method";
    
    if (mysqli_query($con, $alter_sql)) {
        echo "Column payment_gateway added to payments table successfully<br>";
    } else {
        echo "Error adding column: " . mysqli_error($con) . "<br>";
    }
}

echo "Payment gateway tables setup completed!";
?>
