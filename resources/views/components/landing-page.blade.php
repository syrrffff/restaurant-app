<div x-data="{
    activeTab: 'top',
    lightboxOpen: false,
    lightboxImg: '',
    lightboxTitle: '',
    lightboxDesc: '',
    lightboxPrice: ''
}"
class="font-sans text-gray-700 antialiased overflow-x-hidden">

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<nav x-data="{ mobileMenuOpen: false }" class="fixed w-full z-50 bg-white/90 backdrop-blur-md shadow-sm transition-all duration-300">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="#" class="text-2xl font-bold text-gray-800 tracking-tight">
            Riff Restaurants
        </a>

        <div class="hidden md:flex space-x-8 font-semibold text-gray-500">
            <a href="#hero" class="hover:text-red-600 transition">Home</a>
            <a href="#about" class="hover:text-red-600 transition">About</a>
            <a href="#menu" class="hover:text-red-600 transition">Menu</a>
            <a href="#events" class="hover:text-red-600 transition">Events</a>
            <a href="#gallery" class="hover:text-red-600 transition">Gallery</a>
            <a href="#contact" class="hover:text-red-600 transition">Contact</a>
        </div>

        <div class="flex items-center gap-4">
            <a href="#contact" class="hidden sm:inline-block bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-full font-bold shadow-md transition transform hover:scale-105">
                Book a Table
            </a>

            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-800 hover:text-red-600 focus:outline-none">
                <svg x-show="!mobileMenuOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                <svg x-show="mobileMenuOpen" style="display: none;" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <div x-show="mobileMenuOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 -translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-4"
    style="display: none;"
    class="md:hidden absolute top-full left-0 w-full bg-white shadow-lg border-t border-gray-100">

    <div class="flex flex-col px-6 py-4 space-y-4 font-semibold text-gray-600 text-center pb-6">
        <a @click="mobileMenuOpen = false" href="#hero" class="hover:text-red-600 transition">Home</a>
        <a @click="mobileMenuOpen = false" href="#about" class="hover:text-red-600 transition">About</a>
        <a @click="mobileMenuOpen = false" href="#menu" class="hover:text-red-600 transition">Menu</a>
        <a @click="mobileMenuOpen = false" href="#events" class="hover:text-red-600 transition">Events</a>
        <a @click="mobileMenuOpen = false" href="#gallery" class="hover:text-red-600 transition">Gallery</a>
        <a @click="mobileMenuOpen = false" href="#contact" class="hover:text-red-600 transition">Contact</a>

        <a @click="mobileMenuOpen = false" href="#contact" class="sm:hidden inline-block bg-red-600 text-white px-5 py-2.5 rounded-full mt-2 font-bold shadow-md">Book a Table</a>
    </div>
</div>
</nav>

<section id="hero" class="bg-gray-50 min-h-screen flex items-center pt-20 overflow-hidden">
    <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

        <div class="relative z-10" data-aos="fade-up" data-aos-duration="1000">
            <h1 class="text-5xl md:text-6xl font-extrabold text-gray-800 leading-tight mb-6">
                Enjoy Your Healthy <br>Delicious Food
            </h1>
            <p class="text-gray-500 mb-8 text-lg">
                Kami menyajikan hidangan lezat dengan bahan berkualitas tinggi. Rasakan pengalaman kuliner tak terlupakan bersama kami.
            </p>
            <div class="flex gap-4">
                <a href="#menu" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-full font-bold shadow-lg transition transform hover:-translate-y-1">Lihat Menu</a>
                <a href="#about" class="bg-white hover:bg-gray-100 text-gray-800 px-8 py-3 rounded-full font-bold shadow-md transition border border-gray-200">Tentang Kami</a>
            </div>
        </div>

        <div class="relative z-0 order-first md:order-last" data-aos="zoom-in" data-aos-duration="1000">
            <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Hero Dish" class="w-full h-auto drop-shadow-2xl animate-[spin_60s_linear_infinite]">
        </div>

    </div>
</section>

<section id="about" class="py-20 bg-white" x-data="{ videoOpen: false }">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16" data-aos="fade-up">
            <h3 class="text-gray-400 font-bold tracking-widest uppercase text-sm mb-2">About Us</h3>
            <h2 class="text-3xl font-bold text-gray-800">Learn More <span class="text-red-600">About Us</span></h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="relative cursor-pointer group" data-aos="fade-right" @click="videoOpen = true">
                <img src="https://images.unsplash.com/photo-1514933651103-005eec06c04b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="About Restaurant" class="rounded-xl shadow-xl w-full transition duration-500 group-hover:shadow-2xl">
                <div class="absolute inset-0 flex items-center justify-center bg-black/20 rounded-xl group-hover:bg-black/10 transition duration-300">
                    <button class="w-20 h-20 bg-red-600 text-white rounded-full flex items-center justify-center text-3xl shadow-[0_0_20px_rgba(220,38,38,0.5)] group-hover:scale-110 transition duration-300 animate-pulse pl-1">
                        <i class="fa-solid fa-play"></i>
                    </button>
                </div>
            </div>

            <div data-aos="fade-left">
                <p class="italic text-gray-600 mb-4">
                    Berdiri sejak tahun 2010, Yummy Restaurant berkomitmen untuk menyajikan hidangan nusantara dan internasional dengan sentuhan modern.
                </p>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-start gap-3"><span class="text-red-600 font-bold"><i class="fa-solid fa-check-double"></i></span> Bahan baku segar dari petani lokal setiap harinya.</li>
                    <li class="flex items-start gap-3"><span class="text-red-600 font-bold"><i class="fa-solid fa-check-double"></i></span> Koki bersertifikat internasional dengan pengalaman puluhan tahun.</li>
                    <li class="flex items-start gap-3"><span class="text-red-600 font-bold"><i class="fa-solid fa-check-double"></i></span> Suasana nyaman, cocok untuk kumpul keluarga maupun rapat bisnis.</li>
                </ul>
                <p class="text-gray-600">Kami percaya bahwa makanan bukan sekadar pengisi perut, melainkan seni yang menyatukan orang-orang.</p>
            </div>
        </div>
    </div>

    <template x-teleport="body">
        <div x-show="videoOpen" style="display: none;" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/90 p-4" x-transition.opacity>
            <button @click="videoOpen = false" class="absolute top-6 right-6 text-white hover:text-red-500 text-4xl font-bold z-50">&times;</button>

            <div class="w-full max-w-4xl aspect-video bg-black rounded-xl overflow-hidden shadow-2xl relative" @click.outside="videoOpen = false">
                <template x-if="videoOpen">
                    <iframe
                    class="absolute top-0 left-0 w-full h-full"
                    src="https://www.youtube.com/embed/WdWEMXnHBVI?si=E0DUPFP_6rGAR7Ij"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen>
                </iframe>
            </template>
        </div>
    </div>
</template>
</section>

<section id="menu" class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12" data-aos="fade-up">
            <h3 class="text-gray-400 font-bold tracking-widest uppercase text-sm mb-2">Our Menu</h3>
            <h2 class="text-3xl font-bold text-gray-800">Check Our <span class="text-red-600">Riff's Menu</span></h2>
        </div>

        <div class="flex flex-wrap justify-center gap-4 mb-12" data-aos="fade-up" data-aos-delay="100">
            <button @click="activeTab = 'top'" :class="activeTab === 'top' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500 hover:text-red-600'" class="pb-2 font-bold text-lg transition">
                Best Seller
            </button>

            @foreach($categories as $category)
            <button @click="activeTab = '{{ $category->id }}'" :class="activeTab === '{{ $category->id }}' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500 hover:text-red-600'" class="pb-2 font-bold text-lg transition">
                {{ $category->name }}
            </button>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 relative min-h-[400px]">

            @foreach($topMenus as $menu)
            <div x-show="activeTab === 'top'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" style="display: none;"
            class="bg-white rounded-xl shadow-sm hover:shadow-xl transition duration-300 p-6 text-center group relative overflow-hidden">

            <div class="absolute top-4 left-4 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full z-10 shadow-md">
                🔥 Best Seller
            </div>

            <div class="relative overflow-hidden w-48 h-48 mx-auto rounded-full mb-6 cursor-pointer"
            @click="lightboxOpen = true; lightboxImg = '{{ $menu->image ? asset('storage/' . $menu->image) : 'https://placehold.co/400x400?text=No+Image' }}'; lightboxTitle = '{{ $menu->name }}'; lightboxDesc = '{{ $menu->description }}'; lightboxPrice = 'Rp {{ number_format($menu->base_price, 0, ',', '.') }}'">
            <img src="{{ $menu->image ? asset('storage/' . $menu->image) : 'https://placehold.co/400x400?text=No+Image' }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
        </div>

        <h4 class="text-xl font-bold text-gray-800 mb-2">{{ $menu->name }}</h4>
        <p class="text-sm text-gray-500 italic mb-4 line-clamp-2">{{ $menu->description ?? 'Menu favorit pilihan pelanggan kami.' }}</p>
        <div class="text-2xl font-bold text-red-600">Rp {{ number_format($menu->base_price, 0, ',', '.') }}</div>
    </div>
    @endforeach

    @foreach($categories as $category)
    @foreach($category->menus as $menu)
    <div x-show="activeTab === '{{ $category->id }}'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" style="display: none;"
       class="bg-white rounded-xl shadow-sm hover:shadow-xl transition duration-300 p-6 text-center group">

       <div class="relative overflow-hidden w-48 h-48 mx-auto rounded-full mb-6 cursor-pointer"
       @click="lightboxOpen = true; lightboxImg = '{{ $menu->image ? asset('storage/' . $menu->image) : 'https://placehold.co/400x400?text=No+Image' }}'; lightboxTitle = '{{ $menu->name }}'; lightboxDesc = '{{ $menu->description }}'; lightboxPrice = 'Rp {{ number_format($menu->base_price, 0, ',', '.') }}'">
       <img src="{{ $menu->image ? asset('storage/' . $menu->image) : 'https://placehold.co/400x400?text=No+Image' }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
   </div>

   <h4 class="text-xl font-bold text-gray-800 mb-2">{{ $menu->name }}</h4>
   <p class="text-sm text-gray-500 italic mb-4 line-clamp-2">{{ $menu->description ?? 'Hidangan spesial dari koki kami.' }}</p>
   <div class="text-2xl font-bold text-red-600">Rp {{ number_format($menu->base_price, 0, ',', '.') }}</div>
</div>
@endforeach
@endforeach

</div>
</div>
</section>

<section id="events" class="py-20 bg-cover bg-center relative" style="background-image: url('https://images.unsplash.com/photo-1533777857889-4be7c70b33f7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="container mx-auto px-6 relative z-10">
        <div class="text-center mb-12" data-aos="fade-up">
            <h3 class="text-red-500 font-bold tracking-widest uppercase text-sm mb-2">Events</h3>
            <h2 class="text-3xl font-bold text-white">Share Your Moments <br>In Our <span class="text-red-500">Restaurant</span></h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-xl" data-aos="fade-up" data-aos-delay="100">
                <h4 class="text-xl font-bold text-gray-800 mb-2">Private Parties</h4>
                <p class="text-3xl font-bold text-red-600 mb-4 border-b pb-4">Rp 2.000.000</p>
                <p class="text-gray-600 mb-4">Sewa ruangan khusus untuk acara privat Anda dengan dekorasi elegan dan pelayanan eksklusif.</p>
            </div>
            <div class="bg-red-600 p-8 rounded-xl text-white transform md:-translate-y-4 shadow-2xl" data-aos="fade-up" data-aos-delay="200">
                <h4 class="text-xl font-bold mb-2">Wedding Parties</h4>
                <p class="text-3xl font-bold mb-4 border-b border-red-500 pb-4">Mulai Rp 15 Juta</p>
                <p class="text-red-100 mb-4">Rayakan hari bahagia Anda bersama kami. Paket lengkap termasuk katering, dekorasi, dan MC.</p>
            </div>
            <div class="bg-white p-8 rounded-xl" data-aos="fade-up" data-aos-delay="300">
                <h4 class="text-xl font-bold text-gray-800 mb-2">Birthday Parties</h4>
                <p class="text-3xl font-bold text-red-600 mb-4 border-b pb-4">Rp 500.000</p>
                <p class="text-gray-600 mb-4">Paket perayaan ulang tahun meriah dengan bonus kue spesial dari koki pastry kami.</p>
            </div>
        </div>
    </div>
</section>

<section id="gallery" class="py-20 bg-gray-50 overflow-x-hidden">
    <div class="container mx-auto px-6">

        <div class="text-center mb-16" data-aos="fade-up">
            <h3 class="text-gray-400 font-bold tracking-widest uppercase text-sm mb-2">Gallery</h3>
            <h2 class="text-3xl font-bold text-gray-800">Check <span class="text-red-600">Our Gallery</span></h2>
            <p class="text-gray-500 mt-3 max-w-lg mx-auto">Intip suasana hangat dan kelezatan hidangan kami melalui lensa kamera.</p>
        </div>

        <div id="gallery-slider" class="splide relative" data-aos="fade-up" data-aos-delay="100">
            <div class="splide__track overflow-visible py-5">
                <ul class="splide__list flex gap-3">

                    @php
                    $galleryImages = [
                    ['src' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'title' => 'Proses Memasak Chef'],
                    ['src' => 'https://images.unsplash.com/photo-1544148103-0773bf10d330?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'title' => 'Menu Steak Spesial'],
                    ['src' => 'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'title' => 'Kumpul Keluarga Ceria'],
                    ['src' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'title' => 'Dekorasi Meja Makan'],
                    ['src' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'title' => 'Hidangan Nusantara Platter'],
                    ['src' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'title' => 'Minuman Segar Beralkohol'],
                    ];
                    @endphp

                    @foreach($galleryImages as $img)
                    <li class="splide__slide h-80 rounded-2xl overflow-hidden border-4 border-white shadow-lg">
                        <img src="{{ $img['src'] }}" alt="{{ $img['title'] }}" title="{{ $img['title'] }}" class="w-full h-full object-cover hover:scale-110 transition duration-700 cursor-grab active:cursor-grabbing">
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="splide__arrows flex gap-3 justify-center mt-10">
                <button class="splide__arrow splide__arrow--prev w-12 h-12 bg-white hover:bg-red-600 text-gray-800 hover:text-white rounded-full shadow-md transition flex items-center justify-center relative !opacity-100 !-left-0">
                    <i class="fa-solid fa-chevron-left text-lg"></i>
                </button>
                <button class="splide__arrow splide__arrow--next w-12 h-12 bg-white hover:bg-red-600 text-gray-800 hover:text-white rounded-full shadow-md transition flex items-center justify-center relative !opacity-100 !-right-0">
                    <i class="fa-solid fa-chevron-right text-lg"></i>
                </button>
            </div>
        </div>

    </div>
</section>

<section id="contact" class="py-20 bg-white">
    <div class="container mx-auto px-6">

        <div class="text-center mb-12" data-aos="fade-up">
            <h3 class="text-gray-400 font-bold tracking-widest uppercase text-sm mb-2">Contact</h3>
            <h2 class="text-3xl font-bold text-gray-800">Need Help? <span class="text-red-600">Contact Us</span></h2>
        </div>

        <div class="mb-10 rounded-xl overflow-hidden shadow-md border border-gray-100" data-aos="fade-up" data-aos-delay="100">
            <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.334057860472!2d107.733596!3d-6.932971!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68c33939697925%3A0xc48c0814fa864a78!2sCileunyi%2C%20Bandung%20Regency%2C%20West%20Java!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid"
            width="100%"
            height="350"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <div class="lg:col-span-5 space-y-6">

            <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 flex gap-4 items-start shadow-sm hover:shadow-md transition" data-aos="fade-right">
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xl shrink-0"><i class="fa-solid fa-map-location-dot"></i></div>
                <div>
                    <h4 class="font-bold text-gray-800 text-lg mb-1">Our Address</h4>
                    <p class="text-gray-600 text-sm">Jl. Cileunyi Raya No. 123, <br>Kabupaten Bandung, Jawa Barat</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-6">
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 flex gap-4 items-center shadow-sm hover:shadow-md transition" data-aos="fade-right" data-aos-delay="100">
                    <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xl shrink-0"><i class="fa-solid fa-phone"></i></div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">Call Us</h4>
                        <p class="text-gray-600 text-sm">+62 812 3456 7890</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 flex gap-4 items-center shadow-sm hover:shadow-md transition" data-aos="fade-right" data-aos-delay="200">
                    <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xl shrink-0"><i class="fa-solid fa-envelope"></i></div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">Email Us</h4>
                        <p class="text-gray-600 text-sm">info@resto.com</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 flex gap-4 items-start shadow-sm hover:shadow-md transition" data-aos="fade-right" data-aos-delay="300">
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xl shrink-0"><i class="fa-solid fa-clock"></i></div>
                <div class="w-full">
                    <h4 class="font-bold text-gray-800 text-lg mb-2">Opening Hours</h4>
                    <div class="flex justify-between text-sm text-gray-600 border-b border-gray-200 pb-1 mb-1">
                        <span>Senin - Sabtu</span>
                        <span class="font-bold text-gray-800">10:00 - 22:00</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 border-b border-gray-200 pb-1 mb-1">
                        <span>Minggu</span>
                        <span class="font-bold text-gray-800">11:00 - 23:00</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Hari Libur Nasional</span>
                        <span class="font-bold text-red-600">Tutup</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="lg:col-span-7 bg-white p-8 rounded-xl shadow-[0_0_30px_rgba(0,0,0,0.06)] border border-gray-50 flex flex-col justify-center" data-aos="fade-left" data-aos-delay="200">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b border-gray-100 pb-4">Send us a message</h3>

            @if (session()->has('success_message'))
            <div class="mb-5 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r shadow-sm font-semibold">
                ✅ {{ session('success_message') }}
            </div>
            @endif
            @if (session()->has('error_message'))
            <div class="mb-5 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r shadow-sm font-semibold text-sm">
                ⚠️ {{ session('error_message') }}
            </div>
            @endif

            <form wire:submit.prevent="sendMessage" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1">Your Name</label>
                        <input type="text" wire:model="name" required placeholder="John Doe" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:bg-white outline-none transition disabled:bg-gray-100">
                        @error('name') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1">Your Email</label>
                        <input type="email" wire:model="email" required placeholder="john@example.com" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:bg-white outline-none transition disabled:bg-gray-100">
                        @error('email') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600 mb-1">Subject</label>
                    <input type="text" wire:model="subject" required placeholder="How can we help?" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:bg-white outline-none transition disabled:bg-gray-100">
                    @error('subject') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600 mb-1">Message</label>
                    <textarea rows="5" wire:model="message" required placeholder="Write your message here..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:bg-white outline-none transition disabled:bg-gray-100"></textarea>
                    @error('message') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="text-center pt-4">
                    <button type="submit" wire:loading.attr="disabled" class="bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white px-10 py-3.5 rounded-full font-bold shadow-lg shadow-red-500/30 transition transform hover:-translate-y-1">
                        <span wire:loading.remove wire:target="sendMessage">Send Message</span>
                        <span wire:loading wire:target="sendMessage">Sedang mengirim... <i class="fa-solid fa-spinner fa-spin ml-2"></i></span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
</section>

<footer class="bg-gray-900 text-white py-10 text-center">
    <p class="mb-2">&copy; Copyright 2026 <strong>@syrrffff</strong>. All Rights Reserved</p>
</footer>

<div x-show="lightboxOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-4" x-transition.opacity>
    <button @click="lightboxOpen = false" class="absolute top-6 right-6 text-white hover:text-red-500 text-4xl font-bold">&times;</button>

    <div class="bg-white rounded-2xl overflow-hidden flex flex-col md:flex-row max-w-4xl w-full max-h-[90vh]" @click.outside="lightboxOpen = false">
        <div class="w-full md:w-1/2 bg-gray-100 flex items-center justify-center p-6">
            <img :src="lightboxImg" class="w-full h-auto max-h-[60vh] object-cover rounded-xl shadow-lg">
        </div>
        <div class="w-full md:w-1/2 p-8 flex flex-col justify-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4" x-text="lightboxTitle"></h2>
            <p class="text-gray-600 italic mb-6 leading-relaxed" x-text="lightboxDesc"></p>
            <div class="text-3xl font-bold text-red-600 mb-8" x-text="lightboxPrice"></div>
            <button @click="lightboxOpen = false" class="w-full bg-gray-800 hover:bg-black text-white py-3 rounded-full font-bold transition shadow-md">Tutup Detail</button>
        </div>
    </div>
</div>
</div>
