<?php
// List of files to fix
$files = [
    'admin/add-equipment-req.php',
    'admin/add-member-req.php',
    'admin/added-staffs.php',
    'admin/announcement.php',
    'admin/ATTENDANCE-BACKUP.php',
    'admin/edit-equipment-req.php',
    'admin/edit-equipment.php',
    'admin/edit-member-req.php',
    'admin/edit-member.php',
    'admin/edit-staff-form.php',
    'admin/equipment-entry.php',
    'admin/manage-announcement.php',
    'admin/member-entry.php',
    'admin/member-status.php',
    'admin/members.php',
    'admin/payment.php',
    'admin/post-announcement.php',
    'admin/remove-equipment.php',
    'admin/remove-member.php',
    'admin/search-result.php',
    'admin/services-report.php',
    'admin/staffs-entry.php',
    'admin/staffs-new.php',
    'admin/staffs-old.php',
    'admin/staffs.php',
    'admin/update-progress.php',
    'admin/user-payment.php',
    'admin/userpay.php',
    'admin/userprogress-req.php',
    'admin/view-member-report.php',
    'admin/view-progress-report.php'
];

// Process each file
foreach ($files as $file) {
    if (file_exists($file)) {
        // Read the file content
        $content = file_get_contents($file);
        
        // Replace the incorrect path
        $content = str_replace(
            "<?php include '../includes/footer.php'; ?>",
            "<?php include 'includes/footer.php'; ?>",
            $content
        );
        
        // Write the updated content back to the file
        file_put_contents($file, $content);
        
        echo "Fixed: $file\n";
    } else {
        echo "File not found: $file\n";
    }
}

echo "All files processed.\n";
?>
