<?php

// Tên website
define('SITE_NAME', 'Convenience Store');

// URL gốc của project
define(
    'BASE_URL',
    'http://localhost/website-cua-hang-tien-loi/'
);

// Khởi động session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}