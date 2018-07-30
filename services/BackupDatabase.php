<?php
namespace svsoft\yii\backup\services;

use svsoft\yii\backup\exceptions\BackupModuleException;
use svsoft\yii\backup\helpers\BackupHelper;
use yii\base\Component;
use yii\base\Exception;

/**
 * Class BackupDatabase
 * @package svsoft\yii\backup\services
 */
class BackupDatabase extends Component
{
    public $backupFolder;

    public $dbConfig = [];

    public $archiveName;

    CONST COMMAND_MYSQL_DUMP = 'mysqldump --add-drop-table --allow-keywords -q -c -u "{username}" -h "{host}" -p\'{password}\' {db} 2> /dev/null | gzip -9';

    public function __construct($backupFolder, $dbConfig, $archiveName = '', $config = [])
    {
        $this->backupFolder = $backupFolder;
        $this->dbConfig     = $dbConfig;
        $this->archiveName  = $archiveName;

        parent::__construct($config);
    }

    /**
     * Create backups for $databases and save it to "<backups folder>"
     *
     * @return bool
     * @throws Exception
     */
    public function execute()
    {
        // Try to create new directory
        if (!is_dir($this->backupFolder) && !mkdir($this->backupFolder))
            throw new BackupModuleException('Can not create folder for backup: "' . $this->backupFolder . '"');

        $params = $this->dbConfig;

        $command = self::COMMAND_MYSQL_DUMP;

        if ((string)$params['password'] === '')
        {
            // Remove password option
            $command = str_replace('-p\'{password}\'', '', $command);
            unset($params['password']);
        }

        foreach ($params as $k => $v)
            $command = str_replace('{' . $k . '}', $v, $command);

        $archiveName = $this->archiveName ?: $params['db'];

        $file = $this->backupFolder . DIRECTORY_SEPARATOR . $archiveName . '.sql.gz';

        BackupHelper::ExecuteCommand($command . ' > ' . $file);
    }
}