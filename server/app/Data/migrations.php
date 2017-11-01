<?php

// Place here migrations in an order you want them to be executed.

return [
    \App\Data\Migrations\RolesMigration::class,
    \App\Data\Migrations\UsersMigration::class,
    \App\Data\Migrations\BoardsMigration::class,
    \App\Data\Migrations\PostsMigration::class,
    \App\Data\Migrations\CommentsMigration::class,

    \Limoncello\Passport\Package\PassportMigration::class,
    \App\Data\Migrations\RolesScopesMigration::class,
];
