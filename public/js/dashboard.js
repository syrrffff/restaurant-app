document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard interaktif siap!');

    // Ambil elemen sidebar dan tombol dari HTML
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');

    // Jika tombol diklik, tambah/hapus class 'collapsed' pada sidebar
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }
});

