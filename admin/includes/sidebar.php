<div class="d-flex" style="height: calc(100vh - 56px);">
    <aside class="d-none d-lg-flex flex-column justify-content-between bg-white border-end p-3" style="min-width: 300px;">
        <div>
            <ul class="nav nav-pills flex-column gap-1">
                <li class="nav-item">
                    <a href="/toko-obat-arah-aman/admin/dashboard.php" class="nav-link <?= ($page == 'dashboard') ? 'active' : 'link-dark'; ?>">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/toko-obat-arah-aman/admin/products/index.php" class="nav-link <?= ($page == 'products') ? 'active' : 'link-dark'; ?>">
                        <i class="bi bi-capsule me-2"></i> Data Obat / Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/toko-obat-arah-aman/admin/categories/index.php" class="nav-link <?= ($page == 'categories') ? 'active' : 'link-dark'; ?>">
                        <i class="bi bi-tags me-2"></i> Kategori Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/toko-obat-arah-aman/admin/orders/index.php" class="nav-link <?= ($page == 'orders') ? 'active' : 'link-dark'; ?>">
                        <i class="bi bi-cart-check me-2"></i> Manajemen Pesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="pengaturan.html" class="nav-link <?= ($page == 'settings') ? 'active' : 'link-dark'; ?>">
                        <i class="bi bi-shop me-2"></i> Pengaturan Toko
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users.html" class="nav-link <?= ($page == 'users') ? 'active' : 'link-dark'; ?>">
                        <i class="bi bi-people me-2"></i> Manajemen User
                    </a>
                </li>
            </ul>
        </div>
        <div class="pt-3 border-top">
            <a href="/toko-obat-arah-aman/actions/logout.php" class="nav-link text-danger">
                <i class="bi bi-box-arrow-left me-2"></i> Logout
            </a>
        </div>
    </aside>