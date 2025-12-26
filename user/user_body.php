<?php
require_once './../session.php';
require_once './../db.php';

/* ===============================
   AUTH CHECK
================================ */

$conn   = getDB();
$emp_id = $_SESSION['user_id'];

/* ===============================
   FETCH USER DATA
================================ */
$stmt = $conn->prepare("
    SELECT emp_id, name, role, email, contact, address,
           act_doc, act_expirey,
           sia_doc, sia_expirey,
           share_code_doc, share_code_expirey
    FROM users
    WHERE emp_id = ?
");
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* ===============================
   DOCUMENT CONFIG
================================ */
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

$expiryRules = [
    'act_doc'        => '+1 year',
    'sia_doc'        => '+3 years',
    'share_code_doc' => '+3 months'
];

/* ===============================
   IMAGE COMPRESSION FUNCTION
================================ */
function compressImage($source, $destination, $quality = 75) {
    if (!function_exists('imagecreatefromjpeg') || !function_exists('imagecreatefrompng')) {
        // fallback: just move the file without compression
        move_uploaded_file($source, $destination);
        return;
    }

    $info = getimagesize($source);

    if ($info['mime'] === 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
        imagejpeg($image, $destination, $quality);
    } elseif ($info['mime'] === 'image/png') {
        $image = imagecreatefrompng($source);
        imagepng($image, $destination, 7);
    } else {
        move_uploaded_file($source, $destination);
    }
    imagedestroy($image);
}


/* ===============================
   HANDLE UPLOAD / RE-UPLOAD
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $baseDir = __DIR__ . "/Upload/{$user['email']}/";
    if (!is_dir($baseDir)) {
        mkdir($baseDir, 0777, true);
    }

    $allowedMime = ['image/jpeg', 'image/png'];

    foreach ($docs as $column => $label) {

        if (!isset($_FILES[$column]) || $_FILES[$column]['error'] !== 0) {
            continue;
        }

        $tmp  = $_FILES[$column]['tmp_name'];
        $mime = mime_content_type($tmp);

        if (!in_array($mime, $allowedMime)) {
            die("Only JPG and PNG images are allowed.");
        }

        /* DELETE OLD FILE (ANY EXTENSION) */
        foreach (glob($baseDir . $column . '.*') as $oldFile) {
            unlink($oldFile);
        }

        $ext      = pathinfo($_FILES[$column]['name'], PATHINFO_EXTENSION);
        $filename = $column . '.' . strtolower($ext);
        $dest     = $baseDir . $filename;

        compressImage($tmp, $dest, 75);

        if (filesize($dest) > 2 * 1024 * 1024) {
            compressImage($tmp, $dest, 60);
        }

        $relativePath = "Upload/{$user['email']}/{$filename}";
        $expiryColumn = $expiryMap[$column];
        $expiryDate   = date('Y-m-d', strtotime($expiryRules[$column]));

        $sql = "
            UPDATE users
            SET $column = ?, $expiryColumn = ?, updated_at = NOW()
            WHERE emp_id = ?
        ";

        $update = $conn->prepare($sql);
        $update->bind_param("ssi", $relativePath, $expiryDate, $emp_id);
        $update->execute();
        $update->close();
    }

    $_SESSION['upload_success'] = "Documents updated successfully.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Documents</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="p-6 space-y-10">

<!-- USER OVERVIEW -->
<div>
<h2 class="text-2xl font-bold mb-2">User Overview (Read Only)</h2>
<div class="bg-white rounded shadow overflow-x-auto">
<table class="w-full border">
<thead class="bg-gray-200">
<tr>
<th class="p-3 border">Employee ID</th>
<th class="p-3 border">Name</th>
<th class="p-3 border">Email</th>
<th class="p-3 border">Contact</th>
<th class="p-3 border">Address</th>
</tr>
</thead>
<tbody>
<tr class="text-center">
<td class="p-3 border"><?= $user['emp_id'] ?></td>
<td class="p-3 border"><?= htmlspecialchars($user['name']) ?></td>
<td class="p-3 border"><?= htmlspecialchars($user['email']) ?></td>
<td class="p-3 border"><?= htmlspecialchars($user['contact']) ?></td>
<td class="p-3 border"><?= htmlspecialchars($user['address']) ?></td>
</tr>
</tbody>
</table>
</div>
</div>

<?php if (!empty($_SESSION['upload_success'])): ?>
<div id="uploadSuccessMsg" class="bg-green-100 text-green-700 p-3 rounded">
<?= $_SESSION['upload_success']; unset($_SESSION['upload_success']); ?>
</div>
<?php endif; ?>

<!-- VIEW DOCUMENTS -->
<div class="bg-white p-6 rounded shadow">
<h3 class="text-xl font-semibold mb-4">Uploaded Documents</h3>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
<?php foreach ($docs as $key => $label): ?>
<div class="border rounded p-4 text-center">
<h4 class="font-semibold mb-2"><?= $label ?></h4>

<?php if (!empty($user[$key]) && file_exists(__DIR__.'/'.$user[$key])): ?>
<img src="<?= $user[$key] ?>" class="max-h-40 mx-auto border rounded">
<p class="text-sm mt-2 text-gray-600">
Expires on: <?= $user[$expiryMap[$key]] ?>
</p>
<?php else: ?>
<span class="text-red-600 text-sm">Not uploaded yet</span>
<?php endif; ?>
</div>
<?php endforeach; ?>
</div>
</div>

<!-- UPLOAD -->
<div class="bg-white p-6 rounded shadow">
<h3 class="text-xl font-semibold mb-4">Upload / Re-upload Documents</h3>

<form method="POST" enctype="multipart/form-data">
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

<?php foreach ($docs as $key => $label): ?>
<div class="border rounded p-4">
<label class="block font-semibold mb-2">
<?= $label ?>
<span class="text-sm text-gray-500">
(<?= empty($user[$key]) ? 'Upload' : 'Re-upload' ?>)
</span>
</label>

<input type="file"
       name="<?= $key ?>"
       accept="image/*"
       class="w-full border p-2 rounded"
       onchange="previewImage(this,'preview_<?= $key ?>')">

<img id="preview_<?= $key ?>" class="hidden mt-3 max-h-32 mx-auto border rounded">
</div>
<?php endforeach; ?>

</div>

<button id="submitBtn"
        type="submit"
        class="mt-6 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 disabled:opacity-50">
Submit Changes
</button>
</form>
</div>

</div>

<!-- BLUR DETECTION + PREVIEW -->
<script>
let blurStatus = {
    act_doc: false,
    sia_doc: false,
    share_code_doc: false
};

const BLUR_THRESHOLD = 100;

function computeVariance(data) {
    const n = data.length;
    let mean = data.reduce((a,b) => a+b, 0)/n;
    let variance = data.reduce((a,b) => a + (b-mean)*(b-mean), 0)/n;
    return variance;
}

function checkBlur(file) {
    return new Promise(resolve => {
        const img = new Image();
        const reader = new FileReader();
        reader.onload = e => img.src = e.target.result;
        reader.readAsDataURL(file);

        img.onload = () => {
            const canvas = document.createElement("canvas");
            const maxDim = 300; // scale down
            let w = img.width;
            let h = img.height;
            if (Math.max(w,h) > maxDim){
                const scale = maxDim/Math.max(w,h);
                w *= scale; h *= scale;
            }
            canvas.width = w; canvas.height = h;
            const ctx = canvas.getContext("2d");
            ctx.drawImage(img,0,0,w,h);

            const imgData = ctx.getImageData(0,0,w,h);
            const gray = [];
            for (let i=0; i<imgData.data.length; i+=4){
                const r=imgData.data[i], g=imgData.data[i+1], b=imgData.data[i+2];
                gray.push(0.299*r+0.587*g+0.114*b);
            }

            // Laplacian kernel
            const lap = [];
            for (let y=1; y<h-1; y++){
                for (let x=1; x<w-1; x++){
                    const i = y*w + x;
                    const val = -gray[i-w-1]-gray[i-w]-gray[i-w+1]
                                -gray[i-1]+8*gray[i]-gray[i+1]
                                -gray[i+w-1]-gray[i+w]-gray[i+w+1];
                    lap.push(val);
                }
            }

            const variance = computeVariance(lap);
            resolve(variance < BLUR_THRESHOLD);
        };
    });
}

async function previewImage(input, id){
    const file = input.files[0];
    const key = input.name;
    if(!file || !file.type.startsWith('image/')) {
        alert("Only image files allowed"); input.value=""; return;
    }

    // show preview immediately
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById(id);
        img.src = e.target.result;
        img.classList.remove("hidden");
    };
    reader.readAsDataURL(file);

    // check blur in background
    const isBlurry = await checkBlur(file);
    blurStatus[key] = isBlurry;
    const submitBtn = document.getElementById("submitBtn");
    submitBtn.disabled = Object.values(blurStatus).includes(true);

    if(isBlurry){
        alert("Image might be blurry. Please upload a clearer image.");
    }
}
</script>

<script>
setTimeout(() => {
    const msg = document.getElementById("uploadSuccessMsg");
    if (msg) msg.remove();
}, 1500);
</script>

</body>
</html>
