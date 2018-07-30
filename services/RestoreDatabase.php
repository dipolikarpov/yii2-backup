<?php
namespace svsoft\yii\backup\services;

use svsoft\yii\backup\exceptions\BackupModuleException;
use svsoft\yii\backup\helpers\BackupHelper;
use yii\base\Exception;

/**
 * Class BackupDatabase
 * @package svsoft\yii\backup\services
 */
class RestoreDatabase
{
    public $backupFolder;

    public $dbConfig = [];

    public $archiveName;

    CONST COMMAND_MYSQL_RESTORE = 'mysql -u "{username}" -h "{host}" -p\'{password}\' {db} 2> /dev/null';

    /**
     * RestoreDatabase constructor.
     *
     * @param $archiveFilePath
     * @param $dbConfig
     */
    public function __construct($archiveFilePath, $dbConfig)
    {
        $this->archiveFilePath = $archiveFilePath;
        $this->dbConfig     = $dbConfig;
    }

    /**
     * Restore database backup and save it to "<backups folder>/sql"
     *
     * @return bool
     * @throws Exception
     */
    public function execute()
    {
        if (!is_file($this->archiveFilePath))
            throw new BackupModuleException('File "' . $this->archiveFilePath . '" does not exist');

        $params = $this->dbConfig;

        $command = self::COMMAND_MYSQL_RESTORE;

        if ((string)$params['password'] === '')
        {
            // Remove password option
            $command = str_replace('-p\'{password}\'', '', $command);
            unset($params['password']);
        }

        foreach ($params as $k => $v)
        {
            $command = str_replace('{' . $k . '}', $v, $command);
        }

        BackupHelper::ExecuteCommand('gunzip -c ' . $this->archiveFilePath . ' | ' . $command);
    }
}