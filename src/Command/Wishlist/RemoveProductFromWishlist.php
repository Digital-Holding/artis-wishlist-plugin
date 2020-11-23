<?php declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\Command\Wishlist;

use Sylius\ShopApiPlugin\Command\CommandInterface;

class RemoveProductFromWishlist implements CommandInterface
{
    /** @var string */
    protected $wishlistToken;

    /** @var string */
    protected $productCode;

    public function __construct(
        string $wishlistToken,
        string $productCode
    ){
        $this->wishlistToken = $wishlistToken;
        $this->productCode = $productCode;
    }

    public function wishlistToken(): string
    {
        return $this->wishlistToken;
    }

    public function productCode(): string
    {
        return $this->productCode;
    }
}
