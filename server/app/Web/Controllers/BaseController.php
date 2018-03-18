<?php namespace App\Web\Controllers;

use App\Web\Views;
use Limoncello\Contracts\Application\ApplicationConfigurationInterface as A;
use Limoncello\Contracts\Application\CacheSettingsProviderInterface;
use Limoncello\Contracts\L10n\FormatterFactoryInterface;
use Limoncello\Contracts\Templates\TemplatesInterface;
use Limoncello\Flute\Contracts\Validation\FormValidatorFactoryInterface;
use Limoncello\Flute\Contracts\Validation\FormValidatorInterface;
use Limoncello\Flute\Http\Traits\DefaultControllerMethodsTrait;
use Limoncello\Templates\TwigTemplates;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Twig_Extensions_Extension_Text;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
abstract class BaseController
{
    use DefaultControllerMethodsTrait {
        defaultCreateApi as createApi;
        defaultCreateQueryParser as createQueryParser;
        defaultCreateParameterMapper as createParameterMapper;
    }

    /**
     * @param PsrContainerInterface $container
     * @param int                   $viewId
     * @param array                 $parameters
     * @param string                $viewsNamespace
     *
     * @return string
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function view(
        PsrContainerInterface $container,
        int $viewId,
        array $parameters = [],
        string $viewsNamespace = Views::NAMESPACE
    ): string {
        /** @var FormatterFactoryInterface $factory */
        $factory      = $container->get(FormatterFactoryInterface::class);
        $formatter    = $factory->createFormatter($viewsNamespace);
        $templateName = $formatter->formatMessage($viewId);

        /** @var TemplatesInterface $templates */
        $templates = $container->get(TemplatesInterface::class);
        if ($templates instanceof TwigTemplates) {
            $templates->getTwig()->addExtension(new Twig_Extensions_Extension_Text());
        }

        /** @var CacheSettingsProviderInterface $provider */
        $provider  = $container->get(CacheSettingsProviderInterface::class);
        $originUri = $provider->getApplicationConfiguration()[A::KEY_APP_ORIGIN_URI];

        $defaultParams = [
            '_origin_uri' => $originUri,
        ];

        $body = $templates->render($templateName, $parameters + $defaultParams);

        return $body;
    }

    /**
     * @param PsrContainerInterface $container
     * @param string                $rulesClass
     *
     * @return FormValidatorInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function createFormValidator(
        PsrContainerInterface $container,
        string $rulesClass
    ): FormValidatorInterface {
        /** @var FormValidatorFactoryInterface $validatorFactory */
        $validatorFactory = $container->get(FormValidatorFactoryInterface::class);
        $validator        = $validatorFactory->createValidator($rulesClass);

        return $validator;
    }
}
