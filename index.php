<?php
session_start();
include "config/connection.php";

if (isset($_SESSION['id_user'])) {
  $id_user = $_SESSION['id_user'];
  $query_user = "SELECT full_name FROM users WHERE id = '$id_user' LIMIT 1";
  $result = mysqli_query($conn, $query_user);
  if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $full_name = $user['full_name'];
  } else {
    $full_name = 'Nama Lengkap';
  }
}

$products_query = mysqli_query($conn, "SELECT * FROM products ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_user = $_SESSION['id_user'];
  $shipping_address = $_POST['shipping_address'];
  $notes = $_POST['notes'];
  $id_products = $_POST['id_product'];
  $quantities = $_POST['quantity'];

  $errors = [];

  $file_recipe_name = null;
  $file_tmpname = null;
  $file_destination = "";
  $is_uploading = false;

  // START: validasi input address
  if (empty($shipping_address)) {
    $errors['shipping_address'] = "Lokasi/Alamat pengiriman wajib diisi.";
  }
  // END: validasi input address

  // START: validasi input resep
  if (isset($_FILES['recipe']) && $_FILES['recipe']['error'] != 4) {
    $is_uploading = true;

    $file = $_FILES['recipe'];
    $file_name = $file['name'];
    $file_tmpname = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];

    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION)); //mengambil ekstensi file dan mengubahnya menjadi huruf kecil
    $allowed_ext = ['pdf', 'png', 'jpg', 'jpeg'];

    if (!in_array($file_ext, $allowed_ext)) { // memeriksa apakah ekstensi file termasuk dalam daftar ekstensi yang diizinkan
      $errors['recipe'] = "Format resep salah! Hanya diperbolehkan PDF, PNG, atau JPG.";
    } elseif ($file_error !== 0) {
      $errors['recipe'] = "Terjadi kesalahan saat mengupload resep.";
    } elseif ($file_size > 5 * 1024 * 1024) {
      $errors['recipe'] = "Ukuran file resep terlalu besar! Maksimal 5MB.";
    } else {
      $file_recipe_name = "RCP_" . date('md') . "_" . uniqid() . "." . $file_ext;
      $file_destination = 'recipe/' . $file_recipe_name;
    }
  }
  // END: validasi input resep

  // START: validasi input obat dan jumlah (qty)
  $has_medicine = false;
  if (isset($id_products) && is_array($id_products)) {
    foreach ($id_products as $index => $selected_product_id) {
      $order_qty = isset($quantities[$index]) ? $quantities[$index] : '';

      $has_prod = !empty($selected_product_id);
      $has_qty = !empty($order_qty) && intval($order_qty) > 0; // memeriksa apakah jumlah (qty) valid (lebih dari 0) (intval digunakan untuk memastikan bahwa input adalah angka)

      if ($has_prod && !$has_qty) {
        $errors['medicine'] = "Jumlah (Qty) wajib diisi untuk obat yang Anda pilih.";
        break;
      } elseif (!$has_prod && !empty($order_qty)) {
        $errors['medicine'] = "Silakan pilih nama obat untuk jumlah (Qty) yang telah Anda isi.";
        break;
      } elseif ($has_prod && $has_qty) {
        $has_medicine = true;
      }
    }
  }
  if (!$has_medicine && !$is_uploading) {
    $errors['global'] = "Anda harus memilih obat yang dibutuhkan atau mengunggah resep dari Sinshe (boleh juga keduanya).";
  }
  // END: validasi input obat dan jumlah (qty)

  // START: jika tidak ada error, simpan data ke database
  if (empty($errors)) {
    $upload_success = true;

    if ($is_uploading) {
      if (!move_uploaded_file($file_tmpname, $file_destination)) {
        $upload_success = false;
        $errors['recipe'] = "Gagal memindahkan file resep ke server.";
      }
    }

    if ($upload_success) {
      $order_code = "OD" . date('ymd') . rand(10, 99);
      $query_orders = "INSERT INTO orders (id_user, order_code, shipping_address, recipe, notes, status, followed_up_by) 
      VALUES ('$id_user', '$order_code', '$shipping_address', '$file_recipe_name', '$notes', 1, NULL)";

      if (mysqli_query($conn, $query_orders)) {
        $last_order_id = mysqli_insert_id($conn); // mengambil ID order terakhir yang baru saja dimasukkan ke tabel orders
        foreach ($id_products as $index => $selected_product_id) {
          $order_qty = $quantities[$index];
          if (!empty($selected_product_id) && $order_qty > 0) {
            $query_detail = "INSERT INTO orders_details (id_order, id_product, quantity) 
            VALUES ('$last_order_id', '$selected_product_id', '$order_qty')";
            mysqli_query($conn, $query_detail);
          }
        }
        echo "<script>alert('Pesanan Berhasil! Kode: $order_code'); window.location.href='index.php';</script>";
        exit;
      } else {
        if ($is_uploading && file_exists($file_destination)) {
          unlink($file_destination); // menghapus file resep yang telah diunggah jika terjadi kesalahan saat menyimpan data ke database
        }
        $errors['database'] = "Gagal menyimpan pesanan: " . mysqli_error($conn);
      }
    }
  }
  // END: jika tidak ada error, simpan data ke database
}

// START: menampilkan data kontak dan produk terbaru
$contact_query = mysqli_query($conn, "SELECT * FROM contacts LIMIT 1");
$contact = mysqli_fetch_assoc($contact_query);

$last_products_query = mysqli_query($conn, "SELECT products.*, categories.name AS category_name FROM products
JOIN categories ON products.id_category = categories.id ORDER BY products.id DESC LIMIT 4");
// END: menampilkan data kontak dan produk terbaru
?>

<!-- START: mengatur posisi halaman setelah dimuat ulang jika ada error pada form -->
<?php if (!empty($errors)) : ?>
  <script>
    window.location.hash = "contact";
  </script>
<?php endif; ?>
<!-- END: mengatur posisi halaman setelah dimuat ulang jika ada error pada form -->

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
  <!-- START NAVBAR -->
  <nav id="navbar">
    <div class="logo">
      <img
        src="images/logo/logo.png"
        alt="Toko Obat Arah Aman Logo"
        height="30" />
      <a
        href="#home"
        style="font-weight: 600; font-size: 1.25rem"
        class="text-logo">Toko Obat Arah Aman</a>
    </div>
    <ul class="nav-links">
      <li class="active"><a href="#home">Home</a></li>
      <li><a href="#about">Tentang Kami</a></li>
      <li><a href="#products">Produk</a></li>
      <li><a href="#contact">Kontak</a></li>
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
  <!-- START HERO SECTION -->
  <section id="home" class="hero-section reveal">
    <div class="hero-container">
      <div class="hero-text">
        <div class="custom-pill"><span></span> TERPERCAYA SEJAK 1980</div>
        <h1>Solusi Kesehatan Terpercaya & <em>Berkualitas Tinggi</em></h1>
        <p>
          Menghadirkan berbagai macam obat-obatan tradisional dan kebutuhan
          kesehatan lainnya dengan pelayanan profesional dan harga yang tetap
          terjangkau.
        </p>
        <div class="btn-container">
          <a href="#contact" class="btn-primary">Hubungi Kami</a>
          <a href="products.php" class="btn-secondary">Lihat Produk</a>
        </div>
      </div>
      <div class="hero-image-wrapper">
        <div class="main-image-box">
          <img src="images/hero/hero-image.jpeg" alt="Toko Obat Arah Aman" />
          <div class="hero-floating-badge">
            <div class="badge-icon">✓</div>
            <div class="badge-text">
              <strong>100% Original</strong>
              <span>Produk Terjamin</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- END HERO SECTION -->
  <!-- START STATS BAND -->
  <section id="stats" class="stats-section reveal">
    <div class="stats-container">
      <h2>
        <span id="counter">0</span><span id="plus"></span>Tahun Pengalaman
      </h2>
      <p>
        Berdiri selama lebih dari 40 tahun sejak tahun 1980, Toko Obat Arah
        Aman menjadi salah satu pilihan yang terbaik untuk memenuhi kebutuhan
        kesehatan konsumen.
      </p>
    </div>
  </section>
  <!-- END STATS BAND -->
  <!-- START ABOUT US -->
  <section id="about" class="about-section reveal">
    <div class="about-container">
      <div class="about-text">
        <span class="custom-pill">TENTANG KAMI</span>
        <h2>Dedikasi Kesehatan Sejak 1980</h2>
        <p class="intro-p">
          Toko Obat Arah Aman lahir dari visi menghadirkan akses kesehatan
          yang mudah dan terpercaya. Selama lebih dari 4 dekade, kami menjaga
          kualitas produk, didukung Sinse berpengalaman dalam meracik obat
          tradisional.
        </p>
        <div class="vision-misi-box">
          <div class="vm-item">
            <div class="vm-header">
              <span class="vm-number">01</span>
              <h4>Visi</h4>
            </div>
            <p class="vm-text">
              Menjadi penyedia layanan kesehatan pengobatan tradisional secara
              aman dan berkualitas.
            </p>
          </div>
          <div class="vm-item">
            <div class="vm-header">
              <span class="vm-number">02</span>
              <h4>Misi</h4>
            </div>
            <p class="vm-text">
              Menyediakan obat berkualitas dan racikan tradisional oleh Sinse
              berpengalaman secara aman.
            </p>
          </div>
        </div>
        <div class="about-footer">
          <div class="about-stats-row">
            <div class="stat-box">
              <span class="stat-number">40+</span>
              <span class="stat-desc">Tahun Pengalaman</span>
            </div>
            <div class="stat-box">
              <span class="stat-number">100%</span>
              <span class="stat-desc">Produk Original</span>
            </div>
          </div>
        </div>
      </div>
      <div class="about-visual-content">
        <div class="video-frame">
          <video autoplay muted loop playsinline>
            <source src="videos/about-us.mp4" type="video/mp4" />
          </video>
          <div class="floating-badge">
            <svg viewBox="0 0 24 24" fill="#ffcc00" width="18" height="18">
              <path
                d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
            </svg>
            <span>Pilihan Utama</span>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- END ABOUT US -->
  <!-- START WHY CHOOSE US -->
  <section id="why" class="why-section reveal">
    <div class="why-container">
      <div class="why-text">
        <span class="custom-pill">KEUNGGULAN KAMI</span>
        <h2>Mengapa harus memilih kami?</h2>
        <p>
          Memberikan standar pelayanan kesehatan terbaik dengan dedikasi penuh
          untuk kualitas hidup Anda.
        </p>
      </div>
      <div class="why-grid-layout">
        <div class="why-card-solid">
          <div class="why-icon-circle">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
              <path d="M9 12l2 2 4-4" />
            </svg>
          </div>
          <h3>Toko Terpercaya</h3>
          <p>
            Seluruh produk kami terjamin keasliannya dan telah melalui
            pengawasan ketat.
          </p>
        </div>
        <div class="why-card-solid">
          <div class="why-icon-circle">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2">
              <line x1="12" y1="1" x2="12" y2="23" />
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
            </svg>
          </div>
          <h3>Harga Kompetitif</h3>
          <p>
            Kesehatan adalah hak semua orang. Kami pastikan harga produk tetap
            terjangkau & transparan.
          </p>
        </div>
        <div class="why-card-solid">
          <div class="why-icon-circle">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2">
              <path
                d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
              <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
            </svg>
          </div>
          <h3>Produk Terlengkap</h3>
          <p>
            Kebutuhan obat sehari-hari, vitamin, dan obat herbal tersedia
            lengkap dalam satu tempat.
          </p>
        </div>
        <div class="why-card-solid">
          <div class="why-icon-circle">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round">
              <path
                d="M18.5 2h-13a.5.5 0 0 0-.5.5V4h14V2.5a.5.5 0 0 0-.5-.5Z" />
              <path d="M5 4v1a7 7 0 0 0 14 0V4" />
              <path d="M9 11.5a3 3 0 0 0 6 0V4" />
              <path d="m14 2 2 11.5" />
              <path d="M8 22h8" />
            </svg>
          </div>
          <h3>Racikan Sinshe & Herbal</h3>
          <p>
            Kami menerima resep Sinshe dan melayani peracikan obat tradisional
            menggunakan bahan herbal pilihan.
          </p>
        </div>
      </div>
    </div>
  </section>
  <!-- END WHY CHOOSE US -->
  <!-- START PRODUCTS -->
  <section id="products" class="products-section reveal">
    <div class="products-text">
      <span class="custom-pill">PRODUK TERBARU</span>
      <h2>Produk Terbaru Kami</h2>
      <p>
        Menghadirkan obat-obatan, suplemen, dan produk kesehatan berkualitas
        untuk kebutuhan keluarga Anda.
      </p>
    </div>
    <div class="product-grid">
      <!-- START: menampilkan produk berdasarkan terakhir produk yang ditambahkan ke database -->
      <?php while ($product = mysqli_fetch_assoc($last_products_query)) : ?>
        <?php
        $badge_class = "badge-general";

        if ($product['id_category'] == 1) {
          $badge_class = "badge-herbal";
        } elseif ($product['id_category'] == 2) {
          $badge_class = "badge-general";
        } elseif ($product['id_category'] == 3) {
          $badge_class = "badge-suplemen";
        }
        ?>
        <div
          class="product-card"
          data-name="<?= htmlspecialchars($product['name']) ?>"
          data-price="Rp <?= number_format($product['price'], 0, ',', '.') ?>"
          data-desc="<?= htmlspecialchars($product['description']) ?>"
          data-img="images/products/<?= $product['photo'] ?>"
          data-badge="<?= htmlspecialchars($product['category_name']) ?>"
          data-class="<?= $badge_class ?>">

          <div class="product-img-wrapper">
            <img
              src="images/products/<?= $product['photo'] ?>"
              alt="<?= htmlspecialchars($product['name']) ?>">
          </div>

          <div class="product-info">
            <span class="product-badge <?= $badge_class ?>">
              <?= strtoupper($product['category_name']) ?>
            </span>
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p class="product-desc">
              <?= substr(htmlspecialchars($product['description']), 0, 120) ?>...
            </p>
            <div class="product-footer">
              <span class="product-price">
                Rp <?= number_format($product['price'], 0, ',', '.') ?>
              </span>
              <button class="btn-detail">
                Detail
              </button>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
      <!-- END: menampilkan produk berdasarkan terakhir produk yang ditambahkan ke database -->
    </div>
    </div>
    <div class="products-action">
      <a href="products.php" class="btn-view-all">Lihat Selengkapnya <span>→</span></a>
    </div>
  </section>
  <!-- END PRODUCTS -->
  <!-- START CONTACT -->
  <section id="contact" class="contact-section reveal">
    <div class="contact-container">
      <div class="contact-text">
        <span class="custom-pill">HUBUNGI KAMI</span>
        <h2>Siap Melayani Anda</h2>
        <p>
          Kunjungi toko fisik kami atau hubungi melalui WhatsApp untuk
          konsultasi kesehatan.
        </p>
      </div>
      <div class="contact-form-container">
        <div class="contact-form">
          <h2>Form Pembelian Obat</h2>
          <!-- START: menampilkan alert jika ada error global -->
          <?php if (isset($errors['global'])) : ?>
            <script>
              alert("<?= htmlspecialchars($errors['global'], ENT_QUOTES) ?>");
              window.location.hash = "contact";
            </script>
          <?php endif; ?>
          <!-- END: menampilkan alert jika ada error global -->
          <!-- START: membuat form pembelian obat -->
          <form action="" method="POST" class="modern-form" enctype="multipart/form-data">
            <div class="form-group">
              <label for="name">Nama Lengkap</label>
              <input
                type="text"
                id="name"
                name="name"
                placeholder="Tulis nama Anda"
                autocomplete="off" value="<?= $full_name ?? '' ?>" disabled />
              <span class="error-text" id="name-error">Nama wajib diisi</span>
            </div>
            <div class="form-group <?= isset($errors['shipping_address']) ? 'error' : '' ?>">
              <label for="shipping_address">Lokasi (Kota/Kecamatan)
                <span style="color: red">*</span></label>
              <input
                type="text"
                id="shipping_address"
                name="shipping_address"
                placeholder="Contoh: Jl Purnama 2"
                value="<?= htmlspecialchars($_POST['shipping_address'] ?? '') ?>"
                autocomplete="off" />
              <?php if (isset($errors['shipping_address'])) : ?>
                <span class="error-text">
                  <?= htmlspecialchars($errors['shipping_address']) ?>
                </span>
              <?php endif; ?>
            </div>
            <div class="form-group" id="produk-container">
              <label>Obat yang Dibutuhkan <span class="optional">(Kosongkan jika hanya memakai resep)</span></label>
              <?php
              $selected_products = $_POST['id_product'] ?? [''];
              $selected_qty = $_POST['quantity'] ?? [''];
              ?>
              <?php foreach ($selected_products as $index => $selected_product) : ?>
                <div class="produk-row">
                  <div class="produk-inputs">
                    <select name="id_product[]" class="select-obat">
                      <option value="">-- Pilih Obat --</option>
                      <?php
                      mysqli_data_seek($products_query, 0); // mengembalikan hasil query produk ke awal agar bisa digunakan lagi di setiap dropdown
                      while ($product = mysqli_fetch_assoc($products_query)) :
                      ?>
                        <option
                          value="<?= $product['id'] ?>"
                          <?= ($selected_product == $product['id']) ? 'selected' : '' ?>>
                          <?= htmlspecialchars($product['name']) ?>
                        </option>
                      <?php endwhile; ?>
                    </select>
                    <input
                      type="number"
                      name="quantity[]"
                      min="1"
                      class="input-qty"
                      placeholder="Qty"
                      value="<?= htmlspecialchars($selected_qty[$index] ?? '') ?>">
                  </div>
                  <button type="button" class="btn-remove" onclick="hapusBaris(this)">
                    X
                  </button>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="form-group">
              <button type="button" id="btn-add-product" class="btn-tambahproduct">
                + Tambah Obat Lain
              </button>
              <?php if (isset($errors['medicine'])) : ?>
                <span class="error-text" style="display: block;">
                  <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($errors['medicine']) ?>
                </span>
              <?php endif; ?>
            </div>
            <div class="form-group <?= isset($errors['recipe']) ? 'error' : '' ?>">
              <label for="recipe">
                Resep dari Sinse
                <span class="optional">(Upload Gambar/PDF jika ada)</span>
              </label>
              <div class="file-input-wrapper">
                <input
                  type="file"
                  id="recipe"
                  name="recipe"
                  accept=".jpg, .jpeg, .png, .pdf"
                  class="file-input-hidden" />
                <label for="recipe" class="file-input-label">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <path
                      d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                  </svg>
                  <span id="file-name-text">Pilih Foto Resep atau PDF</span>
                </label>
              </div>
              <?php if (isset($errors['recipe'])) : ?>
                <span class="error-text">
                  <?= htmlspecialchars($errors['recipe']) ?>
                </span>
              <?php endif; ?>
            </div>
            <div class="form-group">
              <label for="notes">Catatan Tambahan</label>
              <textarea
                id="notes"
                name="notes"
                rows="4"
                placeholder="Tambahkan instruksi khusus atau detail lainnya..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
            </div>
            <?php
            if (isset($_SESSION['role']) && ($_SESSION['role'] == 'User')) : ?>
              <button
                type="submit"
                class="btn-primary"
                style="border:none"
                id="buttonSendWa">
                <span>Kirim Pesan Sekarang</span>
              </button>
            <?php else : ?>
              <a href="login.php" class="btn-primary">
                Login untuk Memesan
              </a>
            <?php endif; ?>
          </form>
          <!-- END: membuat form pembelian obat -->
        </div>
      </div>
      <div class="contact-grid-layout">
        <div class="contact-map-wrapper">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.8179516666387!2d109.33969309999999!3d-0.0246311!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e1d59007e4aaf63%3A0x60581fed3db5d6be!2sToko%20Obat%20Arah%20Aman!5e0!3m2!1sid!2sid!4v1775661964164!5m2!1sid!2sid"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
        <!-- START: menampilkan informasi toko obat arah aman -->
        <div class="contact-cards-container">
          <div class="contact-card-solid">
            <div class="contact-icon-box">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 640 640"
                fill="currentColor">
                <!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.-->
                <path
                  d="M128 252.6C128 148.4 214 64 320 64C426 64 512 148.4 512 252.6C512 371.9 391.8 514.9 341.6 569.4C329.8 582.2 310.1 582.2 298.3 569.4C248.1 514.9 127.9 371.9 127.9 252.6zM320 320C355.3 320 384 291.3 384 256C384 220.7 355.3 192 320 192C284.7 192 256 220.7 256 256C256 291.3 284.7 320 320 320z" />
              </svg>
            </div>
            <div class="contact-info-text">
              <h4>Lokasi</h4>
              <p><?= htmlspecialchars($contact['location']) ?></p>
            </div>
          </div>
          <a
            href="https://wa.me/<?= htmlspecialchars($contact['whatsapp_number']) ?>"
            target="_blank"
            class="contact-card-solid link-card">
            <div class="contact-icon-box wa-bg">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor">
                <!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.-->
                <path
                  d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.937 3.659 1.432 5.63 1.433h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
              </svg>
            </div>
            <div class="contact-info-text">
              <h4>WhatsApp</h4>
              <span class="contact-cta-label">Chat Sekarang →</span>
            </div>
          </a>
          <div class="contact-card-solid full-width">
            <div class="contact-icon-box">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 640 640"
                fill="currentColor">
                <!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.-->
                <path
                  d="M216 64C229.3 64 240 74.7 240 88L240 128L400 128L400 88C400 74.7 410.7 64 424 64C437.3 64 448 74.7 448 88L448 128L480 128C515.3 128 544 156.7 544 192L544 480C544 515.3 515.3 544 480 544L160 544C124.7 544 96 515.3 96 480L96 192C96 156.7 124.7 128 160 128L192 128L192 88C192 74.7 202.7 64 216 64zM216 176L160 176C151.2 176 144 183.2 144 192L144 240L496 240L496 192C496 183.2 488.8 176 480 176L216 176zM144 288L144 480C144 488.8 151.2 496 160 496L480 496C488.8 496 496 488.8 496 480L496 288L144 288z" />
              </svg>
            </div>
            <div class="contact-info-text">
              <h4>Jam Operasional</h4>
              <p><?= htmlspecialchars($contact['operational_time']) ?></p>
            </div>
          </div>
        </div>
        <!-- END: menampilkan informasi toko obat arah aman -->
      </div>
    </div>
  </section>
  <!-- END CONTACT -->
  <!-- START FOOTER -->
  <footer>
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="logo">
          <img
            src="images/logo/logo.png"
            alt="Toko Obat Arah Aman Logo"
            height="30" />
          <a href="#home" style="font-weight: 600; font-size: 1.25rem">Toko Obat Arah Aman</a>
        </div>
        <p>Toko Obat Terpercaya dan Berkualitas Tinggi</p>
      </div>
      <div class="quick-links">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="#home">Home</a></li>
          <li><a href="#about">Tentang Kami</a></li>
          <li><a href="#products">Produk</a></li>
          <li><a href="#contact">Kontak</a></li>
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
  <!-- END FOOTER -->
  <!-- START MODAL -->
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
  <div id="confirmModal" class="modal-overlay-form">
    <div class="modal-content">
      <h3>Konfirmasi Pesanan</h3>
      <p>Silakan periksa kembali detail pesanan Anda:</p>

      <div class="order-summary">
        <div class="summary-item">
          <span>Nama:</span> <strong id="res-name">-</strong>
        </div>
        <div class="summary-item">
          <span>Lokasi:</span> <strong id="res-location">-</strong>
        </div>
        <div class="summary-item">
          <span>Obat:</span> <strong id="res-medicine">-</strong>
        </div>
        <div class="summary-item">
          <span>Resep:</span> <strong id="res-recipe">-</strong>
        </div>
        <div class="summary-item">
          <span>Catatan:</span> <strong id="res-notes">-</strong>
        </div>
      </div>
      <div class="modal-actions">
        <button
          type="button"
          class="btn-cancel"
          onclick="closeModalForm('confirmModal')">
          Batal
        </button>
        <button type="button" class="btn-confirm" onclick="processPurchase()">
          Beli Sekarang
        </button>
      </div>
    </div>
  </div>
  <div id="successModal" class="modal-overlay-form">
    <div class="modal-content success-box">
      <div class="success-icon">
        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="3">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
      </div>
      <h3>Pemesanan Berhasil!</h3>
      <p style="margin-bottom: 10px">
        Pesanan Anda telah diterima. Kami akan segera menghubungi Anda melalui
        WhatsApp.
      </p>
      <button
        type="button"
        class="btn-confirm"
        onclick="closeModalForm('successModal')">
        Tutup
      </button>
    </div>
  </div>
  <!-- END MODAL -->
  <button id="backToTop" title="Kembali ke atas">
    <i class="fa-solid fa-arrow-up"></i>
  </button>
  <script src="js/main.js"></script>
</body>

</html>