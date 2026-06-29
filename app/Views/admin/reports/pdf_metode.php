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
        <h3>LAPORAN METODE PEMBAYARAN</h3>
    </div>

    <table class="info">
        <tr>
            <td width="20%">Periode</td>
            <td width="80%">: <?= date('d/m/Y', strtotime($dari)); ?> - <?= date('d/m/Y', strtotime($sampai)); ?></td>
        </tr>
        <tr>
            <td>Tanggal Cetak</td>
            <td>: <?= date('d/m/Y H:i'); ?></td>
        </tr>
        <tr>
            <td>Dicetak Oleh</td>
            <td>: <?= esc($admin->first_name . ' ' . $admin->last_name) ?> (<?= esc($admin->name) ?>)</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Metode</th>
                <th class="text-center">Total Transaksi</th>
                <th class="text-right">Total Omzet</th>
                <th class="text-center">Persentase</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
                <?php $persen = $totalOmzet > 0 ? ($r->total_omzet / $totalOmzet) * 100 : 0; ?>
                <tr>
                    <td><?= esc($r->pembayaran) ?></td>
                    <td class="text-center"><?= number_format($r->jumlah_transaksi) ?></td>
                    <td class="text-right">Rp <?= number_format($r->total_omzet) ?></td>
                    <td class="text-center"><?= number_format($persen, 1) ?>%</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td><strong>TOTAL</strong></td>
                <td class="text-center"><strong><?= number_format($totalTransaksi) ?></strong></td>
                <td class="text-right"><strong>Rp <?= number_format($totalOmzet) ?></strong></td>
                <td class="text-center"><strong>100%</strong></td>
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
