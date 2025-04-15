<?php
session_start();

// Check if purchase was successful
if (!isset($_SESSION['purchase_success']) || $_SESSION['purchase_success'] !== true) {
    header("Location: index.php");
    exit;
}

// Get receipt data from session
$member_id = $_SESSION['member_id'];
$fullname = $_SESSION['fullname'];
$username = $_SESSION['username'];
$package_name = $_SESSION['package_name'];
$duration = $_SESSION['duration'];
$amount = $_SESSION['amount'];
$payment_date = $_SESSION['payment_date'];
$payment_method = $_SESSION['payment_method'];
$pending_approval = isset($_SESSION['pending_approval']) ? $_SESSION['pending_approval'] : false;

// Generate receipt number
$receipt_number = 'GMS-' . date('Ymd') . '-' . $member_id;

// Clear session variables
unset($_SESSION['purchase_success']);
unset($_SESSION['member_id']);
unset($_SESSION['fullname']);
unset($_SESSION['username']);
unset($_SESSION['package_name']);
unset($_SESSION['duration']);
unset($_SESSION['amount']);
unset($_SESSION['payment_date']);
unset($_SESSION['payment_method']);
unset($_SESSION['pending_approval']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment Receipt - GymMitra</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link href="font-awesome/css/fontawesome.css" rel="stylesheet" />
    <link href="font-awesome/css/all.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        .receipt-container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .receipt-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 30px;
        }
        .receipt-header h2 {
            color: #ff5722;
            margin-bottom: 5px;
        }
        .receipt-header p {
            color: #777;
            font-size: 16px;
        }
        .receipt-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .receipt-details .left, .receipt-details .right {
            flex: 1;
        }
        .receipt-details h4 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .receipt-details p {
            margin-bottom: 8px;
            color: #555;
        }
        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .receipt-table th {
            background-color: #f9f9f9;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #eee;
        }
        .receipt-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        .receipt-table .total-row td {
            font-weight: bold;
            border-top: 2px solid #eee;
            border-bottom: none;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #777;
        }
        .receipt-footer p {
            margin-bottom: 5px;
        }
        .btn-print {
            background-color: #ff5722;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-print:hover {
            background-color: #e64a19;
        }
        .btn-home {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
            margin-left: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-home:hover {
            background-color: #555;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                background: #fff;
            }
            .receipt-container {
                box-shadow: none;
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h2>Payment Receipt</h2>
            <p>Thank you for your purchase!</p>
        </div>

        <div class="receipt-details">
            <div class="left">
                <h4>GymMitra</h4>
                <p>Near Zudio, Bhoirwadi, Kalyan West</p>
                <p>Phone: 7039895267</p>
                <p>Email: gymmitra@gmail.com</p>
            </div>
            <div class="right">
                <h4>Receipt Information</h4>
                <p><strong>Receipt #:</strong> <?php echo $receipt_number; ?></p>
                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($payment_date)); ?></p>
                <p><strong>Time:</strong> <?php echo date('g:i A', strtotime($payment_date)); ?></p>
            </div>
        </div>

        <div class="receipt-details">
            <div class="left">
                <h4>Member Information</h4>
                <p><strong>Name:</strong> <?php echo $fullname; ?></p>
                <p><strong>Username:</strong> <?php echo $username; ?></p>
                <p><strong>Member ID:</strong> <?php echo $member_id; ?></p>
            </div>
            <div class="right">
                <h4>Payment Information</h4>
                <p><strong>Payment Method:</strong> <?php echo ucfirst($payment_method); ?></p>
                <p><strong>Payment Status:</strong> <span style="color: green;">Paid</span></p>
                <p><strong>Membership Status:</strong> <span style="color: <?php echo $pending_approval ? 'orange' : 'green'; ?>"><?php echo $pending_approval ? 'Pending Admin Approval' : 'Active'; ?></span></p>
            </div>
        </div>

        <table class="receipt-table">
            <thead>
                <tr>
                    <th>Package</th>
                    <th>Duration</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $package_name; ?></td>
                    <td><?php echo $duration; ?> Month<?php echo $duration > 1 ? 's' : ''; ?></td>
                    <td>₹<?php echo number_format($amount); ?></td>
                </tr>
                <tr class="total-row">
                    <td colspan="2" style="text-align: right;">Total Amount:</td>
                    <td>₹<?php echo number_format($amount); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="receipt-footer">
            <?php if ($pending_approval): ?>
            <div style="background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <h4 style="margin-top: 0;"><i class="fas fa-exclamation-triangle"></i> Membership Pending Approval</h4>
                <p>Your membership purchase is currently pending approval from our admin team. You will receive access to the gym facilities once your membership is approved. This usually takes 1-2 business days.</p>
                <p>You can check the status of your membership by logging into your account.</p>
            </div>
            <?php endif; ?>
            <p>This is a computer-generated receipt and does not require a signature.</p>
            <p>For any queries, please contact us at gymmitra@gmail.com or call 7039895267.</p>
            <div class="no-print">
                <button class="btn-print" onclick="window.print();">Print Receipt</button>
                <a href="index.php"><button class="btn-home">Back to Home</button></a>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
