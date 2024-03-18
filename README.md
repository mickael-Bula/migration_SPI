# PROJET DE MIGRATION

L'organisation du projet est un répertoire contenant un projet Symfony.
Ce projet devant créer une archive Phar à partir des commandes du projet Symfony,
je définis un wrapper pour lancer la génération de l'archive depuis le parent de l'application Symfony.

Je crée le répertoire du projet et je l'initialise comme référentiel git.
Puis, j'y crée un projet Symfony, qui sera lui-même et automatiquement initialisé comme répertoire git.
Pour faire un suivi unique dun projet, il faut donc supprimer le répertoire `.git` généré par Symfony.

Le suivi de version ayant été supprimé du répertoire Symfony, ses fichiers peuvent être ajoutés au `.git` parent.

