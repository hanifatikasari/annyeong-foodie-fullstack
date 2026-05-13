<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1>Pesanan Online</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pesanan Online</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body py-2">
                <form action="" method="get" class="form-inline">
                    <label class="mr-2">Filter Status:</label>
                    <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <?php foreach (['pending_payment','pending_verification','verified','processing','ready','completed','cancelled'] as $s): ?>
                            <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>>
                                <?= ucwords(str_replace('_', ' ', $s)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Daftar Pesanan Online</h3></div>
            <div class="card-body table-responsive p-0">
                <?= view('admin/shared/flash_message') ?>
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><a href="<?= site_url('admin/orders/' . $order['id']) ?>"><?= esc($order['invoice_no']) ?></a></td>
                                <td>
                                    <?= esc($order['customer_name']) ?>
                                </td>
                                <td>Rp <?= number_format($order['total_bayar']) ?></td>
                                <td><?= esc($order['pembayaran']) ?></td>
                                <td>
                                    <span class="badge status-badge status-<?= $order['status_order'] ?>">
                                        <?= ucwords(str_replace('_', ' ', $order['status_order'])) ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y H:i', strtotime($order['created_at'])) ?></td>
                                <td><a href="<?= site_url('admin/orders/' . $order['id']) ?>" class="btn btn-sm btn-info">Detail</a></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="7" class="text-center">Tidak ada data.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <?= $pager->links('bootstrap', 'bootstrap') ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>