<?php namespace Settings;

use Faker\Generator;
use Limoncello\Application\Packages\Data\DataSettings;
use Psr\Container\ContainerInterface;

/**
 * @package Settings
 */
class Data extends DataSettings
{
    /**
     * @param ContainerInterface $container
     * @param string             $seedClass
     *
     * @return void
     */
    public static function resetFaker(ContainerInterface $container, string $seedClass)
    {
        /** @var Generator $faker */
        $faker = $container->get(Generator::class);
        $faker->seed(crc32($seedClass));
    }

    /**
     * @return array
     */
    protected function getSeedInit(): array
    {
        return [static::class, 'resetFaker'];
    }

    /**
     * @inheritdoc
     */
    protected function getModelsPath(): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Data', 'Models', '*.php']);
    }

    /**
     * @inheritdoc
     */
    protected function getMigrationsPath(): string
    {
        return realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Data', 'migrations.php']));
    }

    /**
     * @inheritdoc
     */
    protected function getSeedsPath(): string
    {
        return realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Data', 'seeds.php']));
    }
}
