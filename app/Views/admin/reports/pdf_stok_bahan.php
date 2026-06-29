<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0 0 10px 0;
            padding: 0;
            font-size: 18px;
        }

        .header h3 {
            margin: 3px 0;
            font-size: 16px;
        }

        .info {
            width: 100%;
            margin-bottom: 15px;
        }

        .info td {
            border: none;
            padding: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #efefef;
            border: 1px solid #000;
            padding: 6px;
        }

        td {
            border: 1px solid #000;
            padding: 5px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .signature {
            margin-top: 80px;
            text-align: right;
        }

        .signature-line {
            margin-top: 60px;
        }

        .signature-name {
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>ANNYEONG FOODIE</h2>
        <div>
            Jl. Walisongo, Ruko Triumphant Futsal Bawang, Kabupaten Batang, Jawa Tengah 51274
        </div>
        <div>
            Telp. 085879505277
        </div>
        <br>
        <h3>LAPORAN STOK BAHAN BAKU</h3>
    </div>

    <table class="info">
        <tr>
            <td width="20%">Tanggal Cetak</td>
            <td width="80%">: <?= date('d/m/Y H:i'); ?></td>
        </tr>
        <tr>
            <td>Dicetak Oleh</td>
            <td>: <?= esc($admin->first_name . ' ' . $admin->last_name) ?> (<?= esc($admin->name) ?>)</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Bahan</th>
                <th>Kategori</th>
                <th class="text-right">Stok Sekarang</th>
                <th>Satuan</th>
                <th class="text-right">Stok Minimal</th>
                <th>Status</th>
                <th class="text-right">Nilai Stok</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bahan as $item): ?>
                <tr>
                    <td><?= esc($item->kode_bahan) ?></td>
                    <td><?= esc($item->nama_bahan) ?></td>
                    <td><?= esc($item->kategori ?? '-') ?></td>
                    <td class="text-right"><?= (float) $item->stok_sekarang ?></td>
                    <td><?= esc($item->satuan) ?></td>
                    <td class="text-right"><?= (float) $item->stok_minimal ?></td>
                    <td class="text-center"><?= esc($item->stok_sekarang <= $item->stok_minimal ? 'MENIPIS' : 'Aman') ?></td>
                    <td class="text-right">Rp <?= number_format($item->stok_sekarang * $item->harga_beli_satuan) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-right"><strong>Total Nilai Stok</strong></td>
                <td class="text-right"><strong>Rp <?= number_format($totalNilai) ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="signature">
        <div>Mengetahui,</div>
        <div class="signature-line"></div>
        <div class="signature-name"><?= esc($admin->first_name . ' ' . $admin->last_name) ?></div>
        <div class="signature-name">(<?= esc($admin->name) ?>)</div>
    </div>
</body>
</html>
