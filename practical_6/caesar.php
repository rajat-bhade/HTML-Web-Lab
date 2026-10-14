<?php
// Caesar Cipher Encryption and Decryption using PHP

$result = "";
$label = "";
$text = "";
$shift = "";

function caesar($str, $shift) {
    $shift = ((int)$shift % 26 + 26) % 26;
    $out = "";
    foreach (str_split($str) as $ch) {
        if (ctype_upper($ch)) {
            $out .= chr((ord($ch) - 65 + $shift) % 26 + 65);
        } elseif (ctype_lower($ch)) {
            $out .= chr((ord($ch) - 97 + $shift) % 26 + 97);
        } else {
            $out .= $ch;
        }
    }
    return $out;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $text = $_POST["text"] ?? "";
    $shift = $_POST["shift"] ?? 0;

    if (isset($_POST["encrypt"])) {
        $result = caesar($text, $shift);
        $label = "Encrypted Text:";
    } elseif (isset($_POST["decrypt"])) {
        $result = caesar($text, -(int)$shift);
        $label = "Decrypted Text:";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Caesar Cipher</title>
</head>
<body>
    <h1>Caesar Cipher Encryption and Decryption</h1>
    <form method="post">
        Enter Text:<br>
        <input type="text" name="text" value="<?= htmlspecialchars($text) ?>" required><br><br>
        Enter Shift:<br>
        <input type="number" name="shift" value="<?= htmlspecialchars($shift) ?>" required><br><br>
        <button type="submit" name="encrypt">Encrypt</button>
        <button type="submit" name="decrypt">Decrypt</button>
    </form>

    <?php if ($label): ?>
        <h2><?= $label ?></h2>
        <p><?= htmlspecialchars($result) ?></p>
    <?php endif; ?>
</body>
</html>
