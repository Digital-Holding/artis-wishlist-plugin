<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\Factory\ViewFactory\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use DH\ArtisWishlistPlugin\View\Wishlist\WishlistView;
use Sylius\Component\Core\Model\ChannelInterface;

final class WishlistViewFactory implements WishlistViewFactoryInterface
{
    /** @var WishlistProductViewFactoryInterface */
    private $wishlistProductViewFactory;

    /** @var string */
    private $wishlistViewClass;

    public function __construct(
        WishlistProductViewFactoryInterface $wishlistProductViewFactory,
        string $wishlistViewClass
    )
    {
        $this->wishlistProductViewFactory = $wishlistProductViewFactory;
        $this->wishlistViewClass = $wishlistViewClass;
    }

    public function create(WishlistInterface $wishlist, ChannelInterface $channel, string $locale): WishlistView
    {
        /** @var WishlistView $wishlistView */
        $wishlistView = new $this->wishlistViewClass();
        $wishlistView->id = $wishlist->getId();
        $wishlistView->token = $wishlist->getToken();

        foreach ($wishlist->getWishlistProducts() as $wishlistProduct) {
            $wishlistView->wishlistProducts[] = $this->wishlistProductViewFactory->create($wishlistProduct, $channel, $locale);
        }

        return $wishlistView;
    }
}
