<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-md-6 mb-4 mb-md-0">
                <h2 class="font-weight-bold">Tentang <span class="text-primary">Annyeong Foodie</span></h2>
                <p class="lead text-muted">Menghadirkan cita rasa Korea autentik ke meja makan Anda!</p>
                <p>Annyeong Foodie adalah usaha kuliner Korean food yang berbasis di Pemalang, Jawa Tengah. Kami mengkhususkan diri pada produk-produk makanan Korea seperti Kimbab, Dimsum, Tteokbokki, dan berbagai pilihan lainnya.</p>
                <p>Semua produk kami dibuat dari bahan-bahan segar pilihan tanpa bahan pengawet, dimasak dengan penuh cinta oleh tim dapur kami.</p>
                <div class="row mt-4">
                    <div class="col-4 text-center">
                        <h3 class="text-primary font-weight-bold">100+</h3>
                        <small class="text-muted">Pelanggan Puas</small>
                    </div>
                    <div class="col-4 text-center">
                        <h3 class="text-primary font-weight-bold">20+</h3>
                        <small class="text-muted">Menu Tersedia</small>
                    </div>
                    <div class="col-4 text-center">
                        <h3 class="text-primary font-weight-bold">2+</h3>
                        <small class="text-muted">Tahun Berdiri</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <img src="<?= base_url('themes/indomarket/assets/img/slides/slide1.jpg') ?>" class="img-fluid rounded shadow" alt="Annyeong Foodie">
            </div>
        </div>

        <div class="row text-center mb-5">
            <div class="col-12 mb-4"><h3 class="font-weight-bold">Kontak Kami</h3></div>
            <div class="col-md-4 mb-3">
                <i class="fa fa-map-marker fa-2x text-primary mb-2"></i>
                <h6>Alamat</h6>
                <p class="text-muted">Jl. Contoh No. 123, Pemalang, Jawa Tengah</p>
            </div>
            <div class="col-md-4 mb-3">
                <i class="fa fa-whatsapp fa-2x text-success mb-2"></i>
                <h6>WhatsApp</h6>
                <p class="text-muted"><a href="https://wa.me/6281234567890">+62 812-3456-7890</a></p>
            </div>
            <div class="col-md-4 mb-3">
                <i class="fa fa-instagram fa-2x text-danger mb-2"></i>
                <h6>Instagram</h6>
                <p class="text-muted"><a href="#">@annyeongfoodie</a></p>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>