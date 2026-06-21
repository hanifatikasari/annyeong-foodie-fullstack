<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content">
    <div class="container-fluid">

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Payment Settings
                </h3>
            </div>

            <form method="post"
                  action="<?= site_url('admin/settings/payment/save') ?>">

                <?= csrf_field() ?>

                <div class="card-body">
                    <div class="custom-control custom-switch mb-3">
                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="cash"
                            name="cash"
                            value="1"
                            <?= $settings['cash'] ? 'checked' : '' ?>
                        >

                        <label
                            class="custom-control-label"
                            for="cash">
                            Cash
                        </label>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="midtrans"
                            name="midtrans"
                            value="1"
                            <?= $settings['midtrans'] ? 'checked' : '' ?>
                        >

                        <label
                            class="custom-control-label"
                            for="midtrans">
                            Midtrans
                        </label>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="transfer"
                            name="transfer"
                            value="1"
                            <?= $settings['transfer'] ? 'checked' : '' ?>
                        >

                        <label
                            class="custom-control-label"
                            for="transfer">
                            Transfer Bank
                        </label>
                    </div>

                    <div class="custom-control custom-switch mb-3">
                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="qris"
                            name="qris"
                            value="1"
                            <?= $settings['qris'] ? 'checked' : '' ?>
                        >

                        <label
                            class="custom-control-label"
                            for="qris">
                            QRIS
                        </label>
                    </div>

                </div>

                <div class="card-footer">
                    <button
                        type="submit"
                        class="btn btn-primary">

                        Simpan Pengaturan
                    </button>
                </div>

            </form>

        </div>

    </div>
</section>

<?= $this->endSection() ?>