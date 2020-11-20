<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('digital_holding_artis_wishlist_plugin');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
