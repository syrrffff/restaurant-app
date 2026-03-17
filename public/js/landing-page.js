document.addEventListener('DOMContentLoaded', function() {
                // Hanya inisialisasi Splide.js
	new Splide('#gallery-slider', {
		type: 'slide',
		rewind: true,
		drag: 'free',
		snap: true,
		focus: 'center',
		perPage: 4,
		gap: '20px',
		arrows: true,
		pagination: false,
		autoplay: true,
		interval: 4000,
		speed: 1000,
		breakpoints: {
			1024: { perPage: 3 },
			768: { perPage: 1, gap: '10px' }
		}
	}).mount();

	AOS.init({
                once: true, // Animasi hanya berjalan 1 kali saat di-scroll
                offset: 100, // Jarak trigger animasi dari bawah layar
            });
});
