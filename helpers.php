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
