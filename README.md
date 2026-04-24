# Système de Gestion d'Entreprise (ERP/HRM) 🚀

![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Security](https://img.shields.io/badge/Security-Prepared_Statements-success?style=flat-square)

## 🎯 Aperçu du Projet
Ce projet est un **Système complet de gestion d'entreprise (ERP/HRM)** développé initialement lors d'un **stage en entreprise**. Conçu pour centraliser l'administration des ressources humaines, la gestion des stocks, le suivi des commandes et l'attribution des permissions, il n'a finalement pas été adopté par l'entreprise. 

Je l'ai donc conservé, **refactorisé, sécurisé et dockerisé** pour en faire un projet de portfolio démontrant mes compétences en : **Architecture Backend (PHP/MySQL)**, **Déploiement DevOps (Docker)**, et **Conception UI/UX fonctionnelle**.

---

## ✨ Fonctionnalités Clés

*   👥 **Ressources Humaines (HRM) :** Gestion complète des employés, fiches d'identification, upload sécurisé de photos de profil, hiérarchie et gestion des départements.
*   🔒 **Sécurité Avancée :** Protection stricte contre les **injections SQL** via l'utilisation systématique de requêtes préparées (`mysqli_prepare`/`PDO`). Sécurisation des formulaires d'upload (validation MIME/extensions) et hachage des mots de passe.
*   🐳 **Environnement Conteneurisé (DevOps) :** Application entièrement "Dockerisée" avec `docker-compose`, garantissant que le code fonctionne de la même manière sur n'importe quelle machine, sans configuration complexe.
*   📦 **Gestion Opérationnelle :** Suivi des commandes, gestion des stocks (ex: carburant), traitement des formulaires d'avance sur salaire et système de notifications en temps réel.
*   ⚙️ **Administration & Rôles :** Tableau de bord dynamique avec contrôle d'accès basé sur les rôles (RBAC). Les vues s'adaptent selon que l'utilisateur est admin, RH, ou employé classique.

---

## 🛠️ Stack Technique

| Catégorie | Technologies Utilisées |
|---|---|
| **Backend** | PHP 8.2 (Vanilla), Requêtes Préparées (Sécurité) |
| **Base de Données** | MySQL 8.0 (Relationnelle) |
| **DevOps / CI-CD** | Docker, Docker Compose |
| **Frontend** | HTML5, CSS3 (Custom Design), JavaScript (SweetAlert2) |

---

## 📁 Architecture du Projet

```text
systeme-pdo/
├── Formulaires/              # Interfaces de saisie (RH, Avances, etc.)
├── bd/                       # Scripts SQL et initialisation de la base (Docker)
├── css/                      # Feuilles de styles (Custom UI)
├── gestion_users/            # Logique de gestion des comptes
├── js/                       # Scripts JavaScript frontend
├── users_dashboard/          # Tableaux de bord selon le rôle RBAC
├── Dockerfile                # Image du conteneur PHP-Apache
├── docker-compose.yml        # Orchestration de l'environnement complet
├── serv_projet1.php          # Fichier de connexion dynamique à la BD
└── index.php                 # Page de connexion principale
```

---

## 🚀 Guide d'Installation (Docker)

La méthode recommandée pour lancer ce projet est d'utiliser **Docker**. Cela vous évite d'installer un serveur local (Wamp/Xampp) et configure automatiquement la base de données.

### Prérequis
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installé et lancé.

### Lancement en 1 commande
1. **Clonez le dépôt :**
   ```bash
   git clone https://github.com/saleck24/systeme-pdo.git
   cd systeme-pdo
   ```
2. **Démarrez les conteneurs :**
   ```bash
   docker-compose up -d --build
   ```
3. **C'est prêt !**
   - L'application est accessible sur : `http://localhost:8088`
   - La base de données s'initialise toute seule avec le fichier `bd/system_pdo.sql`.
   - **Identifiants de test :** `saleckbaya5@gmail.com` / Mot de passe : `password`

*Note : Si vous préférez utiliser WAMP/XAMPP, l'application reste 100% compatible. Placez le dossier dans `www` ou `htdocs`, importez le SQL via phpMyAdmin, et accédez via `localhost`.*

---

## 💡 Pourquoi ce projet ? (Note aux Recruteurs)

Ce projet a été développé pour démontrer ma capacité à :
1. **Reprendre et améliorer une base de code existante** (Refactoring).
2. **Identifier et corriger des failles de sécurité critiques** (Injection SQL, Upload malveillant).
3. **Mettre en place des pratiques DevOps modernes** (Dockerisation pour faciliter l'onboarding et le déploiement).
4. **Créer des interfaces métier fonctionnelles et user-friendly**.

---

**Auteur :** Saleck BAYA 
**Licence :** MIT  
**Dernière mise à jour :** 2026
