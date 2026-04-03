# Système de Gestion d'Entreprise

## 🎯 Aperçu
Système complet de gestion d'entreprise développé en **PHP avec PDO**, offrant une solution robuste, sécurisée et évolutive pour centraliser l'administration des ressources humaines, des commandes, des stocks et des demandes de l'entreprise.

---

## ✨ Fonctionnalités Principales

### 👥 Gestion des Utilisateurs
- **Création et gestion des utilisateurs** : Ajout, modification et suppression d'utilisateurs
- **Système de rôles et permissions** : Contrôle d'accès granulaire basé sur les rôles
- **Tableau de bord utilisateurs** : Vue personnalisée selon le profil
- **Gestion des sessions sécurisées** : Authentification robuste et déconnexion
- **Contrôle des autorisations** : Pages dédiées pour les accès non autorisés

### 📋 Gestion des Commandes
- **Suivi complet des commandes** : Création, visualisation et gestion des commandes
- **Modification des détails de commandes** : Interface dédiée pour ajuster les paramètres
- **Notifications en temps réel** : Alertes pour les changements de statut

### 📦 Gestion des Stocks
- **Suivi du stock carburant** : Inventaire en temps réel avec modifications faciles
- **Gestion automatisée** : Intégration avec les commandes

### 📝 Formulaires Dynamiques
- **Formulaires de personnel** : Ajout et gestion des informations personnelles
- **Formulaires d'avances** : Gestion des demandes d'avances de salaire
- **Modification et suppression sécurisées** : Interfaces dédiées pour chaque type de formulaire

### 🔔 Système de Notifications
- **Notifications intelligentes** : Alertes pour les demandes et mises à jour
- **Notifications d'expression de besoins** : Suivi des demandes spéciales

### ⚙️ Administration
- **Tableau des paramètres application** : Configuration centralisée
- **Paramètres administrateur** : Gestion des configurations système
- **Impression de permissions** : Documentation des droits d'accès

### 🔒 Sécurité
- **Prévention des injections SQL** : Utilisation de PDO avec requêtes préparées
- **Authentification sécurisée** : Gestion des sessions utilisateur
- **Contrôle d'accès** : Vérification des permissions à chaque action
- **Déconnexion sécurisée** : Nettoyage des sessions

---

## 🛠️ Technologies Utilisées

| Technologie | Utilisation |
|---|---|
| **PHP (PDO)** | Backend sécurisé et flexible pour interactions base de données |
| **MySQL** | Gestion de la base de données relationnelle |
| **HTML/CSS/JavaScript** | Interface utilisateur responsive |
| **WAMP/XAMPP** | Environnement de développement local |

---

## 📁 Architecture du Projet

```
systeme-pdo/
├── Formulaires/              # Formulaires dynamiques
├── bd/                       # Fichiers de base de données
├── css/                      # Styles de l'application
├── gestion_users/            # Gestion des utilisateurs
├── js/                       # Scripts JavaScript
├── users_dashboard/          # Tableaux de bord utilisateurs
├── modif_details_commande/   # Modification commandes
├── modif_stock_carburant/    # Gestion stock carburant
├── modif_supp_form_avance/   # Gestion formulaires avances
├── modif_supp_form_personnel/# Gestion formulaires personnel
├── add_user.php              # Ajout utilisateurs
├── gestion_commandes.php     # Gestion des commandes
├── handle_request.php        # Traitement des requêtes
├── notification.php          # Système de notifications
├── notification_expression_besoins.php # Notifications besoins
├── paramètre_admin.php       # Paramètres administrateur
├── tableau_parametre_appli.php # Configuration application
├── imprimer_permission.php   # Impression des permissions
├── logout.php                # Déconnexion utilisateur
├── unauthorized.php          # Page accès non autorisé
└── index.php                 # Page d'accueil
```

---

## 🚀 Installation

### Prérequis
- **WAMP** ou **XAMPP** installé et en fonctionnement
- **MySQL** actif
- Un navigateur web

### Étapes

1. **Clonez le dépôt :**
   ```bash
   git clone https://github.com/saleck24/systeme-pdo.git
   ```

2. **Démarrez votre serveur local :**
   - Lancez WAMP ou XAMPP
   - Assurez-vous que Apache et MySQL sont actifs

3. **Importez la base de données :**
   - Ouvrez **phpMyAdmin** (http://localhost/phpmyadmin)
   - Créez une nouvelle base de données
   - Importez le fichier `db.sql` situé dans le dossier `bd/`

4. **Configurez la connexion :**
   - Ouvrez le fichier `config.php`
   - Mettez à jour les identifiants MySQL selon votre configuration locale :
     ```php
     $host = 'localhost';
     $db = 'votre_base_de_donnees';
     $user = 'root';
     $password = '';
     ```

5. **Accédez à l'application :**
   - Ouvrez votre navigateur
   - Allez à : `http://localhost/systeme-pdo`

---

## 💡 Points Forts du Projet

✅ **Sécurité maximale** : PDO avec requêtes préparées  
✅ **Scalabilité** : Architecture modulaire et extensible  
✅ **User Experience** : Interface intuitive et réactive  
✅ **Robustesse** : Gestion complète des erreurs  
✅ **Flexibilité** : Système de rôles et permissions  
✅ **Maintenabilité** : Code bien organisé et commenté  

---

## 🎓 Apprentissages et Compétences Développées

- Architecture de base de données relationnelle
- Programmation orientée objet en PHP
- Gestion sécurisée des bases de données
- Contrôle d'accès et authentification
- Interface utilisateur dynamique
- Système de notifications
- Gestion d'erreurs et debugging

---

## 📞 Support

Pour toute question ou amélioration, consultez le code source ou ouvrez une issue sur le dépôt.

---

**Auteur :** saleck24  
**Licence :** Voir fichier LICENSE  
**Dernière mise à jour :** 2026
