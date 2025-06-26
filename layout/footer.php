<?php
// layout/footer.php

// Pastikan tidak ada yang bisa mengakses file ini secara langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}
?>
    </div> <!-- Menutup div class="row" dari header.php -->
</div> <!-- Menutup div class="container-fluid" dari header.php -->

<!-- Bootstrap 5 JS Bundle (termasuk Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- (Opsional) Custom JS jika Anda punya -->
<!-- <script src="assets/js/script.js"></script> -->

</body>
</html>


