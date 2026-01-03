<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<div class="h-screen bg-gray-900 text-gray-100 w-64 flex flex-col fixed left-0 top-0">
  <div class="flex items-center justify-center h-16 bg-gray-800">
    <h1 class="text-xl font-bold">Admin Panel</h1>
  </div>
  
  <nav class="flex-1 px-4 py-6 space-y-2">
    <a href="dashboard.php"
       class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition
       <?php echo $current_page === 'dashboard.php' ? 'bg-gray-800 text-white' : 'text-gray-300'; ?>">
      <i class="fas fa-chart-line mr-3"></i> Visit Dashboard
    </a>
    
    <a href="enquiry_table.php"
       class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition
       <?php echo $current_page === 'enquiry_table.php' ? 'bg-gray-800 text-white' : 'text-gray-300'; ?>">
      <i class="fas fa-table mr-3"></i> Enquiry Dashboard
    </a>
  </nav>
  
  <div class="p-4 border-t border-gray-700">
    <a href="logout.php" class="flex items-center justify-center bg-red-600 hover:bg-red-700 px-3 py-2 rounded-lg text-sm font-medium transition">
      <i class="fas fa-sign-out-alt mr-2"></i> Logout
    </a>
  </div>
</div>
