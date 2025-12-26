<?php
require_once './../session.php';
require_once './../db.php';

$conn = getDB();

/* ===============================
   AUTH CHECK
================================ */
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

/* ===============================
   FILTERS
================================ */
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = "WHERE a.emp_id=?";
$params = [$userId];
$types = "i";

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
if (!empty($params)) $stmt->bind_param($types, ...$params);
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
            COALESCE(p.cash_payment,0) AS cash_payment,
            COALESCE(p.ni_payment,0) AS ni_payment,
            COALESCE(p.expense,0) AS expense,
            COALESCE(p.payment_status,'pending') AS payment_status
        FROM attendance a
        LEFT JOIN payment_track p ON p.attendance_id = a.id
        $where
        ORDER BY a.shift_start DESC
        LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($sql);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Payment Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="bg-gray-100">
    <?php include 'user_nav.php'; ?>

    <div class="max-w-7xl mx-auto mt-6">

        <h1 class="text-2xl font-bold mb-4">My Payment Tracker</h1>

        <!-- FILTER -->
        <form method="GET" class="bg-white p-4 rounded shadow mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" class="border p-2 rounded">
            <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" class="border p-2 rounded">

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full md:w-auto text-center">Filter</button>
                <button type="button" onclick="window.location.href='user_payment.php'" class="bg-gray-400 text-white px-4 py-2 rounded w-full md:w-auto text-center">Reset</button>
            </div>
        </form>

        <!-- TABLE -->
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full border text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border p-2">Shift</th>
                        <th class="border p-2">Mode</th>
                        <th class="border p-2">Payment- I</th>
                        <th class="border p-2">Payment- II</th>
                        <th class="border p-2">Expense</th>
                        <th class="border p-2">Payment Status</th>
                        <th class="border p-2">Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="text-center">
                            <td class="border p-2"><?= date("d M Y, g:i a", strtotime($row['shift_start'])) ?> - <?= date("g:i a", strtotime($row['shift_end'])) ?></td>
                            <td class="border p-2"><?= ucfirst($row['mode']) ?></td>
                            <td class="border p-2"><?= $row['cash_payment'] ?></td>
                            <td class="border p-2"><?= $row['ni_payment'] ?></td>
                            <td class="border p-2"><?= $row['expense'] ?></td>
                            <td class="border p-2"><?= ucfirst($row['payment_status']) ?></td>
                            <td class="border p-2">
                                <button class="bg-blue-500 text-white px-2 py-1 rounded pdf-btn"
                                    data-shift="<?= date("d M Y, g:i a", strtotime($row['shift_start'])) ?> - <?= date("g:i a", strtotime($row['shift_end'])) ?>"
                                    data-mode="<?= $row['mode'] ?>"
                                    data-cash="<?= $row['cash_payment'] ?>"
                                    data-ni="<?= $row['ni_payment'] ?>"
                                    data-expense="<?= $row['expense'] ?>"
                                    data-status="<?= $row['payment_status'] ?>">
                                    Generate PDF
                                </button>
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
                    <a href="?page=<?= $i ?>&from=<?= $from ?>&to=<?= $to ?>"
                        class="px-3 py-1 rounded border <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-white' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    </div>

    <script>
    // Generate PDF
    document.querySelectorAll('.pdf-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const pageWidth = doc.internal.pageSize.getWidth();
            let y = 20;

            doc.setFontSize(18);
            doc.setFont("helvetica", "bold");
            doc.text("Payment Invoice", pageWidth/2, y, { align: "center" });
            y += 15;

            doc.setFontSize(12);
            doc.setFont("helvetica", "normal");
            doc.text(`Shift: ${btn.dataset.shift}`, 20, y);
            doc.text(`Mode: ${btn.dataset.mode}`, 20, y+7);
            y += 20;

            doc.setFont("helvetica", "bold");
            doc.setFillColor(220,220,220);
            doc.rect(20,y,pageWidth-40,15,'F');
            doc.setTextColor(0,0,0);
            doc.text("Description",25,y+10);
            doc.text("Amount",pageWidth-50,y+10,{align:'right'});
            y+=20;

            const payments = [
                {desc:"Payment- I", amount: parseFloat(btn.dataset.cash)},
                {desc:"Payment- II", amount: parseFloat(btn.dataset.ni)},
                {desc:"Expense", amount: parseFloat(btn.dataset.expense || 0)}
            ];
            let total = 0;
            payments.forEach(p => {
                doc.text(p.desc,25,y);
                doc.text(p.amount.toFixed(2),pageWidth-50,y,{align:'right'});
                total += p.amount;
                y+=7;
            });
            y+=5;

            doc.setFont("helvetica","bold");
            doc.text("Total Payment",25,y);
            doc.text(total.toFixed(2),pageWidth-50,y,{align:'right'});
            y+=15;

            doc.setFont("helvetica","normal");
            doc.text(`Payment Status: ${btn.dataset.status.toUpperCase()}`,20,y);
            y+=10;
            doc.setFont("helvetica","italic");
            doc.setFontSize(10);
            doc.text("This is a system-generated invoice.",pageWidth/2,y,{align:"center"});

            doc.save(`invoice_${btn.dataset.shift.replace(/\s|,/g,'_')}.pdf`);
        });
    });
    </script>

</body>
</html>
