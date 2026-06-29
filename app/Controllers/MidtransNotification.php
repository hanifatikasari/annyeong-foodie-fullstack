<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class MidtransNotification extends Controller
{
    public function handle()
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);

        try {
            $notif = new \Midtrans\Notification();
        } catch (\Exception $e) {
            log_message('error', '[Midtrans Webhook] Invalid notification: ' . $e->getMessage());

            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid notification']);
        }

        // Data dari Midtrans
        $midtransOrderId   = $notif->order_id;
        $transactionId     = $notif->transaction_id;
        $transactionStatus = $notif->transaction_status;
        $paymentType       = $notif->payment_type;
        $fraudStatus       = $notif->fraud_status ?? 'accept';

        log_message('info', "[Midtrans Webhook] OrderID={$midtransOrderId} Status={$transactionStatus}");

        // Cari order berdasarkan midtrans_order_id
        $penjualanModel = model('PenjualanModel');
        $order = $penjualanModel->where('midtrans_order_id', $midtransOrderId)->first();

        if (!$order) {
            log_message('error', "[Midtrans Webhook] Order tidak ditemukan: {$midtransOrderId}");

            return $this->response->setStatusCode(200)->setJSON(['message' => 'Order not found']);
        }

        $invoiceNo = $order->invoice_no;
        log_message('info', "[Midtrans Webhook] Invoice={$invoiceNo} ditemukan.");

        // Mapping status
        $newOrderStatus = $order->order_status;
        $newPaymentStatus = $order->payment_status;

        switch ($transactionStatus) {
            case 'capture':
                if ($fraudStatus === 'challenge') {
                    $newOrderStatus = 'pending';
                    $newPaymentStatus = 'pending_verification';
                } else {
                    $newOrderStatus = 'processing';
                    $newPaymentStatus = 'paid';
                }
                break;

            case 'settlement':
                $newOrderStatus = 'processing';
                $newPaymentStatus = 'paid';
                break;

            case 'pending':
                // tidak melakukan apa-apa
                break;

            case 'deny':
            case 'cancel':
            case 'expire':
            case 'failure':
                if ($order->order_status !== 'cancelled') {
                    $detailModel = model('PenjualanDetailModel');
                    $inventoryModel = model('ProductInventoryModel');

                    $details = $detailModel->where('penjualan_id', $order->id)->findAll();
                    foreach ($details as $detail) {
                        $inventory = $inventoryModel->where('product_id', $detail->product_id)->first();
                        if ($inventory) {
                            $inventoryModel->update($inventory->id, ['qty' => $inventory->qty + $detail->qty]);
                        }
                    }

                    log_message('info', "[Midtrans Webhook] Stok dikembalikan untuk Invoice {$invoiceNo}");
                }

                $newOrderStatus = 'cancelled';
                $newPaymentStatus = 'unpaid';
                break;
        }

        // Update database
        $updateData = [
            'order_status' => $newOrderStatus,
            'payment_status' => $newPaymentStatus,
            'midtrans_order_id' => $midtransOrderId,
            'midtrans_transaction_id' => $transactionId,
            'midtrans_payment_type' => $paymentType,
            'midtrans_status' => $transactionStatus,
        ];

        if ($newPaymentStatus === 'paid' && $order->payment_status !== 'paid') {
            $updateData['verified_at'] = date('Y-m-d H:i:s');
            $updateData['verified_by'] = null;
        }

        $penjualanModel->update($order->id, $updateData);

        log_message('info', "[Midtrans Webhook] Invoice={$invoiceNo} berhasil diupdate. order_status={$newOrderStatus}, payment_status={$newPaymentStatus}");

        return $this->response->setStatusCode(200)->setJSON(['message' => 'OK', 'invoice' => $invoiceNo]);
    }
}