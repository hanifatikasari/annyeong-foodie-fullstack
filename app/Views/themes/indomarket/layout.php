<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $title ?? 'Annyeong Foodie' ?></title>

    <link href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i&display=swap" rel="stylesheet">
    <link href="<?= base_url('/themes/indomarket/assets/css/nucleo-icons.css') ?>" rel="stylesheet">
    <link href="<?= base_url('/themes/indomarket/assets/css/font-awesome.css') ?>" rel="stylesheet">
    <link type="text/css" href="<?= base_url('/themes/indomarket/assets/css/jquery-ui.css') ?>" rel="stylesheet">
    <link type="text/css" href="<?= base_url('/themes/indomarket/assets/css/argon-design-system.min.css') ?>" rel="stylesheet">
    <link type="text/css" href="<?= base_url('/themes/indomarket/assets/css/style.css') ?>" rel="stylesheet">

    <style>
        .cart-badge { position:relative; }
        .cart-badge .badge-count { position:absolute; top:-8px; right:-8px; background:#e83e8c; color:#fff; border-radius:50%; width:18px; height:18px; font-size:11px; display:flex; align-items:center; justify-content:center; }
        .product-card { transition: transform .2s; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,.15); }
        .star-rating { color: #ffc107; }
        .status-badge { font-size:12px; padding:4px 10px; border-radius:20px; }
        .status-pending { background:#fff3cd; color:#856404; }
        .status-processing { background:#cfe2ff; color:#084298; }
        .status-shipped { background:#e2d9f3; color:#432874; }
        .status-completed { background:#d1e7dd; color:#0a3622; }
        .status-cancelled { background:#f8d7da; color:#842029; }
    </style>
</head>
<body>
    <?php echo $this->include('themes/indomarket/shared/header'); ?>
    <?php echo $this->renderSection('content') ?>
    <?php echo $this->include('themes/indomarket/shared/footer'); ?>

    <script src="<?= base_url('/themes/indomarket/assets/js/core/jquery.min.js') ?>"></script>
    <script src="<?= base_url('/themes/indomarket/assets/js/core/popper.min.js') ?>"></script>
    <script src="<?= base_url('/themes/indomarket/assets/js/core/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('/themes/indomarket/assets/js/core/jquery-ui.min.js') ?>"></script>

    <script>
    // ================================================================
    // AJAX Add to Cart — berlaku untuk semua .form-add-cart di situs
    // FIX: Selector cart badge diperbaiki (#cartCountBadge)
    // NEW: Handle response needs_variant untuk configurable product
    // ================================================================
    $(document).on('click', '.form-add-cart button[type=submit]', function () {
        $(this).closest('.form-add-cart').data('buy-now', $(this).data('buy-now') == 1);
    });

    $(document).on('submit', '.form-add-cart', function (e) {
        e.preventDefault();
        var form = $(this);
        var buyNow = form.data('buy-now') === true || form.data('buy-now') === 1 || form.data('buy-now') === '1';

        $.ajax({
            url:  '<?= site_url("cart/add") ?>',
            type: 'POST',
            data: form.serialize() + '&<?= csrf_token() ?>=<?= csrf_hash() ?>',
            success: function (res) {
                // Produk adalah configurable — redirect ke halaman detail
                if (res.needs_variant) {
                    window.location.href = res.redirect;
                    return;
                }

                if (res.success) {
                    // Update SEMUA elemen yang menampilkan jumlah cart
                    $('#cartCountBadge, .cart-count').text(res.count);

                    if (buyNow) {
                        window.location.href = '<?= site_url("checkout") ?>';
                        return;
                    }

                    toastMsg(res.message, 'success');
                } else {
                    toastMsg(res.message, 'danger');
                }
            },
            error: function () {
                toastMsg('Terjadi kesalahan. Silakan coba lagi.', 'danger');
            }
        });
    });

    // Toast notification global
    function toastMsg(msg, type) {
        var toast = $('<div class="alert alert-' + type
            + ' position-fixed" style="bottom:20px;right:20px;z-index:9999;'
            + 'min-width:280px;box-shadow:0 4px 15px rgba(0,0,0,.2);">'
            + msg + '</div>');
        $('body').append(toast);
        setTimeout(function () {
            toast.fadeOut(400, function () { $(this).remove(); });
        }, 3000);
    }
    </script>
</body>
</html>