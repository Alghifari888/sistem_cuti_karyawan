<?php
// layout/footer.php

// Pastikan tidak ada yang bisa mengakses file ini secara langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}
?>
    </div> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if (isset($_SESSION['user_id'])): ?>
<script>
    // Mendefinisikan variabel JavaScript dengan data dari session PHP
    const loggedInUserId = <?php echo json_encode($_SESSION['user_id']); ?>;
    const loggedInUserRole = <?php echo json_encode($_SESSION['role']); ?>;
</script>
<?php endif; ?>

<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

<script src="asset/js/realtime.js"></script>

</body>
</html>