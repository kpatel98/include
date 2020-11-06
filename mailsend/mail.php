<?php
        require_once 'mailer/PHPMailer.php';
        require_once 'mailer/Exception.php';
        require_once 'mailer/OAuth.php';
        require_once 'mailer/POP3.php';
        require_once 'mailer/SMTP.php';

        use mailer\PHPMailer\PHPMailer;
        use mailer\PHPMailer\SMTP;
        use mailer\PHPMailer\Exception;

    function mailSend($sendEmail, $sendName, $subject, $body){
        // Import PHPMailer classes into the global namespace
        // These must be at the top of your script, not inside a function
        // Instantiation and passing `true` enables exceptions

        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com.';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'xxx usename@gmail.com';                     // SMTP username
            $mail->Password   = 'xxx username_password';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port       = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('caretree1@gmail.com', 'Care Tree!!!');
            $mail->addAddress($sendEmail, $sendName);     // Add a recipient
            //$mail->addAddress('ellen@example.com');               // Name is optional
            $mail->addReplyTo('caretree1@gmail.com', 'Care Tree!!!');
        // $mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            // Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            $maildata=  'Message has been sent';
            return array('status'=>0,'message'=>'Mail send!...','mail'=>$maildata);
        } catch (Exception $e) {
            $maildata=  "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return array('status'=>0,'message'=>'Mail not send!...','mail'=>$maildata);
        }
    }
    if($_POST){
        $to = $_POST['email'];
        $name = $_POST['name'];
        $body = $_POST['body'];
        $subject = $_POST['subject'];
        // mailSend($to, $name, $subject, $body);
    }