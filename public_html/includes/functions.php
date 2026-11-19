<?php

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../pages/login.php");
        exit();
    }
}

function base_url($suffix = '') {
    // 1. Get the protocol (http or https)
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    
    // 2. Get the hostname (e.g., jonahk1225.com)
    $host = $_SERVER['HTTP_HOST'];
    
    // 3. Get the path to the current folder (e.g., /public_html or /)
    // The SCRIPT_NAME environment variable helps identify the folder structure.
    $script_name = $_SERVER['SCRIPT_NAME'];

    // We need to find the base directory path (e.g., /public_html/)
    // This looks for the location of the index.php or the root file.
    $path_parts = pathinfo($script_name);
    $base_dir = rtrim($path_parts['dirname'], '/') . '/';

    // In your specific case, we know the path is /public_html/
    $base_url = $protocol . $host . '/public_html/';
    
    // Clean up double slashes and add the suffix
    $clean_url = str_replace('//', '/', $base_url . $suffix);
    
    return $clean_url;
}