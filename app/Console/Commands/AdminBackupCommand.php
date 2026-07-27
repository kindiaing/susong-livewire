<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\confirm;

class AdminBackupCommand extends Command
{
    protected $signature = 'admin:backup
                            {--path= : 备份保存路径（默认 storage/backups）}
                            {--compress : 压缩为 .gz}
                            {--only-data : 仅导出数据（不含结构）}
                            {--only-structure : 仅导出结构（不含数据）}';

    protected $description = '备份数据库（支持全量/仅数据/仅结构）';

    public function handle(): int
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver !== 'mysql') {
            $this->error("当前仅支持 MySQL 备份，当前驱动: {$driver}");

            return self::FAILURE;
        }

        $host = config("database.connections.{$connection}.host", '127.0.0.1');
        $port = config("database.connections.{$connection}.port", 3306);
        $database = config("database.connections.{$connection}.database");
        $username = config("database.connections.{$connection}.username");
        $password = config("database.connections.{$connection}.password");

        // 备份路径
        $backupDir = $this->option('path') ?: storage_path('backups');
        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_His');
        $filename = "{$database}_{$timestamp}.sql";
        $filepath = rtrim($backupDir, '/').'/'.$filename;

        // 构建 mysqldump 命令
        $mysqldump = $this->findMysqldump();
        if (! $mysqldump) {
            $this->error('找不到 mysqldump 命令，请确认 MySQL 已安装并加入 PATH');

            return self::FAILURE;
        }

        $command = $this->buildDumpCommand($mysqldump, $host, $port, $username, $password, $database, $filepath);

        $this->info("正在备份数据库 [{$database}]...");

        $result = $this->executeCommand($command);

        if ($result !== 0) {
            $this->error('备份失败！请检查数据库连接配置。');

            return self::FAILURE;
        }

        // 压缩
        if ($this->option('compress') && function_exists('gzopen')) {
            $this->compressFile($filepath);
            $filepath .= '.gz';
            $filename .= '.gz';
        }

        $size = $this->formatBytes(filesize($filepath));

        $this->info('备份完成！');
        $this->line("  文件: <info>{$filename}</info>");
        $this->line("  大小: <info>{$size}</info>");
        $this->line("  路径: <info>{$filepath}</info>");

        // 清理旧备份（保留最近 7 个）
        $this->cleanupOldBackups($backupDir, 7);

        return self::SUCCESS;
    }

    protected function buildDumpCommand(
        string $mysqldump,
        string $host,
        string $port,
        string $username,
        ?string $password,
        string $database,
        string $filepath,
    ): string {
        $parts = [
            $mysqldump,
            '-h', escapeshellarg($host),
            '-P', escapeshellarg($port),
            '-u', escapeshellarg($username),
        ];

        if ($password) {
            $parts[] = '-p'.escapeshellarg($password);
        }

        if ($this->option('only-data')) {
            $parts[] = '--no-create-info';
        } elseif ($this->option('only-structure')) {
            $parts[] = '--no-data';
        }

        $parts[] = '--single-transaction';
        $parts[] = '--routines';
        $parts[] = '--triggers';
        $parts[] = escapeshellarg($database);
        $parts[] = '>';
        $parts[] = escapeshellarg($filepath);

        return implode(' ', $parts);
    }

    protected function findMysqldump(): ?string
    {
        $paths = ['mysqldump'];

        // Windows 常见路径
        if (PHP_OS_FAMILY === 'Windows') {
            $paths[] = 'C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe';
            $paths[] = 'C:\xampp\mysql\bin\mysqldump.exe';
            $paths[] = 'C:\mysql\bin\mysqldump.exe';
        }

        foreach ($paths as $path) {
            if (PHP_OS_FAMILY === 'Windows') {
                if (file_exists($path)) {
                    return '"'.$path.'"';
                }
            } else {
                $which = trim(shell_exec('which mysqldump 2>/dev/null') ?: '');
                if ($which) {
                    return $which;
                }
            }
        }

        // 最后尝试直接用
        return 'mysqldump';
    }

    protected function executeCommand(string $command): int
    {
        exec($command.' 2>&1', $output, $returnCode);

        return $returnCode;
    }

    protected function compressFile(string $filepath): void
    {
        $data = file_get_contents($filepath);
        $gz = gzopen($filepath.'.gz', 'w9');
        gzwrite($gz, $data);
        gzclose($gz);
        unlink($filepath);
    }

    protected function cleanupOldBackups(string $dir, int $keep): void
    {
        $files = glob(rtrim($dir, '/').'/*.sql*') ?: [];
        if (count($files) <= $keep) {
            return;
        }

        usort($files, fn ($a, $b) => filemtime($b) - filemtime($a));
        $toDelete = array_slice($files, $keep);

        foreach ($toDelete as $file) {
            unlink($file);
            $this->line("  清理旧备份: <comment>".basename($file).'</comment>');
        }
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
