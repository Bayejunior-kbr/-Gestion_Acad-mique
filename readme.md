# 🎓 Mini Projet – Gestion Académique

## 📌 Description

Application web développée en **PHP / MySQL** permettant la gestion académique d’un établissement scolaire.

Le système permet de gérer :

* 👨‍🎓 Les étudiants
* 🏫 Les classes
* 📚 Les modules
* 📊 Les évaluations (Devoir / Examen)
* 🏆 Les statistiques académiques

---

## ⚙️ Technologies utilisées

* PHP (PDO)
* MySQL
* HTML5 / CSS3
* Bootstrap 5
* JavaScript
* XAMPP (environnement local)

---

## 🗂️ Structure du Projet

```
mini_projet/
│
├── application/
│   ├── pages/
│   ├── traitement/
│   ├── includes/
│   └── index.php
│
└── database.sql
```

---

## 🧮 Calcul des Moyennes

La moyenne d’un module est calculée comme suit :

```
Moyenne Module = (Devoir × 0.4) + (Examen × 0.6)
```

La moyenne générale de l’étudiant :

```
Somme (Moyenne Module × Coefficient)
--------------------------------------
       Somme des Coefficients
```

---

## 📊 Fonctionnalités du Dashboard

* 🏆 Meilleur étudiant par classe
* 🏆 Meilleur étudiant par niveau
* 📈 Nombre d’étudiants par niveau
* 🏫 Nombre de classes par niveau
* ✅ Nombre d’étudiants admis
* 🔁 Nombre d’étudiants redoublants

---

## 🔍 Fonctionnalités principales

### 👨‍🎓 Gestion des étudiants

* Ajouter un étudiant
* Modifier un étudiant
* Supprimer un étudiant
* Filtrer par :

  * Module
  * Classe
  * Niveau
  * Nom

### 📚 Gestion des modules

* Ajouter un module
* Associer module à une classe
* Définir coefficient

### 📝 Gestion des évaluations

* Ajouter note devoir
* Ajouter note examen
* Calcul automatique des moyennes

---

## 🛠️ Installation

1. Cloner le projet :

```bash
https://github.com/Bayejunior-kbr/-Gestion_Acad-mique.git
```

2. Placer le dossier dans :

```
htdocs/ (si XAMPP)
```

3. Créer la base de données :

* Ouvrir phpMyAdmin
* Créer une base nommée : `academique`
* Importer le fichier `database.sql`

4. Configurer la connexion :

Dans :

```
application/traitement/config.php
```

Modifier :

```php
$pdo = new PDO("mysql:host=localhost;dbname=academique", "root", "");
```

5. Lancer le projet :

```
http://localhost/mini_projet/
```

---

## 🔐 Sécurité

* Utilisation de requêtes préparées (PDO)
* Validation des formulaires côté client (JavaScript)
* Protection contre injection SQL

---

## 📌 Améliorations Futures

* 📊 Ajout de graphiques statistiques
* 👤 Système d’authentification (Admin)
* 📄 Génération automatique de bulletins PDF
* 📥 Export Excel
* 🔎 Recherche avancée

---

## 👨‍💻 Auteur

Projet réalisé dans le cadre d’un mini projet académique.

---

## 📜 Licence

Projet à usage pédagogique uniquement.
