<?php
namespace svsoft\yii\backup\services;

use svsoft\yii\backup\exceptions\BackupModuleException;
use Yii;
use yii\base\Component;
use yii\base\Exception;

/**
 * Создает бекап файлов в существующую директорию
 *
 * Class BackupFiles
 * @package svsoft\yii\backup\services
 */
class BackupFiles extends Component
{
    public $backupFolder;

    public $directories = [];

    public function __construct($backupFolder, $directories, $config = [])
    {
        $this->backupFolder = $backupFolder;
        $this->directories = $directories;

        parent::__construct($config);
    }

    /**
     * Create backups for $directories and save it to "<backups folder>"
     *
     * @return bool
     * @throws Exception
     */
    public function execute()
    {
        // Try to create new directory
        if (!is_dir($this->backupFolder) && !mkdir($this->backupFolder))
            throw new BackupModuleException('Can not create folder for backup: "' . $this->backupFolder . '"');

        foreach ($this->directories as $name => $value)
        {
            if (is_array($value))
            {
                $folder = Yii::getAlias($value['path']);
                $regex = isset($value['regex']) ? $value['regex'] : null;
            }
            else
            {
                $regex = null;
                $folder = Yii::getAlias($value);
            }

            if (!file_exists($folder))
                throw new BackupModuleException('Directory '. $folder . ' does not exist');

            $archiveFile = $this->backupFolder . DIRECTORY_SEPARATOR . $name . '.tar';

            // Create new archive
            $archive = new \PharData($archiveFile);

            // add folder
            $archive->buildFromDirectory($folder, $regex);
        }

        return true;
    }
}