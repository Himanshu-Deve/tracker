<?php
header('Content-Type: text/html; charset=utf-8');
$title = "Property Details — DD Associates";
include __DIR__ . "/includes/header.php";

// Database connection
require_once __DIR__ . '/config/db.php';

$database = new Database();
$conn = $database->getConnection();

// Handle form submission (Logic Preserved)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['propertyId'])) {
  $propertyId = intval($_POST['propertyId']);
  $propertyName = $conn->real_escape_string($_POST['propertyName']);
  $propertyLocation = $conn->real_escape_string($_POST['propertyLocation']);
  $name = $conn->real_escape_string($_POST['name']);
  $phone = $conn->real_escape_string($_POST['phone']);
  $email = $conn->real_escape_string($_POST['email']);
  $visitDate = $conn->real_escape_string($_POST['visitDate']);

  $sql = "INSERT INTO visit_booking (property_id, property_name, property_location, customer_name, customer_phone, customer_email, visit_date, booking_date) 
            VALUES ('$propertyId', '$propertyName', '$propertyLocation', '$name', '$phone', '$email', '$visitDate', NOW())";

  if ($conn->query($sql) === TRUE) {
    $success_message = "✅ Booking request sent successfully!";
  } else {
    $error_message = "Error: " . $conn->error;
  }
}
?>

<style>
  :root {
    --gold: #D4AF37;
    --dark: #0f0f10;
  }

  /* Premium Scrollbar */
  #galleryContainer::-webkit-scrollbar {
    height: 4px;
  }

  #galleryContainer::-webkit-scrollbar-thumb {
    background: var(--gold);
    border-radius: 10px;
  }

  .text-gold {
    color: var(--gold);
  }

  .bg-gold {
    background-color: var(--gold);
  }

  .border-gold {
    border-color: var(--gold);
  }

  @media (max-width: 768px) {

    #galleryLeft,
    #galleryRight {
      display: none !important;
    }
  }

  .alert {
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 600;
    text-align: center;
  }

  .alert-success {
    background-color: #f0fff4;
    color: #22543d;
    border: 1px solid #c6f6d5;
  }

  .alert-error {
    background-color: #fff5f5;
    color: #9b2c2c;
    border: 1px solid #fed7d7;
  }

  /* Gallery Hover Effect */
  .gallery-thumb.active-thumb {
    border-color: var(--gold);
    ring: 2px;
    --tw-ring-color: var(--gold);
  }
</style>

<section class="bg-white min-h-screen">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <?php if (isset($success_message)): ?>
      <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <div id="propertyDetails" class="mt-4">
      <div class="animate-pulse flex space-x-4">
        <div class="flex-1 space-y-4 py-1">
          <div class="h-80 bg-gray-200 rounded-xl"></div>
          <div class="h-6 bg-gray-200 rounded w-3/4"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<div id="bookingModal"
  class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center z-[100] p-4">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden relative">
    <div class="bg-[#0f0f10] p-6 text-white relative">
      <button id="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-white text-2xl">×</button>
      <h2 class="text-2xl font-bold text-gold">Schedule a Private Visit</h2>
      <p class="text-gray-400 text-xs uppercase tracking-widest mt-1">Exclusive Property Viewing</p>
    </div>

    <form id="bookingForm" method="POST" class="p-8 space-y-5">
      <input type="hidden" id="propertyId" name="propertyId">

      <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2 sm:col-span-1">
          <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Property</label>
          <input type="text" id="propertyName" name="propertyName"
            class="w-full border-gray-100 bg-gray-50 rounded-lg px-3 py-2 text-sm font-semibold" readonly>
        </div>
        <div class="col-span-2 sm:col-span-1">
          <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Location</label>
          <input type="text" id="propertyLocation" name="propertyLocation"
            class="w-full border-gray-100 bg-gray-50 rounded-lg px-3 py-2 text-sm font-semibold" readonly>
        </div>
      </div>

      <div>
        <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Full Name</label>
        <input type="text" id="name" name="name"
          class="w-full border-b-2 border-gray-100 focus:border-gold outline-none py-2 transition"
          placeholder="John Doe" required>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Phone</label>
          <input type="tel" id="phone" name="phone"
            class="w-full border-b-2 border-gray-100 focus:border-gold outline-none py-2 transition"
            placeholder="+91..." required>
        </div>
        <div>
          <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Visit Date</label>
          <input type="date" id="visitDate" name="visitDate"
            class="w-full border-b-2 border-gray-100 focus:border-gold outline-none py-2 transition" required>
        </div>
      </div>

      <div>
        <label class="block text-[10px] uppercase tracking-widest font-bold text-gray-500 mb-1">Email Address</label>
        <input type="email" id="email" name="email"
          class="w-full border-b-2 border-gray-100 focus:border-gold outline-none py-2 transition"
          placeholder="example@mail.com" required>
      </div>

      <button type="submit"
        class="w-full bg-[#0f0f10] text-gold hover:bg-gold hover:text-black font-bold py-4 rounded-xl transition-all uppercase tracking-widest text-sm mt-4 shadow-lg">
        Confirm Request
      </button>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(function () {
    const params = new URLSearchParams(window.location.search);
    const propertyId = parseInt(params.get('id'));

    $.getJSON('/assets/data/properties.json', function (data) {
      const property = data.find(p => p.id === propertyId);
      if (!property) {
        $('#propertyDetails').html('<div class="text-center py-20"><p class="text-red-500 font-bold">Property not found in our database.</p></div>');
        return;
      }

      const html = `
        <div class="grid md:grid-cols-2 gap-12 items-start">
          <div class="space-y-6">
            <div class="relative group">
                <img id="mainImage" src="${property.image_thumbnail}" alt="${property.title}" class="rounded-2xl shadow-2xl w-full h-[450px] object-cover transition duration-500">
                <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest text-gray-900">Featured</div>
            </div>
            
            <div class="relative px-2">
              <button id="galleryLeft" class="absolute -left-4 top-1/2 -translate-y-1/2 bg-white shadow-xl rounded-full w-10 h-10 flex items-center justify-center z-10 hover:bg-gold transition">
                <i class="fas fa-chevron-left text-xs"></i>
              </button>

              <div id="galleryContainer" class="flex gap-4 overflow-x-auto no-scrollbar scroll-smooth py-2">
                ${property.images_gallery.map((img, index) => `
                  <img src="${img}" class="gallery-thumb w-28 h-20 object-cover rounded-xl cursor-pointer border-2 border-transparent hover:border-gold transition-all shrink-0 ${index === 0 ? 'active-thumb border-gold' : ''}">
                `).join('')}
              </div>

              <button id="galleryRight" class="absolute -right-4 top-1/2 -translate-y-1/2 bg-white shadow-xl rounded-full w-10 h-10 flex items-center justify-center z-10 hover:bg-gold transition">
                <i class="fas fa-chevron-right text-xs"></i>
              </button>
            </div>
          </div>

          <div class="flex flex-col h-full">
            <nav class="flex text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-4">
                <span>Properties</span> <span class="mx-2">/</span> <span class="text-gold">${property.type}</span>
            </nav>
            
            <h1 class="text-4xl md:text-5xl font-black mb-4 text-[#0f0f10] tracking-tighter uppercase">${property.title}</h1>
            
            <p class="flex items-center text-gray-500 font-medium mb-6">
                <i class="fas fa-map-marker-alt text-gold mr-2"></i> ${property.location}
            </p>

            <div class="flex items-center gap-6 mb-8 py-6 border-y border-gray-100">
                <div class="flex flex-col">
                    <span class="text-[10px] uppercase font-bold text-gray-400">Rating</span>
                    <div class="flex items-center text-gold text-lg">
                        ${[1, 2, 3, 4, 5].map(i => `<i class="fa-solid fa-star ${property.rating >= i ? '' : 'text-gray-200'}"></i>`).join('')}
                        <span class="ml-2 text-gray-900 font-bold text-sm">${property.rating}</span>
                    </div>
                </div>
                <div class="w-[1px] h-10 bg-gray-100"></div>
                <div class="flex flex-col">
                    <span class="text-[10px] uppercase font-bold text-gray-400">Status</span>
                    <span class="text-sm font-bold text-green-600 uppercase tracking-widest">Available</span>
                </div>
            </div>

            <div class="space-y-4">
                <button id="bookVisitBtn" class="w-full flex items-center justify-center gap-3 bg-[#0f0f10] hover:bg-green-600 text-white hover:text-white font-bold py-4 rounded-xl transition-all uppercase tracking-widest text-sm shadow-xl">
                  <i class="fas fa-calendar-check"></i> Book Private Viewing
                </button>

                <button id="interestBtn" class="w-full flex items-center justify-center gap-3 border-2 border-gray-100 hover:border-green-500 hover:text-green-600 font-bold py-4 rounded-xl transition-all uppercase tracking-widest text-sm">
                  <i class="fab fa-whatsapp"></i> Inquire via WhatsApp
                </button>
            </div>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-16 mt-24 items-start">

                <!-- LEFT : PROPERTY DETAILS -->
                <div>
                  <h2 class="text-gold font-bold uppercase tracking-[0.3em] text-xs mb-3">Specifications</h2>
                  <h2 class="text-3xl font-black mb-6 text-gray-900 uppercase tracking-tighter">Property Overview</h2>

                  <p class="text-gray-600 mb-8 leading-relaxed font-light text-lg italic border-l-4 border-gold pl-6">
                    ${property.description}
                  </p>

                  <div class="grid grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-xl">
                      <span class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Configuration</span>
                      <span class="text-sm font-bold text-gray-900">${property.beds} Bedrooms / ${property.baths} Baths</span>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl">
                      <span class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Total Area</span>
                      <span class="text-sm font-bold text-gray-900">${property.area} Sq. Ft.</span>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl">
                      <span class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Property Category</span>
                      <span class="text-sm font-bold text-gray-900">${property.type}</span>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl">
                      <span class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Verified By</span>
                      <span class="text-sm font-bold text-gray-900">DD Associates</span>
                    </div>
                  </div>
                </div>

                <!-- RIGHT : MAP -->
                <div class="h-96 rounded-2xl overflow-hidden shadow-inner border border-gray-200">
                  <iframe 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    loading="lazy"
                    src="https://maps.google.com/maps?q=${property.latitude},${property.longitude}&output=embed">
                  </iframe>
                </div>
                <div class="hidden relative">
                    <div class="absolute -top-4 -left-4 w-24 h-24 border-t-4 border-l-4 border-gold"></div>
                    <img src="${property.architecture_image}" alt="Architecture" class="rounded-2xl shadow-xl w-full h-[400px] object-cover relative z-10">
                  </div>

              </div>



        <div class="hidden grid md:grid-cols-3 gap-8 mt-16">
          <div class="md:col-span-2 h-96 rounded-2xl overflow-hidden shadow-inner border border-gray-100">
            <iframe width="100%" height="100%" style="border:0;" loading="lazy" src="https://maps.google.com/maps?q=${property.latitude},${property.longitude}&output=embed"></iframe>
          </div>
          <div class="bg-[#0f0f10] rounded-2xl p-8 flex flex-col items-center justify-center text-center">
            <div class="w-20 h-20 bg-gold/10 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-file-pdf text-gold text-4xl"></i>
            </div>
            <h3 class="text-white text-xl font-bold mb-2 uppercase tracking-tight">Project Brochure</h3>
            <p class="text-gray-500 text-sm mb-8">Download full specifications and floor plans.</p>
            <a href="${property.bouchure_url}" target="_blank" class="w-full bg-gold text-black py-4 rounded-xl font-black uppercase tracking-widest text-xs hover:bg-white transition shadow-lg">
              Download PDF
            </a>
          </div>
        </div>
      `;

      $('#propertyDetails').html(html);

      // (Logic Preserved) Gallery click logic
      $('.gallery-thumb').click(function () {
        $('#mainImage').attr('src', $(this).attr('src'));
        $('.gallery-thumb').removeClass('active-thumb border-gold');
        $(this).addClass('active-thumb border-gold');
      });

      // (Logic Preserved) Gallery scroll
      const gallery = $('#galleryContainer');
      $('#galleryLeft').click(() => { gallery.animate({ scrollLeft: '-=200' }, 300); });
      $('#galleryRight').click(() => { gallery.animate({ scrollLeft: '+=200' }, 300); });

      // (Logic Preserved) WhatsApp logic
      $('#interestBtn').click(function () {
        const messageText = `🏠 *${property.title}*\n📍 *Location:* ${property.location}\n🖼 *Link:* ${window.location.href}\n\nHello DD Associates, I am interested in this property. Please share more details.`.trim();
        const whatsappLink = "https://wa.me/919953792555?text=" + encodeURIComponent(messageText);
        window.open(whatsappLink, '_blank');
      });

      // (Logic Preserved) Modal Triggers
      $('#bookVisitBtn').click(function () {
        $('#propertyId').val(property.id);
        $('#propertyName').val(property.title);
        $('#propertyLocation').val(property.location);
        $('#bookingModal').removeClass('hidden').addClass('flex');
      });

      $(document).on('click', '#closeModal', function () {
        $('#bookingModal').addClass('hidden').removeClass('flex');
      });


      const today = new Date().toISOString().split('T')[0];
      $('#visitDate').attr('min', today);
    });
  });
</script>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<?php
if (isset($conn)) {
  $conn->close();
}
include __DIR__ . "/includes/footer.php";
?>