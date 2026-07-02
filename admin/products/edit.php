<?php
$page = 'products';

include "../middleware/admin_security.php";
include "../../config/connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<?php
$id = $_GET['id'] ?? '';

if ($id == '') {
    header("Location: index.php");
    exit;
}

$sql = "SELECT * FROM products WHERE id = $id";
$query = mysqli_query($conn, $sql);
$result = mysqli_fetch_assoc($query);

if (!$result) {
    header("Location: index.php");
    exit;
}
$errors = [];
$name = $result['name'];
$id_category = $result['id_category'];
$price = $result['price'];
$stock = $result['stock'];
$description = $result['description'];
$photo_name = $result['photo'];

if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $id_category = trim($_POST['id_category']);
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);
    $description = trim($_POST['description']);

    $fileTmpName = null;
    $is_uploading = false;

    if (empty($name)) {
        $errors['name'] = "Nama produk wajib diisi.";
    } elseif (strlen($name) < 3) {
        $errors['name'] = "Nama produk minimal harus 3 karakter.";
    }

    if (empty($id_category)) {
        $errors['id_category'] = "Kategori produk wajib dipilih.";
    } elseif (!is_numeric($id_category)) {
        $errors['id_category'] = "Kategori produk tidak valid.";
    }

    if (empty($price)) {
        $errors['price'] = "Harga produk wajib diisi.";
    } elseif (!is_numeric($price) || $price < 0) {
        $errors['price'] = "Harga produk harus berupa angka positif.";
    }

    if (empty($stock)) {
        $errors['stock'] = "Stok produk wajib diisi.";
    } elseif (!is_numeric($stock) || $stock < 0) {
        $errors['stock'] = "Stok produk harus berupa angka positif.";
    }

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] != 4) {
        $file = $_FILES['photo'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];

        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['png', 'jpg', 'jpeg'];

        if (!in_array($fileExt, $allowedExtensions)) {
            $errors['photo'] = "Format file salah! Hanya diperbolehkan PNG, JPEG atau JPG.";
        } elseif ($fileError !== 0) {
            $errors['photo'] = "Terjadi kesalahan saat mengupload file.";
        } elseif ($fileSize > 5 * 1024 * 1024) {
            $errors['photo'] = "Ukuran file terlalu besar! Maksimal 5MB.";
        } else {
            $is_uploading = true;
            $base_name = !empty($name) ? $name : 'produk';
            $clean_product_name = strtolower(trim($base_name));
            $clean_product_name = preg_replace('/[^a-z0-9\-]/', '_', $clean_product_name);
            $clean_product_name = preg_replace('/_+/', '_', $clean_product_name);

            $photo_name = $clean_product_name . "_" . uniqid() . "." . $fileExt;
            $fileDestination = '../../images/products/' . $photo_name;
        }
    }

    if (empty($errors)) {
        $upload_success = true;
        if ($is_uploading) {
            if (!move_uploaded_file($fileTmpName, $fileDestination)) {
                $upload_success = false;
                $errors['photo'] = "Gagal mengupload foto produk baru.";
            } else {
                $old_photo_path = '../../images/products/' . $result['photo'];
                if (!empty($result['photo']) && file_exists($old_photo_path)) {
                    unlink($old_photo_path);
                }
            }
        }
        if ($upload_success) {
            $query = "UPDATE products SET 
                        name='$name', 
                        description='$description', 
                        photo='$photo_name', 
                        id_category='$id_category', 
                        price='$price', 
                        stock='$stock' 
                      WHERE id=$id";

            if (mysqli_query($conn, $query)) {
                echo "<script>window.location.href='index.php?success-update=1';</script>";
                exit;
            } else {
                $errors['database'] = "Gagal mengupdate produk: " . mysqli_error($conn);
                if ($is_uploading && file_exists($fileDestination)) unlink($fileDestination);
            }
        }
    }
}

?>
<main class="flex-grow-1 p-4 bg-light">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="index.php" class="btn btn-white border shadow-sm rounded-3 px-2 py-1.5 text-dark d-flex align-items-center justify-content-center" title="Kembali">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div class="d-flex flex-column">
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item small"><a href="../dashboard.php" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item small"><a href="index.php" class="text-decoration-none text-muted">Produk</a></li>
                    <li class="breadcrumb-item small active fw-semibold text-dark" aria-current="page">Edit Produk</li>
                </ol>
            </nav>

            <h1 class="h4 mb-0 text-dark fw-bold">Edit Produk</h1>
            <p class="text-muted small mb-0">Kelola data produk obat-obatan di sini.</p>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (isset($errors['database'])) : ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3" role="alert">
                    <strong>Gagal!</strong> <?= $errors['database']; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Produk</label>
                    <input type="text"
                        class="form-control <?= isset($errors['name']) ? 'is-invalid' : ''; ?>"
                        id="name"
                        name="name"
                        placeholder="Masukkan nama produk"
                        value="<?= htmlspecialchars($name); ?>">
                    <?php if (isset($errors['name'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['name']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : ''; ?>"
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Masukkan deskripsi produk"><?= htmlspecialchars($description); ?></textarea>
                    <?php if (isset($errors['description'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['description']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Upload Foto</label>
                    <input type="file"
                        class="form-control <?= isset($errors['photo']) ? 'is-invalid' : ''; ?>"
                        id="photo"
                        name="photo"
                        onchange="previewImage(this)"
                        placeholder="Masukkan foto produk">
                    <?php if (isset($errors['photo'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['photo']; ?></div>
                    <?php endif; ?>
                    <div class="mt-3">
                        <?php $foto_lama = (!empty($result['photo']) && file_exists('../../images/products/' . $result['photo']))
                            ? '../../images/products/' . htmlspecialchars($result['photo'])
                            : ''; ?>
                        <img id="img-preview" src="<?= $foto_lama ?: '#'; ?>" class="img-thumbnail <?= $foto_lama ? '' : 'd-none'; ?>" style="max-height: 200px;" alt="Pratinjau Gambar">
                    </div>
                    <div class="form-text text-muted small">Foto saat ini: <?= (htmlspecialchars($result['photo'])) ?: '-'; ?></div>
                </div>
                <div class="mb-3">
                    <label for="id_category" class="form-label">Kategori</label>
                    <select class="form-select <?= isset($errors['id_category']) ? 'is-invalid' : ''; ?>"
                        id="id_category"
                        name="id_category">
                        <option value="">Pilih Kategori</option>
                        <?php
                        $categories_query = mysqli_query($conn, "SELECT * FROM categories ORDER BY id ASC");
                        while ($category = mysqli_fetch_assoc($categories_query)): ?>
                            <option value="<?= htmlspecialchars($category['id']) ?>" <?= $id_category == $category['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <?php if (isset($errors['id_category'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['id_category']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Harga</label>
                    <input type="number"
                        class="form-control <?= isset($errors['price']) ? 'is-invalid' : ''; ?>"
                        id="price"
                        name="price"
                        placeholder="Masukkan harga produk"
                        value="<?= htmlspecialchars($price); ?>">
                    <?php if (isset($errors['price'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['price']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="stock" class="form-label">Stok</label>
                    <input type="number"
                        class="form-control <?= isset($errors['stock']) ? 'is-invalid' : ''; ?>"
                        id="stock"
                        name="stock"
                        placeholder="Masukkan stok produk"
                        value="<?= htmlspecialchars($stock); ?>">
                    <?php if (isset($errors['stock'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['stock']; ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary" name="update">Update Produk</button>
            </form>
        </div>
    </div>
</main>
<?php include "../includes/footer.php"; ?>