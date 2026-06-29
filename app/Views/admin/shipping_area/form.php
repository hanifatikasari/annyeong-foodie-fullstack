<div class="form-group">
    <label>Nama Area</label>
    <input type="text" name="area_name" class="form-control" value="<?= old('area_name', $shippingArea->area_name ?? '') ?>" required>
</div>

<div class="form-group">
    <label>Ongkir</label>
    <input type="number" name="ongkir" class="form-control" value="<?= old('ongkir', $shippingArea->ongkir ?? 0) ?>" required>
</div>

<div class="form-group">
    <label>Estimasi</label>
    <input type="text" name="estimasi" class="form-control" value="<?= old('estimasi', $shippingArea->estimasi ?? '') ?>">
</div>

<div class="form-group">
    <label>Status</label>
    <select name="is_active" class="form-control">
        <option value="1" <?= old('is_active', $shippingArea->is_active ?? 1) == 1 ? 'selected' : '' ?>>Aktif</option>
        <option value="0" <?= old('is_active', $shippingArea->is_active ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif</option>
    </select>
</div>

<input type="hidden" name="page" value="<?= $_GET['page'] ?? 1 ?>">
<input type="hidden" name="table_search" value="<?= esc($_GET['table_search'] ?? '') ?>">
<input type="hidden" name="perPage" value="<?= esc($_GET['perPage'] ?? 10) ?>">