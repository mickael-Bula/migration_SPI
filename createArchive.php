<?php

try
{
    $pharFile = 'production/migrate_spi.phar';

    // clean up
    if (file_exists($pharFile))
    {
        unlink($pharFile);
    }

    if (file_exists($pharFile . '.gz'))
    {
        unlink($pharFile . '.gz');
    }

    $phar = new Phar($pharFile);

    $phar->startBuffering();

    // Construit l'archive
    $phar->buildFromDirectory(__DIR__ . DIRECTORY_SEPARATOR . 'migrate');

    // Crée le point d'entrée de l'archive
    $phar->setDefaultStub('bin/migrate.php');

    $phar->stopBuffering();

    // plus - compressing it into gzip
    $phar->compressFiles(Phar::GZ);

    # Rend l'archive exécutable
    chmod(__DIR__ . DIRECTORY_SEPARATOR . $pharFile, 0770);

    echo "Archive $pharFile créée avec succès" . PHP_EOL;
}
catch (Exception $e)
{
    echo $e->getMessage();
}


