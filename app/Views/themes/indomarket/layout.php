<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $title ?? 'Batterbit' ?></title>

    <link href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i&display=swap" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Changa+One:ital@0;1&display=swap" rel="stylesheet">

    <link href="<?= base_url('/themes/indomarket/assets/css/nucleo-icons.css') ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link type="text/css" href="<?= base_url('/themes/indomarket/assets/css/jquery-ui.css') ?>" rel="stylesheet">
    <link type="text/css" href="<?= base_url('/themes/indomarket/assets/css/argon-design-system.min.css') ?>" rel="stylesheet">
    <link type="text/css" href="<?= base_url('/themes/indomarket/assets/css/style.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

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
    <script>
        $(document).ready(function() {
            // ==========================================
            // PENGATURAN WAKTU CAROUSEL (dalam milidetik)
            // 1000 = 1 detik | 3000 = 3 detik | 5000 = 5 detik
            // ==========================================
            var waktuSlide = 5000; 

            setInterval(function() {
                $('#mainCarousel').carousel('next');
            }, waktuSlide);
        });
    </script>
</body>
</html>