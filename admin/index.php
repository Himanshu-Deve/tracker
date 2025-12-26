<?php
require_once './../session.php';
require_once './../db.php';

$conn = getDB();

/* ===============================
   AUTH CHECK
================================ */
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'owner')) {
    header("Location: ./../login.php");
    exit;
}

$adminId = $_SESSION['user_id'];

/* ===============================
   FETCH EMPLOYEES
================================ */
$employees = [];
$empResult = $conn->query("
    SELECT emp_id, name 
    FROM users
    WHERE role='user'
    ORDER BY name
");
while ($row = $empResult->fetch_assoc()) {
    $employees[] = $row;
}

/* ===============================
   DELETE ATTENDANCE
================================ */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM attendance WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['success'] = "Attendance deleted";
    header("Location: index.php");
    exit;
}

/* ===============================
   ADD / UPDATE ATTENDANCE
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_POST['attendance_id'])) {
        $stmt = $conn->prepare("
            UPDATE attendance SET
            emp_id=?, emp_name=?, mode=?,
            shift_start=?, shift_end=?, status=?, updated_by=?
            WHERE id=?
        ");
        $stmt->bind_param(
            "isssssii",
            $_POST['emp_id'],
            $_POST['emp_name'],
            $_POST['mode'],
            $_POST['shift_start'],
            $_POST['shift_end'],
            $_POST['status'],
            $adminId,
            $_POST['attendance_id']
        );
        $_SESSION['success'] = "Attendance updated";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO attendance
            (emp_id, emp_name, mode, shift_start, shift_end, status, updated_by)
            VALUES (?,?,?,?,?,?,?)
        ");
        $stmt->bind_param(
            "isssssi",
            $_POST['emp_id'],
            $_POST['emp_name'],
            $_POST['mode'],
            $_POST['shift_start'],
            $_POST['shift_end'],
            $_POST['status'],
            $adminId
        );
        $_SESSION['success'] = "Attendance added";
    }

    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

/* ===============================
   FILTERS
================================ */
$empFilter    = $_GET['emp_id'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$from         = $_GET['from'] ?? '';
$to           = $_GET['to'] ?? '';

$where = "WHERE 1=1";
$params = [];
$types  = "";

if ($empFilter !== '') {
    $where .= " AND emp_id=?";
    $params[] = $empFilter;
    $types .= "i";
}

if ($statusFilter !== '') {
    $where .= " AND status=?";
    $params[] = $statusFilter;
    $types .= "s";
}

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

/* TOTAL COUNT */
$countSql = "SELECT COUNT(*) total FROM attendance $where";
$countStmt = $conn->prepare($countSql);
if ($params) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$total = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();

$totalPages = ceil($total / $limit);

/* FETCH DATA */
$sql = "
    SELECT *
    FROM attendance
    $where
    ORDER BY shift_start DESC
    LIMIT $limit OFFSET $offset
";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$attendance = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Attendance</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<?php include 'navbar.php'; ?>

<div class="max-w-7xl mx-auto mt-4">

<!-- HEADER -->
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Attendance Management</h1>
    <button onclick="openModal()"
    class="bg-blue-600 text-white px-4 py-2 rounded">
        + Add Shift
    </button>
</div>

<?php if (!empty($_SESSION['success'])): ?>
<div class="bg-green-100 text-green-700 p-3 rounded mb-4">
<?= $_SESSION['success']; unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<!-- FILTER BAR -->
<form method="GET"
class="bg-white p-4 rounded shadow mb-4 grid grid-cols-1 md:grid-cols-6 gap-4">

<select name="emp_id" class="border p-2 rounded">
<option value="">All Employees</option>
<?php foreach ($employees as $emp): ?>
<option value="<?= $emp['emp_id'] ?>"
<?= $empFilter==$emp['emp_id']?'selected':'' ?>>
<?= htmlspecialchars($emp['name']) ?>
</option>
<?php endforeach; ?>
</select>

<select name="status" class="border p-2 rounded">
<option value="">All Status</option>
<option value="present" <?= $statusFilter=='present'?'selected':'' ?>>Present</option>
<option value="half_day" <?= $statusFilter=='half_day'?'selected':'' ?>>Half Day</option>
<option value="absent" <?= $statusFilter=='absent'?'selected':'' ?>>Absent</option>
</select>

<input type="date" name="from" value="<?= $from ?>" class="border p-2 rounded">
<input type="date" name="to" value="<?= $to ?>" class="border p-2 rounded">

<button class="bg-blue-600 text-white px-4 rounded">Filter</button>
<a href="index.php"
class="bg-gray-400 text-white px-4 py-2 rounded text-center">
Reset
</a>

</form>

<!-- TABLE -->
<div class="bg-white rounded shadow overflow-x-auto">
<table class="w-full border text-sm">
<thead class="bg-gray-200">
<tr>
<th class="border p-2">Emp ID</th>
<th class="border p-2">Name</th>
<th class="border p-2">Mode</th>
<th class="border p-2">Shift Start</th>
<th class="border p-2">Shift End</th>
<th class="border p-2">Status</th>
<th class="border p-2">Action</th>
</tr>
</thead>
<tbody>

<?php if ($attendance->num_rows == 0): ?>
<tr>
<td colspan="7" class="text-center p-4 text-gray-500">
No attendance found
</td>
</tr>
<?php endif; ?>

<?php while ($row = $attendance->fetch_assoc()): ?>
<tr class="text-center">
<td class="border p-2"><?= $row['emp_id'] ?></td>
<td class="border p-2"><?= htmlspecialchars($row['emp_name']) ?></td>
<td class="border p-2"><?= ucfirst($row['mode']) ?></td>
<td class="border p-2"><?= date("d M Y, g:i a", strtotime($row['shift_start'])) ?></td>
<td class="border p-2"><?= date("d M Y, g:i a", strtotime($row['shift_end'])) ?></td>
<td class="border p-2">
<span class="px-2 py-1 rounded text-white
<?= $row['status']=='present'?'bg-green-600':
($row['status']=='half_day'?'bg-yellow-500':'bg-red-600') ?>">
<?= ucfirst(str_replace('_',' ',$row['status'])) ?>
</span>
</td>
<td class="border p-2 space-x-2">
<button onclick='editAttendance(<?= json_encode($row) ?>)'
class="bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
<a href="?delete=<?= $row['id'] ?>"
onclick="return confirm('Delete record?')"
class="bg-red-600 text-white px-3 py-1 rounded">Delete</a>
</td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>

<!-- PAGINATION -->
<?php if ($totalPages > 1): ?>
<div class="flex justify-center gap-2 mt-6 flex-wrap">
<?php
$q = $_GET;
unset($q['page']);
$qStr = http_build_query($q);
?>
<?php for ($i=1; $i<=$totalPages; $i++): ?>
<a href="?<?= $qStr ?>&page=<?= $i ?>"
class="px-3 py-1 border rounded
<?= $i==$page?'bg-blue-600 text-white':'bg-white' ?>">
<?= $i ?>
</a>
<?php endfor; ?>
</div>
<?php endif; ?>

</div>

</body>
</html>
