<?php
require_once "connection.php";
require_once "encryption.php";

$flash  = get_flash();
$result = $conn->query("SELECT * FROM students ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container wide">
    <h1>Student Records</h1>

    <?php if ($flash): ?>
        <div class="message <?= e($flash['type']) ?>"><?= e($flash['text']) ?></div>
    <?php endif; ?>

    <a class="button-link" href="insert.php">+ Add New Student</a>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Enrollment No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Semester</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        $enrollment = decryptData($row['enrollment_no']);
                        $name       = decryptData($row['student_name']);
                        $email      = decryptData($row['email']);
                        $failed     = "[decryption failed]";
                    ?>
                    <tr>
                        <td><?= (int)$row['id'] ?></td>
                        <td><?= $enrollment !== false ? e($enrollment) : $failed ?></td>
                        <td><?= $name !== false ? e($name) : $failed ?></td>
                        <td><?= $email !== false ? e($email) : $failed ?></td>
                        <td><?= e($row['department']) ?></td>
                        <td><?= (int)$row['semester'] ?></td>
                        <td class="actions">
                            <a class="edit" href="update.php?id=<?= (int)$row['id'] ?>">Edit</a>
                            <form method="post" action="delete.php"
                                  onsubmit="return confirm('Delete this record?');">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                <button type="submit" class="delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">No records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
