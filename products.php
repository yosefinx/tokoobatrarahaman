<?php
session_start();
include "config/connection.php";

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY id ASC");
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

if ($category > 0) {
  // jalankan ini jika ada kategori yang dipilih
  $products = mysqli_query($conn, "SELECT products.*, categories.name AS category_name FROM products JOIN categories 
  ON products.id_category = categories.id WHERE products.id_category = $category");
} else {
  // jalankan ini jika tidak ada kategori yang dipilih
  $products = mysqli_query($conn, "SELECT products.*, categories.name AS category_name FROM products JOIN categories 
  ON products.id_category = categories.id");
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Toko Obat Arah Aman</title>
  <link rel="stylesheet" href="css/style.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <nav id="navbar">
    <div class="logo">
      <img
        src="images/logo/logo.png"
        alt="Toko Obat Arah Aman Logo"
        height="30" />
      <a
        href="index.php#home"
        style="font-weight: 600; font-size: 1.25rem"
        class="text-logo">Toko Obat Arah Aman</a>
    </div>
    <ul class="nav-links">
      <li><a href="index.php">Beranda</a></li>
      <li><a href="index.php#about">Tentang Kami</a></li>
      <li class="active"><a href="index.php#products">Produk</a></li>
      <li><a href="index.php#contact">Kontak</a></li>
      <!-- START: untuk check logout/login -->
      <?php if (isset($_SESSION['id_user'])) : ?>
        <li><a href="actions/logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?')">Logout</a></li>
      <?php else : ?>
        <li><a href="login.php">Login</a></li>
      <?php endif; ?>
      <!-- END: untuk check logout/login -->
    </ul>
    <button class="menu-toggle">☰</button>
  </nav>
  <!-- END NAVBAR -->
  <!-- START HERO SECTION PRODUCTS -->
  <section id="hero-products" class="hero-products-section reveal">
    <div class="overlay-container">
      <div class="hero-visual-frame">
        <img
          src="images/hero/hero-image-products.jpeg"
          alt="Hero Products"
          class="hero-img-main" />
      </div>
      <div class="hero-floating-card">
        <span class="custom-pill">KATALOG KESEHATAN</span>
        <h1>Produk <span>Terbaru</span> Kami</h1>
        <p>
          Menyediakan pilihan produk perawatan kesehatan yang telah teruji,
          aman, dan terjangkau untuk kesejahteraan Anda dan keluarga.
        </p>

        <div class="hero-mini-info">
          <div class="info-item">
            <strong>100%</strong>
            <span>Original</span>
          </div>
          <div class="divider"></div>
          <div class="info-item">
            <strong>Teruji</strong>
            <span>OLEH SINSHE</span>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- END HERO SECTION PRODUCTS -->
  <!-- START CATEGORY & PRODUCTS -->
  <section class="products-catalog-section reveal">
    <div class="products-catalog-text">
      <h2>Pilih Kategori Obatmu</h2>
      <p>Cari kebutuhan medismu berdasarkan kategori yang tersedia.</p>
    </div>
    <div class="category-filter-wrapper">
      <div class="category-pill-box">
        <!-- START: untuk menandai kategori yang aktif -->
        <?php $current_category = $_GET['category'] ?? ''; ?>
        <a
          href="products.php" class="category-pill-btn <?= $current_category == '' ? 'active' : '' ?>">
          Semua
        </a>
        <?php while ($category = mysqli_fetch_assoc($categories)) : ?>
          <a
            href="products.php?category=<?= $category['id'] ?>"
            class="category-pill-btn <?= $current_category == $category['id'] ? 'active' : '' ?>">
            <?= htmlspecialchars($category['name']) ?>
          </a>
        <?php endwhile; ?>
        <!-- END: untuk menandai kategori yang aktif -->
      </div>
    </div>
    <div class="products-container">
      <div class="product-grid">
        <!-- START: untuk menampilkan produk berdasarkan kategori yang dipilih -->
        <?php while ($row = mysqli_fetch_assoc($products)) : ?>
          <?php
          switch ($row['id_category']) {
            case 1:
              $badge = "badge-herbal";
              break;
            case 2:
              $badge = "badge-general";
              break;
            case 3:
              $badge = "badge-suplemen";
              break;
            default:
              $badge = "badge-general";
          } ?>
          <div
            class="product-card"
            data-name="<?= htmlspecialchars($row['name']) ?>"
            data-price="Rp <?= number_format($row['price'], 0, ',', '.') ?>"
            data-desc="<?= htmlspecialchars($row['description']) ?>"
            data-img="images/products/<?= $row['photo'] ?>"
            data-badge="<?= htmlspecialchars($row['category_name']) ?>"
            data-class="<?= $badge ?>">

            <div class="product-img-wrapper">
              <img
                src="images/products/<?= $row['photo'] ?>"
                alt="<?= htmlspecialchars($row['name']) ?>">
            </div>
            <div class="product-info">
              <span class="product-badge <?= $badge ?>">
                <?= htmlspecialchars($row['category_name']) ?>
              </span>
              <h3><?= htmlspecialchars($row['name']) ?></h3>
              <p class="product-desc">
                <?= htmlspecialchars($row['description']) ?>
              </p>
              <div class="product-footer">
                <span class="product-price">
                  Rp <?= number_format($row['price'], 0, ',', '.') ?>
                </span>
                <button class="btn-detail">
                  Detail
                </button>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
        <!-- END: untuk menampilkan produk berdasarkan kategori yang dipilih -->
      </div>
    </div>
  </section>
  <section id="closing" class="closing-section reveal">
    <div class="closing-container">
      <div class="closing-content-box">
        <span class="pill-tag-gold">SOLUSI TERPERCAYA</span>
        <h1>Langkah Kecil untuk <br /><span>Masa Depan Sehat</span></h1>
        <p>
          Mulailah perjalanan kesehatan Anda bersama kami. Kami siap melayani
          dengan sepenuh hati untuk kualitas hidup yang lebih baik.
        </p>
        <div class="closing-btns">
          <a href="index.php#contact" class="btn-closing-primary">Hubungi Kami Sekarang</a>
        </div>
      </div>
    </div>
  </section>
  <footer>
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="logo">
          <img
            src="images/logo/logo.png"
            alt="Toko Obat Arah Aman Logo"
            height="30" />
          <a href="index.php" style="font-weight: 600; font-size: 1.25rem">Toko Obat Arah Aman</a>
        </div>
        <p>Toko Obat Terpercaya dan Berkualitas Tinggi</p>
      </div>
      <div>
        <h4>Navigasi</h4>
        <ul>
          <li><a href="index.php#home">Beranda</a></li>
          <li><a href="index.php#about">Tentang Kami</a></li>
          <li><a href="index.php#products">Produk</a></li>
          <li><a href="index.php#contact">Kontak</a></li>
        </ul>
      </div>
      <div class="footer-subscribe">
        <h4 style="color: #ffffff; margin-bottom: 15px">Tetap Terkoneksi</h4>
        <p style="color: #cbd5e1; margin-bottom: 20px">
          Ada pertanyaan atau masukan? Kami senang mendengarnya dari Anda.
        </p>

        <a
          href="https://wa.me/6285393988929"
          target="_blank"
          class="wa-btn-flat">
          <div class="wa-icon-box">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="currentColor">
              <!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.-->
              <path
                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.937 3.659 1.432 5.63 1.433h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
            </svg>
          </div>
          <span>Chat via WhatsApp</span>
        </a>
      </div>
    </div>
    <div class="footer-bottom">
      © 2026 Toko Obat Arah Aman. Semua hak dilindungi.
    </div>
  </footer>
  <div id="productModal" class="product-modal">
    <div class="modal-overlay"></div>
    <div class="modal-container">
      <button class="modal-close">&times;</button>
      <div class="modal-body">
        <div class="modal-image">
          <img src="" alt="" id="modalImg" />
        </div>
        <div class="modal-details">
          <span class="product-badge" id="modalBadge"></span>
          <h2 id="modalTitle"></h2>
          <p id="modalDesc"></p>
          <div class="modal-footer">
            <span class="modal-price" id="modalPrice"></span>
            <a href="#" id="modalWA" target="_blank" class="btn-buy">
              Pesan via WhatsApp
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <button id="backToTop" title="Kembali ke Atas">
    <i class="fa-solid fa-arrow-up"></i>
  </button>
  <script src="js/main.js"></script>
</body>

</html>