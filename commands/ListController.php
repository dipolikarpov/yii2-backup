<?php
namespace svsoft\yii\backup\commands;

use svsoft\yii\backup\BackupModule;
use svsoft\yii\backup\exceptions\BackupModuleException;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\Console;

/**
 * Class ListController
 *
 * @package svsoft\yii\backup\commands
 */
class ListController extends Controller
{
    /**
     * @var BackupModule
     */
    public $module;

    /**
     * shpw backup list
     *
     * @throws Exception
     */
    public function actionIndex()
    {
        try
        {
            $backup = $this->module->backup;
            $backup->getBackupsList();

            $this->stdout( PHP_EOL . 'Backup list: ' . PHP_EOL, \yii\helpers\Console::FG_YELLOW);
            foreach($backup->getBackupsList() as $file)
            {
                $this->stdout('   ' . basename($file)  . PHP_EOL, Console::FG_GREEN);
            }
            $this->stdout( PHP_EOL );
        }

        catch(BackupModuleException $e)
        {
            $this->stderr($e->getMessage() . PHP_EOL);
        }
    }
}
