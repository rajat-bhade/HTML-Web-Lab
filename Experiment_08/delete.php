<?php
require_once "connection.php";

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: view.php");
    exit;
}

if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    die("Invalid request (CSRF check failed).");
}

$id = (int)($_POST['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        set_flash('success', 'Student record deleted successfully.');
    } else {
        set_flash('error', 'Record could not be deleted.');
    }
    $stmt->close();
} else {
    set_flash('error', 'Invalid student ID.');
}

header("Location: view.php");
exit;
?>
