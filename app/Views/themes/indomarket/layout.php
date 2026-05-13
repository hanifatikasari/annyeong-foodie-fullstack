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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        .cart-badge { position:relative; }
        .cart-badge .badge-count { position:absolute; top:-8px; right:-8px; background:#e83e8c; color:#fff; border-radius:50%; width:18px; height:18px; font-size:11px; display:flex; align-items:center; justify-content:center; }
        .product-card { transition: transform .2s; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,.15); }
        .star-rating { color: #ffc107; }
        .status-badge { font-size:12px; padding:4px 10px; border-radius:20px; }
        .status-pending_payment { background:#fff3cd; color:#856404; }
        .status-pending_verification { background:#cff4fc; color:#055160; }
        .status-verified { background:#d1e7dd; color:#0a3622; }
        .status-processing { background:#cfe2ff; color:#084298; }
        .status-ready { background:#e2d9f3; color:#432874; }
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
    $(document).on('submit', '.form-add-cart', function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: '<?= site_url("cart/add") ?>',
            type: 'POST',
            data: form.serialize() + '&<?= csrf_token() ?>=<?= csrf_hash() ?>',
            success: function(res) {
                if (res.success) {
                    $('.cart-count').text(res.count);
                    toastMsg(res.message, 'success');
                } else {
                    toastMsg(res.message, 'danger');
                }
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