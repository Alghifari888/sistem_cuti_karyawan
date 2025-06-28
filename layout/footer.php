<?php
// layout/footer.php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}
?>

    </div> <!-- Menutup div class="row" dari header.php -->
</div> <!-- Menutup div class="container-fluid" dari header.php -->

<!-- Footer Visual -->
<footer class="text-center text-muted py-3 mt-auto" style="background-color:#f8f9fa; border-top:1px solid #dee2e6;">
    <small>© <?= date('Y') ?> Sistem Cuti Karyawan</small>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
