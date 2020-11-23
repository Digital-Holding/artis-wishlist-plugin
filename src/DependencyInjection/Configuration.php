<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\DependencyInjection;

use DH\ArtisWishlistPlugin\Request\Wishlist\AddProductToWishlistRequest;
use DH\ArtisWishlistPlugin\Request\Wishlist\AddProductVariantToWishlistRequest;
use DH\ArtisWishlistPlugin\Request\Wishlist\RemoveProductFromWishlistRequest;
use DH\ArtisWishlistPlugin\Request\Wishlist\RemoveProductVariantFromWishlistRequest;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use DH\ArtisWishlistPlugin\View;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('digital_holding_artis_wishlist_plugin');
        $rootNode = $treeBuilder->getRootNode();

        $this->buildViewClassesNode($rootNode);
        $this->buildRequestClassesNode($rootNode);

        return $treeBuilder;
    }

    private function buildViewClassesNode(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('view_classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('wishlist')->defaultValue(View\Wishlist\WishlistView::class)->end()
                        ->scalarNode('wishlist_product')->defaultValue(View\Wishlist\WishlistProductView::class)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function buildRequestClassesNode(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('request_classes')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('add_product_to_wishlist')->defaultValue(AddProductToWishlistRequest::class)->end()
                        ->scalarNode('add_product_variant_to_wishlist')->defaultValue(AddProductVariantToWishlistRequest::class)->end()
                        ->scalarNode('remove_product_from_wishlist')->defaultValue(RemoveProductFromWishlistRequest::class)->end()
                        ->scalarNode('remove_product_variant_from_wishlist')->defaultValue(RemoveProductVariantFromWishlistRequest::class)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
