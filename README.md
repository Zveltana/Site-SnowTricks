# The SITE SNOWTRICK project

***

## Introduction

This project is a study project, realized within the framework of the PHP/Symfony training of OpenClassrooms. You will
find a code designed with the Symfony framework.

This project is the realization of a collaborative website to make snowboarding known to the general public and help to
learn tricks.

The main goal is to capitalize on the content brought by the users in order to develop a rich content that will arouse
the interest of the users of the site.

Codacy's note :

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/46f46b5330244e3d8ebbed0cf78a4af8)](https://app.codacy.com/gh/Zveltana/Site-SnowTricks/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

## Require

This project requires PHP 8.1, MySQL or MariaDB, Composer 2.5, Symfony 6.2 and Doctrine. The CSS framework used is
Tailwinds.

## For start

To start, you need to clone the main branch.

For the project to work well on your machine you need to do :

-   `npm install` to use nodes modules
-   `composer install` to generate a composer.json file

To configure the database, edit the `.env.local` file with your database connection data.

```php
DATABASE_URL="mysql://!ChangeMe!@localhost:3306/!ChangeMe!?serverVersion=mariadb-10.4.27&charset=utf8"
```

To create the database you need to run this command :

```php
symfony console doctrine:database:create
```

Then perform the migrations located in the `./migrations/` folder at the root of the project. This will allow you to get
the same database as me. To perform the migrations use this command :

```php
symfony console doctrine:migrations:migrate
```

After creating your database, you can also inject a dataset by performing the following command :

```php
symfony console doctrine:fixtures:load
```

The command to use to build the CSS is :
``` npm run build ```

To visualize the project in local, thanks to localhost it will be necessary to carry out this command :
``` symfony server:start ```

## Sending emails

For the reception of emails I used mailer but you can use something else, you just have to change this the `MAILER_URL`
part in the root of the project in the `.env` file.

# The SnowTricks site is ready to be used !

***


The fonts come from google font. The logo is an in-house creation. Concerning the images of the figures, they were
recovered on internet to serve as example [Pixabay](https://pixabay.com/fr/).
