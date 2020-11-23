<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\Factory\ViewFactory\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use DH\ArtisWishlistPlugin\View\Wishlist\WishlistView;
use Sylius\Component\Core\Model\ChannelInterface;

interface WishlistViewFactoryInterface
{
    public function create(WishlistInterface $wishlist, ChannelInterface $channel, string $locale): WishlistView;
}
