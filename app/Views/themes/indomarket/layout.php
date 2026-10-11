<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $title ?? 'Batterbit' ?></title>

    <link href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i&display=swap" rel="stylesheet">
    <link href="<?= base_url('/themes/indomarket/assets/css/nucleo-icons.css') ?>" rel="stylesheet">
    <link href="<?= base_url('/themes/indomarket/assets/css/font-awesome.css') ?>" rel="stylesheet">
    <link type="text/css" href="<?= base_url('/themes/indomarket/assets/css/jquery-ui.css') ?>" rel="stylesheet">
    <link type="text/css" href="<?= base_url('/themes/indomarket/assets/css/argon-design-system.min.css') ?>" rel="stylesheet">
    <link type="text/css" href="<?= base_url('/themes/indomarket/assets/css/style.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        /* ==========================================================
           Batterbit — theme accents (inline, page-level)
           Variabel warna diwarisi dari style.css (:root). Fallback
           hex disertakan agar tetap aman meski dimuat terpisah.
           ========================================================== */
        body {
            background-color: var(--af-white, #FFFFFF);
        }

        .cart-badge { position:relative; }
        .cart-badge .badge-count {
            position:absolute;
            top:-8px;
            right:-8px;
            background: var(--af-orange, #F2891F);
            color:#fff;
            border-radius:50%;
            width:18px;
            height:18px;
            font-size:11px;
            display:flex;
            align-items:center;
            justify-content:center;
            box-shadow: 0 2px 6px rgba(242,137,31,0.35);
        }
        .product-card {
            transition: transform .2s, box-shadow .2s;
            border-radius: var(--af-radius-md, 16px);
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--af-shadow-hover, 0 10px 30px rgba(59,90,115,.18));
        }
        .star-rating { color: var(--af-orange, #F2891F); }
        .status-badge {
            font-size:12px;
            padding:4px 10px;
            border-radius: var(--af-radius-pill, 20px);
            font-weight: 600;
        }
        .status-pending { background: var(--af-orange-tint, #FDE9D4); color: var(--af-orange-dark, #D9740A); }
        .status-processing { background: var(--af-navy-tint, #EAF0F4); color: var(--af-navy, #3B5A73); }
        .status-shipped { background:#EFE7F7; color:#5B3E8C; }
        .status-completed { background:#E7F5EC; color:#1F6B3A; }
        .status-cancelled { background:#FCEAE8; color:#B23A2E; }
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="<?= base_url('/themes/indomarket/assets/js/argon-design-system.js') ?>"></script>
    <script src="<?= base_url('/themes/indomarket/assets/js/main.js') ?>"></script>

    <script>
    // AJAX Add to Cart
    let clickedButton = null;

// simpan tombol submit yang diklik
    $(document).on('click', '.form-add-cart button[type=submit]', e => {
    clickedButton = $(e.currentTarget);
    });

    $(document).on('submit', '.form-add-cart', function(e) {
    e.preventDefault();

    const form   = $(this);
    const buyNow = clickedButton ? clickedButton.data('buy-now') : 0;
    const url    = buyNow == 1 ? '<?= site_url("cart/buyNow") ?>' : '<?= site_url("cart/add") ?>';

    $.post(url, form.serialize() + '&<?= csrf_token() ?>=<?= csrf_hash() ?>', res => {
        if (res.success) {
            // buy now
        if (buyNow == 1) return window.location.href = res.redirect;
            // add to cart
        $('.cart-count').text(res.count);
        toastMsg(res.message, 'success');
        } else { 
            // config prod
        if (res.needs_variant) return window.location.href = res.redirect;
        toastMsg(res.message, 'danger');
        }
    });
    });


    function toastMsg(msg, type) {
        var toast = $('<div class="alert alert-' + type + ' position-fixed" style="bottom:20px;right:20px;z-index:9999;min-width:280px;">' + msg + '</div>');
        $('body').append(toast);
        setTimeout(function() { toast.fadeOut(400, function() { $(this).remove(); }); }, 3000);
    }
    </script>
</body>
</html>