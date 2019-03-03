<?php
namespace svsoft\yii\backup\controllers;

use svsoft\yii\backup\BackupModule;
use yii\web\Controller;
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
            throw new NotFoundHttpException('Access token is not set');

        if ($this->module->accessToken !== $token)
            throw new NotFoundHttpException('Page not found');

        $filePath = $this->module->backupsFolder . '/' . $name;

        if (!file_exists($filePath))
            throw new NotFoundHttpException('Page not found');

        return \Yii::$app->response->sendFile($filePath);
    }
}
