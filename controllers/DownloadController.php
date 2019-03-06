<?php
namespace svsoft\yii\backup\controllers;

use svsoft\yii\backup\BackupModule;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class CreateController
 * @package svsoft\yii\backup\commands
 * @property BackupModule $module
 */
class DownloadController extends Controller
{
    public function actionIndex($name, $token)
    {
        if (!$this->module->accessToken)
            throw new ForbiddenHttpException('Access token is not set');

        if ($this->module->accessToken !== $token)
            throw new ForbiddenHttpException('Access token is wrong');

        if (!$this->module->backup->hasBackup($name))
            throw new NotFoundHttpException('File not found');

        $filePath = $this->module->backupsFolder . '/' . $name;

        return \Yii::$app->response->sendFile($filePath);
    }
}
