<?php
require_once './../session.php';
require_once './../db.php';

/* ===============================
   AUTH CHECK (Admin Only)
================================ */
if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'owner') {
    die("Access denied. Only admin can view this page.");
}

$conn = getDB();

/* ===============================
   FETCH ALL USERS
================================ */
$stmt = $conn->prepare("
    SELECT emp_id, name, email, contact, address,
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
<title>Admin Dashboard - Users</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <?php include 'navbar.php'; ?>

<div class="p-6 space-y-6">


<!-- SEARCH FILTER -->
<div class="mb-4">
    <input type="text" id="searchInput" placeholder="Search by Employee ID, Name or Email"
        class="w-full md:w-1/2 border p-2 rounded"
        onkeyup="filterUsers()">
</div>

<div class="bg-white rounded shadow overflow-x-auto">
<table id="usersTable" class="w-full border">
<thead class="bg-gray-200">
<tr>
<th class="p-3 border">Employee ID</th>
<th class="p-3 border">Name</th>
<th class="p-3 border">Email</th>
<th class="p-3 border">Contact</th>
<th class="p-3 border">Address</th>
<?php foreach ($docs as $key => $label): ?>
<th class="p-3 border"><?= $label ?> (Doc)</th>
<th class="p-3 border"><?= $label ?> Expiry</th>
<?php endforeach; ?>
</tr>
</thead>

<tbody>
<?php foreach($users as $user): ?>
<tr class="text-center border-t">
<td class="p-2 border"><?= $user['emp_id'] ?></td>
<td class="p-2 border"><?= htmlspecialchars($user['name']) ?></td>
<td class="p-2 border"><?= htmlspecialchars($user['email']) ?></td>
<td class="p-2 border"><?= htmlspecialchars($user['contact']) ?></td>
<td class="p-2 border"><?= htmlspecialchars($user['address']) ?></td>

<?php foreach($docs as $key => $label): ?>
<td class="p-2 border">
<?php 
$docPath = __DIR__ . '../../user/' . $user[$key];
if(!empty("../../user/$user[$key]") && file_exists($docPath)): ?>
<img src="<?= htmlspecialchars("../../user/$user[$key]") ?>" class="mx-auto max-h-16 border rounded">
<?php else: ?>
<span class="text-red-600 text-sm">Not uploaded</span>
<?php endif; ?>
</td>
<td class="p-2 border">
<?= !empty($user[$expiryMap[$key]]) ? $user[$expiryMap[$key]] : '-' ?>
</td>
<?php endforeach; ?>

</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

</div>

<!-- JS SEARCH FILTER -->
<script>
function filterUsers() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const table = document.getElementById('usersTable');
    const trs = table.getElementsByTagName('tr');

    for (let i = 1; i < trs.length; i++) { // skip header
        const tds = trs[i].getElementsByTagName('td');
        let show = false;

        // Check Employee ID, Name, Email
        for (let j = 0; j < 3; j++) {
            if (tds[j].textContent.toLowerCase().includes(input)) {
                show = true;
                break;
            }
        }

        trs[i].style.display = show ? '' : 'none';
    }
}
</script>

</body>
</html>
