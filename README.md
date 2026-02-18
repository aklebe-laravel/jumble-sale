## JumbleSale

**Jumble Sale** is a lightweight laravel webshop was specially designed as a flea market
based on [Mercy App Scaffold](https://github.com/aklebe-laravel/mercy-scaffold).
It acts with offers and was primary made to provide a **auction house** or **jumble sale / garage sale / flea market**
Theoretical every user can be a customer and/or trader.

Modules based on ```nwidart/laravel-modules```, Livewire based on ```mhmiton/laravel-modules-livewire```
Documentation: https://laravelmodules.com/docs/v11/introduction

### Technical Features

- Up to date in base dependencies: Laravel 11, PHP 8.2, Bootstrap 5, npm 10.7
- [Module ACL](https://github.com/aklebe-laravel/acl-module) ACL Permissions and User groups stored in DB
- [Module DataTable](https://github.com/aklebe-laravel/data-table-module) Self-made datatables using Livewire, Alpine.js, Bootstrap 5
- [Module Form](https://github.com/aklebe-laravel/form-module) Use easy configured forms
- [Module DeployEnv](https://github.com/aklebe-laravel/deploy-env-module) Automatic Deployment/Terraforming with configured data like seeding
- Merging multiple composer.json files on different locations like /Modules/* and /Themes/*
- DB Config Settings
- User and Product Rating

Modules based on [nwidart/laravel-modules](https://github.com/nWidart/laravel-modules),
Livewire based on [mhmiton/laravel-modules-livewire](https://github.com/mhmiton/laravel-modules-livewire),
Themes based on [shipu/themevel](https://github.com/Shipu/themevel)

### Prerequisite
- Composer 2.9
- PHP 8.5 with extensions curl and zip (```sudo apt install php8.5-curl php8.5-zip```)
- *Recommended to use the docker method below*

### Installation

By default, 7 modules will be installed for this shop system:
SystemBase, DeployEnv, Acl, Form, DataTable, WebsiteBase, Market.
The modules SystemBase and DeployEnv are required for every project.


#### Install JumbleSale with Sail

1) Start Install:
   ```
   # get the repo
   git clone https://github.com/aklebe-laravel/jumble-sale.git .
   
   # install
   ./jumble-sale-install.sh
   ```

2) Adjust your ```.env```. At least this 2 vars:
   ```
   APP_DOMAIN=xxx
   COMPOSE_PROJECT_NAME="Unique_Project_Name"
   ```
   If you want running multiple projects at the same time, you have to use different ports for the generated docker services.
   So you need to change the ports below ```# ======= Docker stuff =======```

3) Use sail:
   ```
   # optionally create sail shorthand
   echo "alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'" >> ~/.bashrc
   
   # Install sail (will also adjust your .env if needed)
   # We recommened to chose mariaDb, redis and mailPit in the upcoming menu
   php artisan sail:install --devcontainer
   
   # from now use all commands prefixed with sail (like 'sail composer update') or enter the docker shell using 'sail shell'
   
   # start docker
   sail up -d
   ```

4) Optional SSL: We recommend to use SSL as an extra layer.
   1) Add a shared network docker
      ```
      docker network create proxy-gateway
      ```
   2) Create a local NPM (Nginx Proxy Manager) to handle your domains
      compose.yaml (in your custom npm project folder):
      ```
      services:
        npm:
          image: 'jc21/nginx-proxy-manager:latest'
          container_name: global-proxy
          restart: unless-stopped
          ports:
            - '80:80'   # public HTTP
            - '81:81'   # NPM Admin-Panel
            - '443:443' # public HTTPS
          volumes:
            - ./data:/data
            - ./letsencrypt:/etc/letsencrypt
          networks:
            - proxy-gateway
 
      networks:
        proxy-gateway:
        external: true
      ```
   3) Start:
      ```
      docker compose up -d
      ```
      Access: http://localhost:81
      Default-Login: admin@example.com / changeme

5) Optional decide module branches in ```config/mercy-dependencies.php``` - 2 vars at top
6) Start the first build
   ```
   # use in sail shell, press "u" in menu
   ./ui.sh
   ```


#### Install JumbleSale without Sail:

1) Change into your new application directory and checkout the Jumble Sale Application:
   ```
   git clone https://github.com/aklebe-laravel/jumble-sale.git .
   ```

2) Start Install:
   ```
   ./jumble-sale-install.sh
   ```
   Installation process can take several minutes and is working only once.
   The installation script will check ```APP_KEY``` in your ```.env```
   and some specific files to decide whether the installation will start or not.

3) Adjust your ```.env``` config file. At least notice the following:
   ```
   APP_DOMAIN=xxx
   DB_DATABASE=xxx
   DB_USERNAME=xxx
   DB_PASSWORD=xxx
   
   # default settings for required modules
   MODULE_DEPLOYENV_REQUIRE_MODULES_GIT="https://github.com/${{module_vendor_name}}/${{module_snake_name_git}}.git"
   MODULE_DEPLOYENV_REQUIRE_MODULES_DEFAULT_VENDOR="aklebe-laravel"

   # settings for your own modules
   MODULE_DEPLOYENV_MAKE_MODULE_AUTHOR_NAME="John Doe"
   MODULE_DEPLOYENV_MAKE_MODULE_AUTHOR_EMAIL="john.doe@localhost.test"
   MODULE_DEPLOYENV_MAKE_MODULE_COMPOSER_VENDOR_NAME="john-doe-laravel"
   ```
   
4) Optionally if you use sail/docker:
   - adjust the docker variables in your .env below ```# ======= Docker stuff =======```
   - optionally create sail shorthand like ```echo "alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'" >> ~/.bashrc```
   - install sail: ```php artisan sail:install --devcontainer```
     - sail will also adjust your .env if needed
   - from now use all commands prefixed with ```sail ``` (like ```sail composer update```) or enter the docker shell using ```sail shell```
   - start docker ```sail up -d```

5) update:
   ```
   # use in sail shell
   ./ui.sh
   ```
   Description: The following menu (by ./ui.sh) provides shorthand update (via git) of your installed and/or configured modules and themes based
   on the config ```config/mercy-dependencies.php```. While run this (implicit with --no-interaction), you do not
   need to care about ```composer update```, ```php artisan migrate```
   and ```php artisan deploy-env:terraform-modules```. So execute the following script and choose ```[u]``` for system

### Additional steps for your local development env

After install all thing:

1) Create ide-helper stuff
   ```
   ./ui.sh
   # press i
   ```
2) Copy your images for seedings to your **storage**: ```app/seeder/images/samples/products``` (check config ```seeders.users.media_items.image_storage_source_path```)
3) Data seeding to create users, products, medias, etc ... (feel free to adjust your config ```seeders.php```)
   ```
   php artisan module:seed Market
   ```
   Use the following to undo seeds (edit datetime part):
   ```
   php artisan market:manage model-* delete --since-created="2073-03-27 17:00"
   ```

### Updates / Rebuilds

#### (Pre-)Update (externals)

##### Troubleshooting in ui.sh

If you have trouble with autoloader or something like that by installed/created new modules:

1) ```./ui.sh``` internally runs ```php artisan deploy-env:update-bash``` at very first to update ```system.sh```
   based on your ```.env``` and also can read your configs. If you have errors from artisan but ```system.sh``` was properly generated before,
   you can run ```./ui.sh -n``` to avoid the update-bash. But don't do it by default.

2) If step 1) not works type ```./bash-scripts/scripts/deployment/raw-update.sh``` to update all modules and themes without using any php/artisan calls. 
   Also ```./bash-scripts/scripts/deployment/raw-update.sh {branch-name}``` could be an option for you.
   By using this script, your ```settings.sh``` should be prepared by any last call, or at least adjusted manually. 

3) If you not sure about your current ```settings.sh```, try run ```./ui.sh``` and choose ```[r]``` instead.

After that, the common update/build by ```./ui.sh``` should work fine.

#### Common Updates

To run all-in-one update type ```./ui.sh``` and press choose ```[u]``` in menu.

What it does for you:

1) update/require all modules
2) update/require all themes
3) do system update wich runs composer update
4) cache clearing, rebuild frontend etc.

#### Build

To build frontend via vite, refresh caches etc., type ```./ui.sh``` and press choose ```[b]``` in menu.

### Create Forms and/or DataTables

If you have modules ```Form``` and ```DataTable``` installed, you can easy create the needed files by the following
command:

```
php artisan deploy-env:former MyModule --classes=country,some-thing,AnyThing,nothing
```

This will create the datatable class, the form files and the eloquent model.
If one the files already exists, they will not be changed.
The result should look like this:

```
Modules/MyModule/app/Models/Country.php
Modules/MyModule/app/Forms/Country.php
Modules/MyModule/app/Http/Livewire/Form/Country.php
Modules/MyModule/app/Http/Livewire/DataTable/Country.php
Modules/MyModule/app/Models/SomeThing.php
Modules/MyModule/app/Forms/SomeThing.php
Modules/MyModule/app/Http/Livewire/Form/SomeThing.php
Modules/MyModule/app/Http/Livewire/DataTable/SomeThing.php
Modules/MyModule/app/Models/AnyThing.php
Modules/MyModule/app/Forms/AnyThing.php
Modules/MyModule/app/Http/Livewire/Form/AnyThing.php
Modules/MyModule/app/Http/Livewire/DataTable/AnyThing.php
Modules/MyModule/app/Models/Nothing.php
Modules/MyModule/app/Forms/Nothing.php
Modules/MyModule/app/Http/Livewire/Form/Nothing.php
Modules/MyModule/app/Http/Livewire/DataTable/Nothing.php
```

If you run it without ```--classes```, then for every eloquent model found, datatables and form will be created.

```
php artisan deploy-env:former MyModule
```

Note: more files could be created by 3rd party modules using the event ```DeployEnvFormer```.

### Files

Try to keep all stuff in modules and/or themes,
and keep the app files clean.

### Seeder

Module seeders can be configured in ```Modules/xxx/config/seeders.php```

To start the module market use the following

```
php artisan module:seed Market
```

Also see module specific seeder info!

#### Modules

Folder ```/Modules``` for modules with their own git repo
Enable/disable your installed modules in ```modules_statuses.json```

#### Themes

Folder ```/Themes``` for themes with their own git repo

### Mysql dump and restore

#### dump db

##### spatie backup

```
php artisan backup:run --only-db
```

##### native dump

To back up a db, you could run the following:

```
mysqldump -u homestead -p --databases jumble_sale_01 > db_jumble_sale_01.sql --add-drop-database
mysqldump -u homestead -p --databases jumble_sale_testing > db_jumble_sale_testing.sql --add-drop-database
```

#### Restore db

To import a sql script you could use DeployEnv

```
php artisan deploy-env:db-import db_jumble_sale_testing.sql
```

... or import a script by mysql

```
mysql -u root -p < db_jumble_sale_testing.sql
mysql -u root -p < db_jumble_sale_dusk.sql
```

### Testing

Ensure you have adjusted your ```.env.testing```. Especially the database part.

#### Testing all

```
php artisan test
```

#### Test all in parallel mode

```
php artisan test --parallel --processes=4
```

#### Test a specific file

Note: ```p``` is an alias for ```vendor/bin/phpunit "$@"``` with parameter -f : ```vendor/bin/pest "$@"```

```
p ./tests/Feature/CoreConfig.php
```

#### Test a class

Note: ```pf``` is an alias for ```vendor/bin/phpunit --filter "$@"``` with parameter
-f : ```vendor/bin/pest --filter "$@"```

```
pf CoreConfig
```

#### Dusk - Browser Testing

See: https://laravel.com/docs/11.x/dusk

**Important:**
You should run dusk on a separate system created for the dusk tests.
If you don't do it, notice the following:

**When running tests, Dusk will back up your .env file and rename your Dusk environment to .env.
Once the tests have completed, your .env file will be restored. That's why you should not run
other processes on the same system.**

##### Requirements Quick Guide:

chrome lib

```
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
sudo apt install ./google-chrome-stable_current_amd64.deb
```

dusk itself:
```
composer require laravel/dusk --dev
php artisan dusk:install
php artisan dusk:chrome-driver --detect
```

For problems running dusk:
```
chmod -R 0755 vendor/laravel/dusk/bin/
```


##### Run Dusk Tests

Dusk can be called with phpunit parameters. So if you want to call a specific class, use this format:

```
php artisan dusk --filter HomepageTest
```

to run whole dusk tests enter

```
php artisan dusk
```

#### Rendering Mailables

Test with designer permissions

```
https://jumble-sale-03-01.test/test-mail-template
```

### Useful tools

#### Redis

Check what's going on in redis cache

```
redis-cli monitor
```

### ide-helper

For all in one ide-helper stuff just run ```./ui.sh``` and press ```[i]```.
Otherwise, here is a quick overview:

Clearing ```bootstrap/compiled.php``` first by:

```
php artisan clear-compiled
```

#### Facades

Generate the facades:

```
php artisan clear-compiled
php artisan ide-helper:generate
```

#### Models

Using the --write-mixin (-M) option will only add a mixin tag to your Model file, writing the rest
in (```_ide_helper_models.php```). The class name will be different from the model, avoiding the IDE duplicate
annoyance.

```
php artisan ide-helper:models -M
```

#### Meta

Meta means resolve containers like ```app('events')->fire();```

```
php artisan ide-helper:meta
```

### Provided Console Commands

Generate a password hash. This command will ask you for a secret.
Optionally you can also enter a user id by --user=xxx to update the password in db.

```
password:generate
```

