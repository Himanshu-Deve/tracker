<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$title = "Visit Dashboard";
ob_start(); // ✅ Add this to prevent output issues

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
  
// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_status = $conn->real_escape_string($_POST['status']);
    
    $update_sql = "UPDATE visit_booking SET status = '$new_status' WHERE id = $booking_id";
    
    if ($conn->query($update_sql) === TRUE) {
        $_SESSION['success_message'] = "Status updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating status: " . $conn->error;
    }
    
    header("Location: dashboard");
    exit();
}

// Handle filters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Build query with filters
$sql = "SELECT * FROM visit_booking WHERE 1=1";

if ($status_filter != 'all') {
    $sql .= " AND status = '" . $conn->real_escape_string($status_filter) . "'";
}

if (!empty($date_filter)) {
    $sql .= " AND DATE(booking_date) = '" . $conn->real_escape_string($date_filter) . "'";
}

$sql .= " ORDER BY booking_date DESC";

$result = $conn->query($sql);
?>

<style>
  .dashboard-container {
    min-height: 100vh;
    background-color: #f8fafc;
  }
  
  .status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
  }
  
  .status-pending {
    background-color: #fef3c7;
    color: #92400e;
  }
  
  .status-confirmed {
    background-color: #d1fae5;
    color: #065f46;
  }
  
  .status-cancelled {
    background-color: #fee2e2;
    color: #991b1b;
  }
  
  .stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    border-left: 4px solid;
  }
  
  .stat-card.pending {
    border-left-color: #f59e0b;
  }
  
  .stat-card.confirmed {
    border-left-color: #10b981;
  }
  
  .stat-card.cancelled {
    border-left-color: #ef4444;
  }
  
  .stat-card.total {
    border-left-color: #6366f1;
  }
  
  .booking-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
  }
  
  .booking-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  }
  
  .action-btn {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
  }
  
  .action-btn:hover {
    transform: translateY(-1px);
  }
  
  .filter-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
  }
</style>

<div class="flex">
  <!-- Sidebar -->
  <?php include __DIR__ . "/includes/sidebar.php"; ?>

  <!-- Main Content -->
  <div class="flex-1 ml-64 bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-6 py-8">
  
<div class="dashboard-container">
  
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Property Visit Dashboard</h1>
      <p class="text-gray-600 mt-2">Manage and track all property visit bookings</p>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
      </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
      </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <?php
      // Get counts for each status
      $total_count = $conn->query("SELECT COUNT(*) as count FROM visit_booking")->fetch_assoc()['count'];
      $pending_count = $conn->query("SELECT COUNT(*) as count FROM visit_booking WHERE status = 'pending'")->fetch_assoc()['count'];
      $confirmed_count = $conn->query("SELECT COUNT(*) as count FROM visit_booking WHERE status = 'confirmed'")->fetch_assoc()['count'];
      $cancelled_count = $conn->query("SELECT COUNT(*) as count FROM visit_booking WHERE status = 'cancelled'")->fetch_assoc()['count'];
      ?>
      
      <div class="stat-card total">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <i class="fas fa-calendar-alt text-2xl text-indigo-600"></i>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Bookings</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo $total_count; ?></p>
          </div>
        </div>
      </div>
      
      <div class="stat-card pending">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <i class="fas fa-clock text-2xl text-yellow-600"></i>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Pending</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo $pending_count; ?></p>
          </div>
        </div>
      </div>
      
      <div class="stat-card confirmed">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <i class="fas fa-check-circle text-2xl text-green-600"></i>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Confirmed</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo $confirmed_count; ?></p>
          </div>
        </div>
      </div>
      
      <div class="stat-card cancelled">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <i class="fas fa-times-circle text-2xl text-red-600"></i>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Cancelled</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo $cancelled_count; ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Filter Bookings</h3>
        <a href="logout" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </div>
      <form method="GET" class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Statuses</option>
            <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="confirmed" <?php echo $status_filter == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
            <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
          </select>
        </div>
        
        <div class="flex-1">
          <label class="block text-sm font-medium text-gray-700 mb-1">Booking Date</label>
          <input type="date" name="date" value="<?php echo $date_filter; ?>" 
                 class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        
        <div class="flex items-end">
          <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">
            Apply Filters
          </button>
          <a href="dashboard" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium transition">
            Clear
          </a>
        </div>
      </form>
    </div>

    <!-- Bookings List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Visit Bookings</h3>
      </div>
      
      <div class="divide-y divide-gray-200">
        <?php if ($result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <div class="booking-card">
              <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex-1">
                  <div class="flex items-start justify-between mb-3">
                    <div>
                      <h4 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($row['property_name']); ?></h4>
                      <p class="text-gray-600 text-sm mt-1">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        <?php echo htmlspecialchars($row['property_location']); ?>
                      </p>
                    </div>
                    <span class="status-badge status-<?php echo $row['status']; ?>">
                      <?php echo ucfirst($row['status']); ?>
                    </span>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                    <div>
                      <strong>Customer:</strong> <?php echo htmlspecialchars($row['customer_name']); ?>
                    </div>
                    <div>
                      <strong>Phone:</strong> <?php echo htmlspecialchars($row['customer_phone']); ?>
                    </div>
                    <div>
                      <strong>Email:</strong> <?php echo htmlspecialchars($row['customer_email']); ?>
                    </div>
                    <div>
                      <strong>Visit Date:</strong> <?php echo date('M j, Y', strtotime($row['visit_date'])); ?>
                    </div>
                    <div>
                      <strong>Booking Date:</strong> <?php echo date('M j, Y g:i A', strtotime($row['booking_date'])); ?>
                    </div>
                    <div>
                      <strong>Property ID:</strong> #<?php echo $row['property_id']; ?>
                    </div>
                  </div>
                </div>
                
                <div class="mt-4 lg:mt-0 lg:ml-6 flex flex-col sm:flex-row gap-2">
                  <!-- Status Update Form -->
                  <form method="POST" class="flex gap-2">
                    <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                    <select name="status" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                      <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                      <option value="confirmed" <?php echo $row['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                      <option value="cancelled" <?php echo $row['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                    <button type="submit" name="update_status" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition">
                      Update
                    </button>
                  </form>
                  
                  <!-- Action Buttons -->
                  <div class="flex gap-2">
                    <a href="tel:<?php echo $row['customer_phone']; ?>" 
                       class="action-btn bg-green-600 hover:bg-green-700 text-white">
                      <i class="fas fa-phone mr-1"></i> Call
                    </a>
                    <a href="https://wa.me/91<?php echo $row['customer_phone']; ?>?text=Hello%20<?php echo urlencode($row['customer_name']); ?>%2C%20regarding%20your%20property%20visit%20booking%20for%20<?php echo urlencode($row['property_name']); ?>"
                       target="_blank"
                       class="action-btn bg-green-500 hover:bg-green-600 text-white">
                      <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="text-center py-12">
            <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings found</h3>
            <p class="text-gray-600">No visit bookings match your current filters.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
    </div>
  </div>  

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<?php 
// Close database connection
$conn->close();
?>