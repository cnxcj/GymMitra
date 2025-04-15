<?php
$con = mysqli_connect("localhost","root","","gymmitra");

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}

// Create member_packages table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS member_packages (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    member_id INT(11) NOT NULL,
    package_id INT(11) NOT NULL,
    package_name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    duration INT(11) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('active', 'expired', 'cancelled') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(user_id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE
)";

if (mysqli_query($con, $sql)) {
    echo "Table member_packages created successfully<br>";
} else {
    echo "Error creating table: " . mysqli_error($con) . "<br>";
}

// Create payments table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS payments (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    member_id INT(11) NOT NULL,
    member_package_id INT(11) NULL,
    payment_date DATETIME NOT NULL,
    payment_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    receipt_number VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(user_id) ON DELETE CASCADE,
    FOREIGN KEY (member_package_id) REFERENCES member_packages(id) ON DELETE SET NULL
)";

if (mysqli_query($con, $sql)) {
    echo "Table payments created successfully<br>";
} else {
    echo "Error creating table: " . mysqli_error($con) . "<br>";
}

// Migrate existing member data to member_packages
$sql = "SELECT user_id, services, amount, plan, dor FROM members WHERE services != ''";
$result = mysqli_query($con, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $member_id = $row['user_id'];
        $package_name = $row['services'];
        $amount = $row['amount'];
        $duration = $row['plan'];
        $start_date = $row['dor'];
        
        // Calculate end date
        $end_date = date('Y-m-d', strtotime($start_date . ' + ' . $duration . ' months'));
        
        // Get package_id
        $package_query = "SELECT id FROM packages WHERE package_name = '$package_name' LIMIT 1";
        $package_result = mysqli_query($con, $package_query);
        $package_id = 0;
        
        if ($package_result && mysqli_num_rows($package_result) > 0) {
            $package_row = mysqli_fetch_assoc($package_result);
            $package_id = $package_row['id'];
        }
        
        // Determine status
        $status = 'expired';
        if (strtotime($end_date) >= strtotime('now')) {
            $status = 'active';
        }
        
        // Insert into member_packages
        $insert_sql = "INSERT INTO member_packages (member_id, package_id, package_name, start_date, end_date, duration, amount, status) 
                      VALUES ('$member_id', '$package_id', '$package_name', '$start_date', '$end_date', '$duration', '$amount', '$status')";
        
        if (mysqli_query($con, $insert_sql)) {
            echo "Migrated package data for member ID: $member_id<br>";
            
            // Create a payment record
            $package_id = mysqli_insert_id($con);
            $receipt_number = 'GMS-' . date('Ymd', strtotime($start_date)) . '-' . $member_id;
            
            $payment_sql = "INSERT INTO payments (member_id, member_package_id, payment_date, payment_amount, payment_method, receipt_number) 
                           VALUES ('$member_id', '$package_id', '$start_date', '$amount', 'cash', '$receipt_number')";
            
            if (mysqli_query($con, $payment_sql)) {
                echo "Created payment record for member ID: $member_id<br>";
            }
        } else {
            echo "Error migrating data: " . mysqli_error($con) . "<br>";
        }
    }
}

mysqli_close($con);
echo "Database setup completed!";
?>
