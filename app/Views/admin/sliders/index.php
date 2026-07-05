<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Carousel</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Carousel</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('admin/shared/flash_message') ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <?= $editingSlide ? 'Edit Slide' : 'Tambah Slide Baru' ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <form action="<?= $editingSlide ? site_url('admin/sliders/update/' . $editingSlide['id']) : site_url('admin/sliders/store') ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <?php if ($editingSlide): ?>
                                <input type="hidden" name="_method" value="PUT">
                            <?php endif; ?>

                            <div class="form-group">
                                <label>Judul</label>
                                <input type="text" name="title" class="form-control" value="<?= esc($editingSlide['title'] ?? '') ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Gambar</label>
                                 <ul class="form-text text-muted mb-2" style="padding-left:18px;">
                                    <li>Ukuran ideal: 1920x700px</li>
                                    <li>Maksimal 10MB</li>
                                </ul>
                                <input type="file" name="image" class="form-control-file" <?= $editingSlide ? '' : 'required' ?>>
                                <?php if (!empty($editingSlide['image'])): ?>
                                    <small class="text-muted d-block mt-2">Gambar saat ini:</small>
                                    <img src="<?= base_url($editingSlide['image']) ?>" class="img-fluid mt-2 rounded" style="max-height:120px;">
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <select name="is_active" class="form-control">
                                    <option value="1" <?= (!empty($editingSlide['is_active']) ? 'selected' : '') ?>>Aktif</option>
                                    <option value="0" <?= (empty($editingSlide['is_active']) ? 'selected' : '') ?>>Nonaktif</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <?= $editingSlide ? 'Update Slide' : 'Simpan Slide' ?>
                            </button>
                            <?php if ($editingSlide): ?>
                                <a href="<?= site_url('admin/sliders') ?>" class="btn btn-secondary">Batal</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Slide</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($slides)): ?>
                            <div class="row">
                                <?php foreach ($slides as $slide): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-2">
                                            <?php if (!empty($slide['image'])): ?>
                                                <img src="<?= base_url($slide['image']) ?>" class="img-fluid rounded mb-2" style="height:140px;object-fit:cover;width:100%;">
                                            <?php endif; ?>
                                            <h6 class="mb-1"><?= esc($slide['title'] ?? '-') ?></h6>
                                            <p class="text-muted small mb-2"><?= esc($slide['subtitle'] ?? '-') ?></p>
                                            <div class="small text-muted mb-2">Link: <?= esc($slide['link'] ?? '-') ?></div>
                                            <div class="small mb-2">
                                                <span class="badge badge-<?= !empty($slide['is_active']) ? 'success' : 'secondary' ?>">
                                                    <?= !empty($slide['is_active']) ? 'Aktif' : 'Nonaktif' ?>
                                                </span>
                                            </div>
                                            <div class="btn-group w-100">
                                                <a href="<?= site_url('admin/sliders/edit/' . ($slide['id'] ?? 0)) ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="<?= site_url('admin/sliders/delete/' . ($slide['id'] ?? 0)) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus slide ini?')">Hapus</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">Belum ada slide carousel.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>