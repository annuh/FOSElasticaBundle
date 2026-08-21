<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FOS\ElasticaBundle\Configuration\ConfigManager;
use FOS\ElasticaBundle\DataCollector\ElasticaDataCollector;
use FOS\ElasticaBundle\Elastica\Client;
use FOS\ElasticaBundle\Index\MappingBuilder;
use FOS\ElasticaBundle\Logger\ElasticaLogger;
use FOS\ElasticaBundle\Subscriber\PaginateElasticaQuerySubscriber;
use Symfony\Component\PropertyAccess\PropertyAccessor;

return static function (ContainerConfigurator $container): void {
    $parameters = $container->parameters();

    $parameters->set('fos_elastica.property_accessor.magicCall', '0');
    $parameters->set('fos_elastica.property_accessor.throwExceptionOnInvalidIndex', '0');

    $services = $container->services();

    $services->set('fos_elastica.client_prototype', Client::class)
        ->abstract()
        ->args([
            abstract_arg('configuration'),
            abstract_arg('callback'),
        ])
        ->call('setStopwatch', [
            service('debug.stopwatch')->nullOnInvalid(),
        ])
        ->call('setEventDispatcher', [
            service('event_dispatcher')->nullOnInvalid(),
        ]);

    $services->set('fos_elastica.config_manager', ConfigManager::class)
        ->args([
            abstract_arg('collection of SourceInterface services'),
        ]);

    $services->alias(ConfigManager::class, 'fos_elastica.config_manager');

    $services->set('fos_elastica.config_manager.index_templates', ConfigManager::class)
        ->args([
            abstract_arg('collection of SourceInterface services'),
        ]);

    $services->set('fos_elastica.data_collector', ElasticaDataCollector::class)
        ->tag('data_collector', [
            'template' => '@FOSElastica/Collector/elastica.html.twig',
            'id' => 'elastica',
        ])
        ->args([
            service('fos_elastica.logger'),
        ]);

    $services->set('fos_elastica.paginator.subscriber', PaginateElasticaQuerySubscriber::class)
        ->tag('kernel.event_subscriber')
        ->args([
            service('request_stack'),
        ]);

    $services->set('fos_elastica.logger', ElasticaLogger::class)
        ->tag('monolog.logger', ['channel' => 'elastica'])
        ->args([
            service('logger')->nullOnInvalid(),
            param('kernel.debug'),
        ]);

    $services->set('fos_elastica.mapping_builder', MappingBuilder::class)
        ->args([
            service('event_dispatcher'),
        ]);

    $services->set('fos_elastica.property_accessor', PropertyAccessor::class)
        ->args([
            param('fos_elastica.property_accessor.magicCall'),
            param('fos_elastica.property_accessor.throwExceptionOnInvalidIndex'),
        ]);
};
