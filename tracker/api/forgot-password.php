<?php
require 'config.php';

$data  = json_decode(file_get_contents('php://input'), true);
$email = strtolower(trim($data['email'] ?? ''));

if (!$email) {
    jsonResponse(['error' => 'Email is required'], 400);
}

// Verify the account exists
$stmt = $pdo->prepare('SELECT id_user FROM user WHERE email = ?');
$stmt->execute([$email]);
if (!$stmt->fetch()) {
    // Return generic message to avoid email enumeration
    jsonResponse(['message' => 'If that email is registered, a new password has been sent.']);
}

// Generate a secure random password: 12 chars with upper, lower, digit, symbol
function generatePassword(int $length = 12): string {
    $upper   = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    $lower   = 'abcdefghjkmnpqrstuvwxyz';
    $digits  = '23456789';
    $symbols = '@#$!%*?&';
    $all     = $upper . $lower . $digits . $symbols;

    $password  = $upper[random_int(0, strlen($upper) - 1)];
    $password .= $lower[random_int(0, strlen($lower) - 1)];
    $password .= $digits[random_int(0, strlen($digits) - 1)];
    $password .= $symbols[random_int(0, strlen($symbols) - 1)];

    for ($i = 4; $i < $length; $i++) {
        $password .= $all[random_int(0, strlen($all) - 1)];
    }

    return str_shuffle($password);
}

$newPassword = generatePassword(12);
$hash        = password_hash($newPassword, PASSWORD_DEFAULT);

// Update the database
$stmt = $pdo->prepare('UPDATE user SET password = ? WHERE email = ?');
$stmt->execute([$hash, $email]);

// Send the reset email directly via PHPMailer (SMTP)
require __DIR__ . '/PHPMailer/Exception.php';
require __DIR__ . '/PHPMailer/SMTP.php';
require __DIR__ . '/PHPMailer/PHPMailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = ''; //setup email address to use for forgot-password feature
    $mail->Password   = ''; // Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('projecttestingmail6@gmail.com', 'ExpenseTracker');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'ExpenseTracker - Your Password Has Been Reset';
    $mail->Body    = '<html><body style="font-family: Arial, sans-serif; color: #333; max-width: 480px; margin: auto;">
      <div style="background: linear-gradient(135deg, #667eea, #764ba2); padding: 30px; border-radius: 10px 10px 0 0; text-align: center;">
        <h1 style="color: #fff; margin: 0; font-size: 24px;">ExpenseTracker</h1>
      </div>
      <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e0e0e0;">
        <h2 style="margin-top: 0;">Password Reset</h2>
        <p>Your password has been reset. Use the temporary password below to log in, then change it from your profile settings.</p>
        <div style="background: #fff; border: 2px dashed #764ba2; border-radius: 8px; padding: 16px; text-align: center; margin: 20px 0;">
          <span style="font-size: 22px; font-weight: bold; letter-spacing: 2px; color: #764ba2;">' . htmlspecialchars($newPassword) . '</span>
        </div>
        <p style="color: #777; font-size: 13px;">If you did not request a password reset, please contact support immediately.</p>
      </div>
    </body></html>';
    $mail->AltBody = "ExpenseTracker - Password Reset\n\nYour new temporary password: $newPassword\n\nPlease log in and change your password from your profile settings.";

    $mail->send();
} catch (PHPMailerException $e) {
    error_log("Email send failed: " . $mail->ErrorInfo);
}

jsonResponse(['message' => 'If that email is registered, a new password has been sent.']);
