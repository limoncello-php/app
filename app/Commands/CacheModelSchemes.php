<?php namespace App\Commands;

use App\Database\Models\Model;
use Composer\Script\Event;
use Config\Config;
use Config\ConfigInterface;
use Limoncello\AppCache\CacheScript;
use Limoncello\JsonApi\Contracts\Config\JsonApiConfigInterface;
use Limoncello\Models\Contracts\ModelSchemesInterface;
use Limoncello\Models\RelationshipTypes;
use Limoncello\Models\ModelSchemes;

/**
 * @package App
 */
class CacheModelSchemes extends CacheScript
{
    /** Cached class name */
    const CACHED_CLASS = 'ModelSchemes';

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function cache(Event $event)
    {
        $config        = new Config();
        $jsonApiConfig = $config->getConfig()[ConfigInterface::KEY_JSON_API];

        $modelSchemes = new ModelSchemes();
        $modelClasses = array_keys($jsonApiConfig[JsonApiConfigInterface::KEY_MODEL_TO_SCHEMA_MAP]);
        self::buildModelSchemes($modelSchemes, $modelClasses);
        $schemes = $modelSchemes->getData();
        parent::cacheData($schemes, $event);
    }

    /**
     * @param ModelSchemesInterface $modelSchemes
     * @param array                 $modelClasses
     */
    public static function buildModelSchemes(ModelSchemesInterface $modelSchemes, array $modelClasses)
    {
        $registered = [];
        foreach ($modelClasses as $modelClass) {
            /** @var Model $modelClass */
            $modelSchemes->registerClass(
                $modelClass,
                $modelClass::TABLE_NAME,
                $modelClass::FIELD_ID,
                $modelClass::getAttributeTypes(),
                $modelClass::getAttributeLengths()
            );

            $relationships = $modelClass::getRelationships();

            if (array_key_exists(RelationshipTypes::BELONGS_TO, $relationships) === true) {
                foreach ($relationships[RelationshipTypes::BELONGS_TO] as $relName => list($rClass, $fKey, $rRel)) {
                    if (isset($registered[(string)$modelClass][$relName]) === true) {
                        continue;
                    }
                    $modelSchemes->registerBelongsToOneRelationship($modelClass, $relName, $fKey, $rClass, $rRel);
                    $registered[(string)$modelClass][$relName] = true;
                    $registered[$rClass][$rRel]                = true;
                }
            }

            if (array_key_exists(RelationshipTypes::BELONGS_TO_MANY, $relationships) === true) {
                foreach ($relationships[RelationshipTypes::BELONGS_TO_MANY] as $relName => $data) {
                    if (isset($registered[(string)$modelClass][$relName]) === true) {
                        continue;
                    }
                    list($rClass, $iTable, $fKeyPrimary, $fKeySecondary, $rRel) = $data;
                    $modelSchemes->registerBelongsToManyRelationship(
                        $modelClass,
                        $relName,
                        $iTable,
                        $fKeyPrimary,
                        $fKeySecondary,
                        $rClass,
                        $rRel
                    );
                    $registered[(string)$modelClass][$relName] = true;
                    $registered[$rClass][$rRel]                = true;
                }
            }
        }
    }
}
