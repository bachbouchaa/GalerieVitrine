
# Galerie Vitrine

## Introduction
Galerie Vitrine est une application web créée avec le framework Symfony, permettant de gérer une collection de tableaux de peinture. Les utilisateurs peuvent parcourir différentes collections d'art, consulter les détails de chaque tableau, et découvrir diverses périodes artistiques comme la Renaissance, l'Art Moderne, et l'Impressionnisme.

## Fonctionnalités
- Liste de toutes les collections de peintures.
- Vue détaillée de chaque collection.
- Vue détaillée de chaque tableau de peinture dans la collection.
- Application basée sur Symfony 6.

## Prérequis
Pour exécuter ce projet localement, vous devez avoir installé les éléments suivants :
- [PHP 8.0+](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Symfony CLI](https://symfony.com/download)
- Une base de données compatible (SQLite)

## Installation
Suivez ces étapes pour cloner et exécuter le projet sur votre machine :

1. **Cloner le Dépôt** :
   ```bash
   git clone https://github.com/bachbouchaa/GalerieVitrine.git
   cd GalerieVitrine
   ```

2. **Installer les Dépendances** :
   ```bash
   composer install
   ```

3. **Configurer la Base de Données** :
   - Copiez le fichier `.env` en `.env.local` et configurez vos informations de base de données :

     ```dotenv
     DATABASE_URL="sqlite:///%kernel.project_dir%/my_paintings.sqlite"
     ```

4. **Créer la Base de Données et le Schéma** :
   ```bash
   symfony console doctrine:database:create
   symfony console doctrine:schema:update --force
   ```

5. **Charger les Fixtures** :
   - Utilisez les fixtures fournies pour remplir la base de données avec des collections et des peintures d'exemple :

   ```bash
   symfony console doctrine:fixtures:load -n
   ```

6. **Lancer le Serveur de Développement** :
   ```bash
   symfony server:start
   ```
   Vous pouvez maintenant accéder à l'application en ouvrant `http://localhost:8000` dans votre navigateur.

## Structure des Entités
Le projet contient deux principales entités :
- **MyPaintingCollection** : Représente une collection de tableaux de peinture (par exemple, Renaissance, Moderne).
- **Painting** : Représente un tableau de peinture, avec des attributs comme le titre, l'artiste, l'année de création, la description, et le style.

### Relations
- **MyPaintingCollection** et **Painting** ont une relation **OneToMany**. Chaque collection peut contenir plusieurs tableaux.

## Routes Disponibles
- **`/`** : Liste de toutes les collections de peinture.
- **`/collections/{id}`** : Affiche les détails d'une collection spécifique.

### Exemple d'Utilisation des Routes
- Accéder à la liste des collections : `http://localhost:8000/`
- Afficher une collection particulière : `http://localhost:8000/painting/collection/{id}`


## Auteurs
- **Nermine Bacha** - Développeur principal.

## Remerciements
Un grand merci à tous ceux qui ont contribué au développement de Symfony et à la communauté open-source pour leur soutien.
