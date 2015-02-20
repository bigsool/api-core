<?php

$to = "logs@bigsool.com";
$subject = "There is some Archiweb errors !";

$headers = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

$date = date('Y-m-j', time() - (60 * 60 * 24));
$file = __DIR__ . "/../logs/errors-" . $date . ".log";

if (file_exists($file) && is_file($file)) {
    $file = fopen($file, "r");
    $message = "<html><body><h3>Archiweb errors of the $date :</h3>";
    while (($line = fgets($file)) !== false) {
        $message .= "<h4>" . $line . "</h4>";
    }
    fclose($file);

    $message .= "</body></html>";

    mail($to, $subject, $message, $headers);
}
