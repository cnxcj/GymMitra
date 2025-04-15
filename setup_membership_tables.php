<?php
// Connect to the database
include 'admin/dbcon.php';

// Function to create table if it doesn't exist
function createTableIfNotExists($tableName, $query, $connection) {
    $result = mysqli_query($connection, "SHOW TABLES LIKE '$tableName'");
    if (mysqli_num_rows($result) == 0) {
        if (mysqli_query($connection, $query)) {
            echo "Table '$tableName' created successfully.\n";
        } else {
            echo "Error creating table '$tableName': " . mysqli_error($connection) . "\n";
        }
    } else {
        echo "Table '$tableName' already exists.\n";
    }
}

// Create pending_memberships table
$pending_memberships_query = "CREATE TABLE pending_memberships (
    id INT(11) NOT NULL AUTO_INCREMENT,
    member_id INT(11) NOT NULL,
    package_id INT(11) NOT NULL,
    package_name VARCHAR(255) NOT NULL,
    duration INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    payment_date DATETIME NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (member_id) REFERENCES members(user_id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE
)";
createTableIfNotExists('pending_memberships', $pending_memberships_query, $con);

// Create active_memberships table
$active_memberships_query = "CREATE TABLE active_memberships (
    id INT(11) NOT NULL AUTO_INCREMENT,
    member_id INT(11) NOT NULL,
    package_id INT(11) NOT NULL,
    package_name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    duration INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('active', 'expired', 'cancelled') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (member_id) REFERENCES members(user_id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE
)";
createTableIfNotExists('active_memberships', $active_memberships_query, $con);

// Create pending_cancellations table
$pending_cancellations_query = "CREATE TABLE pending_cancellations (
    id INT(11) NOT NULL AUTO_INCREMENT,
    member_id INT(11) NOT NULL,
    membership_id INT(11) NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending', 'approved_with_refund', 'approved_without_refund', 'rejected') NOT NULL DEFAULT 'pending',
    refund_amount DECIMAL(10,2) DEFAULT NULL,
    request_date DATETIME NOT NULL,
    processed_date DATETIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (member_id) REFERENCES members(user_id) ON DELETE CASCADE,
    FOREIGN KEY (membership_id) REFERENCES active_memberships(id) ON DELETE CASCADE
)";
createTableIfNotExists('pending_cancellations', $pending_cancellations_query, $con);

// Create support_tickets table
$support_tickets_query = "CREATE TABLE support_tickets (
    id INT(11) NOT NULL AUTO_INCREMENT,
    member_id INT(11) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    related_to ENUM('membership_purchase', 'membership_cancellation', 'other') NOT NULL,
    related_id INT(11) DEFAULT NULL,
    status ENUM('open', 'closed') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (member_id) REFERENCES members(user_id) ON DELETE CASCADE
)";
createTableIfNotExists('support_tickets', $support_tickets_query, $con);

// Close the connection
mysqli_close($con);

echo "\nSetup completed successfully.";
?>
