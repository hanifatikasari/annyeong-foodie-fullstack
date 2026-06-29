<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Handling Fee</h3>
            </div>

            <form action="<?= site_url('admin/settings/handling-fee/save') ?>" method="post">
                <?= csrf_field() ?>

                <div class="card-body">
                    <div class="form-group">
                        <label>Default Handling Fee (Rp)</label>
                        <input
                            type="number"
                            name="fee"
                            class="form-control"
                            value="<?= esc($fee) ?>"
                            required
                        >
                    </div>
                </div>

                <div class="card-footer">
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</section>

<?= $this->endSection() ?>