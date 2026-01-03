<?php
// Manual PHPMailer inclusion
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $message = htmlspecialchars(trim($_POST['message']));

    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Your hosting SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'himanshuswami2810@gmail.com'; // Your email
        $mail->Password = 'nhxl bqsl iefi iypd'; // Your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('himanshuswami2810@gmail.com', 'DD Associate');
        $mail->addAddress('himanshuswami2810@gmail.com');
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Message from $name";
        $mail->Body = "
            <html>
            <head>
                <title>New Contact Message</title>
            </head>
            <body style='font-family: Arial, sans-serif;'>
                <h2 style='color:#1e40af;'>New Contact Form Submission</h2>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Phone:</strong> {$phone}</p>
                <p><strong>Message:</strong><br>{$message}</p>
            </body>
            </html>
        ";

        // Optional: Plain text version
        $mail->AltBody = "Name: $name\nEmail: $email\nPhone: $phone\nMessage: $message";

        $mail->send();
        echo "<script>
            alert('Your message has been sent successfully!');
            window.location.href = 'contact.php';
        </script>";
    } catch (Exception $e) {
        echo "<script>
            alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');
            window.location.href = 'contact.php';
        </script>";
    }
} else {
    header("Location: contact.php");
    exit;
}
?>