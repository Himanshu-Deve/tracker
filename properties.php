<?php $title = "Browse Properties — DD Associates"; include __DIR__ . "/includes/header.php"; ?>

<style>
  :root { --gold: #D4AF37; --dark: #0f0f10; }
  
  /* Luxury UI Elements */
  .filter-chip { transition: all 0.3s ease; border: 1px solid #eee; }
  .filter-chip:hover { border-color: var(--gold); color: var(--gold); }
  
  .search-input:focus { border-color: var(--gold); box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.1); }
  
  /* Property Card Hover */
  .property-card { transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); }
  .property-card:hover { transform: translateY(-8px); }

  /* Custom Checkbox */
  .locationCheck { accent-color: var(--gold); cursor: pointer; }

  /* Animated Sidebar Overlay */
  #overlay { backdrop-filter: blur(4px); transition: opacity 0.3s ease; }
</style>

<section class="bg-[#fcfcfc] min-h-screen">
  <div class="bg-[#0f0f10] pt-28 pb-16 px-4">
    <div class="max-w-7xl mx-auto">
        <nav class="flex text-[10px] font-bold uppercase tracking-[0.3em] text-gray-500 mb-4">
            <span>Marketplace</span> <span class="mx-2">/</span> <span class="text-gold">Browse All</span>
        </nav>
        <h1 class="text-4xl md:text-6xl font-black text-white uppercase tracking-tighter mb-4">Find Your <span class="text-gold">Masterpiece</span></h1>
        <p class="text-gray-400 max-w-xl font-light">Curated premium real estate across the NCR region. Use our advanced filters to discover bespoke living spaces.</p>
    </div>
  </div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
    <div class="bg-white p-4 rounded-2xl shadow-xl flex items-center gap-4 border border-gray-100">
      <div class="flex-1 relative">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
        <input id="searchText" type="text" placeholder="Search by title or location..."
          class="search-input w-full rounded-xl border-gray-100 pl-12 pr-4 py-4 text-sm focus:outline-none transition-all" />
      </div>
      <button id="toggleFilters"
        class="flex items-center gap-2 px-8 py-4 bg-[#0f0f10] text-gold rounded-xl font-bold uppercase tracking-widest text-xs transition-all shadow-lg">
        <i class="fas fa-sliders-h"></i> Filters
      </button>
    </div>

    <div class="mt-10 flex items-center justify-between border-b border-gray-100 pb-4">
      <div class="flex items-center gap-2">
          <span class="w-2 h-2 bg-gold rounded-full animate-pulse"></span>
          <p class="text-[11px] font-black uppercase tracking-widest text-gray-500">Live Catalog: <span id="resultsCount" class="text-gray-900">0</span> Properties</p>
      </div>
      <div id="activeFiltersContainer" class="hidden md:flex gap-2">
          </div>
    </div>

    <div id="propertiesGrid" class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 pb-20">
        <div class="animate-pulse bg-white h-96 rounded-2xl"></div>
        <div class="animate-pulse bg-white h-96 rounded-2xl"></div>
        <div class="animate-pulse bg-white h-96 rounded-2xl"></div>
    </div>
  </div>
</section>

<div id="filterSidebar"
  class="fixed top-0 left-0 w-80 h-full bg-white shadow-2xl transform -translate-x-full transition-transform duration-500 z-[100] overflow-y-auto">
  <div class="p-8 border-b border-gray-100 flex items-center justify-between bg-[#0f0f10]">
    <div>
        <h2 class="text-gold font-black uppercase tracking-widest text-sm">Refine Search</h2>
        <p class="text-[9px] text-gray-500 uppercase tracking-widest">Adjust your parameters</p>
    </div>
    <button id="closeFilters" class="w-8 h-8 rounded-full bg-white/10 text-white hover:bg-gold hover:text-black transition flex items-center justify-center font-light">&times;</button>
  </div>

  <div class="p-8 space-y-8">
    <div>
      <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 block mb-4">Project Status</label>
      <select id="statusFilter"
        class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 text-sm focus:ring-gold focus:border-gold">
        <option value="all">All Properties</option>
        <option value="new">Newly Launched</option>
        <option value="complete">Ready to Move</option>
      </select>
    </div>

    <div>
      <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 block mb-4">Minimum Configuration</label>
      <div class="grid grid-cols-2 gap-2">
          <select id="bedsFilter" class="col-span-2 w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 text-sm focus:ring-gold">
            <option value="0">Any BHK</option>
            <option value="1">1+ BHK</option>
            <option value="2">2+ BHK</option>
            <option value="3">3+ BHK</option>
            <option value="4">4+ BHK</option>
          </select>
      </div>
    </div>

    <div>
      <label class="text-[10px] uppercase font-black tracking-widest text-gray-400 block mb-4">Preferred Locations</label>
      <div id="locationFilter" class="space-y-3 bg-gray-50 p-4 rounded-xl max-h-60 overflow-y-auto custom-scrollbar">
        </div>
    </div>

    <div class="pt-6">
        <button id="resetFilters" class="w-full bg-gray-100 hover:bg-red-50 hover:text-red-600 text-[10px] font-black uppercase tracking-widest py-4 rounded-xl transition-all">
            Clear All Filters
        </button>
    </div>
  </div>
</div>

<div id="overlay" class="fixed inset-0 bg-black/60 hidden z-[90]"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
  let properties = [];

  // Logic Preserved: Load JSON and locations
  $.getJSON('/assets/data/properties.json', function (data) {
    properties = data;
    const locations = [...new Set(properties.map(p => p.location))];
    
    let locHtml = "";
    locations.forEach(loc => {
      locHtml += `
        <label class="flex items-center gap-3 group cursor-pointer">
          <input type="checkbox" value="${loc}" class="locationCheck w-4 h-4 rounded border-gray-300">
          <span class="text-sm text-gray-600 group-hover:text-gold transition font-medium">${loc}</span>
        </label>`;
    });
    $("#locationFilter").html(locHtml);
    renderProperties(properties);
  });

  // Logic Preserved: Sidebar toggle
  $('#toggleFilters').click(() => {
    $('#filterSidebar').removeClass('-translate-x-full');
    $('#overlay').removeClass('hidden').addClass('block');
  });
  $('#closeFilters, #overlay').click(() => {
    $('#filterSidebar').addClass('-translate-x-full');
    $('#overlay').addClass('hidden').removeClass('block');
  });

  // Logic Preserved: Filter inputs
  $(document).on("change", ".locationCheck", function () { applyFilters(); });
  $('#searchText, #bedsFilter, #statusFilter').on('input change', function () { applyFilters(); });

  $('#resetFilters').click(function () {
    $('#searchText').val('');
    $('#bedsFilter').val('0');
    $('#statusFilter').val('all');
    $('.locationCheck').prop('checked', false);
    applyFilters();
  });

  function applyFilters() {
    const search = $('#searchText').val().toLowerCase();
    const beds = parseInt($('#bedsFilter').val()) || 0;
    const status = $('#statusFilter').val();
    const selectedLocations = $('.locationCheck:checked').map(function () { return this.value; }).get();

    const filtered = properties.filter(p => {
      return (
        (p.title.toLowerCase().includes(search) || p.location.toLowerCase().includes(search)) &&
        p.beds >= beds &&
        (status === "all" || p.status === status) &&
        (selectedLocations.length === 0 || selectedLocations.includes(p.location))
      );
    });
    renderProperties(filtered);
  }

  function renderProperties(list) {
    const grid = $('#propertiesGrid');
    grid.empty();
    $('#resultsCount').text(list.length);

    if (list.length === 0) {
      grid.html('<div class="col-span-full py-20 text-center"><i class="fas fa-search text-4xl text-gray-200 mb-4"></i><p class="text-gray-400 uppercase tracking-widest text-xs font-bold">No properties match your selection</p></div>');
      return;
    }

    list.forEach(p => {
      const card = `
      <a href="property.php?id=${p.id}" class="property-card group block bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl border border-gray-50">
        <div class="relative h-64 overflow-hidden">
          <img src="${p.image_thumbnail}" alt="${p.title}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
          <div class="absolute top-4 left-4">
             <span class="px-4 py-1.5 text-[9px] font-black uppercase tracking-[0.2em] rounded-full shadow-lg ${p.status === 'new' ? 'bg-green-500 text-white' : 'bg-gold text-black'}">
               ${p.status}
             </span>
          </div>
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
             <span class="text-white text-xs font-bold uppercase tracking-widest">View Property Details —</span>
          </div>
        </div>

        <div class="p-8">
          <div class="flex justify-between items-start mb-2">
              <h2 class="font-black text-xl text-gray-900 uppercase tracking-tighter leading-tight">${p.title}</h2>
          </div>
          
          <div class="flex items-center text-gold text-[10px] mb-4">
            ${[1,2,3,4,5].map(i => `<i class="fa-solid fa-star ${p.rating >= i ? '' : 'text-gray-200'}"></i>`).join('')}
            <span class="ml-2 text-gray-400 font-bold">(${p.rating})</span>
          </div>

          <p class="text-xs text-gray-400 font-bold uppercase tracking-widest flex items-center gap-2 mb-6">
            <i class="fas fa-location-dot text-gold"></i> ${p.location}
          </p>
          
          <div class="flex items-center justify-between pt-6 border-t border-gray-50">
            <div class="flex gap-4">
                <div class="text-center">
                    <span class="block text-[10px] font-black text-gray-900">${p.beds}</span>
                    <span class="block text-[8px] uppercase tracking-widest text-gray-400 font-bold">Beds</span>
                </div>
                <div class="w-[1px] h-6 bg-gray-100"></div>
                <div class="text-center">
                    <span class="block text-[10px] font-black text-gray-900">${p.area}</span>
                    <span class="block text-[8px] uppercase tracking-widest text-gray-400 font-bold">SqFt</span>
                </div>
            </div>
            <i class="fas fa-arrow-right text-gray-200 group-hover:text-gold transition-colors"></i>
          </div>
        </div>
      </a>`;
      grid.append(card);
    });
  }
});
</script>

<?php include __DIR__ . "/includes/footer.php"; ?>