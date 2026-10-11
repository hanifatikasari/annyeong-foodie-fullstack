<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Batterbit | Log in</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600,700" rel="stylesheet">
    <link type="text/css" href="<?= base_url('themes/indomarket/assets/css/argon-design-system.min.css') ?>" rel="stylesheet">
    <link type="text/css" href="<?= base_url('themes/indomarket/assets/css/style.css') ?>" rel="stylesheet">
    <style>
        body { background: #f4f5f7; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-auth { border-radius: 0.5rem; width: 100%; border: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center pad-bottom-5">
            <div class="col-lg-5 col-md-7">
                <div class="auth-brand">
                    <span class="auth-brand-icon"> <a href="<?= site_url() ?>" class="navbar-brand">
                                <img src="<?= base_url('uploads/settings/logo.png') ?>" 
                                    alt="Batterbit" 
                                    class="logo-img">
                            </a> 
                    </span>
                    <span class="auth-brand-name">Batterbit</span>
                    <span class="auth-brand-tagline">From Batter to Bite</span>
                </div>
                <div class="card bg-secondary shadow card-auth">
                    <div class="card-body px-lg-5 py-lg-5">
                        <?= $this->renderSection('content') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>