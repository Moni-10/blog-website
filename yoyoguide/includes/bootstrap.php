<?php
define('APP_BOOTSTRAPPED', true);
if (!defined('CSP_NONCE')) {
    try {
        define('CSP_NONCE', bin2hex(random_bytes(16)));
    } catch (Throwable $e) {
        define('CSP_NONCE', hash('sha256', uniqid('', true)));
    }
}
require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';
require __DIR__ . '/blog-card.php';
send_security_headers();
