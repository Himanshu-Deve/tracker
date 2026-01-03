<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$title = "Enquiry Records";
include __DIR__ . "/includes/header.php";

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login");
    exit();
}

// Database connection
require_once __DIR__ . '/config/db.php';
$database = new Database();
$conn = $database->getConnection();

// Fetch all enquiries
$sql = "SELECT * FROM enquire_table ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<style>
  .dashboard-container {
    min-height: 100vh;
    background-color: #f8fafc;
    padding: 2rem;
  }
  .table-container {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  }
  table {
    width: 100%;
    border-collapse: collapse;
  }
  th {
    background: #f1f5f9;
    text-align: left;
    padding: 12px;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
  }
  td {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
    color: #4b5563;
  }
  tr:hover {
    background-color: #f9fafb;
  }
</style>

<div class="flex">
  <!-- Sidebar -->
  <?php include __DIR__ . "/includes/sidebar.php"; ?>

  <!-- Main Content -->
  <div class="flex-1 ml-64 bg-gray-100 min-h-screen">
    <div class="dashboard-container">
      <h1 class="text-3xl font-bold text-gray-900 mb-6">All Customer Enquiries</h1>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Type</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['id']); ?></td>
                  <td><?php echo htmlspecialchars($row['name']); ?></td>
                  <td><?php echo htmlspecialchars($row['email']); ?></td>
                  <td><?php echo htmlspecialchars($row['number']); ?></td>
                  <td><?php echo nl2br(htmlspecialchars($row['bhk_type'])); ?></td>
                  <td><?php echo date('M j, Y g:i A', strtotime($row['created_at'])); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center text-gray-500 py-6">
                  No enquiries found.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php
$conn->close();
?>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
