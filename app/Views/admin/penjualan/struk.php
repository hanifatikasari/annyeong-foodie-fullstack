<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk <?= esc($penjualan->invoice_no) ?></title>
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        @page{
            size:58mm auto;
            margin:2mm;
        }

        html,
        body{
            width:58mm;
            font-family:Consolas,"Courier New",monospace;
            font-size:10px;
            color:#000;
        }

        .wrapper{
            width:54mm;
            margin:0 auto;
        }

        .center{
            text-align:center;
        }

        .logo img{
            width:90px;
            display:block;
            margin:auto;
        }

        h2{
            font-size:15px;
            margin-top:3px;
        }

        .small{
            font-size:12px;
        }

        .line{
            border-top:1px dashed #000;
            margin:5px 0;
        }

        .info{
            width:100%;
        }

        .info td{
            padding:1px 0;
            vertical-align:top;
        }

        .items{
            width:100%;
            border-collapse:collapse;
        }

        .items td{
            padding:1px 0;
        }

        .nama{
            font-weight:bold;
        }

        .qty{
            width:60%;
        }

        .harga{
            text-align:right;
        }

        .total{
            width:100%;
        }

        .total td{
            padding:2px 0;
        }

        .total td:last-child{
            text-align:right;
        }

        .big{
            font-size:12px;
            font-weight:bold;
        }

        .footer{
            text-align:center;
            margin-top:8px;
            font-size:12px;
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