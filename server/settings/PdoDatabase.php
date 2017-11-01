<?php namespace Settings;

//use Dotenv\Dotenv;
//use Limoncello\Application\Packages\PDO\PdoSettings;
//
///**
// * @package Settings
// */
//class PdoDatabase extends PdoSettings
//{
//    /**
//     * @inheritdoc
//     */
//    protected function getSettings(): array
//    {
//        (new Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])))->load();
//
//        return [
//
//                static::KEY_USER_NAME         => getenv('PDO_USER_NAME'),
//                static::KEY_PASSWORD          => getenv('PDO_USER_PASSWORD'),
//                static::KEY_CONNECTION_STRING => getenv('PDO_CONNECTION_STRING'),
//
//            ] + parent::getSettings();
//    }
//}
