<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
        .header h3 {
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
        }
        th {
            background: #efefef;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .info td {
            border: none;
            padding: 2px;
        }
        .signature {
            margin-top: 70px;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>ANNYEONG FOODIE</h2>
        Jl. Walisongo, Ruko Triumphant Futsal Bawang<br>
        Kabupaten Batang, Jawa Tengah
        <h3>LAPORAN ABSENSI PEGAWAI</h3>
    </div>

    <table class="info">
        <tr>
            <td width="20%">Periode</td>
            <td width="80%">:
                <?= date('F', mktime(0,0,0,$bulan,1)) ?> <?= $tahun ?>
            </td>
        </tr>
        <tr>
            <td>Tanggal Cetak</td>
            <td>: <?= date('d/m/Y H:i') ?></td>
        </tr>
        <tr>
            <td>Dicetak Oleh</td>
            <td>: <?= esc($admin->first_name.' '.$admin->last_name) ?></td>
        </tr>
    </table>

    <br>

    <table>
        <thead>
            <tr>
                <th>Pegawai</th>
                <th>Tanggal</th>
                <th>Masuk</th>
                <th>Status Masuk</th>
                <th>Keluar</th>
                <th>Status Keluar</th>
                <th>Total Jam</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($rows as $r): ?>
                <tr>
                    <td><?= esc(trim($r->first_name.' '.$r->last_name)) ?></td>
                    <td><?= date('d/m/Y', strtotime($r->jam_masuk)) ?></td>
                    <td class="text-center">
                        <?= $r->jam_masuk ? date('H:i', strtotime($r->jam_masuk)) : '-' ?>
                    </td>
                    <td class="text-center"><?= esc($r->status_masuk) ?></td>
                    <td class="text-center">
                        <?= $r->jam_keluar ? date('H:i', strtotime($r->jam_keluar)) : '-' ?>
                    </td>
                    <td class="text-center"><?= esc($r->status_keluar) ?></td>
                    <td class="text-center"><?= $r->total_jam ?></td>
                    <td class="text-center"><?= esc($r->keterangan) ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <div class="signature">
        Mengetahui,<br><br><br><br>
        <b><?= esc($admin->first_name.' '.$admin->last_name) ?></b>
    </div>

</body>
</html>
