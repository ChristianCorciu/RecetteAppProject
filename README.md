# RecetteProject

Ce projet est une application web de gestion de recettes, développée avec Symfony, permettant aux utilisateurs de consulter et d'ajouter des recettes de cuisine avec des informations détaillées, y compris les valeurs nutritionnelles.

## Description

RecetteProject est une plateforme où les utilisateurs peuvent accéder à des recettes classées par catégories (entrées, plats, desserts). Chaque recette contient des informations comme le nom, la description, la difficulté, les étapes de préparation et des données nutritionnelles détaillées telles que les calories, les protéines, les graisses, les glucides et les fibres.

L'application expose une API permettant aux utilisateurs d'interagir avec les données des recettes (ajout, modification, suppression, consultation).

## Fonctionnalités

- **Consultation des recettes** : Visualisation des recettes avec leurs détails (ingrédients, étapes de préparation, informations nutritionnelles).
- **API REST** : Permet aux utilisateurs d'ajouter, modifier et supprimer des recettes via une API.
- **Informations nutritionnelles** : Calcul et affichage des valeurs nutritionnelles détaillées pour chaque recette.
- **Filtrage des recettes** : Possibilité de filtrer les recettes par catégorie.

## Technologies utilisées

- **Backend** : Symfony 7 avec API Platform
- **Base de données** : MySQL
- **Frontend** : Twig pour les templates HTML
- **Gestion de la sécurité** : Authentification avec Symfony Security
- **API externe** : Spoonacular pour les informations nutritionnelles
- **HTTP Client** : GuzzleHttp pour effectuer des requêtes HTTP vers des API externes

## Prérequis

Avant de commencer, assurez-vous d'avoir installé les éléments suivants sur votre machine :

- [PHP 8.0+](https://www.php.net/downloads.php)
- [Symfony 7](https://symfony.com/download)
- [MySQL](https://dev.mysql.com/downloads/)
- [Composer](https://getcomposer.org/)

## Installation

1. Clonez le dépôt du projet :
   ```bash
   git clone https://github.com/yourusername/RecetteProject.git
   cd RecetteProject
