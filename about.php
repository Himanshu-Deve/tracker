<?php 
$title = "About Us"; 
include __DIR__ . "/includes/header.php"; 

// Updated JSON Data with the correct company name
$companyData = json_decode('{
  "company_name": "DD ASSOCIATES",
  "tagline": "Premium Real Estate Advisory & Property Consulting",
  "about": "DD Associates is a modern real estate consultancy dedicated to delivering transparent, data-driven and customer-first property solutions across Gurugram and NCR. With a strong focus on quality, verified listings and client satisfaction, we help families, investors, and businesses make informed real estate decisions with confidence.",
  "mission": "To redefine the real estate buying experience through technology, trust, and professionalism.",
  "highlights": [
    "10+ Years of Real Estate Expertise",
    "1000+ Successful Transactions Completed",
    "Specialized in Residential, Commercial & Investment Properties",
    "Trusted by NRI, Corporate & High-Net-Worth Clients"
  ],
  "values": [
    {
      "title": "Transparency",
      "description": "Every listing is verified to ensure accuracy, honesty and clarity for every client."
    },
    {
      "title": "Expert Guidance",
      "description": "We assist clients with detailed market insights and unbiased consultation."
    },
    {
      "title": "Customer First",
      "description": "Your requirement, budget, and long-term value always remain our priority."
    }
  ],
  "locations": [
    {
      "title": "Gurugram",
      "description": "Premium residential and commercial spaces across Golf Course Road, MG Road & NH8.",
      "image": "https://images.unsplash.com/photo-1560523159-74b3f21f6b97?auto=format&fit=crop&w=1200&q=80"
    },
    {
      "title": "Cyber City",
      "description": "Prime corporate & leasing opportunities in the business capital of NCR.",
      "image": "https://images.unsplash.com/photo-1580741572259-c094f8a981c9?auto=format&fit=crop&w=1200&q=80"
    },
    {
      "title": "Luxury Apartments",
      "description": "Exclusive high-rise apartments with modern lifestyle amenities.",
      "image": "https://images.unsplash.com/photo-1501183638710-841dd1904471?auto=format&fit=crop&w=1200&q=80"
    }
  ],
  "team": [
    {
      "name": "Amit Sharma",
      "role": "Founder & Lead Consultant",
      "description": "Amit brings 10+ years of experience delivering real estate solutions for residential & corporate clients."
    },
    {
      "name": "Priya Mehta",
      "role": "Operations Head",
      "description": "Ensures smooth service operations, client onboarding, and documentation handling."
    },
    {
      "name": "Rohit Verma",
      "role": "Property Specialist",
      "description": "Expert in project tours, locality insights & property comparisons."
    }
  ]
}', true);
?>

<style>
  .text-gold { color: #D4AF37; }
  .bg-gold { background-color: #D4AF37; }
  .border-gold { border-color: #D4AF37; }
</style>

<section class="relative">
  <div class="h-[450px] bg-cover bg-fixed bg-center" 
       style="background-image:url('https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=80');">
    <div class="bg-black/70 h-full w-full flex items-center justify-center text-center px-4">
      <div>
        <span class="text-gold uppercase tracking-[0.3em] text-sm font-semibold mb-4 block">Our Story</span>
        <h1 class="text-4xl md:text-6xl font-bold text-white uppercase tracking-tighter">
          About <span class="text-gold"><?= $companyData["company_name"] ?></span>
        </h1>
        <div class="w-24 h-1 bg-gold mx-auto mt-6"></div>
        <p class="text-lg md:text-xl text-gray-300 mt-6 max-w-2xl mx-auto italic">
          <?= $companyData["tagline"] ?>
        </p>
      </div>
    </div>
  </div>
</section>


<section class="bg-black text-white py-20 px-4">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-16">
      <h2 class="text-3xl md:text-4xl font-bold uppercase tracking-widest text-gold">Who We Are</h2>
      <p class="mt-6 text-gray-400 max-w-4xl mx-auto leading-loose text-lg">
        <?= $companyData["about"] ?>
      </p>
    </div>

    <ul class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
      <?php foreach ($companyData["highlights"] as $point): ?>
        <li class="flex items-center space-x-4 bg-[#111] p-6 rounded-lg border border-yellow-900/20 hover:border-gold/50 transition duration-500">
          <div class="h-10 w-10 rounded-full border border-gold flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check text-gold text-sm"></i>
          </div>
          <p class="text-gray-200 font-medium"><?= $point ?></p>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>


<section class="bg-[#0a0a0a] py-20 border-y border-yellow-900/20">
  <div class="max-w-7xl mx-auto px-4 text-center">
    <i class="fas fa-quote-left text-gold text-4xl mb-6 opacity-50"></i>
    <h2 class="text-3xl font-bold text-white uppercase tracking-widest">Our Mission</h2>
    <p class="mt-6 text-gray-300 max-w-3xl mx-auto text-2xl font-light leading-relaxed">
      "<?= $companyData["mission"] ?>"
    </p>
  </div>
</section>


<section class="bg-black py-20 px-4">
  <div class="max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold text-gold text-center uppercase tracking-widest">Where We Operate</h2>
    <p class="text-gray-500 text-center mt-3 max-w-xl mx-auto">Prime localities we specialize in.</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mt-12">
      <?php foreach ($companyData["locations"] as $loc): ?>
        <div class="group relative overflow-hidden rounded-xl bg-[#111] border border-yellow-900/10">
          <div class="overflow-hidden">
            <img src="<?= $loc["image"] ?>" alt="<?= $loc["title"] ?>" class="h-64 w-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-110 transition duration-700">
          </div>
          <div class="p-8">
            <h3 class="text-xl font-bold text-gold tracking-wider"><?= $loc["title"] ?></h3>
            <div class="w-12 h-0.5 bg-gold/30 my-4 group-hover:w-full transition-all duration-500"></div>
            <p class="text-gray-400 text-sm leading-relaxed"><?= $loc["description"] ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<section class="bg-[#0a0a0a] py-20 px-4 border-t border-yellow-900/20">
  <div class="max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold text-white text-center uppercase tracking-widest mb-12">Our <span class="text-gold">Core Values</span></h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-10">
      <?php foreach ($companyData["values"] as $value): ?>
        <div class="bg-black border border-yellow-900/10 rounded-xl p-10 text-center hover:border-gold transition duration-500">
          <h3 class="text-xl font-bold text-gold uppercase tracking-tighter mb-4"><?= $value["title"] ?></h3>
          <p class="text-gray-400 leading-relaxed"><?= $value["description"] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<section class="bg-black py-20 px-4">
  <div class="max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold text-gold text-center uppercase tracking-widest">Leadership Team</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mt-16">
      <?php foreach ($companyData["team"] as $member): ?>
        <div class="bg-[#111] p-8 rounded-2xl border-b-4 border-transparent hover:border-gold transition-all duration-500 shadow-2xl">
          <div class="h-16 w-16 bg-gold/10 rounded-full flex items-center justify-center mb-6">
            <i class="fas fa-user-tie text-gold text-2xl"></i>
          </div>
          <h4 class="text-xl font-bold text-white"><?= $member["name"] ?></h4>
          <p class="text-sm text-gold font-bold uppercase tracking-widest mt-1"><?= $member["role"] ?></p>
          <p class="mt-4 text-gray-500 text-sm leading-loose"><?= $member["description"] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . "/includes/footer.php"; ?>