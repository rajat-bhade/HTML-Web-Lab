<?php
require_once "connection.php";
require_once "encryption.php";

$errors = [];
$enrollment = $name = $email = $department = "";
$semester = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        die("Invalid request (CSRF check failed).");
    }

    $enrollment = trim($_POST['enrollment_no'] ?? '');
    $name       = trim($_POST['student_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $semester   = trim($_POST['semester'] ?? '');

    // Validation
    if ($enrollment === '') $errors[] = "Enrollment number is required.";
    if ($name === '')       $errors[] = "Student name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email address is required.";
    if ($department === '') $errors[] = "Department is required.";
    if (!ctype_digit($semester) || (int)$semester < 1 || (int)$semester > 8) {
        $errors[] = "Semester must be a number between 1 and 8.";
    }

    if (empty($errors)) {
        // Encrypt before storing
        $encEnrollment = encryptData($enrollment);
        $encName       = encryptData($name);
        $encEmail      = encryptData($email);

        if ($encEnrollment === false || $encName === false || $encEmail === false) {
            $errors[] = "Encryption failed.";
        } else {
            $sem  = (int)$semester;
            $stmt = $conn->prepare(
                "INSERT INTO students (enrollment_no, student_name, email, department, semester)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("ssssi", $encEnrollment, $encName, $encEmail, $department, $sem);

            if ($stmt->execute()) {
                set_flash('success', 'Student record added successfully.');
                header("Location: view.php");
                exit;
            } else {
                $errors[] = "Could not save the record.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Student Registration</h1>

    <?php foreach ($errors as $error): ?>
        <div class="message error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <form method="post" action="insert.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <label for="enrollment_no">Enrollment No.</label>
        <input type="text" id="enrollment_no" name="enrollment_no" value="<?= e($enrollment) ?>" required>

        <label for="student_name">Student Name</label>
        <input type="text" id="student_name" name="student_name" value="<?= e($name) ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required>

        <label for="department">Department</label>
        <input type="text" id="department" name="department" value="<?= e($department) ?>" required>

        <label for="semester">Semester (1-8)</label>
        <input type="number" id="semester" name="semester" min="1" max="8" value="<?= e($semester) ?>" required>

        <button type="submit">Save Student</button>
        <a class="link" href="view.php">View Records</a>
    </form>
</div>
</body>
</html>
