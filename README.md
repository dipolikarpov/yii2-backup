Yii2-backup
===================
Basic Yii2 module backup <br />

Installation
---

Add to composer.json in your project
```json
{
	"require": {
  		"svsoft/backup": "dev-master"
	}
}
```

# Configurations

### Minimal config

Add to common/config/main.php or other file:
```php
    'modules'=>[
        'backup' => [
            'class'=>'svsoft\yii\backup\BackupModule',
            'backupsFolder' => dirname(__DIR__).'/backups', // <project-root>/backups
            'createFolders' => true,
            // Directories that will be added to backup
            'directories' => [
                'uploads' => dirname(__DIR__).'/web/upload',
            ],
        ],
    ],
```