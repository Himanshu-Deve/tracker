<?php $title = "DD ASSOCIATES — Premium Real Estate"; include __DIR__ . "/includes/header.php"; ?>

<style>
  :root { --gold: #D4AF37; --gold-hover: #f4a460; }
  .text-gold { color: var(--gold); }
  .bg-gold { background-color: var(--gold); }
  .border-gold { border-color: var(--gold); }

  /* 3D Floating Building Effect */
  .hero-3d-container {
    perspective: 1200px;
  }
  .floating-building {
    transform: rotateY(-12deg) rotateX(4deg);
    transition: transform 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
    box-shadow: -30px 30px 60px rgba(0,0,0,0.6);
    animation: floatAnim 6s ease-in-out infinite;
  }
  .floating-building:hover {
    transform: rotateY(-2deg) rotateX(0deg) scale(1.03);
  }
  @keyframes floatAnim {
    0%, 100% { transform: rotateY(-12deg) rotateX(4deg) translateY(0); }
    50% { transform: rotateY(-12deg) rotateX(4deg) translateY(-15px); }
  }

  /* Custom scrollbar hide utility */
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<section class="relative min-h-screen bg-[#0f0f10] overflow-hidden font-sans flex items-center pt-24 lg:pt-0">
  <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-gold/5 to-transparent z-0"></div>
  <div class="absolute bottom-0 left-0 w-full h-[25%] bg-[#0f0f10] z-0"></div>

  <div class="relative z-20 max-w-[1400px] mx-auto px-6 lg:px-16 w-full grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
    
    <div class="text-white text-center lg:text-left">
      <div class="inline-flex items-center gap-3 mb-6">
        <div class="w-12 h-[1px] bg-gold"></div>
        <p class="text-gold uppercase tracking-[0.4em] text-xs font-bold">Premium Living</p>
      </div>
      
      <h1 class="text-5xl lg:text-8xl font-bold leading-[1.05] mb-8 tracking-tighter">
        Creating <br />
        <span class="text-gold">Iconic Spaces</span>
      </h1>

      <p class="text-gray-400 max-w-md mb-12 text-lg leading-relaxed mx-auto lg:mx-0 opacity-80">
        Bespoke architecture and interior design solutions. We transform functional requirements into beautiful, high-value realities.
      </p>

      <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-8">
        <a id="cta-button" href="#projects" class="inline-flex items-center gap-6 border border-white/20 rounded-full pl-8 pr-2 py-2 hover:bg-white hover:text-black hover:border-gold transition-all group">
          <span class="font-bold uppercase tracking-widest text-xs">Explore Portfolio</span>
          <span class="w-12 h-12 bg-gold rounded-full flex items-center justify-center text-black group-hover:scale-110 transition-transform">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
            </svg>
          </span>
        </a>

        <div class="lg:hidden flex items-center gap-4 text-white">
          <div class="text-gold text-4xl font-bold">25+</div>
          <p class="text-[10px] uppercase tracking-widest leading-snug font-bold opacity-60">Years of <br/> experience</p>
        </div>
      </div>
    </div>

    <div class="hero-3d-container relative flex justify-center items-center">
      <!-- <div class="hidden lg:flex absolute -left-24 top-1/2 -translate-y-1/2 flex-col items-start gap-6 text-white z-30">
          <div class="flex flex-col text-gold font-light text-2xl">
              <span class="animate-bounce">↓</span>
          </div>
          <p class="text-[10px] uppercase tracking-[0.3em] vertical-rl rotate-180 opacity-40 font-bold">
            Studio Design & Research
          </p>
      </div> -->

      <img
        src="./assets/images/home-building.jpg"
        alt="Modern Architecture"
        class="floating-building w-full max-w-[550px] h-auto object-cover rounded-2xl z-10"
        style="mask-image: linear-gradient(to top, black 98%, transparent 100%);"
      />
      
      <div class="hidden lg:block absolute -left-12 -bottom-20 z-20 bg-white p-10 shadow-[0_20px_50px_rgba(0,0,0,0.1)] rounded-tr-[60px]">
        <div class="text-gold text-6xl font-black leading-none">10+</div>
        <div class="text-[9px] font-black uppercase tracking-[0.3em] mt-4 text-gray-500">
            Years of <br/> industry mastery
        </div>
      </div>
    </div>
  </div>
</section>

<section id="projects" class="py-24 bg-white">
  <div class="container mx-auto px-6">
    <div class="text-center mb-20">
        <h2 class="text-4xl font-bold text-gray-900 tracking-tighter uppercase">Signature Projects</h2>
        <div class="w-16 h-1 bg-gold mx-auto mt-6"></div>
    </div>
    <div id="completedProjects"   class="flex overflow-x-auto space-x-6 pb-4"></div>
  </div>
</section>

<section class="py-24 bg-gray-50 text-gray-900" id="accomplishment-section">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
      <div class="w-full relative">
        <div class="absolute -top-6 -left-6 w-32 h-32 border-t-2 border-l-2 border-gold z-0"></div>
        <img src="https://salvatori-dam.imgix.net/uploads/2021/02/MEDIA-GALLERY_Salvatori_Inspiration_Singapore-loft-style-1.jpg"
             alt="Interior Decor"
             class="relative z-10 rounded-xl shadow-2xl w-full object-cover">
      </div>

      <div>
        <div class="text-left mb-12">
          <span class="text-gold font-bold uppercase tracking-widest text-xs">Our Journey</span>
          <h2 class="text-4xl font-bold mt-2 mb-6 text-gray-900">Measuring Excellence</h2>
          <p class="text-lg text-gray-500 leading-relaxed font-light">
            We don't just build structures; we establish a presence. Our expansion across the NCR region is a testament to our transparent processes.
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
          <div class="bg-white p-8 rounded-xl shadow-sm border-b-4 border-gold">
            <div id="clients-count" class="text-4xl font-bold text-gray-900 mb-2">0+</div>
            <div class="text-[10px] uppercase font-black tracking-widest text-gray-400">Happy Clients</div>
          </div>
          <div class="bg-white p-8 rounded-xl shadow-sm border-b-4 border-gold">
            <div id="projects-count" class="text-4xl font-bold text-gray-900 mb-2">0+</div>
            <div class="text-[10px] uppercase font-black tracking-widest text-gray-400">Handovers</div>
          </div>
          <div class="bg-white p-8 rounded-xl shadow-sm border-b-4 border-gold">
            <div id="awards-count" class="text-4xl font-bold text-gray-900 mb-2">0+</div>
            <div class="text-[10px] uppercase font-black tracking-widest text-gray-400">Recognitions</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="py-24 bg-white mx-auto w-full overflow-hidden">
  <div class="text-center mb-16">
    <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4 tracking-tighter">CLIENT VOICES</h2>
    <p class="text-gold font-bold uppercase tracking-[0.3em] text-xs">Trusted by 8,000+ Customers</p>
  </div>

  <div id="loadingSpinner" class="loading-spinner mb-8 mx-auto"></div>

  <div id="testimonialsWrapper" class="relative max-w-[1440px] mx-auto">
    <button id="scrollLeft" class="hidden md:flex absolute -left-2 top-1/2 -translate-y-1/2 bg-white shadow-2xl rounded-full p-5 hover:bg-gold hover:text-white z-10 transition">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
    </button>

    <div id="testimonialsContainer" class="flex gap-8 overflow-x-auto px-12 pb-10 scroll-smooth no-scrollbar snap-x snap-mandatory"></div>

    <button id="scrollRight" class="hidden md:flex absolute -right-2 top-1/2 -translate-y-1/2 bg-white shadow-2xl rounded-full p-5 hover:bg-gold hover:text-white z-10 transition">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
    </button>
  </div>
</div>

<section id="faqSection" class="py-24 bg-gray-50">
    <div class="max-w-4xl mx-auto px-6">
        <h2 class="text-4xl font-bold text-center mb-16 text-gray-900 tracking-tighter">FREQUENTLY ASKED</h2>
        <div id="faqContainer" class="space-y-6"></div>
    </div>
</section>

<script>
$(function () {
    const faqContainer = $("#faqContainer");
    const faqError = $("#faqError");

    $.ajax({
        url: "/assets/data/faqs.json",
        type: "GET",
        dataType: "json",
        success: function (data) {
            if (!data.faqs || !Array.isArray(data.faqs) || data.faqs.length === 0) {
                return;
            }
            renderFaqs(data.faqs);
        }
    });

    function renderFaqs(faqs) {
        faqs.forEach(faq => {
            const item = `
                <div class="faq-item relative bg-white border border-gray-100 rounded-xl shadow-sm transition-all duration-300">
                    <button class="faq-toggle w-full flex justify-between items-center p-7 text-left focus:outline-none">
                        <span class="text-lg font-bold text-gray-800">${faq.question}</span>
                        <svg class="faq-arrow w-5 h-5 text-gold transform transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 px-7 text-gray-500 bg-gray-50 rounded-b-xl">${faq.answer}</div>
                    <div class="absolute left-0 top-0 h-full w-1 bg-gold rounded-l-xl"></div>
                </div>`;
            faqContainer.append(item);
        });

        $(".faq-toggle").on("click", function () {
            const content = $(this).next(".faq-content");
            const arrow = $(this).find(".faq-arrow");
            if (content.css("max-height") !== "0px") {
                content.css({"max-height": "0px", "padding-top": "0px", "padding-bottom": "0px"});
                arrow.removeClass("rotate-180");
            } else {
                content.css({"max-height": "300px", "padding-top": "30px", "padding-bottom": "30px"});
                arrow.addClass("rotate-180");
            }
        });
    }

    // Property Loading
   $.getJSON('/assets/data/properties.json', function (data) {

  const completed = data.filter(p => p.status === "new");
  const container = $("#completedProjects");

  completed.forEach(p => {

    container.append(`
      <!-- 1 card on mobile / 3 cards on desktop -->
       <a href="property.php?id=${p.id}" class="min-w-[90%] md:min-w-[33%] lg:min-w-[33%] flex-shrink-0">
      <div class="min-w-[90%] md:min-w-[33%] lg:min-w-[33%] 
                  group bg-white rounded-xl overflow-hidden 
                  shadow hover:shadow-2xl transition-all duration-500">

        <div class="overflow-hidden relative h-64">
          <img src="${p.image_thumbnail}" alt="${p.title}"
               class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
          <div class="absolute top-3 left-3 bg-black/60 text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase">
            Verified
          </div>
           <div class="absolute top-3 right-3 bg-green-600 text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase">
            New
          </div>
        </div>

        <div class="p-6">
          <h3 class="text-lg font-bold uppercase">${p.title}</h3>
          <p class="text-gray-400 text-xs uppercase mb-3">${p.location}</p>

          <div class="flex items-center text-yellow-400 text-xs">
            ${[1,2,3,4,5].map(i =>
              `<i class="fa-solid fa-star ${p.rating >= i ? '' : 'text-gray-300'}"></i>`
            ).join('')}
            <span class="ml-2 text-gray-500 font-bold text-sm">(${p.rating})</span>
          </div>
        </div>
      </div>
      </a>
    `);

  });
});


    // Testimonials
    function renderTestimonials(testimonials) {
        const container = $('#testimonialsContainer');
        testimonials.forEach(t => {
            container.append(`
                <div class="testimonial-card bg-white border border-gray-100 rounded-2xl shadow-sm p-10 w-96 flex-shrink-0 snap-center hover:shadow-xl transition">
                    <div class="text-gold text-lg mb-6">${'★'.repeat(t.rating)}</div>
                    <p class="text-gray-500 mb-8 italic leading-relaxed text-lg font-light">"${t.content}"</p>
                    <div class="flex items-center">
                        <div class="w-14 h-14 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mr-4 font-black text-gold text-xl shadow-inner">${t.name.charAt(0)}</div>
                        <div>
                            <h4 class="font-bold text-gray-900 uppercase tracking-tighter">${t.name}</h4>
                            <p class="text-gold text-[10px] font-bold uppercase tracking-widest">${t.title}</p>
                        </div>
                    </div>
                </div>`);
        });
    }

    $.getJSON('/assets/data/testimonials.json', function(data) {
        $('#loadingSpinner').hide();
        renderTestimonials(data.testimonials);
        $('#scrollLeft, #scrollRight').show();
    });

    $('#scrollLeft').click(() => $('#testimonialsContainer').animate({ scrollLeft: '-=400' }, 400));
    $('#scrollRight').click(() => $('#testimonialsContainer').animate({ scrollLeft: '+=400' }, 400));

    // Intersection Observer for Counters
    const counters = [
        { selector: '#clients-count', value: 126 },
        { selector: '#projects-count', value: 300 },
        { selector: '#awards-count', value: 25 }
    ];

    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            counters.forEach(c => {
                $({ n: 0 }).animate({ n: c.value }, {
                    duration: 2500,
                    step: function() { $(c.selector).text(Math.floor(this.n) + "+"); }
                });
            });
            observer.disconnect();
        }
    }, { threshold: 0.5 });
    observer.observe(document.querySelector('#accomplishment-section'));

    // CTA Link Scroll
    $('#cta-button').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: $('#projects').offset().top - 80 }, 800);
    });
});
</script>

<?php include __DIR__ . "/includes/footer.php"; ?>