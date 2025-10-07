# 🚗 EcoRide - Plateforme de Covoiturage

EcoRide est une plateforme moderne de covoiturage développée avec Symfony 7.3.x, PHP 8.2, MySQL 8.0+, Bootstrap 5 et Doctrine ORM. Elle permet aux utilisateurs de proposer, rechercher et réserver des trajets partagés, avec un accent sur la sécurité, l’expérience utilisateur et la gestion transparente des réservations.

## 📋 Table des matières

- Fonctionnalités principales
- Stack technique
- Installation & configuration
- Utilisation
- Déploiement
- Structure du projet
- Sécurité
- Support

## ✨ Fonctionnalités principales

- Authentification sécurisée (inscription, connexion, réinitialisation)
- Gestion des profils et avatars
- Ajout et gestion de véhicules
- Création et recherche de trajets
- Réservation avec workflow complet
- Système de crédits automatisé
- Notifications email
- Dashboard utilisateur (conducteur, passager, employé, admin)
- Interface responsive (desktop/mobile)

## 🛠️ Stack technique

- **Backend** : PHP 8.2, Symfony 7.3.x, Doctrine ORM, Twig
- **Frontend** : Bootstrap 5, JavaScript ES6+, Webpack Encore
- **Base de données** : MySQL 8.0+, Doctrine Migrations
- **Déploiement** : GitHub Actions, VPS Linux (IONOS), LAMP

## 🚀 Installation & configuration

Voir la documentation technique (`DOCUMENTATION_TECHNIQUE.md`) pour l’installation complète.

Principales étapes :

1. Cloner le repo : `git clone https://github.com/kongphengta/EcoRide.git`
2. Installer les dépendances PHP : `composer install`
3. Installer les dépendances JS : `npm install`
4. Configurer `.env.local` (base de données, mailer, secret)
5. Créer la base : `php bin/console doctrine:database:create`
6. Appliquer les migrations : `php bin/console doctrine:migrations:migrate`
7. Charger les fixtures (optionnel) : `php bin/console doctrine:fixtures:load`
8. Compiler les assets : `npm run build`
9. Lancer le serveur : `symfony server:start`

## 🎯 Utilisation

Consultez le [manuel utilisateur](https://github.com/kongphengta/EcoRide/blob/master/Manuel%20d'utilisation.pdf) pour le workflow complet.

Comptes de test :

- **Admin** : admin@ecoride.fr / AdminECF2025!
- **Conducteur** : conducteur@test.fr / TestECF2025!
- **Passager** : passager@test.fr / TestECF2025!
- **Employé** : employe@ecoride.fr / EmployeECF2025!

## Guide d’utilisation

Consultez le [Manuel d'utilisation](https://github.com/kongphengta/EcoRide/blob/master/Manuel%20d'utilisation.pdf) pour découvrir comment utiliser EcoRide.

## 🚀 Déploiement

Déploiement automatique via GitHub Actions sur push vers `master`.
Serveur de production : IONOS, domaine `ecoride.konvix.fr`.

## 🏗️ Structure du projet

Voir la documentation technique pour l’architecture complète.

Principaux dossiers :

- `src/Controller/` : Contrôleurs
- `src/Entity/` : Entités Doctrine
- `src/Repository/` : Requêtes DB
- `src/Security/` : Sécurité
- `templates/` : Templates Twig
- `assets/` : Frontend (styles, images, JS)

## 🔒 Sécurité

- Authentification et autorisation par rôles (ROLE_USER, ROLE_ADMIN, ROLE_CHAUFFEUR, ROLE_EMPLOYE)
- Validation côté serveur et client
- Protection CSRF sur tous les formulaires
- Hashage des mots de passe (bcrypt)
- Sécurisation des uploads
- Doctrine ORM pour éviter les injections SQL

## 📞 Support

- Documentation complète dans le dossier du projet
- Issues GitHub pour signaler les bugs ou demander des améliorations
- Contact développeur : kongphengta@example.com

---

_Développé dans le cadre d’un projet académique. EcoRide est une solution complète et professionnelle de covoiturage._
