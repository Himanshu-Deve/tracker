<?php
require_once './../session.php';
require_once './../db.php';

$conn = getDB();

/* ===============================
   AUTH CHECK
================================ */
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'owner')) {
    header("Location: login.php");
    exit;
}

/* ===============================
   FETCH EMPLOYEES
================================ */
$empResult = $conn->query("SELECT emp_id, name FROM users WHERE role='user' ORDER BY name");
$employees = [];
while ($row = $empResult->fetch_assoc()) {
    $employees[] = $row;
}

/* ===============================
   FILTERS
================================ */
$empFilter = $_GET['emp_id'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = "WHERE 1=1";
$params = [];
$types = "";

if ($empFilter !== '') {
    $where .= " AND a.emp_id=?";
    $params[] = $empFilter;
    $types .= "i";
}
if ($statusFilter !== '') {
    $where .= " AND p.payment_status=?";
    $params[] = $statusFilter;
    $types .= "s";
}
if ($from && $to) {
    $where .= " AND DATE(a.shift_start) BETWEEN ? AND ?";
    $params[] = $from;
    $params[] = $to;
    $types .= "ss";
}

/* ===============================
   PAGINATION
================================ */
$limit = 10;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

/* ===============================
   TOTAL COUNT
================================ */
$countSql = "SELECT COUNT(*) total 
             FROM attendance a
             LEFT JOIN payment_track p ON p.attendance_id = a.id
             $where";
$stmt = $conn->prepare($countSql);
if (!empty($params))
    $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);
$stmt->close();

/* ===============================
   FETCH ATTENDANCE WITH PAYMENT
================================ */
$sql = "SELECT 
            a.id AS attendance_id,
            a.emp_id,
            a.emp_name,
            a.mode,
            a.shift_start,
            a.shift_end,
            a.status AS attendance_status,
            p.payment_id,
            COALESCE(p.cash_payment,0) AS cash_payment,
            COALESCE(p.ni_payment,0) AS ni_payment,
            COALESCE(p.expense,0) AS expense,
            COALESCE(p.payment_status,'pending') AS payment_status,
            u.email,
            u.contact,
            u.role,
            u.address
        FROM attendance a
        LEFT JOIN payment_track p ON p.attendance_id = a.id
        LEFT JOIN users u ON a.emp_id = u.emp_id
        $where
        ORDER BY a.shift_start DESC
        LIMIT $limit OFFSET $offset";


$stmt = $conn->prepare($sql);
if (!empty($params))
    $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Payment Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>

<body class="bg-gray-100">
    <?php include 'navbar.php'; ?>

    <div class="max-w-7xl mx-auto mt-6">

        <h1 class="text-2xl font-bold mb-4">Payment Tracker</h1>

        <!-- FILTER -->
        <form method="GET" class="bg-white p-4 rounded shadow mb-4 grid grid-cols-1 md:grid-cols-5 gap-4">
            <select name="emp_id" class="border p-2 rounded">
                <option value="">All Employees</option>
                <?php foreach ($employees as $emp): ?>
                    <option value="<?= $emp['emp_id'] ?>" <?= ($empFilter == $emp['emp_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($emp['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="status" class="border p-2 rounded">
                <option value="">All Status</option>
                <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="partial" <?= $statusFilter == 'partial' ? 'selected' : '' ?>>Partial</option>
                <option value="paid" <?= $statusFilter == 'paid' ? 'selected' : '' ?>>Paid</option>
            </select>

            <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" class="border p-2 rounded">
            <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" class="border p-2 rounded">

            <div class="flex gap-2">
                <!-- Filter button -->
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full md:w-auto text-center">
                    Filter
                </button>

                <!-- Reset button -->
                <button type="button" onclick="window.location.href='payment.php'"
                    class="bg-gray-400 text-white px-4 py-2 rounded w-full md:w-auto text-center">
                    Reset
                </button>
            </div>

        </form>

        <!-- TABLE -->
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full border text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border p-2">Emp ID</th>
                        <th class="border p-2">Name</th>
                        <th class="border p-2">Mode</th>
                        <th class="border p-2">Shift</th>
                        <th class="border p-2">Payment- I</th>
                        <th class="border p-2">Payment- II</th>
                        <th class="border p-2">Payment Status</th>
                        <th class="border p-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="text-center">
                            <td class="border p-2"><?= $row['emp_id'] ?></td>
                            <td class="border p-2"><?= htmlspecialchars($row['emp_name']) ?></td>
                            <td class="border p-2"><?= ucfirst($row['mode']) ?></td>
                            <td class="border p-2"><?= date("d M Y, g:i a", strtotime($row['shift_start'])) ?> -
                                <?= date("g:i a", strtotime($row['shift_end'])) ?>
                            </td>

                            <td class="border p-2">
                                <input type="number" value="<?= $row['cash_payment'] ?>" class="w-20 p-1 border rounded"
                                    data-payment-id="<?= $row['payment_id'] ?>" min="0"
                                    data-attendance-id="<?= $row['attendance_id'] ?>" data-field="cash_payment">
                            </td>

                            <td class="border p-2">
                                <input type="number" value="<?= $row['ni_payment'] ?>" class="w-20 p-1 border rounded"
                                    data-payment-id="<?= $row['payment_id'] ?>" min="0"
                                    data-attendance-id="<?= $row['attendance_id'] ?>" data-field="ni_payment">
                            </td>

                            <td class="border p-2">
                                <select class="border p-1 rounded" data-payment-id="<?= $row['payment_id'] ?>"
                                    data-attendance-id="<?= $row['attendance_id'] ?>" data-field="payment_status">
                                    <option value="pending" <?= $row['payment_status'] == 'pending' ? 'selected' : '' ?>>
                                        Pending
                                    </option>
                                    <option value="partial" <?= $row['payment_status'] == 'partial' ? 'selected' : '' ?>>
                                        Partial
                                    </option>
                                    <option value="paid" <?= $row['payment_status'] == 'paid' ? 'selected' : '' ?>>Paid
                                    </option>
                                </select>
                            </td>

                            <td class="border p-2 space-x-2">
                                <button class="bg-green-600 text-white px-2 py-1 rounded update-btn"
                                    data-payment-id="<?= $row['payment_id'] ?? '' ?>"
                                    data-attendance-id="<?= $row['attendance_id'] ?>">Update</button>

                                <?php if ($row['payment_id']): ?>
                                    <button class="bg-blue-500 text-white px-2 py-1 rounded pdf-btn"
                                        data-emp-id="<?= $row['emp_id'] ?>"
                                        data-emp-name="<?= htmlspecialchars($row['emp_name']) ?>"
                                        data-emp-email="<?= htmlspecialchars($row['email'] ?? '') ?>"
                                        data-emp-role="<?= htmlspecialchars($row['role'] ?? '') ?>"
                                        data-mode="<?= $row['mode'] ?>"
                                        data-shift="<?= date("d M Y, g:i a", strtotime($row['shift_start'])) ?> - <?= date("g:i a", strtotime($row['shift_end'])) ?>"
                                        data-attendance-status="<?= ucfirst($row['attendance_status']) ?>"
                                        data-cash="<?= $row['cash_payment'] ?>" data-ni="<?= $row['ni_payment'] ?>"
                                        data-expense="<?= $row['expense'] ?>" data-status="<?= $row['payment_status'] ?>"
                                        data-payment-id="<?= $row['payment_id'] ?>">Generate PDF</button>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">No Payment</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <?php if ($totalPages > 1): ?>
            <div class="flex justify-center gap-2 mt-4">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&emp_id=<?= $empFilter ?>&status=<?= $statusFilter ?>&from=<?= $from ?>&to=<?= $to ?>"
                        class="px-3 py-1 rounded border <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-white' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    </div>

    <script>
        // Update payment via AJAX
        document.querySelectorAll('.update-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const row = btn.closest('tr');
                const paymentId = btn.dataset.paymentId || null;
                const attendanceId = btn.dataset.attendanceId;
                const cash = row.querySelector('input[data-field="cash_payment"]').value;
                const ni = row.querySelector('input[data-field="ni_payment"]').value;
                const status = row.querySelector('select[data-field="payment_status"]').value;

                fetch('update_payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ payment_id: paymentId, attendance_id: attendanceId, cash_payment: cash, ni_payment: ni, payment_status: status })
                }).then(res => res.json()).then(data => {
                    alert(data.message);
                    location.reload();
                });
            });
        });

        // Generate PDF - Professional Invoice
        // Professional Payment Invoice with full user details
        document.querySelectorAll('.pdf-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                const pageWidth = doc.internal.pageSize.getWidth();
                let y = 20;

                // Title
                doc.setFontSize(18);
                doc.setFont("helvetica", "bold");
                doc.text("Payment Invoice", pageWidth / 2, y, { align: "center" });

                y += 15;

                // Employee / Attendance Info
                doc.setFontSize(12);
                doc.setFont("helvetica", "normal");
                doc.text(`Employee ID: ${btn.dataset.empId}`, 20, y);
                doc.text(`Name: ${btn.dataset.empName}`, 20, y + 7);
                if (btn.dataset.empEmail) doc.text(`Email: ${btn.dataset.empEmail}`, 20, y + 14);
                if (btn.dataset.empRole) doc.text(`Role: ${btn.dataset.empRole}`, 20, y + 21);
                doc.text(`Shift: ${btn.dataset.shift}`, 20, y + 28);
                doc.text(`Mode: ${btn.dataset.mode}`, 20, y + 35);
                doc.text(`Attendance Status: ${btn.dataset.attendanceStatus}`, 20, y + 42);

                y += 55;

                // Table Header
                // Table Header with more padding
                doc.setFont("helvetica", "bold");
                doc.setFillColor(220, 220, 220);
                doc.rect(20, y, pageWidth - 40, 15, "F"); // increase height from 10 -> 15
                doc.setTextColor(0, 0, 0);
                doc.text("Description", 25, y + 10); // increase offset from 7 -> 10
                doc.text("Amount", pageWidth - 50, y + 10, { align: "right" });

                y += 20; // increase spacing before first row


                // Table Rows
                doc.setFont("helvetica", "normal");
                const payments = [
                    { desc: "Payment- I", amount: parseFloat(btn.dataset.cash) },
                    { desc: "Payment- II", amount: parseFloat(btn.dataset.ni) },
                    { desc: "Expense", amount: parseFloat(btn.dataset.expense || 0) }
                ];

                let total = 0;
                payments.forEach(p => {
                    doc.text(p.desc, 25, y);
                    doc.text(p.amount.toFixed(2), pageWidth - 50, y, { align: "right" });
                    total += p.amount;
                    y += 7;
                });

                y += 5;

                // Total row
                doc.setFont("helvetica", "bold");
                doc.text("Total Payment", 25, y);
                doc.text(total.toFixed(2), pageWidth - 50, y, { align: "right" });

                y += 15;

                // Payment Status
                doc.setFont("helvetica", "normal");
                doc.text(`Payment Status: ${btn.dataset.status.toUpperCase()}`, 20, y);

                y += 10;
                doc.setFont("helvetica", "italic");
                doc.setFontSize(10);
                doc.text("This is a system-generated invoice.", pageWidth / 2, y, { align: "center" });

                // Save PDF
                doc.save(`invoice_${btn.dataset.empId}_${btn.dataset.paymentId}.pdf`);
            });

        });
    </script>

</body>

</html>