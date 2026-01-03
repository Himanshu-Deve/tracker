<?php 
  $title = "Contact Us — DD Associates"; 
  include __DIR__ . "/includes/header.php"; 
?>

<style>
  :root { --gold: #D4AF37; --dark-bg: #0f0f10; }

  /* Map Grayscale Filter for Luxury Look */
  .map-container iframe {
    filter: grayscale(100%) invert(90%) contrast(90%);
    transition: filter 0.5s ease;
  }
  .map-container:hover iframe {
    filter: grayscale(0%) invert(0%) contrast(100%);
  }

  .contact-icon-box {
    background: linear-gradient(135deg, #1a1a1b 0%, #000000 100%);
    border: 1px solid rgba(212, 175, 55, 0.3);
  }

  .form-input {
    background-color: #f9f9f9;
    border: 1px solid #eee;
    transition: all 0.3s ease;
  }

  .form-input:focus {
    background-color: #fff;
    border-color: var(--gold);
    box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
    outline: none;
  }

  .btn-premium {
    background-color: var(--dark-bg);
    color: var(--gold);
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    font-weight: 800;
  }

  .btn-premium:hover {
    background-color: var(--gold);
    color: #000;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
  }
</style>

<section class="bg-white text-gray-900 overflow-hidden">
  
  <div class="w-full h-[450px] map-container relative">
    <div class="absolute inset-0 pointer-events-none shadow-[inset_0px_0px_100px_rgba(0,0,0,0.2)] z-10"></div>
    <iframe 
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8354345094433!2d144.9537363159046!3d-37.81627974202108!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d43f1f3e1b7%3A0x5045675218ce840!2sMelbourne%20VIC%2C%20Australia!5e0!3m2!1sen!2sin!4v1702532078940!5m2!1sen!2sin" 
      width="100%" height="100%" 
      style="border:0;" allowfullscreen="" loading="lazy" 
      referrerpolicy="no-referrer-when-downgrade">
    </iframe>
  </div>

  <div class="max-w-7xl mx-auto px-6 lg:px-16 py-24">
    <div class="grid lg:grid-cols-5 gap-16 items-start">
      
      <div class="lg:col-span-2">
        <nav class="flex text-[10px] font-bold uppercase tracking-[0.3em] text-gold mb-4">
            <span>Inquiries</span> <span class="mx-2">/</span> <span class="text-gray-400">Consultation</span>
        </nav>
        <h1 class="text-5xl font-black mb-6 text-gray-900 uppercase tracking-tighter">Let's Build <br/> <span class="text-gold">Together</span></h1>
        <p class="text-gray-500 mb-12 text-lg font-light leading-relaxed">
          Whether you're looking for a bespoke residential design or a strategic commercial development, our partners are ready to assist.
        </p>

        <div class="space-y-8">
          <div class="flex items-start space-x-6 group">
            <div class="contact-icon-box w-14 h-14 shrink-0 rounded-xl flex items-center justify-center text-gold transition-transform group-hover:scale-110">
              <i class="fa-solid fa-location-dot text-xl"></i>
            </div>
            <div>
              <p class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-1">Corporate Office</p>
              <p class="text-gray-800 font-bold leading-snug">123 Business Avenue, <br/>Melbourne, Australia</p>
            </div>
          </div>

          <div class="flex items-start space-x-6 group">
            <div class="contact-icon-box w-14 h-14 shrink-0 rounded-xl flex items-center justify-center text-gold transition-transform group-hover:scale-110">
              <i class="fa-solid fa-phone text-xl"></i>
            </div>
            <div>
              <p class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-1">Direct Line</p>
              <p class="text-gray-800 font-bold">+61 3 1234 5678</p>
              <p class="text-gray-400 text-sm">Mon - Fri, 9am - 6pm</p>
            </div>
          </div>

          <div class="flex items-start space-x-6 group">
            <div class="contact-icon-box w-14 h-14 shrink-0 rounded-xl flex items-center justify-center text-gold transition-transform group-hover:scale-110">
              <i class="fa-solid fa-envelope text-xl"></i>
            </div>
            <div>
              <p class="text-[10px] uppercase font-black tracking-widest text-gray-400 mb-1">Digital Correspondence</p>
              <p class="text-gray-800 font-bold">concierge@ddassociates.com</p>
              <p class="text-gray-400 text-sm">Fast response within 24h</p>
            </div>
          </div>
        </div>
      </div>

      <div class="lg:col-span-3 bg-white p-10 md:p-14 rounded-[40px] shadow-[0_30px_100px_rgba(0,0,0,0.08)] border border-gray-50 relative">
        <div class="absolute top-10 right-10 opacity-5">
            <i class="fa-solid fa-paper-plane text-8xl text-dark-bg"></i>
        </div>
        
        <h2 class="text-3xl font-black mb-2 text-gray-900 uppercase tracking-tighter">Send an Inquiry</h2>
        <p class="text-gray-400 text-sm mb-10 font-medium">Please fill out the form below and an associate will contact you.</p>

        <form action="contact_mail.php" method="POST" class="space-y-6">
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label class="text-[10px] uppercase font-black tracking-widest text-gray-500 ml-1">Your Name</label>
              <input type="text" name="name" required placeholder="Full Name" 
                class="mt-2 w-full rounded-2xl form-input px-6 py-4 text-sm font-semibold" />
            </div>
            <div>
              <label class="text-[10px] uppercase font-black tracking-widest text-gray-500 ml-1">Email Address</label>
              <input type="email" name="email" required placeholder="email@domain.com"
                class="mt-2 w-full rounded-2xl form-input px-6 py-4 text-sm font-semibold" />
            </div>
          </div>

          <div>
            <label class="text-[10px] uppercase font-black tracking-widest text-gray-500 ml-1">Mobile Number</label>
            <input type="tel" name="phone" required placeholder="+61 ..."
              class="mt-2 w-full rounded-2xl form-input px-6 py-4 text-sm font-semibold" />
          </div>

          <div>
            <label class="text-[10px] uppercase font-black tracking-widest text-gray-500 ml-1">Message / Requirements</label>
            <textarea name="message" rows="4" required placeholder="How can we help you?"
              class="mt-2 w-full rounded-2xl form-input px-6 py-4 text-sm font-semibold"></textarea>
          </div>

          <button type="submit" 
            class="w-full btn-premium py-5 rounded-2xl shadow-xl flex items-center justify-center gap-4 text-sm">
            Initialize Consultation <i class="fa-solid fa-arrow-right-long text-xs"></i>
          </button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . "/includes/footer.php"; ?>