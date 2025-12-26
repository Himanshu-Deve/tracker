<?php
require_once './../session.php';
require_once './../db.php';

/* ===============================
   AUTH CHECK (Admin / Owner)
================================ */
if (
    !isset($_SESSION['user_id']) ||
    !in_array($_SESSION['user_role'], ['admin', 'owner'])
) {
    header("Location: ../login.php");
    exit;
}

$conn = getDB();

/* ===============================
   OWNER UPDATE ROLE / STATUS
================================ */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    $_SESSION['user_role'] === 'owner'
) {
    $emp_id    = intval($_POST['emp_id']);
    $role      = $_POST['role'];
    $is_active = intval($_POST['is_active']);

    if (in_array($role, ['admin', 'user'])) {
        $stmt = $conn->prepare("
            UPDATE users
            SET role = ?, is_active = ?
            WHERE emp_id = ?
        ");
        $stmt->bind_param("sii", $role, $is_active, $emp_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

/* ===============================
   FETCH ALL USERS
================================ */
$stmt = $conn->prepare("
    SELECT emp_id, name, email, contact, address,
           role, is_active,
           act_doc, act_expirey,
           sia_doc, sia_expirey,
           share_code_doc, share_code_expirey
    FROM users
    ORDER BY emp_id ASC
");
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$docs = [
    'act_doc'        => 'ACT Certificate',
    'sia_doc'        => 'SIA Certificate',
    'share_code_doc' => 'Share Code'
];

$expiryMap = [
    'act_doc'        => 'act_expirey',
    'sia_doc'        => 'sia_expirey',
    'share_code_doc' => 'share_code_expirey'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Users Management</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<?php include 'navbar.php'; ?>

<div class="p-6 space-y-6">

<h2 class="text-2xl font-bold">
    <?= $_SESSION['user_role'] === 'owner' ? 'Owner Dashboard - All Users' : 'Admin Dashboard - Users' ?>
</h2>

<!-- SEARCH -->
<input
    type="text"
    id="searchInput"
    placeholder="Search by ID, Name or Email"
    class="w-full md:w-1/2 border p-2 rounded"
    onkeyup="filterUsers()"
>

<div class="bg-white rounded shadow overflow-x-auto">
<table id="usersTable" class="w-full border mt-4">
<thead class="bg-gray-200">
<tr>
<th class="p-3 border">ID</th>
<th class="p-3 border">Name</th>
<th class="p-3 border">Email</th>
<th class="p-3 border">Contact</th>
<th class="p-3 border">Address</th>

<?php if ($_SESSION['user_role'] === 'owner'): ?>
<th class="p-3 border">Role</th>
<th class="p-3 border">Status</th>
<th class="p-3 border">Action</th>
<?php endif; ?>

<?php foreach ($docs as $label): ?>
<th class="p-3 border"><?= $label ?></th>
<th class="p-3 border">Expiry</th>
<?php endforeach; ?>
</tr>
</thead>

<tbody>
<?php foreach ($users as $user): ?>
<tr class="text-center border-t">

<td class="p-2 border"><?= $user['emp_id'] ?></td>
<td class="p-2 border"><?= htmlspecialchars($user['name']) ?></td>
<td class="p-2 border"><?= htmlspecialchars($user['email']) ?></td>
<td class="p-2 border"><?= htmlspecialchars($user['contact']) ?></td>
<td class="p-2 border"><?= htmlspecialchars($user['address']) ?></td>

<?php if ($_SESSION['user_role'] === 'owner'): ?>
<form method="POST">
<td class="p-2 border">
    <input type="hidden" name="emp_id" value="<?= $user['emp_id'] ?>">
    <select name="role" class="border p-1 rounded">
        <option value="user" <?= $user['role']==='user'?'selected':'' ?>>User</option>
        <option value="admin" <?= $user['role']==='admin'?'selected':'' ?>>Admin</option>
    </select>
</td>

<td class="p-2 border">
    <select name="is_active" class="border p-1 rounded">
        <option value="1" <?= $user['is_active']?'selected':'' ?>>Active</option>
        <option value="0" <?= !$user['is_active']?'selected':'' ?>>Inactive</option>
    </select>
</td>

<td class="p-2 border">
    <button class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
        Save
    </button>
</td>
</form>
<?php endif; ?>

<?php foreach ($docs as $key => $label): ?>
<td class="p-2 border">
<?php
$path = __DIR__ . "../../user/" . $user[$key];
if (!empty($user[$key]) && file_exists($path)): ?>
<img src="../../user/<?= htmlspecialchars($user[$key]) ?>" class="mx-auto max-h-16 rounded border">
<?php else: ?>
<span class="text-red-500 text-sm">Not uploaded</span>
<?php endif; ?>
</td>

<td class="p-2 border">
<?= $user[$expiryMap[$key]] ?? '-' ?>
</td>
<?php endforeach; ?>

</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>

<!-- SEARCH SCRIPT -->
<script>
function filterUsers() {
    const val = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#usersTable tbody tr');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(val) ? '' : 'none';
    });
}
</script>

</body>
</html>
