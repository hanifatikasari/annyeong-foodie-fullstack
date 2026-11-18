<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-history text-info"></i> Audit Trail / Log Aktivitas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Log Aktivitas</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('admin/shared/flash_message') ?>

        <!-- FILTER CARD -->
        <div class="card card-outline card-info collapsed-card mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter"></i> Filter Log</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="get" action="<?= site_url('admin/activity-log') ?>">
                    <div class="row">
                        <!-- Tanggal dari -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Dari Tanggal</label>
                                <input type="date" name="date_from" class="form-control form-control-sm"
                                       value="<?= esc($filters['date_from'] ?? '') ?>">
                            </div>
                        </div>
                        <!-- Tanggal sampai -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Sampai Tanggal</label>
                                <input type="date" name="date_to" class="form-control form-control-sm"
                                       value="<?= esc($filters['date_to'] ?? '') ?>">
                            </div>
                        </div>
                        <!-- Pengguna -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Pengguna</label>
                                <select name="user_id" class="form-control form-control-sm">
                                    <option value="">— Semua Pengguna —</option>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?= esc($u->id) ?>"
                                            <?= ($filters['user_id'] == $u->id) ? 'selected' : '' ?>>
                                            <?= esc($u->username) ?> (<?= esc($u->first_name . ' ' . $u->last_name) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!-- Modul -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Modul</label>
                                <select name="module" class="form-control form-control-sm">
                                    <option value="">— Semua Modul —</option>
                                    <?php foreach ($modules as $mod): ?>
                                        <option value="<?= esc($mod) ?>"
                                            <?= ($filters['module'] === $mod) ? 'selected' : '' ?>>
                                            <?= esc(ucfirst($mod)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!-- Aktivitas -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Jenis Aktivitas</label>
                                <select name="action" class="form-control form-control-sm">
                                    <option value="">— Semua Aktivitas —</option>
                                    <?php foreach ($actions as $act): ?>
                                        <option value="<?= esc($act) ?>"
                                            <?= ($filters['action'] === $act) ? 'selected' : '' ?>>
                                            <?= esc(ucfirst($act)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!-- Search -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Cari</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                       placeholder="Username atau deskripsi..."
                                       value="<?= esc($filters['search'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="perPage" value="<?= (int) $perPage ?>">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-search"></i> Terapkan Filter
                            </button>
                            <a href="<?= site_url('admin/activity-log') ?>" class="btn btn-sm btn-secondary">
                                <i class="fas fa-times"></i> Reset Filter
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABEL LOG -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i>
                    Daftar Aktivitas
                    <span class="badge badge-info ml-2"><?= number_format($totalRows) ?> entri</span>
                </h3>
                <div class="card-tools">
                    <form method="get" action="<?= site_url('admin/activity-log') ?>" class="d-inline-flex align-items-center">
                        <!-- Pertahankan filter aktif -->
                        <?php foreach ($filters as $k => $v): if (!empty($v)): ?>
                            <input type="hidden" name="<?= esc($k) ?>" value="<?= esc($v) ?>">
                        <?php endif; endforeach; ?>
                        <label class="mb-0 mr-1 text-sm">Per halaman:</label>
                        <select name="perPage" class="form-control form-control-sm" style="width:auto"
                                onchange="this.form.submit()">
                            <?php foreach ([10, 20, 25, 50] as $n): ?>
                                <option value="<?= $n ?>" <?= ($perPage == $n) ? 'selected' : '' ?>><?= $n ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-sm text-sm">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:140px">Waktu</th>
                            <th>Pengguna</th>
                            <th>Role</th>
                            <th>Modul</th>
                            <th>Aktivitas</th>
                            <th>Deskripsi</th>
                            <th style="width:70px" class="text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logs)): ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td class="text-nowrap text-muted" style="font-size:0.8rem">
                                        <?= esc(date('d/m/Y H:i:s', strtotime($log->created_at))) ?>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold"><?= esc($log->username ?? '-') ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $roleBadgeClass = [
                                            'admin'    => 'danger',
                                            'pemilik'  => 'warning',
                                            'gudang'   => 'info',
                                            'produksi' => 'primary',
                                            'penjualan'=> 'success',
                                            'kurir'    => 'secondary',
                                        ][$log->role ?? ''] ?? 'light';
                                        ?>
                                        <span class="badge badge-<?= $roleBadgeClass ?>">
                                            <?= esc($log->role ?? '-') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-outline-secondary text-secondary border">
                                            <?= esc(ucfirst($log->module ?? '-')) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $actionBadgeClass = [
                                            'login'   => 'success',
                                            'logout'  => 'secondary',
                                            'create'  => 'primary',
                                            'update'  => 'warning',
                                            'delete'  => 'danger',
                                            'restore' => 'info',
                                            'toggle'  => 'dark',
                                            'verify'  => 'success',
                                        ][$log->action ?? ''] ?? 'light';
                                        ?>
                                        <span class="badge badge-<?= $actionBadgeClass ?>">
                                            <?= esc(strtoupper($log->action ?? '-')) ?>
                                        </span>
                                    </td>
                                    <td style="max-width:300px; white-space:normal; word-break:break-word;">
                                        <?= esc($log->description ?? '-') ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('admin/activity-log/detail/' . $log->id) ?>"
                                           class="btn btn-xs btn-outline-info"
                                           title="Lihat detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Tidak ada log aktivitas yang sesuai dengan filter.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <?php if ($totalPages > 1): ?>
            <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-right">
                    <?php
                    // Build query string untuk pagination
                    $queryArr = array_filter(array_merge($filters, ['perPage' => $perPage]));
                    $queryStr = http_build_query($queryArr);
                    ?>

                    <!-- Prev -->
                    <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link"
                           href="<?= site_url('admin/activity-log') ?>?<?= $queryStr ?>&page=<?= max(1, $currentPage - 1) ?>">
                            &laquo;
                        </a>
                    </li>

                    <!-- Halaman -->
                    <?php
                    $start = max(1, $currentPage - 2);
                    $end   = min($totalPages, $currentPage + 2);
                    for ($p = $start; $p <= $end; $p++):
                    ?>
                        <li class="page-item <?= ($p == $currentPage) ? 'active' : '' ?>">
                            <a class="page-link"
                               href="<?= site_url('admin/activity-log') ?>?<?= $queryStr ?>&page=<?= $p ?>">
                                <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next -->
                    <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link"
                           href="<?= site_url('admin/activity-log') ?>?<?= $queryStr ?>&page=<?= min($totalPages, $currentPage + 1) ?>">
                            &raquo;
                        </a>
                    </li>
                </ul>
                <div class="float-left text-muted text-sm mt-1">
                    Menampilkan <?= number_format(min($totalRows, ($currentPage - 1) * $perPage + 1)) ?>–<?= number_format(min($totalRows, $currentPage * $perPage)) ?>
                    dari <?= number_format($totalRows) ?> entri
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
