<?php

function send_smtp_mail(array $config, array $message): void
{
    $host = $config['smtp_host'] ?? '';
    $port = (int)($config['smtp_port'] ?? 587);
    $username = $config['smtp_username'] ?? '';
    $password = $config['smtp_password'] ?? '';
    $encryption = strtolower($config['smtp_encryption'] ?? 'tls');
    $timeout = (int)($config['smtp_timeout'] ?? 20);

    if ($host === '' || $username === '' || $password === '') {
        throw new RuntimeException('Missing SMTP configuration.');
    }

    $transport = $encryption === 'ssl' ? 'ssl://' . $host : $host;
    $socket = stream_socket_client($transport . ':' . $port, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);
    if (!is_resource($socket)) {
        throw new RuntimeException('SMTP connection failed: ' . $errstr);
    }

    stream_set_timeout($socket, $timeout);

    smtp_expect($socket, [220]);
    smtp_command($socket, 'EHLO localhost', [250]);

    if ($encryption === 'tls') {
        smtp_command($socket, 'STARTTLS', [220]);
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException('Unable to start TLS encryption.');
        }
        smtp_command($socket, 'EHLO localhost', [250]);
    }

    smtp_command($socket, 'AUTH LOGIN', [334]);
    smtp_command($socket, base64_encode($username), [334]);
    smtp_command($socket, base64_encode($password), [235]);
    smtp_command($socket, 'MAIL FROM:<' . $message['from_email'] . '>', [250]);
    smtp_command($socket, 'RCPT TO:<' . $message['to_email'] . '>', [250, 251]);
    smtp_command($socket, 'DATA', [354]);

    $boundary = 'b1_' . bin2hex(random_bytes(8));
    $headers = [
        'From: ' . smtp_format_address($message['from_email'], $message['from_name'] ?? ''),
        'To: ' . smtp_format_address($message['to_email'], $message['to_name'] ?? ''),
        'Reply-To: ' . ($message['reply_to'] ?? $message['from_email']),
        'Subject: =?UTF-8?B?' . base64_encode($message['subject']) . '?=',
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
    ];

    $body = "--{$boundary}\r\n"
        . "Content-Type: text/plain; charset=UTF-8\r\n"
        . "Content-Transfer-Encoding: 8bit\r\n\r\n"
        . ($message['text'] ?? strip_tags($message['html'] ?? '')) . "\r\n"
        . "--{$boundary}\r\n"
        . "Content-Type: text/html; charset=UTF-8\r\n"
        . "Content-Transfer-Encoding: 8bit\r\n\r\n"
        . ($message['html'] ?? nl2br(htmlspecialchars($message['text'] ?? ''))) . "\r\n"
        . "--{$boundary}--\r\n";

    $payload = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";
    fwrite($socket, $payload . "\r\n");
    smtp_expect($socket, [250]);
    smtp_command($socket, 'QUIT', [221]);
    fclose($socket);
}

function smtp_command($socket, string $command, array $expectedCodes): string
{
    fwrite($socket, $command . "\r\n");
    return smtp_expect($socket, $expectedCodes);
}

function smtp_expect($socket, array $expectedCodes): string
{
    $response = '';
    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;
        if (preg_match('/^\d{3} /', $line)) {
            break;
        }
    }

    $code = (int)substr($response, 0, 3);
    if (!in_array($code, $expectedCodes, true)) {
        throw new RuntimeException('Unexpected SMTP response: ' . trim($response));
    }

    return $response;
}

function smtp_format_address(string $email, string $name = ''): string
{
    if ($name === '') {
        return $email;
    }

    return '=?UTF-8?B?' . base64_encode($name) . '?= <' . $email . '>';
}
