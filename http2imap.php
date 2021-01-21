<?php

include_once 'http2imap.config.php';

function response($payload) {
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

function errorResponse($error) {
    response([
        'success' => false,
        'error' => $error,
    ]);
}

$mailbox = isset($_POST['mailbox']) ? $_POST['mailbox'] : null;
$user = isset($_POST['user']) ? $_POST['user'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$subject = isset($_POST['subject']) ? $_POST['subject'] : null;
$token = isset($_POST['token']) ? $_POST['token'] : null;


if (null === $mailbox || null === $user || null === $password || null === $token || null === $subject) {
    errorResponse('Wrong parameter values.');
}

if ($token !== AUTH_TOKEN) {
    errorResponse('Wrong auth token.');
}

$mailbox = imap_open($mailbox,$user,$password);

if (false === $mailbox) {
    errorResponse(imap_last_error());
}

$MC = imap_check($mailbox);

$messageUids = imap_search($mailbox, 'SUBJECT "' . $subject . '" SINCE "' . (new DateTimeImmutable('3 days ago'))->format('j F Y') . '"', SE_UID);

$result = [
    'success' => true,
    'messages' => [],
];

foreach ($messageUids as $messageUid) {
    $result['messages'][] = imap_fetchbody($mailbox, $messageUid, "");
}

imap_close($mailbox);

response($result);