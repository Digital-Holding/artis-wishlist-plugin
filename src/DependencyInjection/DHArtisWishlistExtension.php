<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class DHArtisWishlistExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        foreach ($config['view_classes'] as $view => $class) {
            $container->setParameter(sprintf('digital_holding_artis_wishlist_plugin.view.%s.class', $view), $class);
        }

        foreach ($config['request_classes'] as $request => $class) {
            $container->setParameter(sprintf('digital_holding_artis_wishlist_plugin.request.%s.class', $request), $class);
        }

        $loader->load('services.yml');
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }
}
