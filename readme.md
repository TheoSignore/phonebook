# Annuaire intranet - Projet Movianto Phonebook

__Situation initiale:__

La seule chose faisant office d'annuaire est un fichier excel.
Les données de l'annuaire Active Directory de l'entreprise ne sont pas accessibles à tous.

__Ojectif:__
* Permettre à chaque collaborateur d'accéder aux coordonnées de chacun via un site intranet avec différent filtre de recherche: le pays, le site, le service, la fonction et le nom et le prénom.
* Permettre de récupérer un fichier excel contenant toutes les informations de la recherche

__Ressources à disposition:__
* Serveur Apache2
* PHP
* Base de donnée MySQL
* Identifiant de connexion à l'annuaire Active Directory de l'entreprise

__Langages utilisés:__
* PHP (+ HTML & CSS)
* Javascript Asynchrone (AJAX)


## Fonctionnement

Un script PHP récupère les informations dans l'Active Directory de l'entreprise afin de les ajouter dans la base de donnée MySQL, ou de mettre à jour les informations déjà présente dans la base de donnée MySQL.

Le site web utilise ensuite l'AJAX afin de transmettre des requêtes XHTTP à des scripts PHP présent sur le serveur, qui traitent et renvoi les informations, permettant un affichage dynamique sans recharger de page.

![schéma de fonctionnement](https://github.com/TheoSignore/phonebook/documents/schema.png)

## Fonctionnalité
* Connexion administrateur
	* ajout de pays et de site
	* exécuter le script de synchonisation
* Mémorisation du pays de l'utilisateur
* Changement des identifiants du compte Active Directory


## Problèmes rencontrés

### Absence de connaissance

Personne dans le service informatique n'avait de connaissance en PHP, AJAX et web design.
J'ai donc tout fait par moi même avec ce que j'avais appris durant ma première année et avec ce que je trouvait sur internet.
C'est la raison pour laquelle le **site n'a pu être achevé** car présentant trop de dysfonctionnement, bien que la majorité des fonctions furent réalisées.
Vous pourrez ainsi apercevoir de nombreux d'antipatron de programmation dans l'ensemble du projet.

### Une dénomination dans l'Active Directory

Un script PHP récupère les informations dans l'Active Directory, grâce à une requête LDAP.
Malgré de nombreux efforts et un suivis à la lettre de différents tutoriel sur internet, ma requête LDAP ne fonctionnait pas: la connexion au serveur s'effectuait sans problème mais le filtre de ma requête LDAP restait invalide.
Après avoir longtemps chercher et fulminé, la réponse me vient: des parenthèses.
En effet, l'Unité d'Organisation (*Organisation Unit* OU, en anglais), autrement dit, le dossier dans lequel j'effectuait ma requête possède des parenthèse dans son nom.
J'étais habitué à ne pas voir de caractère spéciaux dans les noms de fichier pour des raisons évidente d'encodage et de compatibilité, ainsi donc j'ai pensé, voyant les parenthèse dans le nom du dossier, que cela ne poserait pas de problème.
Cependant, le filtre de ma requête contenant le nom de ce dossier, et donc, des parenthèse, le PHP générait une erreur: le filtre était invalide.

__La solution:__ L'utilisation de la fonction php `str_replace()` pour remplacer les `(` et `)` par des `\(` et `\)` dans le filtre, car les antislashs permettent de faire passer le caratère qui suit pour du simple texte.