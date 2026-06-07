<?php

namespace App\Controllers;

use CodeIgniter\Controller;

/**
 * MidtransNotification — Webhook Handler
 *
 * Midtrans akan melakukan POST ke URL ini setiap kali ada
 * perubahan status transaksi (payment success, expire, cancel, dll).
 *
 * PENTING:
 * - Controller ini extends Controller biasa (BUKAN BaseController)
 *   agar tidak ada session/auth yang jalan
 * - Endpoint ini harus bisa diakses tanpa login
 * - CSRF tidak diperlukan karena ini server-to-server communication
 */
class MidtransNotification extends Controller
{
    public function handle()
    {
        // Inisialisasi Midtrans config
        \Midtrans\Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);

        // ============================================================
        // Baca dan validasi notifikasi dari Midtrans
        // Midtrans\Notification membaca dari php://input (raw JSON)
        // dan otomatis verifikasi signature key
        // ============================================================
        try {
            $notif = new \Midtrans\Notification();
        } catch (\Exception $e) {
            log_message('error', '[Midtrans Webhook] Gagal baca notifikasi: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(400)
                ->setBody(json_encode(['error' => 'Invalid notification']));
        }

        // Ambil data dari notifikasi
        $transactionStatus = $notif->transaction_status; // capture, settlement, pending, expire, cancel, deny
        $fraudStatus       = $notif->fraud_status ?? 'accept'; // accept atau challenge
        $invoiceNo         = $notif->order_id;             // Ini adalah invoice_no kita (AES-YYYYMMDD-001)
        $paymentType       = $notif->payment_type;         // credit_card, gopay, bca_va, qris, dll
        $transactionId     = $notif->transaction_id;       // ID unik dari Midtrans

        log_message('info', "[Midtrans Webhook] Invoice: {$invoiceNo} | Status: {$transactionStatus} | Type: {$paymentType}");

        // ============================================================
        // Cari order berdasarkan invoice_no
        // ============================================================
        $penjualanModel = model('PenjualanModel');
        $order = $penjualanModel->getOrderByInvoice($invoiceNo);

        if (!$order) {
            log_message('error', "[Midtrans Webhook] Order tidak ditemukan: {$invoiceNo}");
            // Tetap return 200 agar Midtrans tidak retry terus-menerus
            return $this->response
                ->setStatusCode(200)
                ->setBody(json_encode(['message' => 'Order not found, ignored']));
        }

        // ============================================================
        // Mapping status Midtrans ke status order kita
        //
        // Midtrans Status:
        //   capture    = pembayaran CC berhasil (perlu cek fraud)
        //   settlement = pembayaran berhasil (bank transfer, ewallet setelah settle)
        //   pending    = menunggu pembayaran (VA/ewallet belum dibayar)
        //   deny       = ditolak bank/Midtrans
        //   cancel     = dibatalkan
        //   expire     = kadaluarsa (biasanya 24 jam)
        //   refund     = dikembalikan
        // ============================================================
        $newOrderStatus   = $order->order_status;   // default: tidak berubah
        $newPaymentStatus = $order->payment_status; // default: tidak berubah

        if ($transactionStatus === 'capture') {
            // Credit card payment
            if ($fraudStatus === 'challenge') {
                // Perlu review manual (flagged sebagai potensi fraud)
                $newOrderStatus   = 'pending_verification';
                $newPaymentStatus = 'menunggu_verifikasi';
                log_message('info', "[Midtrans Webhook] {$invoiceNo}: Challenge fraud, butuh review manual");
            } elseif ($fraudStatus === 'accept') {
                // Pembayaran CC berhasil
                $newOrderStatus   = 'processing';
                $newPaymentStatus = 'lunas';
                log_message('info', "[Midtrans Webhook] {$invoiceNo}: Credit card berhasil - LUNAS");
            }
        } elseif ($transactionStatus === 'settlement') {
            // Bank transfer / e-wallet sudah settle (T+1 atau langsung)
            $newOrderStatus   = 'processing';
            $newPaymentStatus = 'lunas';
            log_message('info', "[Midtrans Webhook] {$invoiceNo}: Settlement - LUNAS");

        } elseif ($transactionStatus === 'pending') {
            // VA/ewallet sudah dibuat tapi belum dibayar
            // Tidak ubah status, biarkan pending_payment
            log_message('info', "[Midtrans Webhook] {$invoiceNo}: Pending - menunggu pembayaran");

        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'])) {
            // Pembayaran gagal/kadaluarsa/dibatalkan
            // CATATAN: Stok sudah terlanjur dikurangi saat order dibuat.
            // Untuk production, pertimbangkan mengembalikan stok di sini.
            $newOrderStatus   = 'cancelled';
            $newPaymentStatus = 'menunggu_pembayaran';
            log_message('info', "[Midtrans Webhook] {$invoiceNo}: GAGAL/CANCEL - status: {$transactionStatus}");
        }

        // ============================================================
        // Update order di database
        // ============================================================
        $updateData = [
            'order_status'            => $newOrderStatus,
            'payment_status'          => $newPaymentStatus,
            'midtrans_transaction_id' => $transactionId,
            'midtrans_payment_type'   => $paymentType,
            'midtrans_status'         => $transactionStatus,
        ];

        // Jika lunas, tandai verified_at dan verified_by system (0 = system)
        if ($newPaymentStatus === 'lunas' && $order->payment_status !== 'lunas') {
            $updateData['verified_at'] = date('Y-m-d H:i:s');
            $updateData['verified_by'] = null; // null = system/otomatis
        }

        $penjualanModel->update($order->id, $updateData);

        log_message('info', "[Midtrans Webhook] {$invoiceNo} updated → order_status: {$newOrderStatus}, payment_status: {$newPaymentStatus}");

        // Selalu return 200 OK agar Midtrans tidak retry
        return $this->response
            ->setStatusCode(200)
            ->setBody(json_encode(['message' => 'OK', 'invoice' => $invoiceNo]));
    }
}