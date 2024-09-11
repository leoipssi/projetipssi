<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return User::findById($_SESSION['user_id']);
    }
    return null;
}

function hasPermission($action) {
    $user = getCurrentUser();
    if ($user) {
        return $user->hasPermission($action);
    }
    return false;
}

function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

?>
