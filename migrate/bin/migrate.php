#!/usr/bin/env php
<?php

use App\Kernel;
use Monolog\Logger;
use App\Service\DbConnect;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Dotenv\Dotenv;
use App\Command\BeforeMigrationCommand;
use Symfony\Component\Console\Application;

require_once dirname(__DIR__).'/vendor/autoload.php';

const FAILURE = 1;

// Récupère le chemin vers les variables d'environnement, expurger du schéma phar://
$envPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env.migration';
$path = preg_replace('/^[^:]+:\/\/(.*)$/', '$1', $envPath);

$dotenv = new Dotenv();
$dotenv->load($path);

// Récupère le conteneur de services
$kernel = new Kernel($_ENV['APP_ENV'], (bool) $_ENV['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();

/**
 * Récupère le service dbConnect
 * @var DbConnect $dbConnect
 */
$dbConnect = $container->get(DbConnect::class);

$connSPI = $dbConnect->getConnectionSPI();

try {
    $infosCaisse = $connSPI->executeQuery('SELECT * FROM cac WHERE id = ?', [1])->fetchAssociative();
    if (!$infosCaisse) {
        echo "Impossible de récupérer les informations de la caisse" . PHP_EOL;

        return FAILURE;
    }
} catch (\Doctrine\DBAL\Exception $e) {
    echo "Problème lors de la récupération des données";

    return FAILURE;
}

// Déclaration des loggers
$suiviScriptsLogger = configureLogger('suivi_scripts');
$beforeMigrationLogger = configureLogger('before_migration');

$application = new Application();
$application->add(new BeforeMigrationCommand($dbConnect, $suiviScriptsLogger, $beforeMigrationLogger));

try {
    $application->run();
} catch (\Exception $e) {
    echo $e->getMessage();

    return FAILURE;
}

/**
 * @param string $name
 * @return Logger
 */
function configureLogger(string $name): Logger
{
    return (new Logger('scripts'))->pushHandler(new StreamHandler(
                                      sprintf("logs/{$name}_%s.log", date('d-m-Y')),
                                      Logger::INFO));
}
