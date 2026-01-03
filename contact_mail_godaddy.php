<?php
// Include PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $message = htmlspecialchars(trim($_POST['message']));

    $mail = new PHPMailer(true);

    try {
        // ======================
        // GoDaddy SMTP Settings
        // ======================
        $mail->isSMTP();
        $mail->Host = "localhost";   // GoDaddy internal mail server
        $mail->SMTPAuth = false;     // No username/password required
        $mail->Port = 25;            // GoDaddy allowed port

        // Sender Email (MUST be your domain email)
        $mail->setFrom('info@yourdomain.com', 'DD Associate');

        // Where you want to receive messages
        $mail->addAddress('info@yourdomain.com');

        // Reply-To (user email)
        $mail->addReplyTo($email, $name);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Message from $name";
        $mail->Body = "
            <html>
            <body style='font-family: Arial;'>
                <h2 style='color:#1e40af;'>New Contact Form Submission</h2>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Phone:</strong> {$phone}</p>
                <p><strong>Message:</strong><br>{$message}</p>
            </body>
            </html>
        ";

        $mail->AltBody = "Name: $name\nEmail: $email\nPhone: $phone\nMessage:\n$message";

        $mail->send();

        echo "<script>
            alert('Your message has been sent successfully!');
            window.location.href = 'contact.php';
        </script>";

    } catch (Exception $e) {
        echo "<script>
            alert('Mail Error: {$mail->ErrorInfo}');
            window.location.href = 'contact.php';
        </script>";
    }

} else {
    header("Location: contact.php");
    exit;
}
?>
