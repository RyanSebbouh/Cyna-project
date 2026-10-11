<?php
require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

$mailerDsn = getenv('MAILER_DSN');
$recipient = getenv('MAIL_TEST_RECIPIENT');

if ($mailerDsn === false || $mailerDsn === '' || $recipient === false || $recipient === '') {
    fwrite(STDERR, "MAILER_DSN and MAIL_TEST_RECIPIENT must be configured.\n");
    exit(1);
}

$transport = Transport::fromDsn($mailerDsn);
$mailer = new Mailer($transport);

$email = (new Email())
    ->from('no-reply@demomailtrap.com')
    ->to($recipient)
    ->subject('Test Brevo')
    ->text('Test envoi Brevo !');

try {
    $mailer->send($email);
    echo "Email envoye !\n";
} catch (\Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
