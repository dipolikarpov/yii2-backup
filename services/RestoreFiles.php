<?php
namespace svsoft\yii\backup\services;

use svsoft\yii\backup\exceptions\BackupModuleException;
use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;

/**
 * Воостанавливает файлы директории с разархивированным файлом бекапа
 *
 * Class RestoreFiles
 * @package svsoft\yii\backup\services
 */
class RestoreFiles
{
    public $restoreFolder;

    public $directories = [];

    public function __construct($restoreFolder, $directories)
    {
        $this->restoreFolder = $restoreFolder;
        $this->directories   = $directories;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function execute()
    {
        if (!is_dir($this->restoreFolder))
            throw new BackupModuleException($this->restoreFolder . ' is not dir');

        foreach ($this->directories as $name => $value)
        {
            if (is_array($value))
                $folder = Yii::getAlias($value['path']);
            else
                $folder = Yii::getAlias($value);

            $restoreDirPathFolder = $this->restoreFolder . '/' . $name;
            $restoreDirPathItem = $this->restoreFolder . '/' . $name . '.tar';

            if (!file_exists($restoreDirPathItem))
                continue;

            $phar = new \PharData($restoreDirPathItem);

            $phar->extractTo($restoreDirPathFolder, null, true);

            FileHelper::copyDirectory($restoreDirPathFolder, $folder);
        }

        return true;
    }
}