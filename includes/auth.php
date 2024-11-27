<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: index.php');
        exit();
    }
}

function getUserData($db, $userId) {
    $stmt = $db->prepare("SELECT id, username, email, full_name, phone, role FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
} 