# Projet réalisé dans le cadre de la formation OpenclassRooms Conception et développement avec Symfony PHP

# 🛒 E-Commerce API & Backend - GreenGoodies (Symfony 7.4)

![Symfony](https://img.shields.io/badge/Symfony-7.4-black?style=for-the-badge&logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php)
![Doctrine](https://img.shields.io/badge/Doctrine-ORM-orange?style=for-the-badge)

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
    - [Authentification Web](#authentification-web)
    - [Authentification API](#authentification-api)
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

**GreenGoodies** est une boutique physique écoresponsable fictive. L'objectif principal de ce projet est de développer la plateforme e-commerce et le backend API sous **Symfony 7.4** afin d'étendre son activité et de recevoir/traiter des commandes sur l'ensemble du territoire français.

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

### Authentification Web

La partie Web utilise l'authentification par formulaire intégrée à **Symfony Security**, distincte de l'authentification JWT de l'API. Elle est gérée par le pare-feu `main` dans `config/packages/security.yaml`, qui est stateful : après connexion, Symfony conserve l'identité authentifiée dans la session Web et le navigateur transmet le cookie de session lors des requêtes suivantes.

#### Connexion

La page `GET /login` est rendue par `SecurityController` avec le template `templates/security/login.html.twig`. Le formulaire envoie l'adresse e-mail dans `_username` et le mot de passe dans `_password` vers la même route. Il comprend également un jeton CSRF (`authenticate`) afin de protéger la soumission contre les requêtes forgées.

À la soumission, l'authentificateur `form_login` du pare-feu `main` délègue la vérification à Symfony :

1. Le fournisseur `app_user_provider` recherche l'entité `User` par adresse e-mail.
2. Symfony compare le mot de passe soumis avec le hash enregistré, en utilisant le hasher configuré avec `auto`. Le mot de passe en clair n'est pas stocké.
3. En cas de succès, Symfony authentifie l'utilisateur dans la session et le redirige vers `app_main` (la cible par défaut configurée).
4. En cas d'échec, la page de connexion récupère l'erreur et le dernier identifiant saisi via `AuthenticationUtils`, puis affiche le message d'erreur.

Le formulaire ne propose pas de case « se souvenir de moi » active et aucun mécanisme de limitation des tentatives de connexion n'est configuré dans ce pare-feu.

#### Création de compte et déconnexion

Après validation du formulaire d'inscription, `RegistrationController` hashe le mot de passe, enregistre le nouvel utilisateur puis le connecte immédiatement avec `Security::login()` sur le pare-feu `main`. L'utilisateur n'a donc pas besoin de soumettre à nouveau le formulaire de connexion après son inscription.

La route `GET /logout` est déclarée par `SecurityController`, mais son action n'est pas exécutée normalement : le mécanisme `logout` du pare-feu intercepte la requête, termine la session d'authentification et redirige vers `app_main`.

#### Autorisation des pages Web

L'authentification établit l'identité de l'utilisateur ; les contrôles d'autorisation déterminent ensuite les pages auxquelles il peut accéder. L'entité `User` ajoute automatiquement le rôle `ROLE_USER` à ses rôles. Certains contrôleurs, comme ceux du panier et des commandes, exigent explicitement ce rôle avec `#[IsGranted('ROLE_USER')]`. Les règles `access_control` actuellement définies dans `security.yaml` concernent les routes API ; elles ne constituent pas une règle générale protégeant toutes les routes Web.

### Authentification API

L'API utilise le bundle **LexikJWTAuthenticationBundle** pour authentifier les clients sans session serveur. Le bundle s'intègre au composant Security de Symfony : il vérifie les identifiants au point d'entrée, émet un jeton JWT signé, puis authentifie les requêtes API suivantes à partir de ce jeton.

#### 1. Obtenir un jeton

Le point d'entrée `POST /api/login` reçoit les identifiants au format JSON. Le champ `username` correspond à l'identifiant Symfony de l'utilisateur, c'est-à-dire son adresse e-mail.

```http
POST /api/login
Content-Type: application/json
```

```json
{
    "username": "client@example.com",
    "password": "votre-mot-de-passe"
}
```

Le fournisseur d'utilisateurs configuré dans `security.yaml` recherche le compte par e-mail. Symfony vérifie le mot de passe à l'aide du hasher configuré (`auto`), puis `ApiUserChecker` vérifie avant l'authentification que le compte est autorisé à utiliser l'API (`accesAPI` activé).

En cas de succès, le gestionnaire de succès de Lexik renvoie un jeton dans une réponse JSON, sous la forme `{"token":"<JWT>"}`. En cas d'identifiants invalides, le gestionnaire `ApiAuthenticationFailureHandler` renvoie une réponse JSON `401 Unauthorized`. Si le compte existe mais que son accès API n'est pas activé, il renvoie `403 Forbidden` avec le message correspondant.

#### 2. Appeler une route protégée

Le client transmet le jeton reçu dans l'en-tête `Authorization` de chaque requête vers `/api` :

```http
GET /api/orders
Authorization: Bearer <JWT>
```

Le pare-feu `api` est stateless et utilise l'authentificateur JWT de Lexik. Celui-ci vérifie la signature et la validité du jeton avant de laisser Symfony traiter la requête. Aucun identifiant ni mot de passe n'est renvoyé avec les appels suivants. La durée de vie configurée du jeton est de **7 200 secondes (2 heures)** ; après expiration, le client doit s'authentifier de nouveau.

#### 3. Signature et configuration des clés

Lexik s'appuie sur une paire de clés configurée dans `lexik_jwt_authentication.yaml` :

- la **clé privée** signe les jetons émis ; elle doit rester confidentielle et être protégée sur le serveur ;
- la **clé publique** permet de vérifier leur signature lors des appels API ;
- la phrase secrète de la clé privée et les chemins des deux clés sont fournis par les variables d'environnement `JWT_PASSPHRASE`, `JWT_SECRET_KEY` et `JWT_PUBLIC_KEY`.

Pour créer ou renouveler la paire de clés dans `config/jwt/`, la commande fournie par le bundle est :

```bash
php bin/console lexik:jwt:generate-keypair
```

En production, les clés et la phrase secrète doivent être configurées de façon sécurisée dans l'environnement de déploiement ; ne publiez jamais la clé privée ni sa phrase secrète.

#### Configuration Symfony mise en œuvre

- Le pare-feu `login` cible `/api/login`, est stateless, autorise la soumission JSON des identifiants et délègue la génération du jeton au gestionnaire de succès de Lexik.
- Le pare-feu `api` cible les routes `/api` et active `jwt: ~` pour authentifier chaque requête à l'aide du jeton Bearer.
- La règle d'accès autorise explicitement `/api/login` et `/api/doc`, puis exige une authentification complète pour les autres routes `/api`.
- Le `ApiUserChecker` contrôle l'habilitation du compte lors de la connexion. Les contrôleurs vérifient également `isAccesAPI()` avant de fournir les données métier.

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

| Code HTTP                | Statut         | Cause / Signification                                                                              | Format de Réponse                                                                                |
| :----------------------- | :------------- | :------------------------------------------------------------------------------------------------- | :----------------------------------------------------------------------------------------------- |
| **`200 OK`**             | Succès         | Liste des commandes ou produits récupérée avec succès.                                             | JSON brut ou page Twig.                                                                          |
| **`401 Unauthorized`**   | Erreur client  | Identifiants invalides à la connexion, ou jeton absent, invalide ou expiré sur une route protégée. | JSON d'erreur d'authentification.                                                                |
| **`403 Forbidden`**      | Erreur client  | Le compte est authentifié mais son accès API (`isAccesAPI()`) n'est pas activé.                    | `{"error": true, "status": 403, "message": "Votre compte n'est pas autorisé à utiliser l'API."}` |
| **`404 Not Found`**      | Erreur client  | La ressource ou la route demandée n'existe pas.                                                    | Page 404 Web ou JSON d'erreur 404 sur l'API.                                                     |
| **`500 Internal Error`** | Erreur serveur | Exception non interceptée au niveau de l'application.                                              | JSON d'erreur formaté par `ExceptionSubscriber` sur l'API.                                       |

---

## 🚀 Guide d'Installation Rapide

### Prérequis

Assurez-vous d'avoir les outils suivants installés sur votre poste :

- **PHP** (version 8.2 ou supérieure)(avec extensions `pdo`, `intl`, `mbstring`, `iconv`, `ctype`)
- **Composer**
- Un serveur de base de données (**MySQL** ou **MariaDB**)
- **Git**

### Installation

1. **Cloner le projet** :

    ```bash
    git clone https://github.com/mutatolaurent/OCR-Projet13.git
    cd OCR-Projet13
    ```

2. **Installer les dépendances PHP** :

    ```bash
    composer install
    ```

3. **Configurer les variables d'environnement** :
   Dupliquez le fichier `.env` en `.env.local` et adaptez le DSN de votre base de données :
    ```env
    # .env.local
    DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/ecommerce_db?serverVersion=8.0&charset=utf8mb4"
    # Ou pour MariaDB :
    # DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
    ```

### Base de données & Fixtures

1.  **Créer la base de données** :

    ```bash
    php bin/console doctrine:database:create
    ```

2.  **Exécuter les migrations** :

    ```bash
    php bin/console doctrine:migrations:migrate --no-interaction
    ```

3.  **(Optionnel) Charger les jeux de données de test (Fixtures)** :

    ```bash
    php bin/console doctrine:fixtures:load --no-interaction
    ```

4.  **Générer les clés JWT (API)** :
    Le projet utilise le bundle LexikJWTAuthentication pour sécuriser l'API. Générez la paire de clés cryptographiques :

        ```bash
        Symfony console lexik:jwt:generate-keypair --skip-if-exists
        ```

    Alternative si cette commande échoue :
    1. Créer le dossier config/jwt (s'il n'existe pas déja)
    2. Créer la clé privé grâce à cette commande (dans un terminal GIT Bash si openssl n’est pas installé sur votre poste)
        ```bash
        openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
        ```
    3. Créer la clé publique grâce à cette commande :
        ```bash
        openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
        ```
    4. Dans le fichier .env.local , nous pouvons écrire ceci :

        ###> lexik/jwt-authentication-bundle ###

        JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem

        JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem

        JWT_PASSPHRASE=[votre passphrase]

        ###< lexik/jwt-authentication-bundle ###

### Lancement du Serveur

Lancez le serveur de développement local Symfony :

```bash
symfony server:start
```

L'application web sera accessible à l'adresse `http://127.0.0.1:8000` et les endpoint API sous `http://127.0.0.1:8000/api/login` et `http://127.0.0.1:8000/api/products`.

---

© 2026 Tous droits réservés. Développé sous Symfony 7.4.
