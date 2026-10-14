<?php
// AES-256-GCM encryption and decryption
//
// WARNING: This key is for classroom demonstration only.
// In a real application, generate a random key and store it
// outside the source code (environment variable / secrets file).
// If the key is lost, existing records can no longer be decrypted.

define('CIPHER_METHOD', 'aes-256-gcm');
define('SECRET_KEY', hash('sha256', 'DemoClassroomSecretKey@2026', true)); // 32 bytes = 256 bits

const IV_LENGTH  = 12;  // recommended IV size for GCM
const TAG_LENGTH = 16;  // authentication tag size

function encryptData($plainText)
{
    $iv  = random_bytes(IV_LENGTH);
    $tag = '';

    $cipherText = openssl_encrypt(
        $plainText,
        CIPHER_METHOD,
        SECRET_KEY,
        OPENSSL_RAW_DATA,
        $iv,
        $tag,
        '',
        TAG_LENGTH
    );

    if ($cipherText === false) {
        return false;
    }

    // Store IV + tag + ciphertext together as Base64 text
    return base64_encode($iv . $tag . $cipherText);
}

function decryptData($encryptedText)
{
    $raw = base64_decode($encryptedText, true);

    if ($raw === false || strlen($raw) <= IV_LENGTH + TAG_LENGTH) {
        return false;
    }

    $iv         = substr($raw, 0, IV_LENGTH);
    $tag        = substr($raw, IV_LENGTH, TAG_LENGTH);
    $cipherText = substr($raw, IV_LENGTH + TAG_LENGTH);

    // Returns false if the key is wrong or the data was modified
    return openssl_decrypt(
        $cipherText,
        CIPHER_METHOD,
        SECRET_KEY,
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );
}
?>
