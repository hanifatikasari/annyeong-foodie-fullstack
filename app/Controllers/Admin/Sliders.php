<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Sliders extends BaseController
{
    protected string $dataPath;

    public function __construct()
    {
        $this->dataPath = WRITEPATH . 'uploads/carousel_slides.json';
    }

    public function index()
    {
        $this->data['title'] = 'Carousel';
        $this->data['slides'] = $this->readSlides();
        $this->data['editingSlide'] = null;
        $this->data['currentAdminMenu'] = 'catalogue';
        $this->data['currentAdminSubMenu'] = 'carousel';

        return view('admin/sliders/index', $this->data);
    }

    public function store()
    {
        if (!$this->validateSlide()) {
            return redirect()->back()->withInput()->with('error', 'Pastikan semua data wajib terisi dan file gambar valid.');
        }

        $slides = $this->readSlides();
        if (count($slides) >= 5) {
            return redirect()->back()->withInput()->with('error', 'Maksimal 5 slide carousel. Hapus salah satu slide terlebih dahulu.');
        }

        $imagePath = $this->handleImageUpload();
        if (!$imagePath) {
            return redirect()->back()->withInput()->with('error', 'Gambar slide wajib diunggah.');
        }

        $slides[] = [
            'id' => time(),
            'title' => trim($this->request->getPost('title') ?? ''),
            'subtitle' => trim($this->request->getPost('subtitle') ?? ''),
            'link' => trim($this->request->getPost('link') ?? ''),
            'image' => $imagePath,
            'order' => count($slides) + 1,
            'is_active' => (int) ($this->request->getPost('is_active') ?? 1),
        ];

        $this->saveSlides($slides);

        return redirect()->to('admin/sliders')->with('success', 'Slide carousel berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $slides = $this->readSlides();
        $slide = $this->findSlide($slides, $id);

        if (!$slide) {
            return redirect()->to('admin/sliders')->with('error', 'Slide tidak ditemukan.');
        }

        $this->data['title'] = 'Edit Carousel';
        $this->data['slides'] = $slides;
        $this->data['editingSlide'] = $slide;
        $this->data['currentAdminMenu'] = 'catalogue';
        $this->data['currentAdminSubMenu'] = 'carousel';

        return view('admin/sliders/index', $this->data);
    }

    public function update($id)
    {
        $slides = $this->readSlides();
        $found = false;
        foreach ($slides as &$slide) {

            if ((int)$slide['id'] === (int)$id) {
                $found = true;
                $imagePath = $slide['image'];
                $uploadedFile = $this->request->getFile('image');
                if ($uploadedFile && $uploadedFile->isValid() && !$uploadedFile->hasMoved()) {
                    $imagePath = $this->handleImageUpload();
                }
                $slide['title'] = trim($this->request->getPost('title') ?? '');
                $slide['subtitle'] = trim($this->request->getPost('subtitle') ?? '');
                $slide['link'] = trim($this->request->getPost('link') ?? '');
                $slide['image'] = $imagePath;
                $slide['is_active'] = (int)($this->request->getPost('is_active') ?? 1);
                break;
            }
        }
        if (!$found) {
            return redirect()->to('admin/sliders')
                ->with('error', 'Slide tidak ditemukan.');
        }
        $this->saveSlides($slides);
        return redirect()->to('admin/sliders')->with('success', 'Slide carousel berhasil diperbarui.');
    }

    public function delete($id)
    {
        $slides = $this->readSlides();
        $slide = $this->findSlide($slides, $id);

        if (!$slide) {
            return redirect()->to('admin/sliders')->with('error', 'Slide tidak ditemukan.');
        }

        $this->removeImage($slide['image'] ?? '');
        $slides = array_values(array_filter($slides, fn ($item) => (int) ($item['id'] ?? 0) !== (int) $id));
        $this->saveSlides($slides);

        return redirect()->to('admin/sliders')->with('success', 'Slide carousel berhasil dihapus.');
    }

    protected function validateSlide(bool $requireImage = true): bool
    {
        $rules = [
            'title' => 'required',
            'image' => 'permit_empty',
        ];

        if ($requireImage) {
            $rules['image'] = 'uploaded[image]|max_size[image,10240]|is_image[image]';
        }

        return $this->validate($rules);
    }

    protected function handleImageUpload(): string
    {
        $file = $this->request->getFile('image');
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return '';
        }

        $uploadDir = FCPATH . 'uploads/carousels';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadDir, $newName);

        $image = \Config\Services::image();
        $image
            ->withFile($uploadDir . '/' . $newName)
            ->fit(
                1920,
                700,
                'center'
            )
            ->save($uploadDir . '/' . $newName);
        return 'uploads/carousels/' . $newName;
    }

    protected function removeImage(string $imagePath): void
    {
        if (!$imagePath) {
            return;
        }

        $fullPath = FCPATH . $imagePath;
        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    protected function readSlides(): array
    {
        if (!is_file($this->dataPath)) {
            return [];
        }

        $json = file_get_contents($this->dataPath);
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return [];
        }

        usort($data, fn ($a, $b) => (int) ($a['order'] ?? 0) <=> (int) ($b['order'] ?? 0));

        return $data;
    }

    protected function saveSlides(array $slides): void
    {
        $dir = dirname($this->dataPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $slides = array_values($slides);
        file_put_contents($this->dataPath, json_encode($slides, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    protected function findSlide(array $slides, $id): ?array
    {
        foreach ($slides as $slide) {
            if ((int) ($slide['id'] ?? 0) === (int) $id) {
                return $slide;
            }
        }

        return null;
    }
}
