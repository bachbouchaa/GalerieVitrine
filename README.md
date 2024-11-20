# 🎨 Système de Gestion des Œuvres - Galerie Vitrine

## 📄 Description
Bienvenue dans **Galerie Vitrine**, une application web conçue pour les passionnés d'art et les professionnels souhaitant organiser et explorer des collections d'œuvres d'art. Cette application propose des fonctionnalités pour créer, gérer et partager des galeries et des peintures.



L'objectif principal de cette application est de fournir une plateforme intuitive où chaque utilisateur peut :
- 🎨 **Créer** et **gérer** une PaintingCollection personnelles de paintings.
- 🖼️ **Organiser** ses paintings dans des Galleries.
- 🖼️ **Partager** ses galeries pour une visibilité publique ou les conserver privées.
- 🔍 **Explorer** les galeries publiques des autres utilisateurs pour découvrir de nouvelles inspirations.
---

## 📝 Nomenclature
- 🎨 **[Objet] = Painting** : Représente une œuvre d'art individuelle.
- 📦 **[Inventaire] = MyPaintingCollection** : Désigne une collection personnelle d'œuvres.
- 🖼️ **[Galerie] = Gallery** : Un sous-ensemble des collections d'un utilisateur, destiné à être mis en avant ou à garder en privé.

---

## 🔧 Modèle de données
- Ajout des entités **Member**, **MyPaintingCollection**, **Gallery**, et **Painting**.
- Gestion des relations :
  - 🔗 **OneToOne (1-1)** : **Member** <-> **MyPaintingCollection** 
  - 🔗 **OneToMany (1-N)** : **MyPaintingCollection** <-> **Painting**
  - 🔗 **ManyToMany (M-N)** : **Gallery** <-> **Painting**

L'application est développée avec **Symfony**, utilisant **Doctrine ORM** pour la persistance des données.

---

## 🛠️ Données de test et gestion des fixtures
### Fixtures disponibles :
- **UserFixtures :**
  - Deux comptes d'utilisateur sont générés :
    - **Nermine (ROLE_USER)** : Email `nermine@example.com` - Mot de passe `123456`.
    - **Khalil (ROLE_ADMIN)** : Email `khalil@example.com` - Mot de passe `123456`.
- **AppFixtures :**
  - 📦 Des `MyPaintingCollections` associées à chaque utilisateur.
  - 🎨 Plusieurs `Paintings` réalistes ajoutées dans chaque collection.
  - 🖼️ Des `Galleries` avec des thèmes variés, intégrant certaines peintures.

### Chargement des données de test :
Les données de test sont chargées via la commande suivante :
```bash
symfony console doctrine:fixtures:load -n
```

---

## 🚀 Fonctionnalités principales

### 🎨 1 | Gestion des Collections :
- **Consultation :** Chaque utilisateur peut voir sa propre collection (`MyPaintingCollection`) regroupant toutes ses peintures.
- **Ajout et Édition :** Les utilisateurs peuvent ajouter de nouvelles peintures à leur collection via un formulaire interactif.
- **Suppression :** Les peintures peuvent être facilement supprimées de la collection.

### 🖼️ 2 | Gestion des Galeries :
- **Création :** Organisez vos peintures en créant des galeries thématiques, publiques ou privées.
- **Consultation :**
  - Les galeries publiques sont accessibles à tous les utilisateurs connectés.
  - Les galeries privées ne sont visibles que par leur propriétaire.
  - Les administrateurs ont un accès illimité à toutes les galeries.
- **Ajout de peintures :** Les utilisateurs peuvent inclure des peintures spécifiques dans une galerie.
- **Édition et Suppression :** Modification des informations de la galerie ou suppression des galeries obsolètes.

### 🔒 3 | Authentification et autorisations :
- 👤 **Utilisateur standard (ROLE_USER) :**
  - Accès limité à sa propre collection et à ses galeries.
  - Peut explorer les galeries publiques des autres utilisateurs.
- 🛡️ **Administrateur (ROLE_ADMIN) :**
  - Accès total à toutes les données (Collections, Galeries, Peintures).
  - Peut gérer les galeries ou collections des autres utilisateurs.

### 🔗 4 | Navigation entre les entités :
- Navigation intuitive grâce à des boutons clairs permettant de passer d'une galerie à une peinture ou à une collection.

### 🎨 5 | Interface utilisateur :
- Interface moderne et responsive grâce à **Bootstrap**.
---

## 🛠️ Comptes de test disponibles :
Pour tester les fonctionnalités :
- **Utilisateur standard :**
  - Email : `nermine@example.com`
  - Mot de passe : `123456`
- **Administrateur :**
  - Email : `khalil@example.com`
  - Mot de passe : `123456`

---

**🎨 Galerie Vitrine** allie gestion fonctionnelle et esthétique pour offrir la meilleure expérience à ses utilisateurs.

