<?php

namespace svsoft\yii\backup;

use svsoft\yii\backup\services\Backup;
use Yii;
use yii\base\Module;
use yii\helpers\FileHelper;

/**
 * Class BackupModule
 * @package svsoft\yii\backup
 * @property  Backup $backup
 */
class BackupModule extends Module
{
	public $controllerNamespace = __NAMESPACE__ . '\controllers';

    /** @var string Path/Alias to folder for backups storing. e.g. "@app/backups" */
    public $backupsFolder;

    /**
     * if string - value will be used in date() function.
     * if callable:
     * function() {
     *     return date('Y_m_d-H_i_s');
     * }
     *
     * @var string|callable
     */
    public $backupFilename = 'Y_m_d-H_i_s';

    /**
     * List of [filename => path] directories for backups.
     * e.g.:
     * [
     *     'images' => '@app/web/upload',
     *     'png.images' => [
     *         'path' => '@app/web/images',
     *         'regex' => '/\.png$/', // for backup only *.png files
     *     ],
     * ];
     * It will generate:
     * "/images.tar" - dump for "/frontend/web/images" directory
     * "/png.images.tar" - dump for "/frontend/web/images" directory only with *.png files
     *
     * @var array
     */
    public $directories = [];


    /**
     * List of databases connections config.
     * e.g.:
     * [
     *    'site' => [
     *        'db' => 'dblogs',
     *        'host' => 'localhost',
     *        'username' => 'root',
     *        'password' => 'BASdas7asdj8',
     *    ],
     * ];
     * It will generate "/site.sql.gz" with dump file "site.sql" of database "logs"
     *
     * If you set $db param, then $databases automatically will be extended with params from Yii::$app->$db
     *
     * @var array
     */
    public $databases = [];

    /**
     * Id of Database component. By default Yii::$app->db.
     * If you do not want backup project database you can set this param as NULL/FALSE
     *
     * @var string
     */
    public $dbComponentId = 'db';

    /**
     * Log target configuration
     *
     * If you set null logs will not be wrote
     *
     * default:
     * [
     *    'class' => 'yii\log\FileTarget',
     *    'categories' => ['backup'],
     *    'logFile' => '@runtime/logs/backup.log',
     *    'logVars' => []
     * ]
     *
     * @var array
     */
    public $logTargetConfig = [];

    /**
     * if set true file .gitignore will be created in backup directory
     *
     * @var bool
     */
    public $createGitIgnore = true;

    public $accessToken = null;

	public function init() {

	    parent::init ();

		if (Yii::$app instanceof \yii\console\Application)
			$this->controllerNamespace = __NAMESPACE__ . '\commands';


        $this->backupsFolder = Yii::getAlias($this->backupsFolder);

        if (empty($this->components['backup']))
            $this->set('backup', [
                'class' => Backup::class,
                'backupsFolder'     => $this->backupsFolder,
                'backupFilename'    => $this->backupFilename,
                'directories'       => $this->directories,
                'databases'         => $this->databases,
                'dbComponentId'     => $this->dbComponentId
            ]);

        // set log target for task backup
        if ($this->logTargetConfig !== null)
        {
            if (!$this->logTargetConfig)
            {
                $this->logTargetConfig = [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['backup'],
                    'logFile' => '@runtime/logs/backup.log',
                    'logVars' => []
                ];
            }

            Yii::$app->log->targets[] = Yii::createObject($this->logTargetConfig);
        }
    }

    /**
     * Create backup directory with .gitignore
     */
    public function createBackupFolder()
    {
        FileHelper::createDirectory($this->backupsFolder);
        if ($this->createGitIgnore)
            file_put_contents($this->backupsFolder . '/.gitignore', '*' . PHP_EOL . '!.gitignore');
    }

    /**
     * Get component backup
     *
     * @return null|object
     */
    public function getBackup()
    {
        return $this->get('backup');
    }


}
