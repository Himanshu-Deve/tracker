<?php
require_once './session.php';
require_once './db.php';

$conn = getDB();

/* ===============================
   ALREADY LOGGED IN → REDIRECT
================================ */
if (isset($_SESSION['user_id'])) {
    if (in_array($_SESSION['user_role'], ['admin', 'owner'])) {
        header("Location: ./admin/index.php");
    } else {
        header("Location: ./user/index.php");
    }
    exit;
}

$error = "";

/* ===============================
   LOGIN PROCESS
================================ */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("
        SELECT emp_id, name, password, role, is_active
        FROM users
        WHERE email = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {
        $error = "User not found";
    }
    elseif ((int)$user['is_active'] === 0) {
        // ❌ BLOCK INACTIVE USER
        $error = "Your account is inactive. Please contact administrator.";
    }
    elseif (password_verify($password, $user['password'])) {

        $_SESSION['user_id']   = $user['emp_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <script>
                localStorage.setItem("user_role", "<?= $user['role'] ?>");
                localStorage.setItem("user_name", "<?= htmlspecialchars($user['name']) ?>");
                localStorage.setItem("user_id", "<?= $user['emp_id'] ?>");
            </script>
        </head>
        </html>
        <?php

        if (in_array($user['role'], ['admin', 'owner'])) {
            header("Location: ./admin/index.php");
        } else {
            header("Location: ./user/index.php");
        }
        exit;

    } else {
        $error = "Invalid password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="bg-white p-8 rounded shadow w-96">

    <h2 class="text-2xl font-bold mb-4 text-center">Login</h2>

    <?php if (isset($_GET['inactive'])): ?>
        <p class="text-red-600 mb-3 text-center">
            Your account has been deactivated.
        </p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="text-red-600 mb-3 text-center">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <input
            type="email"
            name="email"
            required
            placeholder="Email"
            class="w-full p-3 border rounded mb-3"
        >

        <input
            type="password"
            name="password"
            required
            placeholder="Password"
            class="w-full p-3 border rounded mb-3"
        >

        <button class="w-full bg-blue-600 text-white p-3 rounded hover:bg-blue-700">
            Login
        </button>
    </form>

    <div class="text-center mt-4">
        <p class="text-gray-600">
            Don’t have an account?
            <a href="register.php" class="text-blue-600 font-semibold hover:underline">
                Register here
            </a>
        </p>
    </div>

</div>

</body>
</html>
