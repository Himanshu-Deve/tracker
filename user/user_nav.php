<?php
require_once './../session.php';
require_once './../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}
?>

<nav class="bg-gray-900 text-white px-6 py-4 flex justify-between items-center">

    <!-- LEFT -->
    <div class="text-xl font-bold">
        User Dashboard
    </div>

    <!-- RIGHT -->
    <div class="space-x-6 flex items-center text-sm">

        <a href="index.php" class="hover:text-gray-300">
            Profile
        </a>

        <a href="user_attendance.php" class="hover:text-gray-300">
            Attendance
        </a>

        <a href="user_payment.php" class="hover:text-gray-300">
            Payments
        </a>

        <a href="./../logout.php"
           class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">
            Logout
        </a>

    </div>

</nav>
