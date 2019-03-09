<?php
namespace svsoft\yii\backup\commands;

use svsoft\yii\backup\BackupModule;
use svsoft\yii\backup\exceptions\BackupDirectoryNotExistException;
use svsoft\yii\backup\exceptions\BackupModuleException;
use yii\console\Controller;
use yii\helpers\Console;
use yii\httpclient\Client;
use yii\httpclient\Response;

/**
 * Class RestoreController
 * @package svsoft\yii\backup\commands
 */
class DownloadController extends Controller
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
    public function actionIndex($url, $name, $token = null)
    {
        try
        {
            $backup = $this->module->backup;
        }
        catch(BackupDirectoryNotExistException $e)
        {
            if (!$this->confirm('Backup directory does not exist, create?'))
                return;

            return $this->actionIndex($url, $name, $token);
        }
        catch(BackupModuleException $e)
        {
            $this->stderr($e->getMessage() . PHP_EOL);
            return;
        }

        try
        {
            if ($token === null)
                $token = $this->module->accessToken;

            if ($backup->hasBackup($name))
            {
                if (!$this->confirm("File {$name} already exist, overwrite it?"))
                {
                    return;
                }
            }

            $url = "{$url}/backup/download";

            $client = new Client();
            /** @var Response $response */
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl($url)
                ->setData(['name' => $name, 'token' => $token])
                ->send();

            if (!$response->isOk)
            {

                if ($response->statusCode == 403)
                {
                    $this->stderr('wrong access token "'.$token . '"' . PHP_EOL);
                }
                elseif ($response->statusCode == 404)
                {
                    $this->stderr('backup not found "'.$name . '"' . PHP_EOL);
                }
                else
                {
                    $this->stderr('Error' . PHP_EOL);
                }
                return;
            }

            $filePath = $this->module->backupsFolder . '/' . $name;

            file_put_contents($filePath, $response->content);

            $this->stdout('Backup downloaded. ' . PHP_EOL, Console::FG_GREEN);

            return;
        }
        catch(BackupModuleException $e)
        {
            $this->stderr($e->getMessage() . PHP_EOL);
        }
    }
}
