 ## FJKM

### How to install this project?
To get this project working on your local, you need to follow the following steps

- **Clone FJKM Election**
```bash
git clone git clone https://github.com/AG261/fjkm-election.git
```
- **Install dependencies**
```bash
composer install
```
-**Make migration**
php8.2 bin/console make:migration + php8.2 bin/console d:m:m
```

-**Create a new User**
```bash
php8.2 bin/console app:create-user admin@voting.mg Admin123456! admin
```

### Load fake data
 <p><i>only for applicant data</i></p>
```bash
    symfony console doctrine:fixtures:load
```