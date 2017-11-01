<?php

use App\Http\L10n\Views;

return [
    Views::NOT_FOUND_PAGE => implode(DIRECTORY_SEPARATOR, ['pages', 'en', '404.html.twig']),
    Views::BOARDS_PAGE    => implode(DIRECTORY_SEPARATOR, ['pages', 'en', 'boards.html.twig']),
    Views::BOARD_PAGE     => implode(DIRECTORY_SEPARATOR, ['pages', 'en', 'board.html.twig']),
    Views::POST_PAGE      => implode(DIRECTORY_SEPARATOR, ['pages', 'en', 'post.html.twig']),
];
