<?php
namespace svsoft\yii\backup\services;

use svsoft\yii\backup\exceptions\BackupModuleException;
use yii\helpers\FileHelper;

/**
 * Class ArchivetBackup
 * @package svsoft\yii\backup\services
 */
class ArchiveBackup
{
    private $backupFolder;

    public function __construct($backupFolder)
    {
        $this->backupFolder   = $backupFolder;
    }

    /**
     * @return string
     * @throws BackupModuleException
     */
    public function execute()
    {
        if (!file_exists($this->backupFolder))
            throw new BackupModuleException('Backup directory ' . $this->backupFolder . ' does not exist');

        $resultFilename = pathinfo($this->backupFolder, PATHINFO_FILENAME) . '.tar';

        $archiveFile = dirname($this->backupFolder) . DIRECTORY_SEPARATOR . $resultFilename;

        $archive = new \PharData($archiveFile);
        $archive->buildFromDirectory($this->backupFolder);

        FileHelper::removeDirectory($this->backupFolder);

        return $resultFilename;
    }
}