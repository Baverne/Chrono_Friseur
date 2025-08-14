# Chrono_Friseur

Ce projet ludique vise à créer une interface permettant la visualisation d'évènements historiques sur une frise chronologique.

## Développement

### Prérequis

Ce programme nécessite un environnement de développement incluant PHP (≥8.2), [composer](https://getcomposer.org) et NPM (node.js).
Pour une installation simplifiée de ces dépendances sur Windows ou MacOS, il est recommandé d'utiliser [Laravel Herd](https://herd.laravel.com/windows).

### Premier lancement

Une fois ce dépôt **cloné**, ouvrir un terminal et se déplacer dans son dossier puis exécuter les commandes
suivantes pour :

1. Installer les dépendances PHP
2. Installer les dépendances JS 
3. Copier le fichier définissant les variables d'environnement, si nécessaire
4. Créer la clé de cryptographie pour l'environnement local
5. Créer et initialiser la base de données locale
6. Lancer la compilation du CSS et du JS

```shell
composer install
npm install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate
php artisan migrate
npm run dev
```

> La dernière commande doit rester exécutée pour prendre en compte les changements dans le code front-end.

Enfin, dans un nouveau terminal, lancer le serveur de développement.

```shell
php artisan serve
```

Le message `INFO  Server running on [http://127.0.0.1:8000].` doit apparaître.  
Le site de développement local est accessible sur l'URL [http://127.0.0.1:8000](http://127.0.0.1:8000).

### Lancements suivants

Si le premier lancement a déjà été effectué et que le dépôt vient d'être **mis à jour**, ces commandes sont parfois
nécessaires :

1. Charger les changements de dépendances PHP
2. Charger les changements de dépendances JS
3. Déployer les changements dans la base de données locale
4. Lancer la compilation du CSS et du JS

```shell
composer install
npm install
php artisan migrate
npm run dev
```

> La dernière commande doit rester exécutée pour prendre en compte les changements dans le code front-end.

Vérifier si des changements ont été effectués dans le fichier `.env.example` et les intégrer à `.env`.

Enfin, dans un nouveau terminal, lancer le serveur de développement.

```shell
php artisan serve
```

Le message `INFO  Server running on [http://127.0.0.1:8000].` doit apparaître.  
Le site de développement local est accessible sur l'URL [http://127.0.0.1:8000](http://127.0.0.1:8000).

### Arrêter le développement

Une fois les modifications enregistrées, on peut arrêter l'exécution du serveur PHP et de la compilation front-end avec Ctrl+C.
