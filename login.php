<?php
require_once './session.php';
require_once './db.php';

$conn = getDB();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'owner') {
        header("Location: ./admin/index.php");
    } else {
        header("Location: ./user/index.php");
    }
    exit;
}


$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT emp_id, name, password, role FROM users WHERE email=? LIMIT 1"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {

        $user = $res->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['emp_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            ?>
            <!DOCTYPE html>
            <html>

            <head>
                <script>
                    // Save to localStorage
                    localStorage.setItem("user_role", "<?= $user['role'] ?>");
                    localStorage.setItem("user_name", "<?= $user['name'] ?>");
                    localStorage.setItem("user_id", "<?= $user['emp_id'] ?>");

                </script>
            </head>

            </html>
            <?php
            if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'owner') {
                header("Location: ./admin/index.php");
            } else {
                header("Location: ./user/index.php");
            }

            exit;


        } else {
            $error = "Invalid password";
        }

    } else {
        $error = "User not found";
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

        <?php if ($error): ?>
            <p class="text-red-600 mb-3 text-center"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" required placeholder="Email" class="w-full p-3 border rounded mb-3">

            <input type="password" name="password" required placeholder="Password"
                class="w-full p-3 border rounded mb-3">

            <button class="w-full bg-blue-600 text-white p-3 rounded">
                Login
            </button>
        </form>

        <!-- REGISTER LINK -->
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