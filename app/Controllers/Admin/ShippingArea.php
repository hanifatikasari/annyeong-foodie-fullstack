<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ShippingAreaModel;

class ShippingArea extends BaseController
{
    protected $shippingAreaModel;

    public function __construct()
    {
        $this->shippingAreaModel = new ShippingAreaModel();
    }

    /*
    |--------------------------------------------------------------------------
    | LIST AREA PENGIRIMAN
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $keyword = $this->request->getGet('table_search');
        $perPage = $this->request->getGet('perPage') ?? 10;
        $builder = $this->shippingAreaModel;

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('area_name', $keyword)
                ->orLike('estimasi', $keyword)
                ->groupEnd();
        }

        $builder->orderBy('area_name', 'ASC');

        return view('admin/shipping_area/index', [
            'title' => 'Master Area Pengiriman',
            'currentAdminMenu' => 'master',
            'currentAdminSubMenu' => 'shipping_area',
            'shippingAreas' => $builder->paginate($perPage, 'shippingArea'),
            'pager' => $builder->pager,
            'keyword' => $keyword,
            'perPage' => $perPage,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FORM TAMBAH
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('admin/shipping_area/create', [
            'title' => 'Tambah Area Pengiriman',
            'currentAdminMenu' => 'master',
            'currentAdminSubMenu' => 'shipping_area',
            'validation' => \Config\Services::validation(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN
    |--------------------------------------------------------------------------
    */
    public function store()
    {
        $rules = [
            'area_name' => 'required|max_length[30]',
            'ongkir' => 'required|numeric',
            'estimasi' => 'permit_empty|max_length[30]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->shippingAreaModel->save([
            'area_name' => $this->request->getPost('area_name'),
            'ongkir' => $this->request->getPost('ongkir'),
            'estimasi' => $this->request->getPost('estimasi'),
            'is_active' => $this->request->getPost('is_active') ?? 1,
        ]);

        return redirect()
            ->to(site_url('admin/shipping_area'))
            ->with('success', 'Area pengiriman berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | FORM EDIT
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $shippingArea = $this->shippingAreaModel->find($id);

        if (!$shippingArea) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('admin/shipping_area/edit', [
            'title' => 'Edit Area Pengiriman',
            'currentAdminMenu' => 'master',
            'currentAdminSubMenu' => 'shipping_area',
            'shippingArea' => $shippingArea,
            'validation' => \Config\Services::validation(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update($id)
    {
        $rules = [
            'area_name' => 'required|max_length[30]',
            'ongkir' => 'required|numeric',
            'estimasi' => 'permit_empty|max_length[30]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->shippingAreaModel->update($id, [
            'area_name' => $this->request->getPost('area_name'),
            'ongkir' => $this->request->getPost('ongkir'),
            'estimasi' => $this->request->getPost('estimasi'),
            'is_active' => $this->request->getPost('is_active') ?? 1,
        ]);

        $page = $this->request->getGet('page') ?? 1;
        $keyword = $this->request->getGet('table_search');
        $perPage = $this->request->getGet('perPage') ?? 10;

        return redirect()->to(
            site_url(
                'admin/shipping_area?page=' . $page .
                '&perPage=' . $perPage .
                '&table_search=' . urlencode($keyword)
            )
        )->with('success', 'Area pengiriman berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | HAPUS
    |--------------------------------------------------------------------------
    */
    public function delete($id)
    {
        $this->shippingAreaModel->delete($id);

        $page = $this->request->getGet('page') ?? 1;
        $keyword = $this->request->getGet('table_search');
        $perPage = $this->request->getGet('perPage') ?? 10;

        return redirect()->to(
            site_url(
                'admin/shipping_area?page=' . $page .
                '&perPage=' . $perPage .
                '&table_search=' . urlencode($keyword)
            )
        )->with('success', 'Area pengiriman berhasil dihapus.');
    }
}