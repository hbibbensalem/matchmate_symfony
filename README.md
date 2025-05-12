 🎾MatchMate
MatchMate est une application desktop développée en Java avec JavaFX et Scene Builder.Elle a pour but de faciliter la gestion et l'organisation des activités sportives, spécifiquement pour les passionnés de sports de raquette tels que le tennis, le padel, et e pingpong.

📌 Description du projet
MatchMate est une application complète qui offre une expérience enrichie aux sportifs. Elle intègre un large éventail de fonctionnalités pour favoriser l'organisation, la participation et le suivi de l'activité sportive.
Fonctionnalités principales :
Authentification : Création de compte, connexion et gestion des rôles (ROLE_USER, RÔLE_NutrItionniste).
Gestion des événements : Création, modification et suppression d'événements sportifs
Gestion des matchs :  Engagement direct dans des matchs sportifs, avec gestion des participants et des matches .
Suivi alimentaire : Journal nutritionnel pour améliorer les performances
Boutique intégrée : Catalogue de produits sportifs avec panier d'achat
Système de réclamations : Envoi et gestion des réclamations
Espace administrateur : Tableau de bord centralisé pour gérer les utilisateurs, événements, produits, réclamations, etc.
Points forts :
Interface moderne : Design FXML créé avec Scene Builder 20.0.2
Expérience fluide : Application desktop performante
Sécurité : Gestion des accès et des permissions
🗂 Table des matières
Prérequis
Installation
Utilisation
Technologies utilisées
Contribution
Licence
⚙️ Prérequis
JDK 21 
JavaFX SDK 21 
Scene Builder 20.0.2 
MySQL 8.0+ 
🛠️ Installation
Cloner le dépôt
git clone https://github.com/votre-utilisateur/matchmate.git
cd matchmate
Configurer la base de données
Créer une base MySQL nommée matchmate
Configurer les paramètres
Éditer le fichier src/main/java/edu/pidev3a8/tools/myConnection.java avec vos identifiants MySQL:
db.url=jdbc:mysql://localhost:3306/matchmate
db.username=votre_utilisateur
db.password=votre_mot_de_passe
Importer le projet dans votre IDE
IntelliJ IDEA 
Configurer JavaFX SDK dans les modules du projet
🚀 Utilisation
Méthode 1 - Via votre IDE :
Ouvrez le projet dans IntelliJ
Localisez la classe principale : src/main/java/edu/pidev3a8/controllers/Home.java.
Exécutez le fichier avec Java 21


 Méthode 2 - En ligne de commande 
       mvn clean javafx:run
   3.   Créez un compte ou connectez-vous 
      4.  Accédez ensuite à votre tableau de bord principal.
Fonctionnalités
Création et participation aux événements et matchs.
Consultation du calendrier sportif.
Enregistrement et suivi alimentaire.
Accès à la boutique en ligne.
Envoi et gestion des réclamations.


💻 Technologies utilisées
Back-end
Java JDK 21 : Core de l'application
JavaFX 20.0.2 : Framework graphique moderne
JDBC : Connexion à la base de données
MySQL : Stockage des données
Front-end
FXML : Structure des interfaces (séparation logique/UI)
Scene Builder : Design visuel des composants
CSS JavaFX : Style personnalisé (même charte turquoise/orangé)
🤝 Contribution
Les contributions sont les bienvenues !
Forkez ce dépôt
Créez une branche :
git checkout -b feature/NouvelleFonctionnalité
Commitez vos modifications :
git commit -m "Ajout d'une nouvelle fonctionnalité"
Poussez vers la branche 
git push origin feature/NouvelleFonctionnalité
Ouvrez une Pull Request
📄 Licence
Ce projet est sous licence MIT - voir le fichier LICENSE pour plus de détails.

Merci pour votre intérêt pour MatchMate !
