<?php namespace App\Database\Migrations;

/**
 * @package App
 */
class DateTimeFunctionMigration extends FunctionMigration
{
    /** @inheritdoc */
    const FUNCTION_NAME = 'jaDateTime';

    /** @inheritdoc */
    const FUNCTION_CREATE_STATEMENT = <<<EOT
CREATE FUNCTION `jaDateTime`(d DATETIME) RETURNS varchar(24)
DETERMINISTIC
  BEGIN
    DECLARE s VARCHAR(24);
    SET s = DATE_FORMAT(CONVERT_TZ(d, @@session.time_zone, '+00:00'), '%Y-%m-%dT%H:%i:%s+0000');
    RETURN s;
  END;
EOT;
}
