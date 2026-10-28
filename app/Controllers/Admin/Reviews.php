<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Admin\Reviews — Moderasi ulasan produk.
 *
 * Fitur:
 *  - Daftar semua ulasan dengan filter (status, rating, pencarian)
 *  - Sembunyikan ulasan (Hide comment)
 *  - Publikasikan / Tampilkan ulasan (Show comment)
 *  - Toggle visibilitas
 *  - Hapus permanen (dengan konfirmasi, metode POST)
 */
class Reviews extends BaseController
{
    // ---------------------------------------------------------------
    // INDEX — Daftar semua ulasan
    // ---------------------------------------------------------------
    public function index()
    {
        $search       = trim($this->request->getGet('search') ?? '');
        $filterStatus = $this->request->getGet('status') ?? '';
        $filterRating = (int) ($this->request->getGet('rating') ?? 0);

        $reviews = model('ReviewModel')->getReviewsForAdmin($search, $filterStatus, $filterRating);

        $data = [
            'title'               => 'Manajemen Ulasan',
            'currentAdminMenu'    => 'reviews',
            'currentAdminSubMenu' => 'reviews',
            'reviews'             => $reviews,
            'search'              => $search,
            'filterStatus'        => $filterStatus,
            'filterRating'        => $filterRating,
        ];

        return view('admin/reviews/index', $data);
    }

    // ---------------------------------------------------------------
    // SHOW — Tampilkan ulasan di halaman publik
    // ---------------------------------------------------------------
    public function show(int $id)
    {
        return $this->setVisibility($id, 1, 'dipublikasikan (tampil ke publik)');
    }

    // ---------------------------------------------------------------
    // HIDE — Sembunyikan ulasan dari halaman publik
    // ---------------------------------------------------------------
    public function hide(int $id)
    {
        return $this->setVisibility($id, 0, 'disembunyikan dari halaman publik');
    }

    // ---------------------------------------------------------------
    // TOGGLE VISIBILITY — Sembunyikan / Publikasikan ulasan
    // ---------------------------------------------------------------
    public function toggleVisibility(int $id)
    {
        $reviewModel = model('ReviewModel');
        $review = $reviewModel->find($id);

        if (!$review) {
            return redirect()->back()->with('error', 'Ulasan tidak ditemukan.');
        }

        $newVisibility = $review->is_visible ? 0 : 1;
        $statusLabel   = $newVisibility ? 'dipublikasikan (tampil ke publik)' : 'disembunyikan dari halaman publik';

        return $this->setVisibility($id, $newVisibility, $statusLabel);
    }

    // ---------------------------------------------------------------
    // SET VISIBILITY HELPER
    // ---------------------------------------------------------------
    protected function setVisibility(int $id, int $visibility, string $label)
    {
        $reviewModel = model('ReviewModel');
        $review = $reviewModel->find($id);

        if (!$review) {
            return redirect()->back()->with('error', 'Ulasan tidak ditemukan.');
        }

        $reviewModel->update($id, ['is_visible' => $visibility]);

        return redirect()->to('admin/reviews')
            ->with('success', 'Ulasan #' . $id . ' berhasil ' . $label . '.');
    }

    // ---------------------------------------------------------------
    // DELETE — Hapus permanen (POST only)
    // ---------------------------------------------------------------
    public function delete(int $id)
    {
        $reviewModel = model('ReviewModel');
        $review = $reviewModel->find($id);

        if (!$review) {
            return redirect()->back()->with('error', 'Ulasan tidak ditemukan.');
        }

        $reviewModel->delete($id);

        return redirect()->to('admin/reviews')
            ->with('success', 'Ulasan #' . $id . ' berhasil dihapus secara permanen.');
    }
}
