<?php namespace App\Container;

use Limoncello\Application\Contracts\Csrf\CsrfTokenGeneratorInterface;
use Limoncello\Application\Packages\Csrf\CsrfSettings;
use Limoncello\Contracts\Application\ContainerConfiguratorInterface;
use Limoncello\Contracts\Container\ContainerInterface as LimoncelloContainerInterface;
use Limoncello\Contracts\Settings\SettingsProviderInterface;
use Limoncello\Contracts\Templates\TemplatesInterface;
use Limoncello\Templates\Package\TemplatesSettings as C;
use Limoncello\Templates\TwigTemplates;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Twig_Extensions_Extension_Text;
use Twig_Function;

/**
 * @package App\Container
 */
class TwigConfigurator implements ContainerConfiguratorInterface
{
    /** @var callable */
    const CONFIGURATOR = [self::class, self::CONTAINER_METHOD_NAME];

    /**
     * @inheritdoc
     */
    public static function configureContainer(LimoncelloContainerInterface $container): void
    {
        $container[TemplatesInterface::class] = function (PsrContainerInterface $container): TemplatesInterface {
            /** @var SettingsProviderInterface $provider */
            $provider = $container->get(SettingsProviderInterface::class);

            $settings  = $provider->get(C::class);
            $templates = new TwigTemplates(
                $settings[C::KEY_APP_ROOT_FOLDER],
                $settings[C::KEY_TEMPLATES_FOLDER],
                $settings[C::KEY_CACHE_FOLDER] ?? null,
                $settings[C::KEY_IS_DEBUG] ?? false,
                $settings[C::KEY_IS_AUTO_RELOAD] ?? false
            );

            $templates->getTwig()->addExtension(new Twig_Extensions_Extension_Text());

            $templates->getTwig()->addFunction(new Twig_Function(
                'csrf',
                function () use ($container, $provider): string {
                    [CsrfSettings::HTTP_REQUEST_CSRF_TOKEN_KEY => $key] = $provider->get(CsrfSettings::class);

                    /** @var CsrfTokenGeneratorInterface $generator */
                    $generator = $container->get(CsrfTokenGeneratorInterface::class);
                    $token     = $generator->create();

                    $result = '<input type="hidden" name="' . $key . '" value="' . $token . '">';

                    return $result;
                },
                ['is_safe' => ['html']]
            ));

            $templates->getTwig()->addFunction(new Twig_Function(
                'csrf_name',
                function () use ($container, $provider): string {
                    [CsrfSettings::HTTP_REQUEST_CSRF_TOKEN_KEY => $key] = $provider->get(CsrfSettings::class);

                    return $key;
                },
                ['is_safe' => ['html']]
            ));

            $templates->getTwig()->addFunction(new Twig_Function(
                'csrf_value',
                function () use ($container, $provider): string {
                    /** @var CsrfTokenGeneratorInterface $generator */
                    $generator = $container->get(CsrfTokenGeneratorInterface::class);
                    $token     = $generator->create();

                    return $token;
                },
                ['is_safe' => ['html']]
            ));

            return $templates;
        };
    }
}
