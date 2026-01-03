<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$title = "Systems Access — DD Associates";
ob_start();
include __DIR__ . "/includes/header.php";

// Database connection logic (Preserved)
require_once __DIR__ . '/config/db.php';

$error_message = '';
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $input_username = $conn->real_escape_string($_POST['username']);
    $input_password = $_POST['password'];

    // Check if admin user exists (Preserved)
    $sql = "SELECT * FROM admin_users WHERE username = '$input_username' AND status = 'active'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Secure password check (Preserved)
        if ($input_password === $admin['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];

            header("Location: dashboard");
            exit();
        } else {
            $error_message = "Access Denied: Invalid credentials.";
        }
    } else {
        $error_message = "Access Denied: Account not found.";
    }
    $conn->close();
}
?>

<style>
  :root { --gold: #D4AF37; --dark-bg: #0f0f10; }

  .login-container {
    min-height: 100vh;
    background-color: var(--dark-bg);
    /* Subtle architectural pattern background */
    background-image: radial-gradient(circle at 2px 2px, rgba(212, 175, 55, 0.05) 1px, transparent 0);
    background-size: 40px 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  
  .login-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 3rem 2.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    width: 100%;
    max-width: 440px;
    position: relative;
    overflow: hidden;
  }

  .login-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: linear-gradient(to right, var(--gold), #f4a460);
  }
  
  .login-title {
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--dark-bg);
    letter-spacing: -0.025em;
    text-transform: uppercase;
  }
  
  .login-subtitle {
    font-size: 0.75rem;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    font-weight: 700;
  }

  .input-field {
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
  }

  .input-field:focus {
    border-color: var(--gold);
    box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
    outline: none;
  }

  .btn-gold {
    background-color: var(--dark-bg);
    color: var(--gold);
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    font-size: 0.875rem;
    font-weight: 800;
  }

  .btn-gold:hover {
    background-color: var(--gold);
    color: #000;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
  }
</style>

<div class="login-container">
  <div class="login-card">
    <div class="text-center mb-10">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-6">
        <i class="fas fa-shield-halved text-2xl text-gold"></i>
      </div>
      <h1 class="login-title">Management Portal</h1>
      <p class="login-subtitle mt-2">DD Associates Control Center</p>
    </div>

    <?php if (!empty($error_message)): ?>
      <div class="flex items-center gap-3 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg mb-8 animate-pulse">
        <i class="fas fa-circle-exclamation"></i>
        <p class="text-xs font-bold uppercase tracking-tight"><?php echo $error_message; ?></p>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
      <div>
        <label class="block text-[10px] uppercase font-black tracking-[0.15em] text-gray-400 mb-2 ml-1">Identity</label>
        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300">
                <i class="fas fa-user text-sm"></i>
            </span>
            <input type="text" name="username" 
                   class="input-field w-full bg-gray-50 rounded-xl pl-12 pr-4 py-4 text-sm font-semibold placeholder-gray-300"
                   placeholder="Username" required>
        </div>
      </div>
      
      <div>
        <label class="block text-[10px] uppercase font-black tracking-[0.15em] text-gray-400 mb-2 ml-1">Security Key</label>
        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300">
                <i class="fas fa-key text-sm"></i>
            </span>
            <input type="password" name="password" 
                   class="input-field w-full bg-gray-50 rounded-xl pl-12 pr-4 py-4 text-sm font-semibold placeholder-gray-300"
                   placeholder="••••••••" required>
        </div>
      </div>
      
      <div class="pt-2">
        <button type="submit" name="login" 
                class="btn-gold w-full py-4 rounded-xl flex items-center justify-center gap-3 shadow-lg">
          Authenticate <i class="fas fa-arrow-right text-xs"></i>
        </button>
      </div>
    </form>
    
    <div class="mt-10 pt-6 border-t border-gray-50 text-center">
      <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">
        Secure Environment. Unauthorized access is monitored.
      </p>
    </div>
  </div>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<?php include __DIR__ . "/includes/footer.php"; ?>