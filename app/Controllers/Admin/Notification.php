<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class Notification extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function getUnread()
    {
        $userId = $this->currentUser->id ?? 0;
        
        $notifications = $this->notificationModel
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $count = count($notifications);

        return $this->response->setJSON([
            'status' => 'success',
            'count' => $count,
            'data' => $notifications
        ]);
    }

    public function markAsRead($id)
    {
        $userId = $this->currentUser->id ?? 0;

        $notif = $this->notificationModel
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($notif) {
            $this->notificationModel->update($id, ['is_read' => 1]);
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Notifikasi tidak ditemukan']);
    }

    public function markAllAsRead()
    {
        $userId = $this->currentUser->id ?? 0;

        $this->notificationModel
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->set(['is_read' => 1])
            ->update();

        return $this->response->setJSON(['status' => 'success']);
    }
}
