# SecurEvent

Plateforme de gestion et réservation d'événements de cybersécurité (CTF, conférences, workshops).

## Stack technique
- Symfony 5.16.1 (CLI)
- PHP 8.4.14 (CLI)
- MySQL 8
- Twig + Bootstrap 

## Installation

1. Cloner le projet
   git clone https://github.com/mxou-954/SecurEvent.git
   cd SecurEvent

2. Installer les dépendances
   composer install

3. Configurer la base de données
   Créer le .env.local et modifier la ligne :
   DATABASE_URL="mysql://root:password@127.0.0.1:3306/securevent" par votre propre URL de connexion mysql

4. Créer la base et lancer les migrations
   symfony console doctrine:database:create
   symfony console doctrine:migrations:migrate

5. Charger les données de test
   symfony console doctrine:fixtures:load

6. Lancer le serveur
   symfony serve

## Comptes de test

| Rôle       | Email                  | Mot de passe |
|------------|------------------------|--------------|
| Admin      | admin@securevent.com   | Admin1234!   |
| Utilisateur | alice@test.com        | Password1!   |

## PostMortem

### Difficultés rencontrées

- La gestion de la vérification email a été abandonnée pour simplifier le flux d'inscription.
- L'extension PHP `intl` absente sur Windows a causé des erreurs avec `NoSuspiciousCharacters`. 
  Pour les besoins de l'évaluation, nous avons supprimé cette couche de sécurité. 
  Il fallait aller modifier le `php.ini` pour décommenter une ligne, ce qui n'était pas intuitif.
- La configuration d'Argon2id nécessitait l'activation de l'extension `sodium`.
- Le temps a également joué contre nous, ne nous permettant pas d'implémenter les fonctionnalités optionnelles.

### Réussites

- Sécurité globale solide : CSRF, throttling, Argon2id, 403 admin
- API REST fonctionnelle avec groupes de sérialisation
- Architecture MVC propre avec thin controllers

### Ce qui aurait pu être amélioré

- Ajouter la réinitialisation de mot de passe
- Ajouter des catégories sur les événements
- Internationalisation FR/EN
- Connexion OAuth (Google/GitHub)

### Corrélation avec d'autres langages et frameworks

Symfony ressemble dans son fonctionnement à la combinaison NestJS + NextJS. Au début je 
pensais être un peu perdu mais dès que j'ai fait la corrélation, je me suis tout de suite 
senti à l'aise.

**Points positifs :** il est plus simple de coder avec Symfony car le frontend et le backend 
cohabitent dans le même dossier, ce qui est plus intuitif. Le déploiement est également 
simplifié. La barre Symfony Profiler en bas de page est très utile pour analyser les 
performances sans avoir à passer par le Network de Chrome DevTools. Le développement 
des formulaires était vraiment agréable grâce à une documentation complète et bien pensée.

**Points négatifs :** Symfony est assez lent, la génération de controllers, de formulaires 
ou le simple rechargement de pages prend un temps colossal. J'aime avoir le contrôle 
sur ce que je développe, or certaines fonctionnalités très pratiques de Symfony sont déjà 
implémentées "automagiquement", ce qui peut nuire à la compréhension en profondeur.

### Commentaires supplémentaires

Les fichiers `DataFixtures/AppFixtures.php` et certains composants Twig ont été générés 
avec l'aide d'un outil IA par manque de temps. J'ai néanmoins pris le soin de lire et 
comprendre leur fonctionnement. Je compte y revenir pour les reproduire from scratch.