Yii2-backup
===================
Модуль создания и восстановления резервных копий. Работает в консольном режиме <br />


Установка
---

Добавить в composer.json
```json
{
	"require": {
  		"svsoft/backup": "*"
	}
}
```
Или
```bash
    composer require svsoft/backup
```

# Конфигурирование

### Базовая конфигурация

Добавить common/config/main.php или другой файл конвигурации:
```php
    'modules'=>[
        'backup' => [
            'class'=>'svsoft\yii\backup\BackupModule',
            'backupsFolder' => '@common/backups', // Directory for backups
            // Directories that will be added to backup
            'directories' => [
                'uploads' => '@frontend/upload/files',
            ],
        ],
    ],
```

# Создание бекапа
Выполните команду в консоле
```bash
    ./yii backup/create
```

После выполнения будет создан файл в папке для хранения бекапов, которая указан в конфиге (параметр backupsFolder)

Если папка для бекапов отсутствует, она будет создана автоматически с файлом .gitignore
Бекап состоит из архива БД и архива файлов и папок указанных в конфиге (параметр directories)

# Восстановление бекапа
Выполните команду в консоле
```bash
./yii backup/restore <названия файла бекапа>
```
Будет предложено создать бекап перед восстановлением.
После чего будет восстановлена БД, и файлы у казанные в конфиге

Список бекапов можно посмотреть выполнив комманду
```bash
./yii backup/list <названия файла бекапа>
```

