<?php

namespace svsoft\yii\backup\helpers;

use svsoft\yii\backup\exceptions\BackupModuleException;

/**
 * Class BackupHelper
 * @package svsoft\yii\backup\helpers
 */
class BackupHelper
{
    /**
     * Execute
     *
     * @param string $command - system command
     *
     * @return int
     * @throws BackupModuleException
     */
    static function ExecuteCommand($command)
    {
        $status = 0;
        if (@system($command, $status) === false)
            throw new BackupModuleException('Error executing command "' . $command . '"');

        if($status)
            throw new BackupModuleException('Command "' . $command . '" returned status='.$status);

        return $status;
    }
}

