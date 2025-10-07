# 🔧 Documentation Technique - EcoRide

## 📋 Vue d'Ensemble

### Informations Générales

- **Nom du Projet** : EcoRide - Plateforme de Covoiturage
- **Framework** : Symfony 7.2.8
- **Langage** : PHP 8.2.12
- **Base de Données** : MySQL 8.0+
- **Frontend** : Bootstrap 5.3.5 + Stimulus + Webpack Encore

---

## 🏗️ Architecture du Projet

### Pattern MVC (Model-View-Controller)

```
├── Model (Entities)     → Logique métier et données
├── View (Templates)     → Interface utilisateur
└── Controller          → Logique de contrôle
```

### Structure des Répertoires

```
src/
├── Controller/         # Contrôleurs web
│   ├── CovoiturageController.php
│   ├── ReservationController.php
│   ├── SecurityController.php
│   ├── ProfileController.php
│   └── AdminController.php
├── Entity/            # Entités Doctrine (Modèles)
│   ├── User.php
│   ├── Covoiturage.php
│   ├── Reservation.php
│   ├── Voiture.php
│   └── Avis.php
├── Repository/        # Repositories (Requêtes BDD)
├── Service/          # Services métier
│   └── EmailService.php
├── Form/             # Formulaires Symfony
├── Security/         # Configuration sécurité
└── Command/          # Commandes CLI

templates/            # Templates Twig
├── base.html.twig    # Layout principal
├── covoiturage/      # Templates covoiturage
├── profile/          # Templates profil
├── security/         # Templates auth
└── emails/           # Templates emails

assets/               # Assets frontend
├── styles/           # CSS/SCSS
├── images/           # Images
└── controllers/      # Stimulus JS

config/               # Configuration Symfony
├── packages/         # Config des bundles
├── routes/           # Routes personnalisées
└── services.yaml     # Services container
```

---

## 🗄️ Base de Données

### Schéma Relationnel

#### Table `user`

```sql
CREATE TABLE `user` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(180) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    firstname VARCHAR(128) NOT NULL,
    lastname VARCHAR(128) NOT NULL,
    telephone VARCHAR(50),
    pseudo VARCHAR(255) UNIQUE NOT NULL,
    adresse VARCHAR(255),
    date_naissance DATE,
    photo VARCHAR(255),
    sexe VARCHAR(10),
    date_inscription DATETIME NOT NULL,
    is_verified BOOLEAN DEFAULT 0,
    verification_token VARCHAR(255),
    reset_token VARCHAR(255),
    reset_token_created_at DATETIME,
    is_profile_complete BOOLEAN DEFAULT 0,
    is_chauffeur BOOLEAN DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    credits INT DEFAULT 0,
    roles JSON NOT NULL
);
```

#### Table `covoiturage`

```sql
CREATE TABLE covoiturage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chauffeur_id INT NOT NULL,
    voiture_id INT NOT NULL,
    date_depart DATETIME NOT NULL,
    heure_depart VARCHAR(50) NOT NULL,
    lieu_depart VARCHAR(255) NOT NULL,
    date_arrivee DATETIME NOT NULL,
    heure_arrivee VARCHAR(50) NOT NULL,
    lieu_arrivee VARCHAR(255) NOT NULL,
    statut VARCHAR(255) NOT NULL,
    nb_place_total INT NOT NULL,
    nb_place_restantes INT NOT NULL,
    prix_personne DOUBLE NOT NULL,
    description TEXT,
    FOREIGN KEY (chauffeur_id) REFERENCES user(id),
    FOREIGN KEY (voiture_id) REFERENCES voiture(id)
);
```

#### Table `reservation`

```sql
CREATE TABLE reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    covoiturage_id INT NOT NULL,
    passager_id INT NOT NULL,
    nb_places_reservees INT NOT NULL,
    statut VARCHAR(50) NOT NULL,
    date_reservation DATETIME NOT NULL,
    FOREIGN KEY (covoiturage_id) REFERENCES covoiturage(id),
    FOREIGN KEY (passager_id) REFERENCES user(id)
);
```

#### Table `voiture`

```sql
CREATE TABLE voiture (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proprietaire_id INT NOT NULL,
    marque_id INT NOT NULL,
    modele VARCHAR(50) NOT NULL,
    immatriculation VARCHAR(50) NOT NULL,
    couleur VARCHAR(50) NOT NULL,
    motorisation VARCHAR(50) NOT NULL,
    date_premiere_immatriculation VARCHAR(50),
    energie VARCHAR(50),
    FOREIGN KEY (proprietaire_id) REFERENCES user(id),
    FOREIGN KEY (marque_id) REFERENCES marque(id)
);
```

### Relations Principales

- **User ←→ Covoiturage** : Un utilisateur peut créer plusieurs covoiturages
- **User ←→ Reservation** : Un utilisateur peut avoir plusieurs réservations
- **Covoiturage ←→ Reservation** : Un covoiturage peut avoir plusieurs réservations
- **User ←→ Voiture** : Un utilisateur peut posséder plusieurs véhicules
- **User ←→ Avis** : Relations pour les évaluations (auteur/receveur)

### Index et Performance

```sql
-- Index pour les recherches fréquentes
CREATE INDEX idx_covoiturage_lieu_date ON covoiturage(lieu_depart, date_depart);
CREATE INDEX idx_reservation_statut ON reservation(statut);
CREATE INDEX idx_user_email ON user(email);
```

---

## 🔐 Sécurité

### Authentification

- **Système** : Symfony Security Bundle
- **Provider** : Base de données (Entity User)
- **Encodeur** : bcrypt (password_hash)
- **Sessions** : Sécurisées avec tokens

### Autorisation

```php
// Contrôle d'accès par attributs
#[IsGranted('ROLE_USER')]
#[IsGranted('ROLE_CHAUFFEUR')]

// Vérifications manuelles
if ($this->getUser() !== $covoiturage->getChauffeur()) {
    throw $this->createAccessDeniedException();
}
```

### Protection CSRF

```php
// Dans tous les formulaires
if (!$this->isCsrfTokenValid('action_id', $token)) {
    throw new InvalidCsrfTokenException();
}
```

### Validation des Données

```php
// Entities avec contraintes
#[Assert\NotBlank]
#[Assert\Email]
#[Assert\Length(min: 8)]
#[Assert\Regex(pattern: "/^[0-9]+$/")]
```

### Sécurisation des Uploads

```php
// Types de fichiers autorisés
private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif'];
private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
```

---

## 📧 Système d'Emails

### Service EmailService

```php
namespace App\Service;

class EmailService
{
    public function sendReservationCreatedEmail(Reservation $reservation): bool
    public function sendNewReservationToDriverEmail(Reservation $reservation): bool
    public function sendReservationConfirmedEmail(Reservation $reservation): bool
    public function sendReservationRejectedEmail(Reservation $reservation): bool
    public function sendReservationCancelledByDriverEmail(Reservation $reservation): bool
}
```

### Templates d'Emails

- **reservation_created.html.twig** : Confirmation de réservation
- **new_reservation_to_driver.html.twig** : Notification au conducteur
- **reservation_confirmed.html.twig** : Réservation acceptée
- **reservation_rejected.html.twig** : Réservation refusée

### Configuration Mailer

```yaml
# config/packages/mailer.yaml
framework:
    mailer:
        dsn: '%env(MAILER_DSN)%'
```

---

## 💰 Système de Crédits

### Logique Métier

#### Attribution Initiale

```php
// Dans UserFixtures ou lors de l'inscription
$user->setCredits(100); // Crédits de bienvenue
```

#### Workflow de Réservation

1. **Réservation** : Débit immédiat des crédits du passager
2. **En attente** : Crédits bloqués temporairement
3. **Confirmation** : Transfert vers le conducteur
4. **Rejet/Annulation** : Remboursement automatique

#### Implémentation

```php
// Réservation
$user->setCredits($user->getCredits() - $prixTotal);

// Confirmation
$conducteur->setCredits($conducteur->getCredits() + $montantTransfert);

// Annulation
$passager->setCredits($passager->getCredits() + $prixRemboursement);
```

---

## 🎨 Interface Utilisateur

### Framework CSS

- **Bootstrap 5.3.5** pour le responsive design
- **CSS personnalisé** dans `assets/styles/`
- **Thème moderne** avec couleurs cohérentes

### JavaScript

```javascript
// Stimulus Controllers
import { Application } from "@hotwired/stimulus"
import { definitionsFromContext } from "@symfony/stimulus-bridge"

const application = Application.start()
const context = require.context("./controllers", true, /\.js$/)
application.load(definitionsFromContext(context))
```

### Templates Twig

```twig
{# Layout de base #}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{% block title %}EcoRide{% endblock %}</title>
    {{ encore_entry_link_tags('app') }}
</head>
<body>
    {% include '_partials/navbar.html.twig' %}
    {% block body %}{% endblock %}
    {{ encore_entry_script_tags('app') }}
</body>
</html>
```

---

## 🔧 Configuration Système

### Variables d'Environnement

```env
# Base de données
DATABASE_URL=mysql://user:password@127.0.0.1:3306/ecoride

# Mailer
MAILER_DSN=smtp://localhost:1025

# App
APP_ENV=dev
APP_SECRET=your_secret_key
```

### Bundles Principaux

```php
// config/bundles.php
return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
];
```

### Services Personnalisés

```yaml
# config/services.yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\Service\EmailService:
        arguments:
            $mailer: '@mailer'
            $twig: '@twig'
```

---

## 📱 API et Routes

### Routes Principales

```bash
# Covoiturage
GET    /covoiturage              # Liste des trajets
GET    /covoiturage/search       # Recherche
POST   /covoiturage/new          # Création
GET    /covoiturage/{id}         # Détails
GET    /covoiturage/{id}/passengers # Gestion passagers

# Réservations
POST   /reservation/create/{id}   # Nouvelle réservation
POST   /reservation/{id}/confirm  # Confirmation
POST   /reservation/{id}/reject   # Rejet
POST   /reservation/{id}/annuler  # Annulation

# Profil
GET    /profil                   # Dashboard utilisateur
GET    /profil/mes-reservations  # Réservations
GET    /profil/mes-covoiturages  # Trajets créés
```

### Réponses JSON (pour AJAX)

```php
return $this->json([
    'success' => true,
    'message' => 'Opération réussie',
    'data' => $data
], Response::HTTP_OK);
```

---

## 🧪 Tests

### Tests Manuels

- **TESTS_RESERVATION.md** : Scénarios de test complets
- **Fixtures** : Données de test automatiques
- **Environnement de test** : Base de données séparée

### Commandes de Test

```bash
# Tests unitaires
php bin/phpunit

# Tests avec couverture
php bin/phpunit --coverage-html coverage/

# Fixtures de test
php bin/console doctrine:fixtures:load
```

---

## 🚀 Déploiement

### Environnements

- **Développement** : XAMPP local
- **Production** : VPS Linux avec Apache/Nginx

### Processus de Déploiement

1. **Git Push** → Déclenchement GitHub Actions
2. **Tests automatiques** → Validation du code
3. **Déploiement** → Pull + build sur le serveur
4. **Migration BDD** → Mise à jour du schéma
5. **Cache** → Invalidation et warm-up

### Scripts de Production

```bash
# Optimisations production
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
npm run build
```

---

## 📊 Monitoring et Logs

### Logs Symfony

- **Répertoire** : `var/log/`
- **Fichiers** : `dev.log`, `prod.log`
- **Niveaux** : DEBUG, INFO, WARNING, ERROR

### Monitoring Applicatif

```php
// Logging des actions critiques
$this->logger->info('Réservation créée', [
    'user_id' => $user->getId(),
    'covoiturage_id' => $covoiturage->getId(),
    'montant' => $prixTotal
]);
```

---

## 🔄 Workflow Git

### Branches

- **main** : Version stable (production)
- **develop** : Développement actif
- **feature/** : Nouvelles fonctionnalités

### Commits Standards

```bash
git commit -m "feat: ajout système de notification email"
git commit -m "fix: correction calcul crédits"
git commit -m "refactor: amélioration ReservationController"
```

---

## 📈 Performance

### Optimisations BDD

- **Index** sur les colonnes de recherche fréquente
- **Relations lazy** pour éviter les requêtes N+1
- **Pagination** avec KnpPaginatorBundle

### Cache

- **Doctrine Query Cache** : Cache des requêtes
- **Template Cache** : Cache Twig (production)
- **OpCache** : Cache PHP bytecode

### Frontend

- **Webpack Encore** : Minification JS/CSS
- **Images optimisées** : Compression automatique
- **CDN** : Pour les assets statiques (en production)

---

## 🔧 Maintenance

### Commandes Utiles

```bash
# Nettoyage cache
php bin/console cache:clear

# Mise à jour BDD
php bin/console doctrine:migrations:migrate

# Debug des services
php bin/console debug:container

# Debug des routes
php bin/console debug:router
```

### Sauvegarde BDD

```bash
# Export complet
mysqldump -u user -p ecoride > backup_ecoride.sql

# Import
mysql -u user -p ecoride < backup_ecoride.sql
```

---

*Documentation technique - Version 1.0 - Août 2025*
