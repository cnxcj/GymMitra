<?php
session_start();
include 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $package_id = $_POST['package_id'];
    $package_name = $_POST['package_name'];
    $duration = $_POST['duration'];
    $amount = $_POST['amount'];
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $payment_method = $_POST['payment_method'];

    // Calculate total amount
    $total_amount = $amount;

    // Current date for registration
    $dor = date('Y-m-d');

    // Check if username already exists
    $check_query = "SELECT COUNT(*) as count FROM members WHERE username = ?";
    $check_stmt = $con->prepare($check_query);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_row = $check_result->fetch_assoc();

    if ($check_row['count'] > 0) {
        // Username already exists
        echo "<script>alert('Username already exists. Please choose a different username.');</script>";
        echo "<script>window.location.href='index.php#pricing';</script>";
        exit;
    }

    // Insert new member
    $insert_query = "INSERT INTO members (fullname, username, gender, contact, address, email, dob, dor, services, amount, plan)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $con->prepare($insert_query);
    $insert_stmt->bind_param("sssssssssii", $fullname, $username, $gender, $contact, $address, $email, $dob, $dor, $package_name, $total_amount, $duration);

    if ($insert_stmt->execute()) {
        // Get the new member ID
        $member_id = $con->insert_id;

        // Insert into pending_memberships instead of directly activating
        $payment_date = date('Y-m-d H:i:s');
        $pending_query = "INSERT INTO pending_memberships (member_id, package_id, package_name, duration, price, payment_date, payment_method, status)
                         VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
        $pending_stmt = $con->prepare($pending_query);
        $pending_stmt->bind_param("iisidss", $member_id, $package_id, $package_name, $duration, $total_amount, $payment_date, $payment_method);

        if ($pending_stmt->execute()) {
            // Set session variables for receipt
            $_SESSION['purchase_success'] = true;
            $_SESSION['member_id'] = $member_id;
            $_SESSION['fullname'] = $fullname;
            $_SESSION['username'] = $username;
            $_SESSION['package_name'] = $package_name;
            $_SESSION['duration'] = $duration;
            $_SESSION['amount'] = $total_amount;
            $_SESSION['payment_date'] = $payment_date;
            $_SESSION['payment_method'] = $payment_method;
            $_SESSION['pending_approval'] = true; // Flag to indicate pending approval

            // Redirect to receipt page
            header("Location: receipt.php");
            exit;
        } else {
            // Error in creating pending membership
            echo "<script>alert('Error in processing your membership request. Please try again.');</script>";
            echo "<script>window.location.href='index.php#pricing';</script>";
        }

        $pending_stmt->close();
    } else {
        // Error in registration
        echo "<script>alert('Error in registration. Please try again.');</script>";
        echo "<script>window.location.href='index.php#pricing';</script>";
    }

    $insert_stmt->close();
} else {
    // Invalid request
    header("Location: index.php");
    exit;
}

$con->close();
?>
