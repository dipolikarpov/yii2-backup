<?php
namespace svsoft\yii\backup\services;

use svsoft\yii\backup\exceptions\BackupModuleException;
use yii\base\Exception;
use yii\helpers\FileHelper;

/**
 * Распаковывает архив с бекапом
 *
 * Class ExtractBackup
 * @package svsoft\yii\backup\services
 */
class ExtractBackup
{
    private $backupFilePath;
    private $restoreDir;

    public function __construct($backupFilePath, $restoreDir)
    {
        $this->backupFilePath   = $backupFilePath;
        $this->restoreDir       = $restoreDir;
    }

    /**
     * Create backups for $directories and save it to "<backups folder>"
     *
     * @return bool
     * @throws Exception
     */
    public function execute()
    {
        $filePath = $this->backupFilePath;

        if (!file_exists($filePath))
            throw new BackupModuleException('Backup file ' . $filePath . ' does not exist');

        $this->createRestoreDir();

        // extract archive file
        $phar = new \PharData($filePath);
        if (!$phar->extractTo($this->restoreDir))
            throw new BackupModuleException('Error of extracting backup');
    }

    private function createRestoreDir()
    {
        if (file_exists($this->restoreDir))
            FileHelper::removeDirectory($this->restoreDir);

        FileHelper::createDirectory($this->restoreDir);
    }
}