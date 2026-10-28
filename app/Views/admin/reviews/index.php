<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-star text-warning mr-2"></i>Manajemen Ulasan Produk</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Ulasan Produk</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <?= view('admin/shared/flash_message') ?>

        <!-- Filter & Search Card -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-header py-2">
                <h3 class="card-title text-sm font-weight-bold">
                    <i class="fas fa-filter mr-1"></i> Filter & Pencarian
                </h3>
            </div>
            <div class="card-body py-3">
                <form action="" method="get" class="form-inline flex-wrap">
                    <div class="form-group mr-3 mb-2">
                        <label class="mr-2 font-weight-normal">Cari:</label>
                        <input type="text" name="search" value="<?= esc($search) ?>" 
                               class="form-control form-control-sm" 
                               placeholder="Nama produk, pelanggan, ulasan..." style="min-width:240px;">
                    </div>

                    <div class="form-group mr-3 mb-2">
                        <label class="mr-2 font-weight-normal">Status Publikasi:</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">Semua Status</option>
                            <option value="visible" <?= $filterStatus === 'visible' ? 'selected' : '' ?>>Tampil ke Publik (Visible)</option>
                            <option value="hidden" <?= $filterStatus === 'hidden' ? 'selected' : '' ?>>Disembunyikan (Hidden)</option>
                        </select>
                    </div>

                    <div class="form-group mr-3 mb-2">
                        <label class="mr-2 font-weight-normal">Rating:</label>
                        <select name="rating" class="form-control form-control-sm">
                            <option value="0">Semua Rating</option>
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?= $i ?>" <?= $filterRating === $i ? 'selected' : '' ?>>
                                    <?= $i ?> Bintang (<?= str_repeat('★', $i) ?>)
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <button type="submit" class="btn btn-sm btn-primary mr-1">
                            <i class="fas fa-search mr-1"></i> Terapkan
                        </button>
                        <a href="<?= site_url('admin/reviews') ?>" class="btn btn-sm btn-outline-secondary">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    Daftar Ulasan Pelanggan
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info p-2">Total Ditemukan: <?= count($reviews) ?></span>
                </div>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width:40px" class="text-center">#</th>
                            <th style="width:160px">Pelanggan</th>
                            <th style="width:160px">Produk</th>
                            <th style="width:120px">Rating</th>
                            <th>Komentar Ulasan</th>
                            <th style="width:110px" class="text-center">Status</th>
                            <th style="width:110px">Tanggal</th>
                            <th style="width:180px" class="text-center">Kelola (Show / Hide)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reviews)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-comment-slash fa-2x mb-2 d-block text-secondary"></i>
                                    Tidak ada data ulasan yang sesuai dengan kriteria filter.
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($reviews as $idx => $r): ?>
                            <tr class="<?= !$r->is_visible ? 'bg-light text-muted' : '' ?>">
                                <td class="text-center"><?= $idx + 1 ?></td>
                                <td>
                                    <strong><?= esc(($r->first_name ?? '') . ' ' . ($r->last_name ?? '')) ?></strong>
                                    <br><small class="text-muted"><i class="far fa-envelope mr-1"></i><?= esc($r->email ?? '-') ?></small>
                                </td>
                                <td>
                                    <span class="font-weight-bold text-dark"><?= esc($r->product_name ?? '-') ?></span>
                                    <br><small class="text-muted">ID Produk: #<?= $r->product_id ?></small>
                                </td>
                                <td>
                                    <div class="text-warning text-nowrap">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star" style="color:<?= $i <= $r->rating ? '#f5a623' : '#ddd' ?>; font-size:13px;"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <small class="font-weight-bold"><?= $r->rating ?> dari 5</small>
                                </td>
                                <td>
                                    <div style="max-width:320px; word-wrap:break-word; white-space:normal; line-height:1.4;">
                                        <?= nl2br(esc($r->ulasan ?? '-')) ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if ($r->is_visible): ?>
                                        <span class="badge badge-success p-2">
                                            <i class="fas fa-eye mr-1"></i> Tampil
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary p-2">
                                            <i class="fas fa-eye-slash mr-1"></i> Hidden
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?= date('d M Y', strtotime($r->created_at)) ?></small>
                                    <br><small class="text-muted"><?= date('H:i', strtotime($r->created_at)) ?> WIB</small>
                                    <?php if ($r->updated_at && $r->updated_at !== $r->created_at): ?>
                                        <br><span class="badge badge-light border" title="Ulasan pernah diedit"><i class="fas fa-pen-alt mr-1"></i>Diedit</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <?php if ($r->is_visible): ?>
                                            <!-- Tombol Hide Comment -->
                                            <form action="<?= site_url('admin/reviews/hide/' . $r->id) ?>" method="post" class="d-inline mr-1">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-warning" 
                                                        title="Sembunyikan komentar dari halaman publik"
                                                        onclick="return confirm('Sembunyikan komentar ulasan ini dari halaman produk publik?')">
                                                    <i class="fas fa-eye-slash mr-1"></i> Hide
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <!-- Tombol Show Comment -->
                                            <form action="<?= site_url('admin/reviews/show/' . $r->id) ?>" method="post" class="d-inline mr-1">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-success" 
                                                        title="Tampilkan komentar ke halaman publik"
                                                        onclick="return confirm('Tampilkan kembali ulasan ini ke halaman produk publik?')">
                                                    <i class="fas fa-eye mr-1"></i> Show
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Tombol Hapus Permanen -->
                                        <form action="<?= site_url('admin/reviews/delete/' . $r->id) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    title="Hapus permanen ulasan"
                                                    onclick="return confirm('PERHATIAN: Ulasan akan dihapus secara PERMANEN dari database.\n\nYakin ingin menghapus ulasan ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white text-muted text-sm">
                <i class="fas fa-info-circle mr-1 text-primary"></i>
                <strong>Catatan Moderasi:</strong> Ulasan yang berstatus <strong>Hidden</strong> tidak akan tampil di halaman produk publik dan tidak dihitung ke dalam nilai rating rata-rata produk. Gunakan fitur <strong>Hide</strong> untuk komentar spam/tidak pantas tanpa harus menghapus data transaksi.
            </div>
        </div>

    </div>
</section>

<?= $this->endSection() ?>
