<?php

use App\Json\Validators\Rules\AppErrorCodes;
use Limoncello\Flute\Resources\Messages\En\Validation;

return Validation::getMessages() + [

        AppErrorCodes::IS_EMAIL => 'The value should be a valid email address.',

    ];
