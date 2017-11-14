<?php


namespace AppBundle\Install;

use Composer\Script\Event;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler;

class AppBundleInstaller extends ScriptHandler
{
    public static function createSchema(Event $event)
    {
        $databaseFilePath = __DIR__ . '/../../../var/data/data.sqlite';
        $consoleDir = static::getConsoleDir($event, 'create database schema');

        // Don't create the file if it already exists
        if (file_exists($databaseFilePath)) return;

        static::executeCommand($event, $consoleDir, 'doctrine:schema:create', 60);
    }
}