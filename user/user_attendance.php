<?php
require_once './../session.php';
require_once './../db.php';

$conn = getDB();

/* ===============================
   AUTH CHECK (USER ONLY)
================================ */
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header("Location: ./../login.php");
    exit;
}

$empId = $_SESSION['user_id'];

/* ===============================
   FILTERS
================================ */
$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';

$where = "WHERE emp_id = ?";
$params = [$empId];
$types  = "i";

if ($from && $to) {
    $where .= " AND DATE(shift_start) BETWEEN ? AND ?";
    $params[] = $from;
    $params[] = $to;
    $types .= "ss";
}

/* ===============================
   PAGINATION
================================ */
$limit = 10;
$page  = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

/* ===============================
   TOTAL COUNT
================================ */
$countSql = "SELECT COUNT(*) total FROM attendance $where";
$stmt = $conn->prepare($countSql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$totalPages = ceil($total / $limit);

/* ===============================
   FETCH ATTENDANCE
================================ */
$sql = "
    SELECT emp_name, mode, shift_start, shift_end, status
    FROM attendance
    $where
    ORDER BY shift_start DESC
    LIMIT $limit OFFSET $offset
";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Attendance</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<?php include 'user_nav.php'; ?>

<div class="max-w-6xl mx-auto mt-6">

<!-- HEADER -->
<h1 class="text-2xl font-bold mb-4">My Attendance</h1>

<!-- FILTER -->
<form method="GET" class="bg-white p-4 rounded shadow mb-4 flex gap-4 flex-wrap">
    <div>
        <label class="block text-sm font-semibold">From</label>
        <input type="date" name="from" value="<?= htmlspecialchars($from) ?>"
               class="border p-2 rounded">
    </div>

    <div>
        <label class="block text-sm font-semibold">To</label>
        <input type="date" name="to" value="<?= htmlspecialchars($to) ?>"
               class="border p-2 rounded">
    </div>

    <div class="flex items-end">
        <button class="bg-blue-600 text-white px-6 py-2 rounded">
            Filter
        </button>
    </div>
</form>

<!-- TABLE -->
<div class="bg-white rounded shadow overflow-x-auto">
<table class="w-full border text-sm">
<thead class="bg-gray-200">
<tr>
    <th class="border p-2">Name</th>
    <th class="border p-2">Mode</th>
    <th class="border p-2">Shift Start</th>
    <th class="border p-2">Shift End</th>
    <th class="border p-2">Status</th>
</tr>
</thead>

<tbody>
<?php if ($result->num_rows === 0): ?>
<tr>
    <td colspan="4" class="text-center p-4 text-gray-500">
        No attendance found
    </td>
</tr>
<?php endif; ?>

<?php while ($row = $result->fetch_assoc()): ?>
<tr class="text-center">
    <td class="border p-2"><?= ucfirst($row['emp_name']) ?></td>
    <td class="border p-2"><?= ucfirst($row['mode']) ?></td>
    <td class="border p-2">
    <?= date("d M Y, g:i a", strtotime($row['shift_start'])) ?>
</td>

<td class="border p-2">
    <?= date("d M Y, g:i a", strtotime($row['shift_end'])) ?>
</td>

    <td class="border p-2">
        <span class="
            px-3 py-1 rounded text-white
            <?= $row['status'] === 'present' ? 'bg-green-600' :
                ($row['status'] === 'half_day' ? 'bg-yellow-500' : 'bg-red-600') ?>">
            <?= ucfirst(str_replace('_',' ', $row['status'])) ?>
        </span>
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
class="px-3 py-1 rounded border
<?= $i == $page ? 'bg-blue-600 text-white' : 'bg-white' ?>">
<?= $i ?>
</a>
<?php endfor; ?>
</div>
<?php endif; ?>

</div>

</body>
</html>
