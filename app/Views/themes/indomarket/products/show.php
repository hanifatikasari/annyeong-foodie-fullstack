<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="product-detail py-5">
    <div class="container">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="<?= site_url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= site_url('products') ?>">Produk</a></li>
                <li class="breadcrumb-item active"><?= esc($product->name) ?></li>
            </ol>
        </nav>

        <div class="row">

            <!-- =====================================================
                 KOLOM KIRI: Gambar Produk
                 ===================================================== -->
            <div class="col-md-5 mb-4">
                <?php if (!empty($product->images)): ?>
                    <img id="mainImg"
                         src="<?= base_url($product->images[0]->large) ?>"
                         class="img-fluid rounded w-100"
                         style="max-height:450px;object-fit:cover;"
                         alt="<?= esc($product->name) ?>">

                    <div class="d-flex flex-wrap mt-2" id="thumbnailContainer">
                        <?php if (count($product->images) > 1): ?>
                            <?php foreach ($product->images as $img): ?>
                                <img src="<?= base_url($img->small) ?>"
                                     class="img-thumbnail mr-2 mb-1"
                                     style="width:70px;height:70px;object-fit:cover;cursor:pointer;"
                                     data-large="<?= base_url($img->large) ?>"
                                     onclick="document.getElementById('mainImg').src=this.dataset.large"
                                     alt="">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                         style="height:350px;" id="imgPlaceholder">
                        <i class="fa fa-image fa-4x text-muted"></i>
                    </div>
                    <div class="d-flex flex-wrap mt-2" id="thumbnailContainer"></div>
                <?php endif; ?>
            </div>

            <!-- =====================================================
                 KOLOM KANAN: Detail Produk
                 ===================================================== -->
            <div class="col-md-7">

                <!-- Nama Produk -->
                <h2 class="font-weight-bold mb-0"><?= esc($product->name) ?></h2>

                <!-- Nama Varian Terpilih (tersembunyi, muncul via JS) -->
                <h6 class="text-primary mt-1 mb-2 d-none" id="displayVariantName"></h6>

                <!-- Rating -->
                <div class="d-flex align-items-center mb-3">
                    <div class="star-rating mr-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa fa-star<?= $i <= round($avg_rating) ? '' : '-o' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <small class="text-muted"><?= $avg_rating ?>/5 (<?= count($reviews) ?> ulasan)</small>
                </div>

                <!-- Harga -->
                <div class="mb-3">
                    <?php if ($product->type === 'configurable'): ?>
                        <small class="text-muted d-block" id="pricePrefixLabel">Mulai dari</small>
                    <?php endif; ?>
                    <span class="h3 text-primary font-weight-bold" id="displayPrice">
                        Rp <?= number_format(
                            $product->type === 'configurable'
                                ? ($product->lowest_price ?? 0)
                                : ($product->price ?? 0)
                        ) ?>
                    </span>
                </div>

                <!-- Deskripsi Singkat -->
                <?php if (!empty($product->short_description)): ?>
                    <p class="text-muted mb-3"><?= esc($product->short_description) ?></p>
                <?php endif; ?>

                <!-- EAV Attributes -->
                <?php if (!empty($product->attributes)): ?>
                    <div class="mb-3">
                        <table class="table table-sm table-bordered">
                            <?php foreach ($product->attributes as $attr): ?>
                                <tr>
                                    <td class="font-weight-bold text-nowrap" style="width:140px;">
                                        <?= esc($attr['attr_name']) ?>
                                    </td>
                                    <td><?= esc($attr['attr_value'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- =====================================================
                     PEMILIHAN VARIAN (khusus configurable product)
                     ===================================================== -->
                <?php if ($product->type === 'configurable' && !empty($variants)): ?>
                    <div class="mb-3">
                        <label class="font-weight-bold d-block mb-2">
                            <i class="fa fa-tags mr-1 text-primary"></i>Pilih Varian:
                        </label>
                        <div id="variantButtons">
                            <?php foreach ($variants as $v): ?>
                                <button type="button"
                                        class="btn btn-outline-secondary btn-variant mr-2 mb-2<?= $v->stok <= 0 ? ' disabled' : '' ?>"
                                        data-variant-id="<?= (int) $v->id ?>"
                                        <?= $v->stok <= 0 ? 'disabled' : '' ?>>
                                    <?= esc($v->name) ?>
                                    <?php if ($v->stok <= 0): ?>
                                        <br><small class="d-block" style="font-size:10px;">(Habis)</small>
                                    <?php endif; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php elseif ($product->type === 'configurable' && empty($variants)): ?>
                    <div class="alert alert-warning mb-3">
                        <i class="fa fa-exclamation-triangle mr-1"></i>
                        Belum ada varian tersedia untuk produk ini.
                    </div>
                <?php endif; ?>

                <!-- Stok & SKU -->
                <div class="mb-3" id="stockSection">
                    <?php if ($product->type === 'configurable'): ?>
                        <span class="badge badge-secondary">
                            <i class="fa fa-info-circle mr-1"></i>Pilih varian untuk melihat stok
                        </span>
                    <?php elseif ($product->stok > 0): ?>
                        <span class="badge badge-success">
                            <i class="fa fa-check mr-1"></i>Stok tersedia
                            (<span id="displayStok"><?= (int) $product->stok ?></span>)
                        </span>
                    <?php else: ?>
                        <span class="badge badge-danger">
                            <i class="fa fa-times mr-1"></i>Stok habis
                        </span>
                    <?php endif; ?>
                    <small class="text-muted ml-2">
                        SKU: <span id="displaySku"><?= esc($product->sku) ?></span>
                    </small>
                </div>

                <!-- =====================================================
                     ADD TO CART
                     ===================================================== -->

                <?php if ($product->type === 'configurable'): ?>

                    <?php if (!empty($variants)): ?>
                        <!-- Hint: wajib pilih varian dulu -->
                        <div class="alert alert-info py-2 mb-3" id="variantHint">
                            <i class="fa fa-hand-pointer-o mr-1"></i>
                            Silakan pilih varian di atas untuk melanjutkan.
                        </div>

                        <!-- Form cart (tersembunyi, muncul setelah varian dipilih) -->
                        <form class="form-add-cart align-items-center mb-3 d-none"
                              id="variantCartForm">
                            <?= csrf_field() ?>
                            <input type="hidden"
                                   name="product_id"
                                   id="selectedVariantId"
                                   value="">
                            <div class="input-group mr-3" style="max-width:130px;">
                                <div class="input-group-prepend">
                                    <button type="button"
                                            class="btn btn-outline-secondary btn-qty-variant"
                                            data-action="minus">−</button>
                                </div>
                                <input type="number"
                                       name="qty"
                                       id="variantQtyInput"
                                       class="form-control text-center"
                                       value="1" min="1" max="99">
                                <div class="input-group-append">
                                    <button type="button"
                                            class="btn btn-outline-secondary btn-qty-variant"
                                            data-action="plus">+</button>
                                </div>
                            </div>
                            <button type="submit"
                                    class="btn btn-primary btn-lg"
                                    id="btnAddVariant">
                                <i class="fa fa-cart-plus mr-1"></i> Tambah ke Keranjang
                            </button>
                        </form>

                        <!-- Tombol Checkout (muncul setelah varian dipilih & stok ada) -->
                        <div id="checkoutBtnWrapper" class="d-none">
                            <a href="<?= site_url('checkout') ?>"
                               class="btn btn-success btn-block btn-lg shadow-sm">
                                <i class="fa fa-lock mr-1"></i> Checkout Sekarang
                            </a>
                        </div>
                    <?php endif; ?>

                <?php elseif ($product->stok > 0): ?>

                    <!-- Simple product: add to cart -->
                    <form class="form-add-cart d-flex align-items-center mb-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int) $product->id ?>">
                        <div class="input-group mr-3" style="max-width:130px;">
                            <div class="input-group-prepend">
                                <button type="button"
                                        class="btn btn-outline-secondary btn-qty"
                                        data-action="minus">−</button>
                            </div>
                            <input type="number" name="qty"
                                   class="form-control text-center"
                                   value="1" min="1"
                                   max="<?= (int) $product->stok ?>">
                            <div class="input-group-append">
                                <button type="button"
                                        class="btn btn-outline-secondary btn-qty"
                                        data-action="plus">+</button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg mr-2">
                            <i class="fa fa-cart-plus mr-1"></i> Tambah ke Keranjang
                        </button>
                    </form>
                    <a href="<?= site_url('checkout') ?>"
                       class="btn btn-success btn-block btn-lg shadow-sm">
                        <i class="fa fa-lock mr-1"></i> Checkout Sekarang
                    </a>

                <?php else: ?>
                    <!-- Simple product: stok habis -->
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle mr-1"></i>
                        Produk sedang tidak tersedia.
                    </div>
                <?php endif; ?>

            </div><!-- /col-md-7 -->
        </div><!-- /row -->

        <!-- =====================================================
             TAB: Deskripsi & Ulasan
             ===================================================== -->
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTab">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#desc">Deskripsi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#review">
                            Ulasan (<?= count($reviews) ?>)
                        </a>
                    </li>
                </ul>
                <div class="tab-content border border-top-0 p-4 rounded-bottom">

                    <!-- Deskripsi -->
                    <div class="tab-pane active" id="desc">
                        <?= $product->description ?: '<p class="text-muted">Belum ada deskripsi.</p>' ?>
                    </div>

                    <!-- Ulasan -->
                    <div class="tab-pane" id="review">
                        <?php if ($auth->loggedIn()): ?>
                            <form action="<?= site_url('products/review') ?>"
                                  method="post"
                                  class="mb-4 p-3 bg-light rounded">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= $product->id ?>">
                                <h6 class="font-weight-bold">Tulis Ulasan</h6>
                                <div class="form-group">
                                    <label>Rating</label>
                                    <select name="rating"
                                            class="form-control form-control-sm"
                                            style="max-width:120px;">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <option value="<?= $i ?>">
                                                <?= str_repeat('⭐', $i) ?> (<?= $i ?>)
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Ulasan</label>
                                    <textarea name="ulasan"
                                              class="form-control"
                                              rows="3"
                                              placeholder="Bagikan pengalaman Anda..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    Kirim Ulasan
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <a href="<?= site_url('auth/login') ?>">Login</a>
                                untuk menulis ulasan.
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="media mb-3 pb-3 border-bottom">
                                    <div class="mr-3">
                                        <?php if (!empty($review->avatar)): ?>
                                            <img src="<?= base_url($review->avatar) ?>"
                                                 class="rounded-circle"
                                                 width="45" height="45"
                                                 style="object-fit:cover;">
                                        <?php else: ?>
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width:45px;height:45px;">
                                                <?= strtoupper(substr($review->first_name ?? 'U', 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="media-body">
                                        <h6 class="mb-0">
                                            <?= esc(($review->first_name ?? '') . ' ' . ($review->last_name ?? '')) ?>
                                        </h6>
                                        <div class="star-rating" style="font-size:13px;">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fa fa-star<?= $i <= $review->rating ? '' : '-o' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="mt-1 mb-0"><?= esc($review->ulasan) ?></p>
                                        <small class="text-muted">
                                            <?= date('d M Y', strtotime($review->created_at)) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Belum ada ulasan.</p>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <!-- =====================================================
             Produk Terkait
             ===================================================== -->
        <?php if (!empty($related)): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <h4 class="font-weight-bold mb-4">Produk Terkait</h4>
                </div>
                <?php foreach ($related as $rel): ?>
                    <div class="col-lg-3 col-md-4 col-6 mb-3">
                        <div class="single-product product-card border rounded p-2">
                            <?php if (!empty($rel->featured_image)): ?>
                                <a href="<?= site_url('products/' . $rel->sku . '/' . $rel->slug) ?>">
                                    <img src="<?= base_url($rel->featured_image->medium) ?>"
                                         class="img-fluid rounded" alt="">
                                </a>
                            <?php endif; ?>
                            <div class="mt-2">
                                <h6>
                                    <a href="<?= site_url('products/' . $rel->sku . '/' . $rel->slug) ?>"
                                       class="text-dark">
                                        <?= esc($rel->name) ?>
                                    </a>
                                </h6>
                                <span class="text-primary font-weight-bold">
                                    Rp <?= number_format($rel->price) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div><!-- /container -->
</section>

<!-- =====================================================
     JAVASCRIPT: Qty Control (Simple Product)
     ===================================================== -->
<script>
document.querySelectorAll('.btn-qty').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var input = this.closest('.input-group').querySelector('input[name=qty]');
        var val   = parseInt(input.value) || 1;
        var max   = parseInt(input.max)   || 99;
        if (this.dataset.action === 'plus'  && val < max) input.value = val + 1;
        if (this.dataset.action === 'minus' && val > 1)   input.value = val - 1;
    });
});
</script>

<!-- =====================================================
     JAVASCRIPT: Configurable Product — Variant Selector
     ===================================================== -->
<?php if ($product->type === 'configurable' && !empty($variants)): ?>
<script>
(function () {
    'use strict';

    /* -------------------------------------------------------
       DATA
       ------------------------------------------------------- */
    var variantsData  = <?= $variantsJson ?>;
    var selectedVar   = null;

    /* -------------------------------------------------------
       ELEMENT REFS
       ------------------------------------------------------- */
    var elVariantBtns  = document.querySelectorAll('.btn-variant');
    var elDisplayPrice = document.getElementById('displayPrice');
    var elStockSection = document.getElementById('stockSection');
    var elVariantHint  = document.getElementById('variantHint');
    var elVariantForm  = document.getElementById('variantCartForm');
    var elSelectedId   = document.getElementById('selectedVariantId');
    var elQtyInput     = document.getElementById('variantQtyInput');
    var elBtnAdd       = document.getElementById('btnAddVariant');
    var elPricePrefix  = document.getElementById('pricePrefixLabel');
    var elVarName      = document.getElementById('displayVariantName');
    var elCheckoutBtn  = document.getElementById('checkoutBtnWrapper');

    /* -------------------------------------------------------
       HELPER: escape HTML untuk innerHTML
       ------------------------------------------------------- */
    function esc(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(String(str || '')));
        return d.innerHTML;
    }

    /* -------------------------------------------------------
       HELPER: Update seksi stok
       ------------------------------------------------------- */
    function updateStockSection(v) {
        var badge = v.stok > 0
            ? '<span class="badge badge-success"><i class="fa fa-check mr-1"></i>Stok tersedia (' + v.stok + ')</span>'
            : '<span class="badge badge-danger"><i class="fa fa-times mr-1"></i>Stok habis</span>';
        if (elStockSection) {
            elStockSection.innerHTML = badge
                + ' <small class="text-muted ml-2">SKU: ' + esc(v.sku) + '</small>';
        }
    }

    /* -------------------------------------------------------
       HELPER: Update gambar (main + thumbnail)
       ------------------------------------------------------- */
    function updateImages(images) {
        if (!images || images.length === 0) return;

        var mainImg     = document.getElementById('mainImg');
        var placeholder = document.getElementById('imgPlaceholder');
        var thumbCont   = document.getElementById('thumbnailContainer');

        // Jika sebelumnya hanya placeholder, buat elemen <img> baru
        if (!mainImg && placeholder) {
            mainImg           = document.createElement('img');
            mainImg.id        = 'mainImg';
            mainImg.className = 'img-fluid rounded w-100';
            mainImg.style.cssText = 'max-height:450px;object-fit:cover;';
            placeholder.parentNode.replaceChild(mainImg, placeholder);
        }

        if (mainImg) mainImg.src = images[0].large;

        // Rebuild thumbnail strip
        if (thumbCont) {
            thumbCont.innerHTML = '';
            if (images.length > 1) {
                images.forEach(function (img) {
                    var t         = document.createElement('img');
                    t.src         = img.small;
                    t.className   = 'img-thumbnail mr-2 mb-1';
                    t.style.cssText = 'width:70px;height:70px;object-fit:cover;cursor:pointer;';
                    t.dataset.large = img.large;
                    t.addEventListener('click', function () {
                        var mi = document.getElementById('mainImg');
                        if (mi) mi.src = this.dataset.large;
                    });
                    thumbCont.appendChild(t);
                });
            }
        }
    }

    /* -------------------------------------------------------
       EVENT: Klik tombol varian
       ------------------------------------------------------- */
    elVariantBtns.forEach(function (btn) {
        if (btn.disabled) return; // Lewati tombol yang stok habis

        btn.addEventListener('click', function () {
            var varId = parseInt(this.getAttribute('data-variant-id'), 10);

            // Cari data varian
            selectedVar = null;
            for (var i = 0; i < variantsData.length; i++) {
                if (variantsData[i].id === varId) { selectedVar = variantsData[i]; break; }
            }
            if (!selectedVar) return;

            // ── Active state tombol ──
            elVariantBtns.forEach(function (b) {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-primary');

            // ── Tampilkan nama varian ──
            if (elVarName) {
                elVarName.textContent = selectedVar.name;
                elVarName.classList.remove('d-none');
            }

            // ── Update harga (hilangkan label "Mulai dari") ──
            if (elDisplayPrice) elDisplayPrice.textContent = selectedVar.price_formatted;
            if (elPricePrefix)  elPricePrefix.style.display = 'none';

            // ── Update stok & SKU ──
            updateStockSection(selectedVar);

            // ── Update max qty sesuai stok varian ──
            if (elQtyInput) {
                elQtyInput.max = selectedVar.stok;
                if (parseInt(elQtyInput.value) > selectedVar.stok) {
                    elQtyInput.value = Math.max(1, selectedVar.stok);
                }
            }

            // ── Set variant id ke hidden input form ──
            if (elSelectedId) elSelectedId.value = selectedVar.id;

            // ── Sembunyikan hint, tampilkan form ──
            if (elVariantHint) elVariantHint.classList.add('d-none');
            if (elVariantForm) {
                elVariantForm.classList.remove('d-none');
                elVariantForm.classList.add('d-flex');
            }

            // ── Enable / disable tombol tambah ke keranjang ──
            if (elBtnAdd) elBtnAdd.disabled = (selectedVar.stok <= 0);

            // ── Tampilkan / sembunyikan tombol Checkout ──
            if (elCheckoutBtn) {
                if (selectedVar.stok > 0) {
                    elCheckoutBtn.classList.remove('d-none');
                } else {
                    elCheckoutBtn.classList.add('d-none');
                }
            }

            // ── Update gambar ──
            updateImages(selectedVar.images);
        });
    });

    /* -------------------------------------------------------
       Qty control khusus untuk form varian
       ------------------------------------------------------- */
    document.querySelectorAll('.btn-qty-variant').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = this.closest('.input-group').querySelector('input[name=qty]');
            var val   = parseInt(input.value) || 1;
            var max   = parseInt(input.max)   || 99;
            if (this.dataset.action === 'plus'  && val < max) input.value = val + 1;
            if (this.dataset.action === 'minus' && val > 1)   input.value = val - 1;
        });
    });

})();
</script>
<?php endif; ?>

<?= $this->endSection() ?>