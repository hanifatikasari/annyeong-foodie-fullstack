<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Backup extends BaseController
{
    public function __construct()
    {
        helper(['url', 'form']);
    }

    public function index()
    {
        $data = [
            'title'               => 'Backup and Restore',
            'currentAdminMenu'    => 'data_management',
            'currentAdminSubMenu' => 'backup',
            'flash'               => session()->getFlashdata('message'),
            'error'               => session()->getFlashdata('error'),
        ];

        return view('admin/backup/index', $data);
    }

    public function download()
    {
        $dbConfig = config('Database');
        $group = $dbConfig->defaultGroup;
        $settings = $dbConfig->{$group};

        $host = $settings['hostname'];
        $user = $settings['username'];
        $pass = $settings['password'];
        $name = $settings['database'];

        $fileName = 'backup_' . date('Ymd_His') . '.sql';
        $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;

        $mysqldump = '"C:\\xampp\\mysql\\bin\\mysqldump.exe"';
        $command = sprintf(
            '%s --user=%s --password=%s --host=%s --databases %s --single-transaction --skip-lock-tables --result-file=%s',
            $mysqldump,
            escapeshellarg($user),
            escapeshellarg($pass),
            escapeshellarg($host),
            escapeshellarg($name),
            escapeshellarg($tmpFile)
        );

        exec($command . ' 2>&1', $output, $status);

        if ($status !== 0 || ! file_exists($tmpFile)) {
            session()->setFlashdata('error', 'Backup gagal: ' . implode(' ', $output));
            return redirect()->to(site_url('admin/backup'));
        }

        $content = file_get_contents($tmpFile);

        return $this->response
            ->setHeader('Content-Type', 'application/sql')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Content-Transfer-Encoding', 'binary')
            ->setBody($content);
    }

    public function restore()
    {
        $file = $this->request->getFile('sql_file');

        if (! $file || ! $file->isValid()) {
            session()->setFlashdata('error', 'File SQL tidak ditemukan atau tidak valid.');
            return redirect()->to(site_url('admin/backup'));
        }

        if ($file->getClientExtension() !== 'sql') {
            session()->setFlashdata('error', 'Ekstensi file harus .sql');
            return redirect()->to(site_url('admin/backup'));
        }

        $tmpName = $file->getRandomName();
        $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $tmpName;
        $file->move(sys_get_temp_dir(), $tmpName);

        $dbConfig = config('Database');
        $group = $dbConfig->defaultGroup;
        $settings = $dbConfig->{$group};

        $host = $settings['hostname'];
        $user = $settings['username'];
        $pass = $settings['password'];
        $name = $settings['database'];

        $mysql = '"C:\\xampp\\mysql\\bin\\mysql.exe"';
        $command = sprintf(
            '%s --user=%s --password=%s --host=%s %s < %s',
            $mysql,
            escapeshellarg($user),
            escapeshellarg($pass),
            escapeshellarg($host),
            escapeshellarg($name),
            escapeshellarg($tmpFile)
        );

        exec($command . ' 2>&1', $output, $status);

        if ($status !== 0) {
            session()->setFlashdata('error', 'Restore gagal: ' . implode(' ', $output));
        } else {
            session()->setFlashdata('message', 'Restore database berhasil.');
        }

        return redirect()->to(site_url('admin/backup'));
    }
}
