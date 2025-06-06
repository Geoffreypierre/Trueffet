# 🎵 Trueffet

Trueffet est une plateforme web innovante permettant d’**acheter en ligne** et d’**écouter des vinyles** en streaming. Développée avec **Symfony**, **Twig** et **JavaScript**, l'application allie e-commerce, design moderne et plaisir musical vintage.

---

## 🚀 Fonctionnalités principales

- 🛒 Catalogue de vinyles avec achat en ligne sécurisé
- 🎧 Écoute en streaming des extraits de vinyles via api
- 🔍 Recherche par genre, artiste ou année
- 🧾 Gestion de panier et commandes
- 👤 Authentification et espace utilisateur
---

## Images
![desktop](https://github.com/user-attachments/assets/9ea0e57f-e609-4241-8826-eb7ab028640c)
![mobile](https://github.com/user-attachments/assets/8d384744-b425-4375-b743-c6e797854bed)
---

## 🛠️ Technologies utilisées

- **Symfony 6** – Framework PHP backend
- **Twig** – Moteur de templates pour le rendu côté serveur
- **JavaScript** – Interactivité et appels asynchrones
- **Doctrine ORM** – Gestion de la base de données
- **Webpack Encore** – Compilation des assets front-end
- **API Deezer** – Pour l'écoute des extraits
---

## 📦 Installation

1. Clonez le dépôt :
   ```bash
   git clone https://github.com/Geoffreypierre/trueffet.git
   cd trueffet
   #Installez les dépendances PHP
   composer install

   #Installez les dépendances front-end
   npm install
   npm run build

   #Configurez votre base de données dans .env
   DATABASE_URL="mysql://user:password@127.0.0.1:3306/trueffet_db"

   #Créez la base et les tables
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate

   #Lancer le serveur
   symfony server:start


