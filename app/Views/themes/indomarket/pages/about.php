<?= $this->extend('themes/indomarket/layout') ?>
<?= $this->section('content') ?>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-md-6 mb-4 mb-md-0">              
                <h2 class="font-weight-bold">
                    Tentang <span class="text-primary">Batterbit</span>
                </h2>
                <p class="lead text-muted">
                    From Batter to Bite — dibuat dengan hangat,
                    dinikmati di setiap momen.
                </p>
                <p>
                    Batterbit adalah usaha bakery rumahan yang menghadirkan
                    beragam roti dan kue untuk menemani keseharianmu.
                    Berawal dari dapur keluarga, kami percaya bahwa camilan
                    sederhana pun bisa membawa kebahagiaan, baik untuk
                    dinikmati sendiri, dibagikan bersama orang tersayang,
                    maupun melengkapi momen spesial.
                </p>
                <p>
                    Dari roti manis dan soft bun hingga bolu, mini pizza,
                    serta kue untuk berbagai acara, setiap produk kami hadir
                    dengan perhatian pada rasa, kualitas, dan harga yang
                    tetap bersahabat.
                </p>
                <p>
                    Bagi kami, Batterbit bukan sekadar tentang adonan yang
                    menjadi roti dan kue. Ini tentang kehangatan yang tumbuh
                    dari dapur keluarga, kebahagiaan dalam setiap gigitan,
                    dan momen sederhana yang menjadi lebih berkesan ketika
                    dibagikan.
                </p>

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
                        <h3 class="text-primary font-weight-bold">2024</h3>
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
                <p class="text-muted"><a href="https://maps.app.goo.gl/YvsK61M7WwdsryRx7?g_st=aw"></a> RT.12/RW.04, Jl.Sunan Ampel, Bantir, Bawang, Batang Regency, Central Java 51274</p>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7918.43763012398!2d109.91358673488767!3d-7.100619299999989!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7013d9c7bff50d%3A0x47f4ef810aead2e4!2sbatterbit!5e0!3m2!1sen!2sid!4v1791722932893!5m2!1sen!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
            </div>
            <div class="col-md-4 mb-3">
                <i class="fab fa-whatsapp fa-2x text-success mb-2"></i>
                <h6>WhatsApp</h6>
                <p class="text-muted"><a href="https://wa.me/6285879505277">+62 858-7950-5277</a></p>
            </div>
            <div class="col-md-4 mb-3">
                <i class="fab fa-instagram fa-2x text-danger mb-2"></i>
                <h6>Instagram</h6>
                <p class="text-muted"><a href="https://www.instagram.com/batterbit_" target="_blank">@batterbit_</a></p>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>