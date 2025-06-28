<?php
// Pastikan tidak ada yang bisa mengakses file ini secara langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Cuti Karyawan</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Sistem Cuti</a>
   <button
  class="navbar-toggler position-absolute d-md-none collapsed"
  type="button"
  data-bs-toggle="collapse"
  data-bs-target="#sidebarMenu"
  aria-controls="sidebarMenu"
  aria-expanded="false"
  aria-label="Toggle navigation"
  style="top: 50px; right: 20px; background-color: rgba(0, 123, 255, 0.3); border: none; z-index: 1051;"
>
  <span class="navbar-toggler-icon"></span>
</button>

    <div class="navbar-nav">
        <div class="nav-item text-nowrap">
            <a class="nav-link px-3" href="auth/logout.php">Logout <i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>
</header>

<div class="container-fluid">
    <div class="row">
