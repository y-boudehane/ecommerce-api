# API de gestion des produits

## Description

Cette API permet de gérer des produits pour une plateforme e-commerce. Elle inclut des fonctionnalités pour :
- Créer, lire, mettre à jour et supprimer des produits.
- Gérer les relations entre produits et catégories.
- Rechercher des produits par nom ou description.
- Filtrer et trier les produits.
- Implémenter l'authentification avec Sanctum.
- Envoyer des notifications par email lorsque le stock d'un produit est faible.

---

## Prérequis

Avant de commencer, assurez-vous d'avoir les outils suivants installés sur votre machine :

- **PHP** >= 8.2
- **Composer** (pour la gestion des dépendances PHP)
- **MySQL** ou **SQLite** (ou tout autre SGBD compatible)

## Installation

1. **Clonez le dépôt :**
   ```bash
   git clone https://votre-repo-url.git
   cd votre-repo
   ```

2. **Installez les dépendances :**
   ```bash
   composer install
   ```

3. **Configurez le fichier `.env` :**
   - Copiez le fichier `.env.example` en `.env` :
     ```bash
     cp .env.example .env
     ```
   - Modifiez les paramètres de connexion à la base de données dans le fichier `.env`.
   - **Configuration de l'email :**
     - Ajoutez les lignes suivantes pour configurer l'email :
       ```env
       MAIL_MAILER=smtp
       MAIL_HOST=smtp.votre-fournisseur-email.com
       MAIL_PORT=587
       MAIL_USERNAME=votre-email@example.com
       MAIL_PASSWORD=votre-mot-de-passe
       MAIL_ENCRYPTION=tls
       MAIL_FROM_ADDRESS=votre-email@example.com
       MAIL_FROM_NAME="Nom de votre application"
       ```

4. **Générez la clé d'application :**
   ```bash
   php artisan key:generate
   ```

5. **Exécutez les migrations :**
   ```bash
   php artisan migrate
   ```

6. **Démarrez le serveur :**
   ```bash
   php artisan serve
   ```

Votre API est maintenant prête à être utilisée !
