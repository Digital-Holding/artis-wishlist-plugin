<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\View\Wishlist;

class WishlistView
{
    /** @var int */
    public $id;

    /** @var string */
    public $token;

    /** @var array|WishlistProductView[] */
    public $wishlistProducts = [];
}
