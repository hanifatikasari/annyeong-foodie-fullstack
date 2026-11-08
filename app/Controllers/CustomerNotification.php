<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class CustomerNotification extends BaseController
{
    protected $notificationModel;

    private function isStaff(): bool
    {
        return $this->auth->inGroup([
            'admin',
            'gudang',
            'produksi',
            'penjualan',
            'pemilik'
        ]);
    }

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    // Mengambil notifikasi milik pelanggan
    public function index()
    {
        if ($this->isStaff()) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Akses khusus pelanggan.'
                ]);
        }
        $userId = $this->currentUser->id;

        $notifications = $this->notificationModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $notifications
        ]);
    }

    // Menghitung notifikasi yang belum dibaca
    public function unreadCount()
    {
        if ($this->isStaff()) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Akses khusus pelanggan.'
                ]);
        }
        $userId = $this->currentUser->id;

        $count = $this->notificationModel
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();

        return $this->response->setJSON([
            'status' => 'success',
            'count' => $count
        ]);
    }

    // Menandai satu notifikasi sebagai sudah dibaca
    public function markAsRead($id)
    {
        $userId = $this->currentUser->id;

        $notification = $this->notificationModel
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Notifikasi tidak ditemukan.'
                ]);
        }

        $this->notificationModel->update($id, [
            'is_read' => 1
        ]);

        return $this->response->setJSON([
            'status' => 'success'
        ]);
    }

    // Menandai semua notifikasi sebagai sudah dibaca
    public function markAllAsRead()
    {
        $userId = $this->currentUser->id;

        $this->notificationModel
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->set(['is_read' => 1])
            ->update();

        return redirect()->to('account/notifications')
            ->with('success', 'Semua notifikasi telah dibaca.');
        
    }

    public function page()
    {
        if ($this->isStaff()) {
            return redirect()->to('admin/dashboard')
                ->with('error', 'Halaman ini khusus untuk pelanggan.');
        }
        $userId = (int) $this->currentUser->id;

        $notifications = $this->notificationModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $this->data['notifications'] = $notifications;
        $this->data['title'] = 'Notifikasi Saya';

        return view(
            'themes/indomarket/account/notifications',
            $this->data
        );
    }

    public function open($id)
    {
        $userId = (int) $this->currentUser->id;
        $notification = $this->notificationModel
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            return redirect()->to('account/notifications')
                ->with('error', 'Notifikasi tidak ditemukan.');
        }

        $this->notificationModel->update($notification->id, [
            'is_read' => 1
        ]);

        if (!empty($notification->reference_id)) {
            $order = model('PenjualanModel')->find((int) $notification->reference_id);
            if ($order && (int) $order->customer_id === $userId) {
                return redirect()->to(
                    'account/orders/detail/' . $order->id
                );
            }
        }
        return redirect()->to('account/notifications');
    }
}