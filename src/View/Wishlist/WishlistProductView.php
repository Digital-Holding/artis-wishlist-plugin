<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\View\Wishlist;

use Sylius\ShopApiPlugin\View\Product\ProductVariantView;
use Sylius\ShopApiPlugin\View\Product\ProductView;

class WishlistProductView
{
    /** @var int */
    public $id;

    /** @var int */
    public $quantity;

    /** @var ProductView */
    public $product;

    /** @var ProductVariantView */
    public $productVariant;
}
