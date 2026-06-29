<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk <?= esc($penjualan->invoice_no) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Consolas, "Courier New", monospace;
            font-size: 12px;
            width: 80mm;
            margin: auto;
            color: #000;
        }

        .wrapper {
            width: 80mm;
            padding: 15px;
        }

        .center {
            text-align: center;
        }

        .logo img {
            width: 200px;
            margin: auto;
            display: block;
        }

        h2 {
            font-size: 15px;
            margin-top: 5px;
        }

        .small {
            font-size: 11px;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .info table {
            width: 100%;
            font-size: 12px;
        }

        .info td {
            vertical-align: top;
            padding: 1px 0;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
        }

        .items td {
            padding: 2px 0;
        }

        .items .nama {
            width: 100%;
        }

        .items .qty {
            width: 30%;
        }

        .items .harga {
            text-align: right;
            white-space: nowrap;
        }

        .total {
            width: 100%;
        }

        .total td {
            padding: 2px 0;
        }

        .total td:last-child {
            text-align: right;
        }

        .big {
            font-size: 15px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 11px;
        }

        @media print {
            body {
                width: 80mm;
            }

            @page {
                margin: 0;
                size: 80mm auto;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="wrapper">
        <div class="center logo">
            <?php
            $logo = FCPATH . 'uploads/settings/logo.png';
            if (file_exists($logo)):
            ?>
                <img src="<?= base_url('uploads/settings/logo.png') ?>">
            <?php endif; ?>
            <h2>ANNYEONG.FOODIE</h2>
            <div class="small">Let's Get Something To Eat</div>
            <div class="small">Since 2022</div>
            <br>
            <div class="small">Ruko Triumphant Futsal Bawang</div>
            <div class="small">Batang</div>
            <div class="small">0858 7950 5277</div>
        </div>
        <div class="line"></div>
        <table class="info">
            <tr>
                <td><?= date('d M Y H:i', strtotime($penjualan->created_at)) ?></td>
                <td style="text-align:right"><?= esc($penjualan->invoice_no) ?></td>
            </tr>
            <tr>
                <td colspan="2">
                    Kasir : <?= esc(trim($penjualan->kasir_name ?? '-')) ?>
                </td>
            </tr>
            <?php if (!empty($penjualan->shipping_address)): ?>
                <tr>
                    <td colspan="2"><?= esc($penjualan->shipping_address) ?></td>
                </tr>
            <?php endif; ?>
        </table>
        <div class="line"></div>
        <table class="items">
            <?php foreach ($detail as $item): ?>
                <tr>
                    <td class="nama" colspan="3"><?= esc($item->product_name) ?></td>
                </tr>
                <tr>
                    <td class="qty">
                        <?= $item->qty ?> x <?= number_format($item->selling_price, 0, ',', '.') ?>
                    </td>
                    <td></td>
                    <td class="harga"><?= number_format($item->subtotal, 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="line"></div>
        <table class="total">
            <tr>
                <td>Subtotal</td>
                <td>Rp<?= number_format($penjualan->subtotal_produk, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Handling Fee</td>
                <td>Rp<?= number_format($penjualan->handling_fee, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Ongkir</td>
                <td>Rp<?= number_format($penjualan->shipping_cost, 0, ',', '.') ?></td>
            </tr>
            <?php if ($penjualan->diskon > 0): ?>
                <tr>
                    <td>Diskon</td>
                    <td>-Rp<?= number_format($penjualan->diskon, 0, ',', '.') ?></td>
                </tr>
            <?php endif; ?>
        </table>
        <div class="line"></div>
        <table class="total">
            <tr class="big">
                <td>TOTAL</td>
                <td>Rp<?= number_format($penjualan->grand_total, 0, ',', '.') ?></td>
            </tr>
        </table>
        <div class="line"></div>
        <table class="total">
            <tr>
                <td>Pembayaran</td>
                <td><?= esc($penjualan->pembayaran) ?></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <?php
                    if ($penjualan->payment_status == 'paid') {
                        echo 'LUNAS';
                    } elseif ($penjualan->payment_status == 'pending_verification') {
                        echo 'MENUNGGU VERIFIKASI';
                    } else {
                        echo strtoupper($penjualan->payment_status);
                    }
                    ?>
                </td>
            </tr>
        </table>
        <div class="line"></div>
        <div class="footer">
            Thankyou for your order !!!<br><br>
            Follow Instagram<br>
            <strong>@annyeong.foodie</strong><br>
            Don't Forget To Mention Us
        </div>
    </div>
</body>
</html>