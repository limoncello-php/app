<?php

use App\Validation\ErrorCodes;
use Limoncello\Flute\Resources\Messages\En\Validation;

return Validation::getMessages() + [

        ErrorCodes::IS_EMAIL => 'The value should be a valid email address.',

    ];
