<?php
echo "<h1 style='color:red'>INI FILE CHECKOUT YANG SEDANG DIPAKAI</h1>";

echo "<pre>";
print_r($cart);
echo "</pre>";
// exit;
?>
<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<?php
$user = $user ?? null;
?>

<section class="checkout-section py-5">
    <div class="container">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="<?= site_url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('cart') ?>">Keranjang</a></li>
                <li class="breadcrumb-item active">Checkout</li>
            </ol>
        </nav>

        <h3 class="font-weight-bold mb-4">
            <i class="fa fa-lock mr-2 text-primary"></i>Checkout
        </h3>

        <!-- Validation Errors -->
        <?php if (!empty(session('errors'))): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="fa fa-exclamation-triangle mr-1"></i>Mohon perbaiki kesalahan berikut:</strong>
                <ul class="mb-0 mt-1 pl-4">
                    <?php foreach (session('errors') as $fieldName => $errMsg): ?>
                        <li><?= esc($errMsg) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('checkout/process') ?>" method="post" id="checkoutForm">
            <?= csrf_field() ?>

            <div class="row">
                <!-- ============================
                     KOLOM KIRI: Form Pengiriman
                     ============================ -->
                <div class="col-lg-7 mb-4">

                    <!-- Data Penerima -->
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-primary text-white border-0">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fa fa-map-marker mr-2"></i>Data Pengiriman
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Nama Penerima -->
                            <div class="form-group">
                                <label for="nama_penerima">
                                    Nama Penerima <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       id="nama_penerima"
                                       name="nama_penerima"
                                       class="form-control <?= session('errors.nama_penerima') ? 'is-invalid' : '' ?>"
                                       value="<?= old('nama_penerima', ($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?>"
                                       placeholder="Nama lengkap penerima"
                                       required>
                                <?php if (session('errors.nama_penerima')): ?>
                                    <div class="invalid-feedback"><?= session('errors.nama_penerima') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- No HP Penerima
                                 FIX ISSUE #4: field name HARUS "no_hp_penerima" -->
                            <div class="form-group">
                                <label for="no_hp_penerima">
                                    No. HP Penerima <span class="text-danger">*</span>
                                </label>
                                <input type="tel"
                                       id="no_hp_penerima"
                                       name="no_hp_penerima"
                                       class="form-control <?= session('errors.no_hp_penerima') ? 'is-invalid' : '' ?>"
                                       value="<?= old('no_hp_penerima', $user->phone ?? '') ?>"
                                       placeholder="contoh: 08123456789"
                                       required>
                                <?php if (session('errors.no_hp_penerima')): ?>
                                    <div class="invalid-feedback"><?= session('errors.no_hp_penerima') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Alamat Pengiriman
                                 FIX ISSUE #4: field name HARUS "alamat_pengiriman" -->
                            <div id="deliveryFields">
                                <div class="form-group">
                                    <label for="alamat_pengiriman">
                                        Alamat Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="alamat_pengiriman"
                                            name="alamat_pengiriman"
                                            class="form-control <?= session('errors.alamat_pengiriman') ? 'is-invalid' : '' ?>"
                                            rows="3"
                                            placeholder="Jl. ..., RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos"
                                            required><?= old('alamat_pengiriman', $user->address ?? '') ?></textarea>
                                    <?php if (session('errors.alamat_pengiriman')): ?>
                                        <div class="invalid-feedback"><?= session('errors.alamat_pengiriman') ?></div>
                                    <?php endif; ?>
                                    <small class="text-muted">
                                        <i class="fa fa-info-circle mr-1"></i>
                                        Isi alamat lengkap agar pengiriman tepat sasaran.
                                        <a href="<?= site_url('account/profile') ?>" target="_blank">Update profil</a>
                                        untuk mengisi otomatis.
                                    </small>
                                </div>
                            </div>

                            <!-- Catatan Order -->
                            <div class="form-group mb-0">
                                <label for="catatan_customer">Catatan Order (opsional)</label>
                                <input type="text"
                                       id="catatan_customer"
                                       name="catatan_customer"
                                       class="form-control"
                                       placeholder="Catatan khusus untuk penjual..."
                                       value="<?= old('catatan_customer') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Pilihan Pengiriman -->
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Metode Pengambilan <span class="text-danger">*</span>
                        </label>

                        <div class="custom-control custom-radio mb-2">
                            <input
                                type="radio"
                                id="pickup"
                                name="delivery_type"
                                value="pickup"
                                class="custom-control-input"
                                <?= old('delivery_type', 'pickup') == 'pickup' ? 'checked' : '' ?>
                            >

                            <label class="custom-control-label" for="pickup">
                                <strong>Pickup di Toko</strong>
                                <small class="d-block text-muted">
                                    Ambil sendiri ke toko.
                                </small>
                            </label>
                        </div>

                        <div class="custom-control custom-radio">
                            <input
                                type="radio"
                                id="delivery"
                                name="delivery_type"
                                value="delivery"
                                class="custom-control-input"
                                <?= old('delivery_type') == 'delivery' ? 'checked' : '' ?>
                            >

                            <label class="custom-control-label" for="delivery">
                                <strong>Delivery</strong>
                                <small class="d-block text-muted">
                                    Pesanan diantar ke alamat Anda.
                                </small>
                            </label>
                        </div>
                    </div>

                    <!-- Drppdown Shipping Area -->
                    <div id="shippingAreaWrapper" style="display:block;">
                        <div class="form-group mt-3">
                            <label>
                                Area Pengiriman
                                <span class="text-danger">*</span>
                            </label>
                            <select
                                name="shipping_area_id"
                                id="shipping_area_id"
                                class="form-control">
                                <option value="">
                                    -- Pilih Area --
                                </option>
                                <?php foreach($shippingAreas as $area): ?>
                                    <option
                                        value="<?= $area->id ?>"
                                        data-cost="<?= $area->ongkir ?>">
                                        <?= esc($area->area_name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white border-0">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fa fa-credit-card mr-2"></i>Metode Pembayaran
                            </h6>
                        </div>
                        <div class="card-body">

                            <!-- ========================================
                                OPSI 1: Midtrans (Direkomendasikan)
                                Kartu Kredit, GoPay, OVO, Dana, VA Bank
                                ======================================== -->
                            <?php if ($enableMidtrans): ?>
                            <div class="custom-control custom-radio mb-3">
                                <input type="radio"
                                    id="payMidtrans"
                                    name="pembayaran"
                                    value="Midtrans"
                                    class="custom-control-input"
                                    <?= old('pembayaran', 'Midtrans') === 'Midtrans' ? 'checked' : '' ?>
                                    required>

                                <label class="custom-control-label" for="payMidtrans">
                                    <strong>
                                        <i class="fa fa-credit-card mr-1 text-primary"></i>
                                        Bayar Online (Midtrans)
                                        <span class="badge badge-success ml-1" style="font-size:10px;">RECOMMENDED</span>
                                    </strong>
                                    <small class="text-muted d-block ml-4 mt-1">
                                        <strong>Tersedia:</strong> Kartu Kredit/Debit, GoPay, OVO, Dana,
                                        ShopeePay, Virtual Account BCA/BNI/BRI/Mandiri, QRIS, dan lainnya.
                                        <br>Proses otomatis, tidak perlu upload bukti.
                                    </small>
                                </label>
                            </div>
                            <?php endif; ?>

                            <!-- Divider -->
                           <?php if (
                                $enableMidtrans &&
                                ($enableQris || $enableTransfer)
                            ): ?>
                            <div class="d-flex align-items-center mb-3">
                                <hr class="flex-grow-1">
                                <span class="text-muted small px-2">atau bayar manual</span>
                                <hr class="flex-grow-1">
                            </div>
                            <?php endif; ?>

                            <!-- ========================================
                                OPSI 2: QRIS (Manual Upload)
                                ======================================== -->
                            <?php if ($enableQris): ?>
                            <div class="custom-control custom-radio mb-3">
                                <input type="radio"
                                    id="payQRIS"
                                    name="pembayaran"
                                    value="QRIS"
                                    class="custom-control-input"
                                    <?= old('pembayaran') === 'QRIS' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="payQRIS">
                                    <strong><i class="fa fa-qrcode mr-1 text-info"></i> QRIS (Upload Bukti Manual)</strong>
                                    <small class="text-muted d-block ml-4 mt-1">
                                        Scan QR code kami, lalu upload bukti pembayaran.
                                        Diverifikasi tim dalam 1-3 jam kerja.
                                    </small>
                                </label>
                            </div>
                            <?php endif; ?>

                            <!-- ========================================
                                OPSI 3: Transfer Bank (Manual Upload)
                                ======================================== -->
                            <?php if ($enableTransfer): ?>
                            <div class="custom-control custom-radio">
                                <input type="radio"
                                    id="payTransfer"
                                    name="pembayaran"
                                    value="Transfer"
                                    class="custom-control-input"
                                    <?= old('pembayaran') === 'Transfer' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="payTransfer">
                                    <strong><i class="fa fa-university mr-1 text-success"></i> Transfer Bank (Upload Bukti Manual)</strong>
                                    <small class="text-muted d-block ml-4 mt-1">
                                        BCA: <strong>1234567890</strong> a/n <strong>Annyeong Foodie</strong>
                                        <br>Transfer lalu upload bukti. Diverifikasi tim dalam 1-3 jam kerja.
                                    </small>
                                </label>
                            </div>
                            <?php endif; ?>

                            <?php if (session('errors.pembayaran')): ?>
                                <div class="text-danger small mt-2">
                                    <?= session('errors.pembayaran') ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                <!-- ============================
                    KOLOM KANAN: Ringkasan Order
                    ============================ -->
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 sticky-top" style="top:20px;">
                        <div class="card-header bg-primary border-0">
                            <h6 class="mb-0 font-weight-bold text-white">
                                <i class="fa fa-list-alt mr-2"></i>
                                Ringkasan Pesanan
                            </h6>
                        </div>

                        <div class="card-body p-0">
                            <table class="table mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($cart as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">
                                                <?= esc($item['name']) ?>
                                            </div>
                                            <small class="text-muted">
                                                Rp <?= number_format((int)$item['price']) ?>
                                                ×
                                                <?= $item['qty'] ?>
                                            </small>
                                        </td>
                                        <td class="text-right align-middle">
                                            Rp <?= number_format((int)$item['price'] * (int)$item['qty']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>Total Belanja</td>
                                        <td class="text-right">
                                            Rp <?= number_format($total) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Handling Fee</td>
                                        <td class="text-right">
                                            <span id="handlingFee">
                                                Rp <?= number_format($handlingFee) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr id="shippingRow" style="display:none;">
                                        <td>Ongkos Kirim</td>
                                        <td class="text-right">
                                            <span id="shippingCost">
                                                Rp 0
                                            </span>
                                        </td>
                                    </tr>

                                    <tr id="discountRow" style="display:none;">
                                        <td class="text-success">
                                            Diskon
                                        </td>
                                        <td class="text-right text-success">
                                            - <span id="discountAmount">Rp 0</span>
                                        </td>
                                    </tr>

                                    <tr class="table-primary">
                                        <th>Grand Total</th>
                                        <th class="text-right">
                                            <span id="grandTotal">
                                                Rp <?= number_format($total + $handlingFee) ?>
                                            </span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="card-footer bg-white">
                            <button
                                type="submit"
                                class="btn btn-success btn-block btn-lg font-weight-bold"
                                id="btnCheckout">

                                <i class="fa fa-check-circle mr-2"></i>
                                Buat Pesanan
                            </button>

                            <a href="<?= site_url('cart') ?>"
                            class="btn btn-outline-secondary btn-block btn-sm mt-2">
                                <i class="fa fa-arrow-left mr-1"></i>
                                Kembali ke Keranjang
                            </a>

                            <p class="text-muted text-center small mt-2 mb-0">
                                <i class="fa fa-shield mr-1"></i>
                                Transaksi aman
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
// Disable tombol submit setelah klik untuk mencegah double submit
document.getElementById('checkoutForm').addEventListener('submit', function() {
    var btn = document.getElementById('btnCheckout');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i>Memproses...';
});


const pickupRadio = document.getElementById('pickup');
const deliveryRadio = document.getElementById('delivery');

const deliveryFields = document.getElementById('deliveryFields');
const shippingRow = document.getElementById('shippingRow');
const alamatField = document.getElementById("alamat_pengiriman");

const subtotal = <?= $total ?>;
const handlingFee = <?= $handlingFee ?>;
const shippingSelect = document.getElementById('shipping_area_id');
function updateDeliveryUI(){
    let shipping = 0;
    if(deliveryRadio.checked){
        deliveryFields.style.display = '';
        shippingRow.style.display = '';
        alamatField.required = true;
        shipping =
            Number(
                shippingSelect.options[
                    shippingSelect.selectedIndex
                ].dataset.cost || 0
            );

    }else{
        deliveryFields.style.display = 'none';
        shippingRow.style.display = 'none';
        alamatField.required = false;
    }
        document.getElementById("handlingFee").innerHTML =
        "Rp " + handlingFee.toLocaleString('id-ID');

        document.getElementById("shippingCost").innerHTML =
            "Rp " + shipping.toLocaleString('id-ID');

        const grandTotal = subtotal + handlingFee + shipping;

        document.getElementById("grandTotal").innerHTML =
            "Rp " + grandTotal.toLocaleString('id-ID');
}

pickupRadio.addEventListener('change', updateDeliveryUI);
deliveryRadio.addEventListener('change', updateDeliveryUI);
shippingSelect.addEventListener('change', updateDeliveryUI);

updateDeliveryUI();
</script>

<?= $this->endSection() ?>