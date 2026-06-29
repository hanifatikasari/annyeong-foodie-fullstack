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
            cellspacing:0;
            cellpadding:3;
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

        tfoot td {
            font-weight: bold;
            background: #f7f7f7;
        }

        .summary {
            margin-top: 18px;
            width: 40%;
            align: right;
        }

        .summary td {
            border: none;
            padding: 4px;
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
        <h3>LAPORAN PENJUALAN</h3>
    </div>

    <table class="info">
        <tr>
            <td width="20%">Periode</td>
            <td width="80%">
                : <?= date('d/m/Y', strtotime($dari)); ?> - <?= date('d/m/Y', strtotime($sampai)); ?>
            </td>
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
                <th>Tanggal</th>
                <th>Transaksi</th>
                <th>Gross</th>
                <th>Ongkir</th>
                <th>Handling</th>
                <th>Diskon</th>
                <th>Nett</th>
                <th>Laba</th>
                <th>Margin</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
                <?php
                    $margin = $r->grand_total > 0
                        ? round(($r->laba / $r->grand_total) * 100, 1)
                        : 0;
                ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($r->tanggal)); ?></td>
                    <td class="text-center"><?= $r->jumlah_transaksi ?></td>
                    <td class="text-right">Rp <?= number_format($r->subtotal_produk) ?></td>
                    <td class="text-right">Rp <?= number_format($r->shipping_cost) ?></td>
                    <td class="text-right">Rp <?= number_format($r->handling_fee) ?></td>
                    <td class="text-right">Rp <?= number_format($r->diskon) ?></td>
                    <td class="text-right">Rp <?= number_format($r->grand_total) ?></td>
                    <td class="text-right">Rp <?= number_format($r->laba) ?></td>
                    <td class="text-center"><?= $margin ?>%</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-center">TOTAL</td>
                <td class="text-right">Rp <?= number_format($totalGross) ?></td>
                <td class="text-right">Rp <?= number_format($totalShipping) ?></td>
                <td class="text-right">Rp <?= number_format($totalHandling) ?></td>
                <td class="text-right">Rp <?= number_format($totalDiskon) ?></td>
                <td class="text-right">Rp <?= number_format($totalNett) ?></td>
                <td class="text-right">Rp <?= number_format($totalLaba) ?></td>
                <td class="text-center"><?= $totalNett > 0 ? round(($totalLaba / $totalNett) * 100, 1) : 0 ?>%</td>
            </tr>
        </tfoot>
    </table>

    <table class="summary">
        <tr>
            <td>Total Omzet</td>
            <td class="text-right"><b>Rp <?= number_format($totalNett) ?></b></td>
        </tr>
        <tr>
            <td>Total Laba</td>
            <td class="text-right"><b>Rp <?= number_format($totalLaba) ?></b></td>
        </tr>
        <tr>
            <td>Rata-Rata Margin</td>
            <td class="text-right"><b><?= $totalNett > 0 ? round(($totalLaba / $totalNett) * 100, 1) : 0 ?>%</b></td>
        </tr>
    </table>

    <div class="signature">
        <div>Mengetahui,</div>
        <div class="signature-line"></div>
        <div class="signature-name"><?= esc($admin->first_name . ' ' . $admin->last_name) ?></div>
        <div class="signature-name">(<?= esc($admin->name) ?>)</div>
    </div>
</body>
</html>