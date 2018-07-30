<?php
namespace svsoft\yii\backup\commands;

use svsoft\yii\backup\BackupModule;
use svsoft\yii\backup\exceptions\BackupModuleException;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class RestoreController
 * @package svsoft\yii\backup\commands
 */
class RestoreController extends Controller
{
    /**
     * @var BackupModule
     */
    public $module;

    /**
     * restore backup
     *
     * @param $filename
     */
    public function actionIndex($filename)
    {
        if (!$this->confirm('Do you want restore backup '. $filename))
            return;

        try
        {
            $backup = $this->module->backup;

            if ($this->confirm('Create backup before restore?', true))
            {
                $this->stdout('Backup creating...' . PHP_EOL, Console::FG_GREEN);
                $newFilename = $backup->create();
                $this->stdout('Backup file created: ' . $newFilename . PHP_EOL, Console::FG_GREEN);
            }

            $this->stdout('Backup restoring...' . PHP_EOL, Console::FG_GREEN);
            $backup->restore($filename);

            $this->stdout('Backup was restored: ' . $filename . PHP_EOL, Console::FG_GREEN);
        }
        catch(BackupModuleException $e)
        {
            $this->stderr($e->getMessage() . PHP_EOL);
        }
    }
}
