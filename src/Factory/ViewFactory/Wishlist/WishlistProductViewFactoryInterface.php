<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\Factory\ViewFactory\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use DH\ArtisWishlistPlugin\View\Wishlist\WishlistProductView;
use Sylius\Component\Core\Model\ChannelInterface;

interface WishlistProductViewFactoryInterface
{
    public function create(WishlistProductInterface $wishlistProduct, ChannelInterface $channel, string $locale): WishlistProductView;
}
