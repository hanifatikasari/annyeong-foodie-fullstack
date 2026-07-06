<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Annyeong Foodie | Log in</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,600,700" rel="stylesheet">
    <link type="text/css" href="<?= base_url('themes/indomarket/assets/css/argon-design-system.min.css') ?>" rel="stylesheet">
    <style>
        :root {
            --af-navy: #3B5A73;
            --af-navy-dark: #2C4457;
            --af-orange: #F2891F;
            --af-orange-dark: #D9740A;
            --af-cream: #FBEFE3;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(160deg, var(--af-cream) 0%, #ffffff 55%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Dekorasi lembut di background — murni CSS, tidak mempengaruhi konten/logic */
        body::before,
        body::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        body::before {
            width: 420px;
            height: 420px;
            background: rgba(59, 90, 115, 0.07);
            top: -120px;
            left: -140px;
        }

        body::after {
            width: 360px;
            height: 360px;
            background: rgba(242, 137, 31, 0.08);
            bottom: -120px;
            right: -120px;
        }

        .auth-brand {
            position: relative;
            z-index: 1;
            text-align: center;
            margin-bottom: 24px;
        }

        .auth-brand .auth-brand-icon .logo-img {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 18px;
            color: #fff;
            font-size: 24px;
            margin-bottom: 12px;
            box-shadow: 0 8px 20px rgba(59, 90, 115, 0.25);
        }

        .navbar-brand{
            align-items: center;
            justify-content: center;
        }
        .auth-brand .auth-brand-name {
            display: block;
            font-weight: 700;
            font-size: 20px;
            color: var(--af-navy-dark);
            letter-spacing: -0.01em;
        }

        .auth-brand .auth-brand-tagline {
            display: block;
            font-size: 13px;
            color: #8a8378;
            margin-top: 2px;
        }

        .card-auth {
            position: relative;
            z-index: 1;
            border-radius: 22px;
            width: 100%;
            max-content: 450px;
            border: 0;
            background: #ffffff !important;
            box-shadow: 0 16px 40px rgba(59, 90, 115, 0.14) !important;
        }

        /* Aksen pita tipis di atas card, khas kemasan bakery */
        .card-auth::before {
            content: "";
            display: block;
            height: 5px;
            border-radius: 22px 22px 0 0;
            background: linear-gradient(90deg, var(--af-navy) 0%, var(--af-orange) 100%);
        }

        /* Sentuhan halus ke elemen umum di dalam form login/register bawaan CI4/Ion Auth,
           tanpa mengubah nama class yang sudah ada */
        .card-auth .form-control {
            border-radius: 10px;
            border: 1.5px solid #efe3d6;
            padding: 10px 16px;
        }

        .card-auth .form-control:focus {
            border-color: var(--af-orange);
            box-shadow: 0 0 0 3px rgba(242, 137, 31, 0.12);
        }

        .card-auth .btn-primary {
            background-color: var(--af-orange) !important;
            border-color: var(--af-orange) !important;
            border-radius: 999px;
            font-weight: 500;
        }

        .card-auth .btn-primary:hover {
            background-color: var(--af-orange-dark) !important;
            border-color: var(--af-orange-dark) !important;
        }

        .card-auth a {
            color: var(--af-navy);
        }

        .card-auth a:hover {
            color: var(--af-orange);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center pad-bottom-5">
            <div class="col-lg-5 col-md-7">
                <div class="auth-brand">
                    <span class="auth-brand-icon"> <a href="<?= site_url() ?>" class="navbar-brand">
                                <img src="<?= base_url('uploads/settings/logo.png') ?>" 
                                    alt="Annyeong Foodie" 
                                    class="logo-img">
                            </a> 
                    </span>
                    <span class="auth-brand-name">Annyeong Foodie</span>
                    <span class="auth-brand-tagline">Let's Get Something To Eat</span>
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