<?php namespace App\Commands;

use App\Container\SetUpConfig;
use App\Database\Models\ModelInterface;
use Composer\Script\Event;
use Config\Database as C;
use Limoncello\AppCache\CacheScript;
use Limoncello\ContainerLight\Container;
use Limoncello\Core\Contracts\Config\ConfigInterface;
use Limoncello\JsonApi\Contracts\Models\ModelSchemesInterface;
use Limoncello\JsonApi\Models\ModelSchemes;
use Limoncello\JsonApi\Models\RelationshipTypes;

/**
 * @package App
 */
class CacheModelSchemes extends CacheScript
{
    use SetUpConfig;

    /** Cached class name */
    const CACHED_CLASS = 'ModelSchemes';

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function cache(Event $event)
    {
        $modelSchemes = new ModelSchemes();
        $modelClasses = static::getModelClasses();

        self::buildModelSchemes($modelSchemes, $modelClasses);
        $schemes = $modelSchemes->getData();
        parent::cacheData($schemes, $event);
    }

    /**
     * @param ModelSchemesInterface $modelSchemes
     * @param array                 $modelClasses
     * @param bool                  $requireReverseRel
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function buildModelSchemes(
        ModelSchemesInterface $modelSchemes,
        array $modelClasses,
        $requireReverseRel = true
    ) {
        $registered    = [];
        $registerModel = function ($modelClass) use ($modelSchemes, &$registered, $requireReverseRel) {
            /** @var ModelInterface $modelClass */
            $modelSchemes->registerClass(
                $modelClass,
                $modelClass::getTableName(),
                $modelClass::getPrimaryKeyName(),
                $modelClass::getAttributeTypes(),
                $modelClass::getAttributeLengths()
            );

            $relationships = $modelClass::getRelationships();

            if (array_key_exists(RelationshipTypes::BELONGS_TO, $relationships) === true) {
                foreach ($relationships[RelationshipTypes::BELONGS_TO] as $relName => list($rClass, $fKey, $rRel)) {
                    /** @var string $rClass */
                    $modelSchemes->registerBelongsToOneRelationship($modelClass, $relName, $fKey, $rClass, $rRel);
                    $registered[(string)$modelClass][$relName] = true;
                    $registered[$rClass][$rRel]                = true;

                    // Sanity check. Every `belongs_to` should be paired with `has_many` on the other side.
                    /** @var ModelInterface $rClass */
                    $rRelationships   = $rClass::getRelationships();
                    $isRelationshipOk = $requireReverseRel === false ||
                        (isset($rRelationships[RelationshipTypes::HAS_MANY][$rRel]) === true &&
                            $rRelationships[RelationshipTypes::HAS_MANY][$rRel] === [$modelClass, $fKey, $relName]);
                    /** @var string $modelClass */

                    assert($isRelationshipOk, "`belongsTo` relationship `$relName` of class $modelClass " .
                        "should be paired with `hasMany` relationship.");
                }
            }

            if (array_key_exists(RelationshipTypes::HAS_MANY, $relationships) === true) {
                foreach ($relationships[RelationshipTypes::HAS_MANY] as $relName => list($rClass, $fKey, $rRel)) {
                    // Sanity check. Every `has_many` should be paired with `belongs_to` on the other side.
                    /** @var ModelInterface $rClass */
                    $rRelationships   = $rClass::getRelationships();
                    $isRelationshipOk = $requireReverseRel === false ||
                        (isset($rRelationships[RelationshipTypes::BELONGS_TO][$rRel]) === true &&
                            $rRelationships[RelationshipTypes::BELONGS_TO][$rRel] === [$modelClass, $fKey, $relName]);
                    /** @var string $modelClass */
                    assert($isRelationshipOk, "`hasMany` relationship `$relName` of class $modelClass " .
                        "should be paired with `belongsTo` relationship.");
                }
            }

            if (array_key_exists(RelationshipTypes::BELONGS_TO_MANY, $relationships) === true) {
                foreach ($relationships[RelationshipTypes::BELONGS_TO_MANY] as $relName => $data) {
                    if (isset($registered[(string)$modelClass][$relName]) === true) {
                        continue;
                    }
                    /** @var string $rClass */
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
        };

        array_map($registerModel, $modelClasses);
    }

    /**
     * @return string[]
     */
    private static function getModelClasses()
    {
        $container = new Container();
        self::setUpConfig($container);

        $dbConfig     = $container->get(ConfigInterface::class)->getConfig(C::class);
        $modelClasses = $dbConfig[C::MODELS_LIST];

        return $modelClasses;
    }
}
