<?php
namespace svsoft\yii\backup\commands;

use svsoft\yii\backup\BackupModule;
use svsoft\yii\backup\exceptions\BackupDirectoryNotExistException;
use svsoft\yii\backup\exceptions\BackupModuleException;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Class CreateController
 * @package svsoft\yii\backup\commands
 */
class CreateController extends Controller
{
    /**
     * @var BackupModule
     */
    public $module;

    /**
     * Create backup
     * @param bool $confirm
     *
     * @return int
     */
    public function actionIndex($confirm = true)
    {
        if ($confirm && !$this->confirm('Do you want create backup?'))
            return ExitCode::OK;

        try
        {
            $backup = $this->module->backup;
            $this->stdout('Backup creating...' . PHP_EOL, Console::FG_GREEN);
            $backupFilename = $backup->create();
            $this->stdout('Backup file created: ' . $backupFilename . PHP_EOL, Console::FG_GREEN);
        }
        catch(BackupDirectoryNotExistException $e)
        {
            if ($this->confirm('Backup directory does not exist, create?'))
            {
                $this->module->createBackupFolder();
                return $this->actionIndex();
            }
        }
        catch(BackupModuleException $e)
        {
            $this->stderr($e->getMessage() . PHP_EOL);
        }

        return self::EXIT_CODE_NORMAL;
    }

    /**
     * Создать бекап через планировщик
     */
    public function actionTask()
    {
        try
        {
            $backup = $this->module->backup;
            $backupFilename = $backup->create();
            \Yii::info('Backup file created: ' . $backupFilename, 'backup');
        }
        catch(BackupModuleException $e)
        {
            \Yii::error($e->getMessage(), 'backup');
        }
    }
}
