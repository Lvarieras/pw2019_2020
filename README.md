# Pré-requis

Télécharger Git : https://git-scm.com/download/win  
Télécharger WAMP : https://sourceforge.net/projects/wampserver/files/  
Télécharger Composer : https://getcomposer.org/download/  
Télécharger VSCode : https://code.visualstudio.com/docs/?dv=win  
Extensions VSCode recommandées : 
* https://marketplace.visualstudio.com/items?itemName=MS-CEINTL.vscode-language-pack-fr
* https://marketplace.visualstudio.com/items?itemName=felixfbecker.php-intellisense
* https://marketplace.visualstudio.com/items?itemName=TheNouillet.symfony-vscode

# Commandes à ne taper qu'une seule fois pour initialiser le projet
```sh
git config --global credential.helper store
git config --global user.email "mon.e-mail.gitlab@gmail.com"
git config --global user.name "MonPseudoGitLab"

cd c:\wamp64\www\public_html\
git clone https://gitlab.com/Kiou/pw-2019_2020.git

cd pw-2019_2020
composer install
// sur Linux : 
cp .env .env.local
// sur Windows :
copy .env .env.local
```

Modifier le nouveau fichier .env.local la ligne :
DATABASE_URL=mysql://`db_user`:`db_password`@127.0.0.1:3306/projet_pw_2019_2020?serverVersion=5.7
avec vos identifiants PhpMyAdmin

Ensuite, modifier le fichier **C:\wamp64\bin\apache\apache2.4.37\conf\extra\httpd-vhosts.conf**
```sh
# Virtual Hosts
#
<VirtualHost *:80>
  ServerName localhost
  ServerAlias localhost
  DocumentRoot "${INSTALL_DIR}/www/public_html/pw-2019_2020/public"
  <Directory "${INSTALL_DIR}/www/public_html/pw-2019_2020/public/">
    Options +Indexes +Includes +FollowSymLinks +MultiViews
    AllowOverride All
    Require local
  </Directory>
</VirtualHost>
```
Enfin, il faut **redémarrer WAMP** pour que les modifications soient prises en compte.

# Commandes à taper pour mettre à jour les dépendances
```sh
cd c:\wamp64\www\public_html\pw-2019_2020
composer update
php bin\console doctrine:migrations:migrate
```


# Commandes à taper une seule fois (pour créer un nouveau projet symfony)
`ATTENTION : NE PAS TAPER CETTE COMMANDE`
```sh
cd c:\wamp64\www\public_html\
composer create-project symfony/website-skeleton pw-2019_2020
composer require annotations
composer require symfony/apache-pack
```