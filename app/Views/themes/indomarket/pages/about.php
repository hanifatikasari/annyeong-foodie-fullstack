<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-md-6 mb-4 mb-md-0">
                <h2 class="font-weight-bold">Tentang <span class="text-primary">Annyeong Foodie</span></h2>
                <p class="lead text-muted">Kami hadir untuk membawa kehangatan rasa Korea ke meja makan Anda.</p>
                <p>Annyeong Foodie lahir dari kecintaan kami pada kuliner Korea di Kabupaten Batang, Jawa Tengah. Dari dapur kecil di Kecamatan Bawang, kami meracik Kimbab, Dimsum, Tteokbokki, dan banyak pilihan lain yang siap menemani momen santai maupun acara spesial Anda.</p>
                <p>Setiap hidangan dibuat dari bahan segar tanpa pengawet, dengan sentuhan tangan tim dapur yang penuh semangat. Kami percaya makanan bukan sekadar mengenyangkan, tapi juga menghadirkan cerita dan kebahagiaan di setiap gigitan.</p>
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
                        <h3 class="text-primary font-weight-bold">2022</h3>
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
                <p class="text-muted"><a href="https://share.google/ArTUKP1gRSHXwWX2t"></a> TRIUMPHANT FUTSAL, Bantir, Bawang, Kec. Bawang, Kabupaten Batang, Jawa Tengah 51274</p>
            </div>
            <div class="col-md-4 mb-3">
                <i class="fa fa-whatsapp fa-2x text-success mb-2"></i>
                <h6>WhatsApp</h6>
                <p class="text-muted"><a href="https://wa.me/6285879505277">+62 858-7950-5277</a></p>
            </div>
            <div class="col-md-4 mb-3">
                <i class="fa fa-instagram fa-2x text-danger mb-2"></i>
                <h6>Instagram</h6>
                <p class="text-muted"><a href="https://www.instagram.com/annyeong.foodie" target="_blank">@annyeong.foodie</a></p>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>