<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once str_replace('/', DIRECTORY_SEPARATOR, 'file-utilities.php');
require FileUtils::normalizeFilePath(__DIR__ . "/../../vendor/autoload.php");
include_once FileUtils::normalizeFilePath('error-reporting.php');

// CONFIGURATION

// Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

// Enable verbose debug output. Uncomment if needed—debugging purposes
$mail->SMTPDebug = SMTP::DEBUG_SERVER;

// Send using SMTP
$mail->isSMTP();

// Enable SMTP authentication
$mail->SMTPAuth = true;

// SMTP server
$mail->Host = "smtp.gmail.com";

// TLS encryption — secure than SSL
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

// TCP Port
$mail->Port = 587;

// SMTP username
$mail->Username = "carltabuso2275@gmail.com";

// SMTP password
$mail->Password = "ahjswewzzwhirmbb";

// Set email format to HTML
$mail->isHtml(true);

return $mail;