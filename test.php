<?php

require_once('ImapMailbox.php');

$attachmentsDir = 'attachments/';
$mailboxToMoveMessage = 'old'; // specific mailbox must exist
$hostname = '{imap.wp.pl:993/imap/ssl}INBOX';
$user = 'user@wp.pl';
$password = 'pass';

$inbox = new ImapMailbox($hostname, $user, $password);

try {
    $emails = $inbox->search('ALL');

    rsort($emails);

    foreach ($emails as $email) {
        foreach ($email->getAttachments() as $attachment) {
            $savePath = $attachmentsDir . $attachment->getFilename();
            file_put_contents($savePath, $attachment);

            if (!empty($mailboxToMoveMessage)) {
                try {
                    $email->moveToSpecificMailbox($mailboxToMoveMessage);
                } catch (Exception $e) {
                    echo $e->getMessage() . PHP_EOL;
                }
            }
        }
    }

    echo 'all done :)' . PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

