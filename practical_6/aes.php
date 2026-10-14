<?php
// AES Encryption and Decryption using PHP

$encryptedText = "";
$decryptedText = "";
$message = "";
$error = "";

// Generate a 256-bit AES key
$key = hash('sha256', 'MySecretKey123', true);

// Generate a random Initialization Vector
$iv = openssl_random_pseudo_bytes(
    openssl_cipher_iv_length('AES-256-CBC')
);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $message = $_POST["message"] ?? "";

    if (empty($message)) {
        $error = "Please enter a message.";
    } else {

        // AES Encryption
        $encrypted = openssl_encrypt(
            $message,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        // Convert encrypted data to Base64
        $encryptedText = base64_encode($encrypted);

        // Convert Base64 back to binary before decryption
        $encryptedData = base64_decode($encryptedText);

        // AES Decryption
        $decrypted = openssl_decrypt(
            $encryptedData,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decrypted !== false) {
            $decryptedText = $decrypted;
        } else {
            $error = "Decryption failed.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>AES Encryption</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .box { max-width: 700px; margin: 30px auto; background: #fff; padding: 25px;
               box-shadow: 0 0 8px rgba(0,0,0,.2); }
        textarea { width: 100%; height: 90px; }
        button { background: #333; color: #fff; border: 0; padding: 10px 18px;
                 border-radius: 4px; margin-top: 10px; cursor: pointer; }
        .out { background: #ececec; padding: 15px; margin-top: 20px; }
        code { font-size: 12px; word-break: break-all; }
    </style>
</head>
<body>
<div class="box">
    <h2>AES Encryption and Decryption</h2>
    <form method="post">
        <label><b>Enter Message:</b></label><br>
        <textarea name="message" placeholder="Enter message to encrypt..."></textarea><br>
        <button type="submit">Encrypt &amp; Decrypt</button>
    </form>

    <?php if ($error): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($encryptedText): ?>
        <div class="out">
            <h3>Original Message</h3><p><?= htmlspecialchars($message) ?></p>
            <h3>AES Algorithm</h3><p>AES-256-CBC</p>
            <h3>Secret Key</h3><code><?= base64_encode($key) ?></code>
            <h3>Initialization Vector (IV)</h3><code><?= base64_encode($iv) ?></code>
            <h3>Encrypted Message</h3><code><?= $encryptedText ?></code>
            <h3>Decrypted Message</h3><p><?= htmlspecialchars($decryptedText) ?></p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
