# WEBTE2 Konfigurácia serveru

## Úvod 
Konfigurácia webového servera a "LEMP Stack-u" (Linux-Nginx-MySQL-PHP) pre predmet WEBTE2. Server je dostupný na verejnej IP adrese v tvare `147.175.105.XX`. Server má priradené aj doménové meno v tvare `nodeXX.webte.fei.stuba.sk` . Znaky **XX** v doménovom tvare adresy sú nahradené posledným číslom z IP adresy (môžu to byť 2 alebo 3 číslice).

V celom tomto návode je potrebné kľúčové slová **username** a **password** nahrádzať vlastným prihlasovacím menom (login) a heslom, **ktorými sa prihlasujete do AIS**.

**Nemeňte heslo priradené k vášmu kontu!**

Z Microsoft Teams si stiahnite dva súbory (`webte.fei.stuba.sk.key` a `webte_fei_stuba_sk.pem`), ktoré nájdete v adresári "Certifikáty" v skupine B-WEBTE2. Tieto súbory si skopírujte na server do adresára `/home/username` pomocou WinSCP, FileZilla alebo iného súborového manažéru.

## Softvér a verzie
- Ubuntu 22.04.3 LTS
- Nginx 
- PHP 8.3
- MySQL 8
- PhpMyAdmin

## Pripojenie k serveru pomocou SSH

Cez terminál/príkazový riadok/kozolu (CMD/Windows terminal/Bash/Terminal...) sa pripojte k svojmu pridelenému serveru. Namiesto `XX` bude **vaše posledné číslo z IP adresy**.

```sh
ssh username@147.175.105.XX
```


Násldedne budete vyzvaní zadať heslo. Pri prvom prihlásení sa zobrazí takéto, alebo podobné upozornenie:

```
The authenticity of host '147.175.105.XX (147.175.105.XX)' can't be established.
ED25519 key fingerprint is SHA256:lhGu321iNdaG+aoYfcIXf4qpJCIMkKDj49HTF1oqwic.
This host key is known by the following other names/addresses:
    ~/.ssh/known_hosts:36: [hashed name]
Are you sure you want to continue connecting (yes/no/[fingerprint])?
```

zadajte `yes`, stlačte enter a následne sa zobrazí výzva k zadaniu hesla. **Nemeňte heslo priradené k vášmu kontu!**

## Update systému
Po prihlásení systém aktualizujte. Príkaz `sudo` vyžaduje zadanie hesla. Použite vaše prihlasovacie heslo. 

```sh
sudo apt update && sudo apt -y upgrade
```

Ak sa po upgrade objaví táto notifikácia - *Pending kernel upgrade*:

![nginx](https://raw.githubusercontent.com/katarina02/webte2-installation/main/img/package_configuration_1.png)

stlačte Ok (klávesu enter).

V prípade, že sa po inštalácii zobrazí okno *Daemons using outdated libraries*:

![nginx](https://raw.githubusercontent.com/katarina02/webte2-installation/main/img/package_configuration_2.png)

nemeňte žiadne nastavenia a stlačte Ok (klávesu enter).

Po aktualizácii systému urobte reštart príkazom:
```sh
sudo reboot now
```

Reštart systému ukončí reláciu (prihlásenie), po niekoľkých sekundách treba znova nadviazať [ssh spojenie](#pripojenie-k-serveru-pomocou-ssh).

Pridanie repozitárov pre novšie verzie PHP a PhpMyAdmin.
```sh
sudo add-apt-repository ppa:ondrej/php
sudo add-apt-repository ppa:phpmyadmin/ppa
sudo apt update && sudo apt -y upgrade
```

## Nginx
Inštalácia balíkov webserveru `nginx`, textového editora `micro`.
```sh
sudo apt install nginx micro
```
> Poznámka: Miesto editora `micro` môžete používať aj iný editor, napríklad `nano`, ktorý je predinštalovaný.

Po navštívení IP adresy alebo `nodeXX.webte.fei.stuba.sk` by webový prehliadač mal zobrazovať:
![nginx](https://raw.githubusercontent.com/matej172/webte2-installation/main/img/nginx.png)

Pridanie používateľa do skupiny www-data:
```sh
sudo usermod -aG www-data $USER
```
Zmena sa prejaví až pri novej relácii - odhlásení a prihlásení. Odhlásiť sa z relácie je možné príkazom:
```sh
exit
```
Následne je možné znovu sa [prihlásiť cez ssh](#pripojenie-k-serveru-pomocou-ssh).
Po prihlásení a zadaní príkazu:

```sh
groups
```
by výstup mal vyzerať:
```sh
username sudo www-data
```

## MySQL
Inštalácia MySQL databázového serveru.
```sh
sudo apt install mysql-server
```

Spustenie skriptu na inicializáciu a zabezpečenie `mysql-server` databázového serveru:
```sh
sudo mysql_secure_installation
```
**POZOR! Počas inštalácie pozorne sledujte otázky a zadajte korektné odpovede!**

**Odpovede na otázky počas konfigurácie:**
- Setup validate password component? - **no** <!-- - Change the password for root? - **no** -->
- Remove anonymous user? - **yes**
- Disallow root login remotely? - **yes**
- Remove test database and access to it? - **no**
- Reload privilege tables now? - **yes**

Pripojenie ku MySQL konzole.
```sh
sudo mysql
```
Prompt sa zmení na `mysql>`
Vytvorenie nového používateľa pre prístup a srpávu databáz. Vytvorte si používateľa s rovnkým menom, s akým sa prihlasujete na server. Heslo si zvoľte iné ako `password` a odovzdajte ho do MS Teams - Úloha pre 1. cvičenie.
```sh
CREATE USER 'username'@'localhost' IDENTIFIED BY 'password';
```
Pridanie privilégií pre prácu s databázami:
```sh
GRANT ALL PRIVILEGES ON *.* TO 'username'@'localhost';
FLUSH PRIVILEGES;
```
Opustenie konzoly MySQL pomocou `Ctrl + d` alebo `exit`.

Pre kontrolu sa prihláste do MySQL konzoly pod novým používateľom (tým, ktorého ste práve vytvorili):
```sh
mysql -u username -p
```

## PHP
Inštalácia PHP 8.3:

```sh
sudo apt install php-fpm
```

Odpoveďou na príkaz `php -v` by malo byť
```sh
PHP 8.3.2-1+ubuntu22.04.1+deb.sury.org+1 (cli) (built: Jan 20 2024 14:16:40) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.3.2, Copyright (c) Zend Technologies
    with Zend OPcache v8.3.2-1+ubuntu22.04.1+deb.sury.org+1, Copyright (c), by Zend Technologies
```

### Vytvorenie Virtual host konfigurácie pre URL
Reťazec **XX** nahradiť prideleným číslom podľa URL

```sh
sudo micro /etc/nginx/sites-available/nodeXX.webte.fei.stuba.sk
```
 
> V editore `micro` zmeny v súbore uložíte kl. skratkou `Ctrl+s` a ukončíte kl. skratkou `Ctrl+q`. 

Do súboru vložiť obsah a zameniť režazec **XX** za posledný číselný segment priradenej IP adresy:

```sh
server {
       listen 80;
       listen [::]:80;

       server_name nodeXX.webte.fei.stuba.sk;

       access_log /var/log/nginx/access.log;
       error_log  /var/log/nginx/error.log info;

       root /var/www/nodeXX.webte.fei.stuba.sk;
       index index.html index.php;
       
       location / {
               try_files $uri $uri/ =404;
       }
       
       location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
       }
}
```

Vytvorenie symbolického odkazu súboru.
```sh
sudo ln -s /etc/nginx/sites-available/nodeXX.webte.fei.stuba.sk /etc/nginx/sites-enabled/
```

Po spustení príkazu ```sudo service nginx restart``` by mal webový prehliadač ukazovať  chybu 404.
### Vytvorenie adresára pre webový server
Skripty a súbory v tomto adresáre sa zobrazia po navštívení pridelenej domény.

Aby bolo možné vytvárať zápisy do adresára musí patriť skupine www-data a mať prístup na zápis pre skupinu.
```sh
cd /var
sudo chown -R www-data:www-data www/
sudo chmod g+w -R www/
```
Po zmene oprávnení je možné zapisovať do adresára ```/var/www``` bez sudo privilégií.
```sh
cd /var/www
mkdir nodeXX.webte.fei.stuba.sk
cd nodeXX.webte.fei.stuba.sk
vim index.php
```

Vytvorte jednoduchý PHP skript, napr.
```php
<?php
	echo "Hello world!"
?>
```
Po navštívení pridellenej URL by sa mala načítať prázdna stránka s textom _Hello world!_.

### SSL certifikát pre HTTPS

V domovskom adresári `/home/username` by ste mali mať dva dúbory z MS Teams. Ak nie, skopírujte si ich pomocou WinSCP, FileZilla a pod.:
- webte_fei_stuba_sk.pem
- webte.fei.stuba.sk.key

Tieto súbory presuňte nasledovne:
```sh
sudo mv /home/$USER/webte_fei_stuba_sk.pem /etc/ssl/certs/
sudo mv /home/$USER/webte.fei.stuba.sk.key /etc/ssl/private/
```

Zmeňte konfiguráciu Nginx v súbore ```/etc/nginx/sites-available/nodeXX.webte.fei.stuba.sk```

```sh
server {
       listen 80;
       listen [::]:80;

       server_name nodeXX.webte.fei.stuba.sk;

       rewrite ^ https://$server_name$request_uri? permanent;
}

server {
        listen 443 ssl;
        listen [::]:443 ssl;

        server_name nodeXX.webte.fei.stuba.sk;

        access_log /var/log/nginx/access.log;
        error_log  /var/log/nginx/error.log info;

        root /var/www/nodeXX.webte.fei.stuba.sk;
        index index.php index.html;

        ssl_certificate /etc/ssl/certs/webte_fei_stuba_sk.pem;
        ssl_certificate_key /etc/ssl/private/webte.fei.stuba.sk.key;

        location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        }
}
```

Reštartovať Nginx príkazom
```sh
sudo service nginx restart
```

Po navštívení pridelenej domény vo webovom prehliadači by sa mala stránka načítať prostredníctvom HTTPS protokolu. 

## PhpMyAdmin

Inštalácia GUI utility pre správu databázy cez prehliadač.

```sh
sudo apt install phpmyadmin
```

Po spustení inštalácie sa zobrazí séria okien s otázkami, nikde nič nevypĺňať, len stlačiť enter
![phpmyadmin_1](https://raw.githubusercontent.com/matej172/webte2-installation/main/img/phpmyadmin_1.png)
![phpmyadmin_2](https://raw.githubusercontent.com/matej172/webte2-installation/main/img/phpmyadmin_2.png)
![phpmyadmin_3](https://raw.githubusercontent.com/matej172/webte2-installation/main/img/phpmyadmin_3.png)

V prípade, že sa počas inštalácie zobrazí okno podobné tomuto

![nginx](https://raw.githubusercontent.com/katarina02/webte2-installation/main/img/package_configuration_3.png)

nemeňte žiadne nastavenia, len stlačte Ok a pokračujte ďalej.

Vytvoriť súbor ```/etc/nginx/snippets/phpmyadmin.conf``` a vložiť obsah:

```sh
location /phpmyadmin {
    root /usr/share/;
    index index.php index.html index.htm;
    location ~ ^/phpmyadmin/(.+\.php)$ {
        try_files $uri =404;
        root /usr/share/;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include /etc/nginx/fastcgi_params;
    }

    location ~* ^/phpmyadmin/(.+\.(jpg|jpeg|gif|css|png|js|ico|html|xml|txt))$ {
        root /usr/share/;
    }
}
```

Do konfiguračného súboru ```/etc/nginx/sites-available/nodeXX.webte.fei.stuba.sk ``` pridať riadok

```sh
include snippets/phpmyadmin.conf;
```

Finálny konfiguračný súbor bude vyzerať

```sh
server {
    listen 80;
    listen [::]:80;

    server_name nodeXX.webte.fei.stuba.sk;

    rewrite ^ https://$server_name$request_uri? permanent;
}

server {
    listen 443 ssl;
    listen [::]:443 ssl;

    server_name nodeXX.webte.fei.stuba.sk;

    access_log /var/log/nginx/access.log;
    error_log  /var/log/nginx/error.log info;

    root /var/www/nodeXX.webte.fei.stuba.sk;
    index index.php index.html;

    ssl on;
    ssl_certificate /etc/ssl/certs/webte_fei_stuba_sk.pem;
    ssl_certificate_key /etc/ssl/private/webte.fei.stuba.sk.key;

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
    }

    include snippets/phpmyadmin.conf;
}

```

Po reštarte nginx serveru príkazom ```sudo service nginx restart``` otvoriť stránku [https://nodeXX.webte.fei.stuba.sk/phpmyadmin](https://siteXX.webte.fei.stuba.sk/phpmyadmin). Úvodná obrazovka by mala vyzerať takto:

![phpmyadmin_5](https://raw.githubusercontent.com/matej172/webte2-installation/main/img/phpmyadmin_5.png)

Po zadaní prihlasovacích údajov zo sekcie [MySQL](#mysql) by sa mala zobraziť táto aplikácia

![phpmyadmin_6](https://raw.githubusercontent.com/matej172/webte2-installation/main/img/phpmyadmin_6.png)

## License

MIT
