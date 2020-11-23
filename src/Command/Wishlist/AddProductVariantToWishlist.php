<?php declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\Command\Wishlist;

use Sylius\ShopApiPlugin\Command\CommandInterface;

class AddProductVariantToWishlist implements CommandInterface
{
    /** @var string */
    protected $wishlistToken;

    /** @var string */
    protected $productVariantCode;

    public function __construct(
        string $wishlistToken,
        string $productVariantCode
    ){
        $this->wishlistToken = $wishlistToken;
        $this->productVariantCode = $productVariantCode;
    }

    public function wishlistToken(): string
    {
        return $this->wishlistToken;
    }

    public function productVariantCode(): string
    {
        return $this->productVariantCode;
    }
}
