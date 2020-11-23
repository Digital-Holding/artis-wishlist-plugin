<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\Factory\ViewFactory\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use DH\ArtisWishlistPlugin\View\Wishlist\WishlistProductView;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductVariantViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\Product\ProductViewFactoryInterface;

final class WishlistProductViewFactory implements WishlistProductViewFactoryInterface
{
    /** @var ProductViewFactoryInterface */
    private $productViewFactory;

    /** @var ProductVariantViewFactoryInterface */
    private $productVariantViewFactory;

    /** @var string */
    private $wishlistProductViewClass;

    public function __construct(
        ProductViewFactoryInterface $productViewFactory,
        ProductVariantViewFactoryInterface $productVariantViewFactory,
        string $wishlistProductViewClass
    )
    {
        $this->productViewFactory = $productViewFactory;
        $this->productVariantViewFactory = $productVariantViewFactory;
        $this->wishlistProductViewClass = $wishlistProductViewClass;
    }

    public function create(WishlistProductInterface $wishlistProduct, ChannelInterface $channel, string $locale): WishlistProductView
    {
        /** @var WishlistProductView $wishlistProductView */
        $wishlistProductView = new $this->wishlistProductViewClass();
        $wishlistProductView->id = $wishlistProduct->getId();
        $wishlistProductView->quantity = $wishlistProduct->getQuantity();

        if (null !== $wishlistProduct->getProduct()) {
            $wishlistProductView->product = $this->productViewFactory->create($wishlistProduct->getProduct(), $channel, $locale);
        }

        if (null !== $wishlistProduct->getVariant()) {
            $wishlistProductView->productVariant = $this->productVariantViewFactory->create($wishlistProduct->getVariant(), $channel, $locale);
        }

        return $wishlistProductView;
    }
}
