<?php
require_once './session.php';
require_once './db.php';

$conn = getDB();
$error = "";
$success = "";

$name = $email = $contact = $address = $user_type = "";

/* =====================================================
   HANDLE FORM SUBMISSION
===================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name      = trim($_POST['name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $contact   = trim($_POST['contact'] ?? '');
    $password  = $_POST['password'] ?? '';
    $address   = trim($_POST['address'] ?? '');
    $user_type = $_POST['user_type'] ?? '';

    /* ---------------- VALIDATION ---------------- */
    if (empty($name) || empty($email) || empty($contact) || empty($password) || empty($address) || empty($user_type)) {
        $error = "All fields are required.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    }
    elseif (!preg_match('/^\+?[0-9]{7,15}$/', $contact)) {
        $error = "Invalid contact number.";
    }
    elseif (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password)
    ) {
        $error = "Password must contain uppercase, lowercase and number (min 8 chars).";
    }
    elseif (!in_array($user_type, ['event', 'static'])) {
        $error = "Invalid user type.";
    }
    else {

        /* ---------- CHECK DUPLICATE EMAIL ---------- */
        $check = $conn->prepare("SELECT emp_id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already registered.";
        } else {

            /* ---------- FORCE BACKEND VALUES ---------- */
            $role      = 'user';
            $is_active = 1;

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            /* ---------- INSERT USER ---------- */
            $stmt = $conn->prepare("
                INSERT INTO users
                (name, email, contact, password, address, role, user_type, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "sssssssi",
                $name,
                $email,
                $contact,
                $hashedPassword,
                $address,
                $role,
                $user_type,
                $is_active
            );

            if ($stmt->execute()) {
                $success = "Registration successful. Redirecting to login...";
            } else {
                $error = "Registration failed: " . $stmt->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded shadow w-full max-w-md">

    <h2 class="text-2xl font-bold text-center mb-6">Register</h2>

    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 p-3 mb-4 rounded text-center">
            <?= htmlspecialchars($success) ?><br>
            <span class="text-sm">Redirecting in <span id="countdown">3</span> seconds...</span>
        </div>

        <script>
            let s = 3;
            const el = document.getElementById('countdown');
            const t = setInterval(() => {
                s--;
                el.textContent = s;
                if (s === 0) {
                    clearInterval(t);
                    window.location.href = "login.php";
                }
            }, 1000);
        </script>
    <?php endif; ?>

    <form method="POST" class="space-y-4">

        <input type="text" name="name" placeholder="Full Name" required
            value="<?= htmlspecialchars($name) ?>"
            class="w-full p-3 border rounded">

        <input type="email" name="email" placeholder="Email" required
            value="<?= htmlspecialchars($email) ?>"
            class="w-full p-3 border rounded">

        <input type="text" name="contact" placeholder="Contact Number" required
            value="<?= htmlspecialchars($contact) ?>"
            class="w-full p-3 border rounded">

        <input type="password" name="password" placeholder="Password" required
            class="w-full p-3 border rounded">

        <textarea name="address" placeholder="Address" required
            class="w-full p-3 border rounded"><?= htmlspecialchars($address) ?></textarea>

        <select name="user_type" required class="w-full p-3 border rounded">
            <option value="">Select User Type</option>
            <option value="event" <?= ($user_type === 'event') ? 'selected' : '' ?>>Event</option>
            <option value="static" <?= ($user_type === 'static') ? 'selected' : '' ?>>Static</option>
        </select>

        <button class="w-full bg-blue-600 text-white p-3 rounded hover:bg-blue-700">
            Register
        </button>
    </form>

    <p class="text-center text-gray-600 mt-4">
        Already have an account?
        <a href="./login.php" class="text-blue-600 hover:underline">Login</a>
    </p>

</div>

</body>
</html>
