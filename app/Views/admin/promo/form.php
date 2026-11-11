<?php
$promo = $promo ?? null;

$val = static function (string $field, $default = '') use ($promo) {
    return old($field, $promo->$field ?? $default);
};

$dt = static function (string $field) use ($promo) {
    $old = old($field);
    if ($old !== null && $old !== '') {
        return $old;
    }
    return !empty($promo->$field) ? date('Y-m-d\TH:i', strtotime($promo->$field)) : '';
};

$jenis = $val('jenis_diskon', 'fixed');
?>

<div class="form-group">
    <label>Nama Promo <span class="text-danger">*</span></label>
    <input type="text" name="nama_promo" class="form-control" maxlength="100"
           value="<?= esc($val('nama_promo')) ?>" placeholder="Contoh: Hemat 20 Ribu" required>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Minimal Belanja (Rp) <span class="text-danger">*</span></label>
            <input type="number" name="min_subtotal" class="form-control" min="1" step="1"
                   value="<?= esc($val('min_subtotal')) ?>" placeholder="20000" required>
            <small class="text-muted">Dihitung dari subtotal produk (tanpa ongkir &amp; handling fee).</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Jenis Diskon <span class="text-danger">*</span></label>
            <select name="jenis_diskon" id="jenisDiskon" class="form-control">
                <option value="fixed" <?= $jenis === 'fixed' ? 'selected' : '' ?>>Nominal tetap (Rp)</option>
                <option value="percentage" <?= $jenis === 'percentage' ? 'selected' : '' ?>>Persentase (%)</option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label id="labelNilai">Nilai Diskon <span class="text-danger">*</span></label>
            <input type="number" name="nilai_diskon" id="nilaiDiskon" class="form-control" min="1" step="1"
                   value="<?= esc($val('nilai_diskon')) ?>" placeholder="1000" required>
        </div>
    </div>
    <div class="col-md-6" id="groupMaxDiskon">
        <div class="form-group">
            <label>Maksimal Diskon (Rp)</label>
            <input type="number" name="max_diskon" class="form-control" min="1" step="1"
                   value="<?= esc($val('max_diskon')) ?>" placeholder="Kosongkan jika tanpa batas">
            <small class="text-muted">Hanya untuk diskon persentase.</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Tanggal Mulai</label>
            <input type="datetime-local" name="starts_at" class="form-control" value="<?= esc($dt('starts_at')) ?>">
            <small class="text-muted">Kosongkan jika langsung berlaku.</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Tanggal Berakhir</label>
            <input type="datetime-local" name="ends_at" class="form-control" value="<?= esc($dt('ends_at')) ?>">
            <small class="text-muted">Kosongkan jika tanpa batas waktu.</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Kuota Pemakaian</label>
            <input type="number" name="usage_limit" class="form-control" min="1" step="1"
                   value="<?= esc($val('usage_limit')) ?>" placeholder="Kosongkan jika tanpa batas">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Status</label>
            <?php $aktif = (string) $val('is_active', 1); ?>
            <select name="is_active" class="form-control">
                <option value="1" <?= $aktif === '1' ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= $aktif === '0' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
        </div>
    </div>
</div>

<script>
(function () {
    var jenis = document.getElementById('jenisDiskon');
    var label = document.getElementById('labelNilai');
    var input = document.getElementById('nilaiDiskon');
    var group = document.getElementById('groupMaxDiskon');
    if (!jenis) return;

    function sync() {
        var pct = jenis.value === 'percentage';
        label.innerHTML = (pct ? 'Persentase Diskon (%)' : 'Nominal Diskon (Rp)') +
            ' <span class="text-danger">*</span>';
        input.placeholder = pct ? '5' : '1000';
        input.max = pct ? '100' : '';
        group.style.display = pct ? '' : 'none';
    }

    jenis.addEventListener('change', sync);
    sync();
})();
</script>