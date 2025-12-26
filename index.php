<?php
session_start();

/*
|--------------------------------------------------------------------------
| AUTO NAVIGATION
|--------------------------------------------------------------------------
| If user is already logged in → redirect by role
| If not logged in → redirect to login page
*/

if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {

    // Admin user
    if ($_SESSION['user_role'] === 'admin') {
        header("Location: admin/index.php");
        exit;
    }

    // Normal user
    if ($_SESSION['user_role'] === 'user') {
        header("Location: user/index.php");
        exit;
    }

}

// Not logged in → go to login
header("Location: login.php");
exit;
