<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\ElasticaBundle\Configuration\Source\ContainerSource;
use FOS\ElasticaBundle\Configuration\Source\TemplateContainerSource;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('fos_elastica.config_source.container', ContainerSource::class)
        ->tag('fos_elastica.config_source')
        ->args([
            abstract_arg('index configs'),
        ]);

    $services->set('fos_elastica.config_source.template_container', TemplateContainerSource::class)
        ->tag('fos_elastica.config_source', ['source' => 'index_template'])
        ->args([
            abstract_arg('index configs'),
        ]);
};
