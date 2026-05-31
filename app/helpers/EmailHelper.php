<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailHelper
{
    public static function send($to, $subject, $body, $options = [])
    {
        $config = require __DIR__ . '/../config/email.php';
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['username'];
            $mail->Password   = $config['password'];
            $mail->SMTPSecure = $config['encryption'];
            $mail->Port       = $config['port'];
            $mail->setFrom($config['from_email'], $config['from_name']);

            foreach ((array)$to as $email) {
                $mail->addAddress($email);
            }
            if (!empty($options['cc'])) {
                foreach ((array)$options['cc'] as $cc) {
                    $mail->addCC($cc);
                }
            }

            if (!empty($options['attachments'])) {
                foreach ($options['attachments'] as $file) {
                    if (file_exists($file['path'])) {
                        $mail->addAttachment(
                            $file['path'],
                            $file['name'] ?? basename($file['path'])
                        );
                    }
                }
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
