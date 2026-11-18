<?php
/**
 * Footer Template
 * Server A (Client UI)
 */
$flash_success = get_flash('success');
$flash_error   = get_flash('error');
?>
        </main>
    </div><!-- /.app-main -->
</div><!-- /.app-wrapper -->

<!-- Bootstrap 5.3.3 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script Notifikasi & Interaksi -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Mobile Sidebar Toggle
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.app-sidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('d-none');
        });
    }

    // Flash Messages
    <?php if ($flash_success): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: <?= json_encode($flash_success) ?>,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
    <?php endif; ?>

    <?php if ($flash_error): ?>
    Swal.fire({
        icon: 'error',
        title: 'Perhatian!',
        text: <?= json_encode($flash_error) ?>,
        confirmButtonColor: '#0284c7'
    });
    <?php endif; ?>
});

// Helper Konfirmasi Hapus Data
function confirmDelete(url, itemName = 'data ini') {
    Swal.fire({
        title: 'Hapus ' + itemName + '?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
</script>
</body>
</html>
