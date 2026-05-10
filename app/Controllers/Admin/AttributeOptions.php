<?php 

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AttributeModel;
use App\Models\AttributeOptionModel;

class AttributeOptions extends BaseController
{
    protected $attributeModel;
    protected $attributeOptionModel;

    public function __construct()
    {
        helper(['url', 'form']);
        $this->attributeModel = new AttributeModel();
        $this->attributeOptionModel = new AttributeOptionModel();
    }

    // app/Controllers/Admin/AttributeOptions.php

    public function index($attributeId = null, $optionId = null)
    {
        $attribute = $this->attributeModel->find($attributeId);
        if (!$attribute) {
            return redirect()->to('admin/attributes')->with('error', 'Attribute not found');
        }

        // Ambil semua opsi untuk tabel di sebelah kanan
        $attributeOptions = $this->attributeOptionModel->where('attribute_id', $attributeId)->paginate(10, 'bootstrap');

        // LOGIKA EDIT: Jika ada $optionId di URL, cari datanya
        $attributeOption = null;
        if ($optionId) {
            $attributeOption = $this->attributeOptionModel->find($optionId);
        }

        $data = [
            'attribute'        => $attribute,
            'attributeOptions' => $attributeOptions,
            'attributeOption'  => $attributeOption, // Kirim ke view
            'pager'            => $this->attributeOptionModel->pager,
        ];

        return view('admin/attribute_options/index', $data);
    }

    public function store($attributeId = null)
    {
        $params = [
            'attribute_id' => $attributeId,
            'name'         => $this->request->getPost('name'),
        ];

        if ($this->attributeOptionModel->save($params)) {
            return redirect()->to("admin/attributes/{$attributeId}/options")->with('success', 'Saved');
        }
        return redirect()->back()->withInput()->with('errors', $this->attributeOptionModel->errors());
    }

public function update($attributeId = null, $optionId = null)
{
    // Cek apakah ID Option ada agar tidak terjadi error "Deletes are not allowed..."
    if (empty($optionId)) {
        return redirect()->back()->with('error', 'ID Option tidak ditemukan.');
    }

    $params = [
        'id'           => $optionId,
        'attribute_id' => $attributeId,
        'name'         => $this->request->getPost('name'),
    ];

    if ($this->attributeOptionModel->save($params)) {
        return redirect()->to("admin/attributes/{$attributeId}/options")->with('success', 'Updated');
    }
    return redirect()->back()->withInput()->with('errors', $this->attributeOptionModel->errors());
}

public function destroy($attributeId, $optionId)
{
    // Validasi agar ID tidak kosong
    if (empty($optionId)) {
        return redirect()->back()->with('error', 'Gagal menghapus: ID Kosong.');
    }

    // Eksekusi hapus dengan kondisi WHERE ID yang jelas
    if ($this->attributeOptionModel->delete($optionId)) {
        return redirect()->to("admin/attributes/{$attributeId}/options")->with('success', 'Deleted');
    }

    return redirect()->back()->with('error', 'Gagal menghapus data.');
}
}