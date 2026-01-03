<?php
include_once(__DIR__ . '/../config/db.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $database = new Database();
    $conn = $database->getConnection();

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $number = trim($_POST['number'] ?? '');
    $bhk_type = trim($_POST['bhk_type'] ?? '');

    if ($name !== '' && $number !== '') {
        $sql = "INSERT INTO enquire_table (name, email, number, bhk_type) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo "Prepare failed: " . htmlspecialchars($conn->error);
            exit;
        }

        $stmt->bind_param("ssss", $name, $email, $number, $bhk_type);
        if ($stmt->execute()) {
            echo "Enquiry submitted successfully!";
        } else {
            echo "Error executing query: " . htmlspecialchars($stmt->error);
        }

        $stmt->close();
    } else {
        echo "Name and mobile number are required.";
    }

    $conn->close();
    exit; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>DD Associates | Luxury Real Estate</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com"></script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    body { font-family: 'Inter', sans-serif; }
    #mobileMenu { transition: all 0.3s ease; overflow: hidden; }
    /* Custom Gold Variable */
    .text-gold { color: #D4AF37; }
    .bg-gold { background-color: #D4AF37; }
    .border-gold { border-color: #D4AF37; }
    .hover-bg-gold:hover { background-color: #B8962E; }
  </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased">

  <header class="sticky top-0 z-50 bg-black text-white shadow-xl border-b border-yellow-900/30">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">

        <a href="/" class="flex items-center space-x-2">
          <div class="h-10 w-10 bg-gold flex items-center justify-center font-bold rounded text-black text-lg">DD</div>
          <span class="font-bold tracking-widest text-xl"> ASSOCIATES</span>
        </a>

        <div class="hidden md:flex items-center space-x-10 text-sm uppercase tracking-wider font-medium">
          <?php $page = basename($_SERVER['PHP_SELF']); ?>

<a href="/" class="<?= $page == 'index.php' ? 'text-gold border-b-2 border-gold' : '' ?>">Home</a>
<a href="/about.php" class="<?= $page == 'about.php' ? 'text-gold border-b-2 border-gold' : '' ?>">About Us</a>
<a href="/properties.php" class="<?= $page == 'properties.php' ? 'text-gold border-b-2 border-gold' : '' ?>">Projects</a>
<a href="/contact.php" class="<?= $page == 'contact.php' ? 'text-gold border-b-2 border-gold' : '' ?>">Contact Us</a>

        </div>

        <div class="hidden md:flex">
          <a id="quoteBtn"  class="px-6 py-2.5 rounded-full bg-gold text-black font-bold hover-bg-gold transition shadow-lg shadow-yellow-900/20">
            GET A QUOTE <i class="fas fa-arrow-right ml-2 text-sm"></i>
          </a>
        </div>

        <div id="mobileMenuBtn" class="md:hidden inline-flex items-center justify-center p-2 rounded-md hover:text-gold focus:outline-none transition">
          <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </div>

      </div>
    </nav>

    <div id="mobileMenu" class="max-h-0 overflow-hidden bg-[#0a0a0a] transition-all duration-300 ease-in-out border-b border-yellow-900/20">
      <div class="px-4 py-6 space-y-4">
        <a href="/" class="block px-3 py-2 rounded hover:text-gold">Home</a>
        <a href="/about" class="block px-3 py-2 rounded hover:text-gold">About Us</a>
        <a href="/properties" class="block px-3 py-2 rounded hover:text-gold">Projects</a>
        <a href="/contact" class="block px-3 py-2 rounded hover:text-gold">Contact Us</a>
        
        <a id="quoteBtn" class="block px-3 py-4 rounded bg-gold text-black font-bold text-center">GET A QUOTE</a>
      </div>
    </div>
  </header>

  <button id="enquiryBtn"
    class="fixed top-1/2 right-0 transform -translate-y-1/2 bg-gold text-black px-4 py-4 rounded-l-xl shadow-2xl hover-bg-gold transition z-50 border-y border-l border-yellow-700/50"
    style="writing-mode: vertical-rl; text-orientation: mixed; font-weight: 700; letter-spacing: 2px;">
    ENQUIRE NOW
  </button>


  <div id="enquiryModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-[#111] border border-yellow-900/50 rounded-2xl p-8 w-[400px] relative shadow-[0_0_50px_rgba(212,175,55,0.1)]">
      <button id="closeModal" class="absolute top-4 right-5 text-gray-500 hover:text-gold text-2xl transition">&times;</button>
      
      <h2 class="text-2xl font-bold mb-1 text-center text-white">Property Enquiry</h2>
      <p class="text-gray-500 text-center text-sm mb-6">Let us find your dream home</p>

      <form id="enquiryForm" method="POST" class="space-y-4">
        <div>
          <label class="block text-xs uppercase tracking-widest font-semibold text-gold mb-1">Full Name</label>
          <input type="text" name="name" required
            class="w-full bg-black border border-gray-800 text-white rounded-lg px-4 py-3 focus:border-gold focus:ring-1 focus:ring-gold outline-none transition">
        </div>
        <div>
          <label class="block text-xs uppercase tracking-widest font-semibold text-gold mb-1">Email Address</label>
          <input type="email" name="email"
            class="w-full bg-black border border-gray-800 text-white rounded-lg px-4 py-3 focus:border-gold focus:ring-1 focus:ring-gold outline-none transition">
        </div>
        <div>
          <label class="block text-xs uppercase tracking-widest font-semibold text-gold mb-1">Mobile Number</label>
          <input type="text" name="number" required
            class="w-full bg-black border border-gray-800 text-white rounded-lg px-4 py-3 focus:border-gold focus:ring-1 focus:ring-gold outline-none transition">
        </div>
        <div>
          <label class="block text-xs uppercase tracking-widest font-semibold text-gold mb-1">BHK Type</label>
          <select name="bhk_type" class="w-full bg-black border border-gray-800 text-white rounded-lg px-4 py-3 focus:border-gold focus:ring-1 focus:ring-gold outline-none transition">
            <option value="" class="bg-black">Select Preference</option>
            <option value="1 BHK" class="bg-black">1 BHK</option>
            <option value="2 BHK" class="bg-black">2 BHK</option>
            <option value="3 BHK" class="bg-black">3 BHK</option>
            <option value="4 BHK" class="bg-black">4 BHK</option>
          </select>
        </div>
        <button type="submit"
          class="w-full bg-gold text-black font-bold py-4 rounded-lg hover-bg-gold transition mt-4 shadow-lg shadow-yellow-900/20 uppercase tracking-widest">
          Submit Enquiry
        </button>
      </form>
    </div>
  </div>

  <script>
    const enquiryBtn = document.getElementById('enquiryBtn');
    const quoteBtn = document.getElementById('quoteBtn');
    const enquiryModal = document.getElementById('enquiryModal');
    const closeModal = document.getElementById('closeModal');
    const enquiryForm = document.getElementById('enquiryForm');

    enquiryBtn.onclick = () => enquiryModal.classList.remove('hidden');
    quoteBtn.onclick = () => enquiryModal.classList.remove('hidden');
    closeModal.onclick = () => enquiryModal.classList.add('hidden');

    enquiryModal.addEventListener('click', (e) => {
      if (e.target === enquiryModal) enquiryModal.classList.add('hidden');
    });

    enquiryForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(enquiryForm);
      const submitBtn = this.querySelector('button[type="submit"]');
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

      fetch('', {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(response => {
        alert(response);
        enquiryModal.classList.add('hidden');
        submitBtn.innerHTML = 'Submit Enquiry';

        // WhatsApp Redirect
        const name = formData.get('name');
        const number = formData.get('number');
        const bhk = formData.get('bhk_type');
        const message = encodeURIComponent(`Luxury Enquiry: Hello, I'm ${name}. Interested in a ${bhk}. Contact: ${number}`);
        window.open(`https://wa.me/+919953792555?text=${message}`, '_blank');
      })
      .catch(err => {
        alert("Error submitting enquiry.");
        submitBtn.innerHTML = 'Submit Enquiry';
      });
    });

    // Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    let isOpen = false;

    mobileMenuBtn.addEventListener('click', () => {
      isOpen = !isOpen;
      if (isOpen) {
        mobileMenu.classList.remove('max-h-0');
        mobileMenu.classList.add('max-h-96');
      } else {
        mobileMenu.classList.remove('max-h-96');
        mobileMenu.classList.add('max-h-0');
      }
    });
  </script>

</body>
</html>