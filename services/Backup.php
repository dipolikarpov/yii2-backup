<?php
namespace svsoft\yii\backup\services;

use svsoft\yii\backup\exceptions\BackupDirectoryNotExistException;
use svsoft\yii\backup\exceptions\BackupModuleException;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\FileHelper;

class Backup extends Component
{
    public $backupsFolder;

    public $backupFilename;

    public $directories = [];

    public $databases = [];

    public $dbComponentId;

    /**
     * @throws BackupModuleException
     */
    public function init()
    {
        parent::init();

        if (!$this->backupsFolder )
            throw new BackupModuleException('Config param backupsFolder is not set');
        
        if (!is_dir($this->backupsFolder))
            throw new BackupDirectoryNotExistException('Directory for backups "' . $this->backupsFolder . '" does not exists');

        if (!is_writable($this->backupsFolder))
            throw new BackupModuleException('Directory for backups "' . $this->backupsFolder . '" is not writable');

        if (!$this->backupFilename)
            throw new BackupModuleException('Config param backupFilename is not set');

        if ($this->directories)
        {
            // ПРоверку пока убрал, т.к. в слуае с advanced app бекап не делается
//            foreach($this->directories as $directory)
//            {
//                // проверяем чтоб директории для бекапа были внутри @app
//                if (is_array($directory))
//                    $path = ArrayHelper::getValue($directory, 'path');
//                else
//                    $path = $directory;
//
//                $path = Yii::getAlias($path);
//
//                if (strpos($path, Yii::getAlias('@app')) !== 0)
//                    throw new BackupModuleException('Directory "' . $path . ' ' . $this->backupsFolder . '" must be subdirectory application directory');
//            }
        }
    }

    /**
     * Create dump of all directories and all databases and save result to backup folder with timestamp named tar-archive
     *
     * @return string filename of created backup file
     * @throws Exception
     */
    public function create()
    {
        $backupFolder = $this->getBackupFolder();

        // create backup files
        $backupFiles = new BackupFiles($backupFolder, $this->directories);
        $backupFiles->execute();

        if ($this->dbComponentId)
        {
            // create backup databases
            foreach($this->getDatabaseConfigs() as $archiveName=>$database)
            {
                $databaseBackup = new BackupDatabase($backupFolder, $database, $archiveName);
                $databaseBackup->execute();
            }
        }

        // archive backup
        $archiveBackup = new ArchiveBackup($backupFolder);
        return $archiveBackup->execute();
    }

    public function restore($backupFilename)
    {
        $backupFilePath = $this->backupsFolder . DIRECTORY_SEPARATOR . $backupFilename;
        $restoreFolder = $this->getRestoreFolder($backupFilename);

        // Extract backup file
        $extractBackup = new ExtractBackup($backupFilePath, $restoreFolder);
        $extractBackup->execute();

        // Restore files
        $filesRestore = new RestoreFiles($restoreFolder, $this->directories);
        $filesRestore->execute();

        if ($this->dbComponentId)
        {
            // Restore databases
            foreach($this->getDatabaseConfigs() as $archiveName=>$database)
            {
                $archiveFilePath = $restoreFolder . DIRECTORY_SEPARATOR . $archiveName . '.sql.gz';
                $restoreDatabase = new RestoreDatabase($archiveFilePath, $database);
                $restoreDatabase->execute();
            }
        }

        FileHelper::removeDirectory($restoreFolder);
    }

    /**
     * Get backup file list
     *
     * @return array
     */
    public function getBackupsList()
    {
        $files = FileHelper::findFiles($this->backupsFolder, ['only' => ['*.tar'], 'recursive' => false]);

        return $files;
    }


    /**
     * Generate backup name
     *
     * @return string
     */
    private function generateBackupName()
    {
        if (is_callable($this->backupFilename))
        {
            return call_user_func($this->backupFilename);
        } else {
            return date($this->backupFilename);
        }
    }

    /**
     * Get full path to backup folder.
     * Directory will be automatically created.
     *
     * @return string
     * @throws Exception
     */
    private function getBackupFolder()
    {
        $current = $this->generateBackupName();

        $filePath = $this->backupsFolder . DIRECTORY_SEPARATOR . $current;

        return $filePath;
    }


    /**
     * Get database configurations.
     *
     * automatically will be extended with params from Yii::$app->$db
     *
     * @return array
     */
    private function getDatabaseConfigs()
    {
        if (!$this->databases)
        {
            /** @var \yii\db\Connection $db */
            $db = Yii::$app->get($this->dbComponentId);
            $dbName = $db->createCommand('select database()')->queryScalar();
            $this->databases['db'] = [
                'db' => $dbName,
                'host' => 'localhost',
                'username' => $db->username,
                'password' => addcslashes($db->password, '\''),
            ];
        }

        return $this->databases;
    }

    /**
     * Get file path to unarchived directory
     *
     * @param $backupFilename
     *
     * @return string
     */
    private function getRestoreFolder($backupFilename)
    {
        return $this->backupsFolder . DIRECTORY_SEPARATOR . pathinfo($backupFilename, PATHINFO_FILENAME);
    }
}
