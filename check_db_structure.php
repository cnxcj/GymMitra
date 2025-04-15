<?php
// Connect to the database
include 'admin/dbcon.php';

// Function to check if a table exists
function tableExists($tableName, $connection) {
    $result = mysqli_query($connection, "SHOW TABLES LIKE '$tableName'");
    return mysqli_num_rows($result) > 0;
}

// Function to get table structure
function getTableStructure($tableName, $connection) {
    $result = mysqli_query($connection, "DESCRIBE $tableName");
    $structure = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $structure[] = $row;
        }
    }
    return $structure;
}

// Check existing tables
$tables = array(
    'members',
    'packages',
    'member_packages',
    'payments',
    'pending_memberships',
    'active_memberships',
    'pending_cancellations',
    'support_tickets'
);

echo "CHECKING DATABASE STRUCTURE:\n\n";

foreach ($tables as $table) {
    if (tableExists($table, $con)) {
        echo "Table '$table' exists.\n";
        $structure = getTableStructure($table, $con);
        echo "Structure:\n";
        foreach ($structure as $column) {
            echo "  - " . $column['Field'] . " (" . $column['Type'] . ")" .
                 ($column['Null'] == 'NO' ? " NOT NULL" : "") .
                 ($column['Key'] == 'PRI' ? " PRIMARY KEY" : "") .
                 ($column['Default'] ? " DEFAULT '" . $column['Default'] . "'" : "") .
                 ($column['Extra'] ? " " . $column['Extra'] : "") . "\n";
        }
    } else {
        echo "Table '$table' does not exist.\n";
    }
    echo "\n";
}

// Close the connection
mysqli_close($con);
?>
