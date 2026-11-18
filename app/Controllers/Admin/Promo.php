<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\PromoModel;

class Promo extends BaseController
{
    protected PromoModel $promoModel;
    protected ActivityLogger $activityLogger;

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->promoModel = new PromoModel();
        $this->activityLogger = new ActivityLogger();
    }

    // ---------------------------------------------------------------
    // LIST
    // ---------------------------------------------------------------
    public function index()
    {
        $keyword = trim((string) $this->request->getGet('table_search'));
        $perPage = (int) ($this->request->getGet('perPage') ?? 10);

        if (! in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $promos = $this->promoModel->adminList($keyword)->paginate($perPage, 'promo');

        foreach ($promos as $row) {
            $row->status = $this->promoModel->getStatus($row);
        }

        return view('admin/promo/index', [
            'title'               => 'Promo & Diskon',
            'currentAdminMenu'    => 'promo',
            'currentAdminSubMenu' => 'promo',
            'promos'              => $promos,
            'pager'               => $this->promoModel->pager,
            'keyword'             => $keyword,
            'perPage'             => $perPage,
        ]);
    }

    // ---------------------------------------------------------------
    // FORM TAMBAH
    // ---------------------------------------------------------------
    public function create()
    {
        return view('admin/promo/create', [
            'title'               => 'Tambah Promo',
            'currentAdminMenu'    => 'promo',
            'currentAdminSubMenu' => 'promo',
        ]);
    }

    // ---------------------------------------------------------------
    // SIMPAN
    // ---------------------------------------------------------------
    public function store()
    {
        [$data, $errors] = $this->buildData(null);

        if ($data === null) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $this->promoModel->insert($data);
        $insertedId = $this->promoModel->getInsertID();

        // Catat ke Audit Trail
        $this->activityLogger->log(
            'create',
            'promo',
            "Promo baru '{$data['nama_promo']}' berhasil dibuat.",
            null,
            $data,
            'promo',
            (int) $insertedId
        );

        return redirect()->to(site_url('admin/promo'))
            ->with('success', 'Promo berhasil ditambahkan.');
    }

    // ---------------------------------------------------------------
    // FORM EDIT
    // ---------------------------------------------------------------
    public function edit($id)
    {
        $promo = $this->findAutomatic((int) $id);

        return view('admin/promo/edit', [
            'title'               => 'Edit Promo',
            'currentAdminMenu'    => 'promo',
            'currentAdminSubMenu' => 'promo',
            'promo'               => $promo,
            'dipakai'             => $this->promoModel->countUsage((int) $promo->id),
        ]);
    }

    // ---------------------------------------------------------------
    // UPDATE
    // ---------------------------------------------------------------
    public function update($id)
    {
        $promo = $this->findAutomatic((int) $id);

        [$data, $errors] = $this->buildData((int) $promo->id);

        if ($data === null) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $oldData = [
            'nama_promo'   => $promo->nama_promo,
            'jenis_diskon' => $promo->jenis_diskon,
            'nilai_diskon' => (int) $promo->nilai_diskon,
            'min_subtotal' => (int) $promo->min_subtotal,
            'max_diskon'   => $promo->max_diskon !== null ? (int) $promo->max_diskon : null,
            'starts_at'    => $promo->starts_at,
            'ends_at'      => $promo->ends_at,
            'usage_limit'  => $promo->usage_limit !== null ? (int) $promo->usage_limit : null,
            'is_active'    => (int) $promo->is_active,
        ];

        $this->promoModel->update($promo->id, $data);

        // Catat ke Audit Trail
        $this->activityLogger->log(
            'update',
            'promo',
            "Promo '{$promo->nama_promo}' (ID: {$promo->id}) diperbarui.",
            $oldData,
            $data,
            'promo',
            (int) $promo->id
        );

        return redirect()->to(site_url('admin/promo'))
            ->with('success', 'Promo berhasil diperbarui.');
    }

    // ---------------------------------------------------------------
    // AKTIF / NONAKTIF (satu klik dari daftar)
    // ---------------------------------------------------------------
    public function toggle($id)
    {
        $promo   = $this->findAutomatic((int) $id);
        $newState = (int) $promo->is_active === 1 ? 0 : 1;

        if ($newState === 1
            && $this->promoModel->hasActiveTier((int) $promo->min_subtotal, (int) $promo->id)) {
            return redirect()->back()->with(
                'error',
                'Tidak bisa mengaktifkan: sudah ada promo aktif lain dengan minimal belanja yang sama.'
            );
        }

        $this->promoModel->update($promo->id, ['is_active' => $newState]);

        // Catat ke Audit Trail
        $this->activityLogger->log(
            'toggle',
            'promo',
            "Promo '{$promo->nama_promo}' (ID: {$id}) " . ($newState === 1 ? 'diaktifkan.' : 'dinonaktifkan.'),
            ['is_active' => (int) $promo->is_active],
            ['is_active' => $newState],
            'promo',
            (int) $id
        );

        return redirect()->back()->with(
            'success',
            $newState === 1 ? 'Promo diaktifkan.' : 'Promo dinonaktifkan.'
        );
    }

    // ---------------------------------------------------------------
    // HAPUS (ditolak jika sudah pernah dipakai transaksi)
    // ---------------------------------------------------------------
    public function delete($id)
    {
        $promo  = $this->findAutomatic((int) $id);
        $dipakai = $this->promoModel->countUsage((int) $promo->id);

        if ($dipakai > 0) {
            return redirect()->back()->with(
                'error',
                "Promo \"{$promo->nama_promo}\" sudah dipakai di {$dipakai} transaksi sehingga tidak bisa "
                . 'dihapus (riwayat pesanan akan hilang). Nonaktifkan saja.'
            );
        }

        $this->promoModel->delete($promo->id);

        // Catat ke Audit Trail
        $this->activityLogger->log(
            'delete',
            'promo',
            "Promo '{$promo->nama_promo}' (ID: {$id}) dihapus.",
            [
                'nama_promo'   => $promo->nama_promo,
                'nilai_diskon' => (int) $promo->nilai_diskon,
                'min_subtotal' => (int) $promo->min_subtotal,
            ],
            null,
            'promo',
            (int) $id
        );

        return redirect()->to(site_url('admin/promo'))
            ->with('success', 'Promo berhasil dihapus.');
    }

    // ===============================================================
    // PRIVATE
    // ===============================================================

    private function findAutomatic(int $id): object
    {
        $promo = $this->promoModel
            ->where('tipe_promo', 'automatic')
            ->find($id);

        if (! $promo) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $promo;
    }

    private function rules(): array
    {
        return [
            'nama_promo' => [
                'label'  => 'Nama promo',
                'rules'  => 'required|max_length[100]',
                'errors' => [
                    'required'   => 'Nama promo wajib diisi.',
                    'max_length' => 'Nama promo maksimal 100 karakter.',
                ],
            ],
            'jenis_diskon' => [
                'label'  => 'Jenis diskon',
                'rules'  => 'required|in_list[fixed,percentage]',
                'errors' => ['in_list' => 'Jenis diskon tidak valid.'],
            ],
            'nilai_diskon' => [
                'label'  => 'Nilai diskon',
                'rules'  => 'required|is_natural_no_zero',
                'errors' => [
                    'required'           => 'Nilai diskon wajib diisi.',
                    'is_natural_no_zero' => 'Nilai diskon harus berupa angka bulat lebih dari 0.',
                ],
            ],
            'min_subtotal' => [
                'label'  => 'Minimal belanja',
                'rules'  => 'required|is_natural_no_zero',
                'errors' => [
                    'required'           => 'Minimal belanja wajib diisi.',
                    'is_natural_no_zero' => 'Minimal belanja harus berupa angka bulat lebih dari 0.',
                ],
            ],
            'max_diskon' => [
                'label'  => 'Maksimal diskon',
                'rules'  => 'permit_empty|is_natural_no_zero',
                'errors' => ['is_natural_no_zero' => 'Maksimal diskon harus berupa angka bulat lebih dari 0.'],
            ],
            'starts_at' => [
                'label'  => 'Tanggal mulai',
                'rules'  => 'permit_empty|valid_date[Y-m-d\TH:i]',
                'errors' => ['valid_date' => 'Format tanggal mulai tidak valid.'],
            ],
            'ends_at' => [
                'label'  => 'Tanggal berakhir',
                'rules'  => 'permit_empty|valid_date[Y-m-d\TH:i]',
                'errors' => ['valid_date' => 'Format tanggal berakhir tidak valid.'],
            ],
            'usage_limit' => [
                'label'  => 'Kuota pemakaian',
                'rules'  => 'permit_empty|is_natural_no_zero',
                'errors' => ['is_natural_no_zero' => 'Kuota pemakaian harus berupa angka bulat lebih dari 0.'],
            ],
            'is_active' => [
                'label'  => 'Status',
                'rules'  => 'required|in_list[0,1]',
                'errors' => ['in_list' => 'Status tidak valid.'],
            ],
        ];
    }

    /**
     * Validasi + aturan bisnis. Mengembalikan [data, errors].
     * $data bernilai null jika ada error.
     */
    private function buildData(?int $exceptId): array
    {
        if (! $this->validate($this->rules())) {
            return [null, $this->validator->getErrors()];
        }

        $p      = $this->request->getPost();
        $jenis  = $p['jenis_diskon'];
        $nilai  = (int) $p['nilai_diskon'];
        $min    = (int) $p['min_subtotal'];
        $aktif  = (int) $p['is_active'];
        $start  = $this->toDb($p['starts_at'] ?? null);
        $end    = $this->toDb($p['ends_at'] ?? null);
        $limit  = ($p['usage_limit'] ?? '') !== '' ? (int) $p['usage_limit'] : null;
        $max    = ($jenis === 'percentage' && ($p['max_diskon'] ?? '') !== '')
            ? (int) $p['max_diskon']
            : null;

        $errors = [];

        if ($jenis === 'percentage' && $nilai > 100) {
            $errors['nilai_diskon'] = 'Persentase diskon maksimal 100%.';
        }

        if ($jenis === 'fixed' && $nilai >= $min) {
            $errors['nilai_diskon'] = 'Nominal diskon harus lebih kecil dari minimal belanja.';
        }

        if ($start !== null && $end !== null && $end <= $start) {
            $errors['ends_at'] = 'Tanggal berakhir harus setelah tanggal mulai.';
        }

        if ($aktif === 1 && $this->promoModel->hasActiveTier($min, $exceptId)) {
            $errors['min_subtotal'] = 'Sudah ada promo aktif lain dengan minimal belanja yang sama. '
                . 'Ubah minimal belanja atau nonaktifkan promo tersebut.';
        }

        if ($errors) {
            return [null, $errors];
        }

        return [[
            'nama_promo'   => trim($p['nama_promo']),
            'kode_promo'   => null,
            'tipe_promo'   => 'automatic',
            'jenis_diskon' => $jenis,
            'nilai_diskon' => $nilai,
            'min_subtotal' => $min,
            'max_diskon'   => $max,
            'starts_at'    => $start,
            'ends_at'      => $end,
            'usage_limit'  => $limit,
            'is_active'    => $aktif,
        ], []];
    }

    /** "2026-11-12T08:00" (datetime-local) -> "2026-11-12 08:00:00", kosong -> null */
    private function toDb(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $dt = \DateTime::createFromFormat('Y-m-d\TH:i', $value);

        return $dt ? $dt->format('Y-m-d H:i:s') : null;
    }
}
