<?php

use App\Web\Views;

return [
    Views::NOT_UNAUTHORIZED_PAGE => implode(DIRECTORY_SEPARATOR, ['pages', 'en', '401.html.twig']),
    Views::NOT_FORBIDDEN_PAGE    => implode(DIRECTORY_SEPARATOR, ['pages', 'en', '403.html.twig']),
    Views::NOT_FOUND_PAGE        => implode(DIRECTORY_SEPARATOR, ['pages', 'en', '404.html.twig']),

    Views::HOME_PAGE    => implode(DIRECTORY_SEPARATOR, ['pages', 'en', 'home.html.twig']),
    Views::SIGN_IN_PAGE => implode(DIRECTORY_SEPARATOR, ['pages', 'en', 'sign-in.html.twig']),

    Views::USERS_INDEX_PAGE => implode(DIRECTORY_SEPARATOR, ['pages', 'en', 'users.html.twig']),
    Views::USER_MODIFY_PAGE => implode(DIRECTORY_SEPARATOR, ['pages', 'en', 'user-modify.html.twig']),

    Views::ROLES_INDEX_PAGE => implode(DIRECTORY_SEPARATOR, ['pages', 'en', 'roles.html.twig']),
    Views::ROLE_MODIFY_PAGE => implode(DIRECTORY_SEPARATOR, ['pages', 'en', 'role-modify.html.twig']),
];
