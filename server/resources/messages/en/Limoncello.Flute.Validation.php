<?php

use App\Validation\ErrorCodes;
use Limoncello\Flute\Resources\Messages\En\Validation;

return Validation::getMessages() + [

        ErrorCodes::IS_EMAIL                           => 'The value should be a valid email address.',
        ErrorCodes::CONFIRMATION_SHOULD_MATCH_PASSWORD => 'Passwords should match.',

    ];
