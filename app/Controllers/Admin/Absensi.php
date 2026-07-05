<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\AbsensiModel;
class Absensi extends BaseController
{
    protected $absensi;
    public function __construct()
    {
        $this->absensi = new AbsensiModel();
    }

    public function index()
    {
        $user = $this->auth->user()->row();
        $userId = $user->id;
        $absen = $this->absensi->getTodayAttendance($userId);
        return view('admin/absensi/index', [
            'title' => 'Absensi',
            'currentAdminMenu' => 'absensi',
            'currentAdminSubMenu' => 'absensi',
            'absen' => $absen
        ]);
    }

    public function masuk()
    {
        $user = $this->auth->user()->row();
        $userId = $user->id;
        $today = $this->absensi->getTodayAttendance($userId);
        if ($today) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absensi masuk hari ini.');
        }
        $jamMasuk = date('Y-m-d H:i:s');
        $statusMasuk = (date('H:i:s') <= '08:00:00')
            ? 'Tepat Waktu'
            : 'Terlambat';

        $this->absensi->insert([
            'user_id'       => $userId,
            'jam_masuk'     => $jamMasuk,
            'status_masuk'  => $statusMasuk,
            'keterangan'    => 'Hadir'
        ]);
        return redirect()->back()->with('success', 'Berhasil absen masuk.');
    }

    public function keluar()
    {
        $user = $this->auth->user()->row();
        $userId = $user->id;
        $today = $this->absensi->getTodayAttendance($userId);
        if (!$today) {
            return redirect()->back()->with('error', 'Silakan absen masuk terlebih dahulu.');
        }
        if ($today->jam_keluar != null) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absensi keluar.');
        }
        $jamKeluar = date('Y-m-d H:i:s');

        $statusKeluar = (date('H:i:s') < '17:00:00')
            ? 'Pulang Cepat'
            : 'Normal';

        $totalJam = round(
            (strtotime($jamKeluar) - strtotime($today->jam_masuk)) / 3600,
            2
        );

        $this->absensi->update($today->id, [
            'jam_keluar'     => $jamKeluar,
            'status_keluar'  => $statusKeluar,
            'total_jam'      => $totalJam
        ]);
        return redirect()->back()->with('success', 'Berhasil absen keluar.');
    }

    public function riwayat()
    {
        $user = $this->auth->user()->row();

        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $pegawai = $this->request->getGet('pegawai');

        $isAdmin = $this->auth->inGroup(['admin', 'pemilik']);

        if ($isAdmin) {
            $riwayat = $this->absensi->getRiwayatFiltered($bulan, $tahun, $pegawai);
            $pegawaiList = $this->absensi->getPegawaiList();
        } else {
            $riwayat = $this->absensi->getRiwayatUserFiltered(
                $user->id,
                $bulan,
                $tahun
            );

            $pegawaiList = [];
        }

        return view('admin/absensi/riwayat', [
            'title' => 'Riwayat Absensi',
            'currentAdminMenu' => 'absensi',
            'currentAdminSubMenu' => 'absensi',

            'isAdmin' => $isAdmin,
            'riwayat' => $riwayat,

            'pegawaiList' => $pegawaiList,

            'bulan' => $bulan,
            'tahun' => $tahun,
            'pegawai' => $pegawai
        ]);
    }

    //print pdf
    public function pdf()
    {
        $user = $this->auth->user()->row();

        $bulan   = $this->request->getGet('bulan') ?? date('m');
        $tahun   = $this->request->getGet('tahun') ?? date('Y');
        $pegawai = $this->request->getGet('pegawai');

        $isAdmin = $this->auth->inGroup(['admin','pemilik']);

        if ($isAdmin) {
            $rows = $this->absensi->getRiwayatFiltered($bulan, $tahun, $pegawai);
        } else {
            $rows = $this->absensi->getRiwayatUserFiltered(
                $user->id,
                $bulan,
                $tahun
            );
        }

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4-L'
        ]);

        $html = view('admin/reports/pdf_absensi', [
            'rows'   => $rows,
            'bulan'  => $bulan,
            'tahun'  => $tahun,
            'admin'  => $user
        ]);

        $mpdf->WriteHTML($html);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setBody($mpdf->Output('laporan_absensi.pdf', 'S'));
    }
}