<div class="d-flex">
    <aside class="d-none d-lg-flex flex-column justify-content-between bg-white border-end p-3 vh-100 position-sticky top-0" style="width: 280px;">
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
                    <a href="pesanan.html" class="nav-link <?= ($page == 'orders') ? 'active' : 'link-dark'; ?>">
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
    </aside>
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel" style="width: 280px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2" id="mobileSidebarLabel">
                <i class="bi bi-capsule-capsule text-success"></i> Toko Obat
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column justify-content-between p-3">
            <ul class="nav nav-pills flex-column gap-1">
                <li class="nav-item">
                    <a href="index.html" class="nav-link active">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="produk.html" class="nav-link link-dark">
                        <i class="bi bi-capsule me-2"></i> Data Obat / Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a href="kategori.html" class="nav-link link-dark">
                        <i class="bi bi-tags me-2"></i> Kategori Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a href="pesanan.html" class="nav-link link-dark">
                        <i class="bi bi-cart-check me-2"></i> Manajemen Pesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="pengaturan.html" class="nav-link link-dark">
                        <i class="bi bi-shop me-2"></i> Pengaturan Toko
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users.html" class="nav-link link-dark">
                        <i class="bi bi-people me-2"></i> Manajemen User
                    </a>
                </li>
            </ul>
            <div class="border-top pt-3 d-flex align-items-center gap-3">
                <i class="bi bi-person-circle fs-3 text-secondary"></i>
                <div>
                    <h6 class="mb-0 fw-bold">Admin Utama</h6>
                    <small class="text-muted">Apoteker / Admin</small>
                </div>
            </div>
        </div>
    </div>