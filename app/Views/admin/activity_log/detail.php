<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-history text-info"></i> Detail Log Aktivitas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/activity-log') ?>">Log Aktivitas</a></li>
                    <li class="breadcrumb-item active">Detail #<?= (int) $log->id ?></li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?= view('admin/shared/flash_message') ?>

        <div class="row">
            <!-- Informasi Utama Log -->
            <div class="col-md-5">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Informasi Log</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-sm mb-0">
                            <tr>
                                <th style="width:35%" class="bg-light">ID Log</th>
                                <td><?= (int) $log->id ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Waktu</th>
                                <td><?= esc(date('d F Y H:i:s', strtotime($log->created_at))) ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Pengguna (ID)</th>
                                <td><?= esc($log->username ?? '-') ?> <?= $log->user_id ? '(ID: ' . (int)$log->user_id . ')' : '' ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Role</th>
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
                            </tr>
                            <tr>
                                <th class="bg-light">Modul</th>
                                <td><span class="badge badge-secondary"><?= esc(ucfirst($log->module ?? '-')) ?></span></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Aktivitas</th>
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
                            </tr>
                            <tr>
                                <th class="bg-light">Deskripsi</th>
                                <td><?= esc($log->description ?? '-') ?></td>
                            </tr>
                            <?php if ($log->reference_type || $log->reference_id): ?>
                            <tr>
                                <th class="bg-light">Referensi</th>
                                <td>
                                    <?php if ($log->reference_type): ?>
                                        <span class="badge badge-outline-secondary border"><?= esc($log->reference_type) ?></span>
                                    <?php endif; ?>
                                    <?php if ($log->reference_id): ?>
                                        #<?= (int) $log->reference_id ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th class="bg-light">IP Address</th>
                                <td><?= esc($log->ip_address ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">User Agent</th>
                                <td style="font-size:0.8rem; word-break:break-all;"><?= esc($log->user_agent ?? '-') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="<?= site_url('admin/activity-log') ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Data Sebelum & Sesudah Perubahan -->
            <div class="col-md-7">
                <?php if ($oldValues !== null || $newValues !== null): ?>
                    <div class="card card-outline card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exchange-alt"></i> Perubahan Data</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($oldValues !== null && $newValues !== null): ?>
                                <!-- Perubahan: tampilkan diff side-by-side -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-danger"><i class="fas fa-minus-circle"></i> Data Sebelum</h6>
                                        <?= renderValueTable($oldValues) ?>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-success"><i class="fas fa-plus-circle"></i> Data Sesudah</h6>
                                        <?= renderValueTable($newValues, $oldValues) ?>
                                    </div>
                                </div>
                            <?php elseif ($newValues !== null): ?>
                                <h6 class="text-primary"><i class="fas fa-plus-circle"></i> Data Baru</h6>
                                <?= renderValueTable($newValues) ?>
                            <?php elseif ($oldValues !== null): ?>
                                <h6 class="text-danger"><i class="fas fa-minus-circle"></i> Data Dihapus</h6>
                                <?= renderValueTable($oldValues) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card card-outline card-secondary">
                        <div class="card-body text-center text-muted py-4">
                            <i class="fas fa-database fa-2x mb-2 d-block"></i>
                            Tidak ada data perubahan yang tercatat untuk log ini.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
/**
 * Helper function untuk merender nilai log sebagai tabel HTML.
 * Menyorot baris yang berbeda jika $compareValues diberikan.
 */
function renderValueTable(array $values, array $compareValues = null): string
{
    if (empty($values)) {
        return '<p class="text-muted text-sm">— tidak ada data —</p>';
    }

    $html = '<table class="table table-sm table-bordered" style="font-size:0.82rem">';
    $html .= '<thead class="thead-light"><tr><th>Field</th><th>Nilai</th></tr></thead><tbody>';

    foreach ($values as $key => $val) {
        // Deteksi perubahan
        $changed = ($compareValues !== null && array_key_exists($key, $compareValues) && $compareValues[$key] !== $val);
        $rowClass = $changed ? ' class="table-warning"' : '';

        $displayVal = renderValue($val);
        $html .= "<tr{$rowClass}><td><code>" . htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8') . '</code></td>';
        $html .= '<td>' . $displayVal . '</td></tr>';
    }

    $html .= '</tbody></table>';
    return $html;
}

/**
 * Render satu nilai agar mudah dibaca.
 */
function renderValue($val): string
{
    if ($val === null) {
        return '<em class="text-muted">null</em>';
    }
    if ($val === '') {
        return '<em class="text-muted">kosong</em>';
    }
    if (is_bool($val)) {
        return $val ? '<span class="badge badge-success">true</span>' : '<span class="badge badge-danger">false</span>';
    }
    if (is_array($val)) {
        $encoded = json_encode($val, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return '<pre class="mb-0" style="font-size:0.78rem;white-space:pre-wrap">' . htmlspecialchars($encoded, ENT_QUOTES, 'UTF-8') . '</pre>';
    }

    $str = (string) $val;
    // Jika tampak seperti datetime
    if (preg_match('/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}/', $str)) {
        return '<span class="text-info">' . htmlspecialchars($str, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>

<?= $this->endSection() ?>
