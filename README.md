# 🛒 E-Commerce API & Backend - GreenGoodies (Symfony 7.4)

![Symfony](https://img.shields.io/badge/Symfony-7.4-black?style=for-the-badge&logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php)
![Doctrine](https://img.shields.io/badge/Doctrine-ORM-orange?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-blue.style=for-the-badge)

Une solution e-commerce backend moderne et écoresponsable basée sur **Symfony 7.4**, offrant une gestion fluide des paniers, de la passation de commandes (`SalesOrder`) et une **API REST sécurisée** permettant la consultation des produits et de l'historique d'achats des clients.

---

## 📋 Table des matières

- [🎯 Objectifs du Projet](#-objectifs-du-projet)
- [✨ Fonctionnalités Principales](#-fonctionnalités-principales)
- [🛠️ Architecture & Choix Techniques](#️-architecture--choix-techniques)
    - [Stack Technique](#stack-technique)
    - [Modèle de Données & Entités](#modèle-de-données--entités)
    - [Optimisation des Performances (Problème N+1)](#optimisation-des-performances-problème-n1)
- [🔒 Sécurité & Contrôle d'Accès API](#-sécurité--contrôle-daccès-api)
    - [Habilitations API (`isAccesAPI`)](#habilitations-api-isaccesapi)
    - [Sérialisation & Protection contre les Fuites de Données](#sérialisation--protection-contre-les-fuites-de-données)
- [🚨 Gestion des Erreurs & Codes HTTP](#-gestion-des-erreurs--codes-http)
- [🚀 Guide d'Installation Rapide](#-guide-dinstallation-rapide)
    - [Prérequis](#prérequis)
    - [Installation](#installation)
    - [Base de données & Fixtures](#base-de-données--fixtures)
    - [Lancement du Serveur](#lancement-du-serveur)

---

## 🎯 Objectifs du Projet

**GreenGoodies** est une boutique physique écoresponsable implantée à Lyon. L'objectif principal de ce projet est de développer la plateforme e-commerce et le backend API sous **Symfony 7.4** afin d'étendre son activité et de recevoir/traiter des commandes sur l'ensemble du territoire français.

### Vision & Engagement Écoresponsable (Green Code)

En alignement direct avec les valeurs écoresponsables de GreenGoodies, l'application intègre nativement des bonnes pratiques de **Green Code** afin de minimiser son empreinte carbone et d'optimiser ses performances web :

- **Optimisation des médias** : Réduction et compression systématique du poids des images utilisées sur le site.
- **Minification des ressources** : Compression et minification des fichiers CSS et JavaScript pour réduire la consommation de bande passante.

---

## ✨ Fonctionnalités Principales

L'application s'articule autour de 3 grands volets fonctionnels répondant au cahier des charges :

### 1. 🌐 Partie Publique (Accessible à tous)

- **Menu de Navigation Dynamique** :
    - Logo GreenGoodies orientant vers la page d'accueil.
    - Liens contextuels : _Connexion_ et _Inscription_ pour les visiteurs anonymes ; _Nos produits_, _Mon panier_, _Mon compte_ et _Déconnexion_ pour les utilisateurs authentifiés.
- **Page d'Accueil & Vitrine Produits** :
    - _Hero Header_ accueillant les visiteurs.
    - Grille des produits vendus (avec nom, photo, prix, courte description et bouton _"Voir le produit"_).
    - Fixtures de démonstration pré-chargées (a minima 3 produits).
- **Page Fiche Produit** :
    - Affichage détaillé du produit (photo, nom, prix, description longue).
    - Formulaire contextuel d'ajout au panier : bouton de redirection vers la connexion pour les invités, ou sélecteur de quantité (_"Ajouter au panier"_ / _"Mettre à jour"_) pour les utilisateurs connectés.
- **Inscription & Authentification** :
    - Formulaire d'inscription sécurisé (Prénom, Nom, Email unique, Mot de passe, Confirmation, Case à cocher CGU).
    - Validation stricte des formulaires côté serveur avec Symfony (affichage des erreurs ciblées sous chaque champ).
    - Inscription réussie entraînant l'authentification et la redirection automatique vers l'accueil.
    - Formulaire de connexion avec gestion des messages d'erreur (_"Identifiants incorrects"_).
- **Pied de page institutionnel** : Mention légale _"© GreenGoodies - [Année en cours]"_ présente sur toutes les pages.

### 2. 👤 Partie Privée (Espace Utilisateur Authentifié)

- **Gestion du Panier (`Basket`)** :
    - Consultation récapitulative du panier (nom, photo, quantité, prix unitaire, prix total).
    - Bouton _"Vider le panier"_ pour annuler la commande en cours.
    - Bouton _"Valider la commande"_ permettant de transformer le panier en commande ferme (`SalesOrder`) sans paiement en ligne pour cette version, avec message de confirmation flash.
- **Espace "Mon compte"** :
    - Historique synthétique des dernières commandes passées (numéro de commande, date de validation, montant total).
    - Option de suppression définitive du compte utilisateur et de ses données/commandes associées (_"Supprimer mon compte"_).
    - Gestion dynamique du droit d'accès API avec bascule instantanée via les boutons _"Activer mon accès API"_ / _"Désactiver mon accès API"_.

### 3. 🔌 Partie API REST Sécurisée

- **Endpoints API dédiés** (Authentification JWT / Session) :
    - Endpoint d'authentification et d'obtention de token.
    - Endpoints de récupération de la liste des produits et des historiques de commandes client.
- **Contrôle d'accès granulaire** : L'utilisation de l'API est strictement réservée aux utilisateurs connectés ayant explicitement activé la fonctionnalité `isAccesAPI()` depuis leur profil client.

---

## 🛠️ Architecture & Choix Techniques

### Stack Technique

- **Framework** : Symfony 7.4 (PHP 8.2+)
- **ORM** : Doctrine ORM 3.x
- **Sérialiseur** : `symfony/serializer`
- **Base de données** : MySQL
- **Moteur de Templates** : Twig (pour la partie Web / Espace client)

### Modèle de Données & Entités

L'architecture repose sur 3 entités majeures pour le système de commandes :

1. **`User`** : Représente l'utilisateur de la plateforme. Contient les identifiants, les rôles ainsi que l'attribut `accesAPI` (méthode `isAccesAPI(): bool`).
2. **`SalesOrder`** : Représente une commande finalisée.
    - Contient la date de création (`createdAt`), le montant total historique (`totalPriceHistorical`) typé en `Types::DECIMAL(precision: 8, scale: 2)` pour éviter toute imprécision liée aux nombres à virgule flottante.
    - Relation `ManyToOne` vers `User` et `OneToMany` vers `SalesOrderProduct`.
3. **`SalesOrderProduct`** : Représente les lignes de produits associées à une commande à un instant _T_.
    - Conserve les données historiques indispensables (nom du produit, prix unitaire `historicalPrice`, référence `sku`, code-barres `ean13`, quantité `quantity`, identifiant d'origine `historicalIdProduct`).

### Optimisation des Performances (Problème N+1)

Pour éviter l'impact sur les performances lié à l'effet **N+1 SELECT**, les requêtes de récupération des commandes dans `SalesOrderRepository` exploitent une jointure optimisée (`JOIN FETCH`) :

```php
// src/Repository/SalesOrderRepository.php
public function findByUserWithProducts(User $user): array
{
    return $this->createQueryBuilder('o')
        ->addSelect('p') // Jointure gourmande (FETCH) pour charger les produits en une seule requête DQL
        ->leftJoin('o.salesOrderProducts', 'p')
        ->andWhere('o.user = :user')
        ->setParameter('user', $user)
        ->orderBy('o.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
}
```

---

## 🔒 Sécurité & Contrôle d'Accès API

### Habilitations API (`isAccesAPI`)

L'accès à l'endpoint API `/api/orders` nécessite deux conditions d'habilitation :

1. L'utilisateur doit être authentifié (`ROLE_USER`).
2. Le drapeau `accesAPI` de l'entité `User` doit valoir `true`.

Exemple d'implémentation dans `SalesOrderController` :

```php
#[Route('/api/orders', name: 'api_order_list', methods: ['GET'])]
public function getOrderList(SalesOrderRepository $orderRepository, SerializerInterface $serializer): JsonResponse
{
    /** @var User|null $user */
    $user = $this->getUser();

    // Vérification stricte des droits d'accès à l'API
    if (!$user || !$user->isAccesAPI()) {
        return new JsonResponse([
            'error' => 'Accès API non autorisé.',
            'message' => 'Veuillez activer votre accès API dans votre espace client.'
        ], JsonResponse::HTTP_FORBIDDEN); // 403 Forbidden
    }

    $orderList = $orderRepository->findByUserWithProducts($user);
    $jsonOrderList = $serializer->serialize($orderList, 'json', ['groups' => 'order:read']);

    return new JsonResponse($jsonOrderList, JsonResponse::HTTP_OK, [], true);
}
```

### Sérialisation & Protection contre les Fuites de Données

Le groupe de sérialisation `order:read` est explicitement configuré sur les entités `SalesOrder` et `SalesOrderProduct` :

- **Champs exposés** :
    - `SalesOrder` : `id`, `totalPriceHistorical`, `createdAt`, `salesOrderProducts`
    - `SalesOrderProduct` : `id`, `quantity`, `name`, `historicalPrice`, `sku`, `ean13`, `historicalIdProduct`
- **Champs exclus** :
    - La relation `user` de `SalesOrder` est volontairement **exclue** de ce groupe afin de bloquer toute exposition non désirée d'informations personnelles (email, hash de mot de passe, rôles) et d'éliminer les références circulaires lors de la sérialisation JSON.

---

## 🚨 Gestion des Erreurs & Codes HTTP

### Centralisation par EventSubscriber (`ExceptionSubscriber`)

La gestion des erreurs dans l'application est entièrement **centralisée** grâce au composant EventDispatcher de Symfony et à la mise en place d'un abonné d'événements dédié : [`ExceptionSubscriber`](file:///c:/Users/Laurent/Documents/dev/OCR-Projet13/src/EventSubscriber/ExceptionSubscriber.php).

- **Écoute des Kernel Exceptions** : Le souscripteur écoute l'événement système `KernelEvents::EXCEPTION` pour intercepter toutes les exceptions non gérées survenues au cours du cycle de vie de la requête.
- **Traitement des erreurs Web (Page d'erreur personnalisée)** : Lorsqu'une erreur survient sur la partie Web de l'application, l'exception est enregistrée dans les logs et l'utilisateur est automatiquement redirigé vers une **page d'erreur élégante et personnalisée** ([`templates/bundles/TwigBundle/Exception/error.html.twig`](file:///c:/Users/Laurent/Documents/dev/OCR-Projet13/templates/bundles/TwigBundle/Exception/error.html.twig)). Cette page informe l'utilisateur du problème technique avec un bouton de retour rapide vers la page d'accueil, tout en exposant des informations de débogage supplémentaires (code, message, fichier et ligne) lorsque l'application s'exécute en mode développement (`dev`).
- **Réponses API JSON unifiées** : Lorsqu'une erreur survient sur une route d'API (URL commençant par `/api`), l'événement est intercepté par le souscripteur et génère automatiquement une réponse au format JSON structuré avec le code de statut HTTP approprié :
  ```json
  {
      "error": true,
      "status": 403,
      "message": "Accès API non autorisé."
  }
  ```

### Journalisation & Traçabilité avec Monolog

L'ensemble des erreurs est tracé via le service **Monolog** grâce à des canaux d'injection autonomes (`webLogger` et `apiLogger`) :

- **Fichiers de logs tournants (à la journée)** : Les logs sont automatiquement enregistrés et archivés par date dans des fichiers tournants situés dans le répertoire `var/log/` :
  - **Erreurs Web** : Tracées dans le fichier journal dédié aux requêtes de l'interface utilisateur web (`var/log/web_error.log`).
  - **Erreurs API** : Tracées dans le fichier journal dédié aux appels d'endpoints API (`var/log/api_error.log`).
- **Contexte détaillé** : Chaque entrée de log inclut l'URL, la méthode HTTP, le code de statut, la classe d'exception, le fichier source et la ligne où l'erreur a été levée.

### Tableau des Codes de Retour HTTP

| Code HTTP | Statut | Cause / Signification | Format de Réponse |
| :--- | :--- | :--- | :--- |
| **`200 OK`** | Succès | Liste des commandes ou produits récupérée avec succès. | JSON brut ou page Twig. |
| **`401 Unauthorized`** | Erreur client | L'utilisateur n'est pas connecté à l'application. | Redirection ou JSON d'erreur d'authentification. |
| **`403 Forbidden`** | Erreur client | L'utilisateur est connecté mais `isAccesAPI()` renvoie `false`. | `{"error": true, "status": 403, "message": "Accès API non autorisé."}` |
| **`404 Not Found`** | Erreur client | La ressource ou la route demandée n'existe pas. | Page 404 Web ou JSON d'erreur 404 sur l'API. |
| **`500 Internal Error`** | Erreur serveur | Exception non interceptée au niveau de l'application. | JSON d'erreur formaté par `ExceptionSubscriber` sur l'API. |

---

## 🚀 Guide d'Installation Rapide

### Prérequis

- **PHP** : >= 8.2 (avec extensions `pdo`, `intl`, `mbstring`, `iconv`, `ctype`)
- **Composer** : >= 2.5
- **Symfony CLI** (recommandé)
- **Base de données** : PostgreSQL, MySQL ou MariaDB

### Installation

1. **Cloner le projet** :

    ```bash
    git clone https://github.com/votre-org/votre-projet-ecommerce.git
    cd votre-projet-ecommerce
    ```

2. **Installer les dépendances PHP** :

    ```bash
    composer install
    ```

3. **Configurer les variables d'environnement** :
   Dupliquez le fichier `.env` en `.env.local` et adaptez le DSN de votre base de données :
    ```env
    # .env.local
    DATABASE_URL="mysql://root:password@127.0.0.1:3306/ecommerce_db?serverVersion=8.0&charset=utf8mb4"
    # Ou pour PostgreSQL :
    # DATABASE_URL="postgresql://app:password@127.0.0.1:5432/ecommerce_db?serverVersion=16&charset=utf8"
    ```

### Base de données & Fixtures

1. **Créer la base de données** :

    ```bash
    php bin/console doctrine:database:create
    ```

2. **Exécuter les migrations** :

    ```bash
    php bin/console doctrine:migrations:migrate --no-interaction
    ```

3. **(Optionnel) Charger les jeux de données de test (Fixtures)** :
    ```bash
    php bin/console doctrine:fixtures:load --no-interaction
    ```

### Lancement du Serveur

Lancez le serveur de développement local Symfony :

```bash
symfony server:start
```

L'application web sera accessible à l'adresse `http://127.0.0.1:8000` et l'endpoint API sous `http://127.0.0.1:8000/api/orders`.

---

© 2026 Tous droits réservés. Développé sous Symfony 7.4.
