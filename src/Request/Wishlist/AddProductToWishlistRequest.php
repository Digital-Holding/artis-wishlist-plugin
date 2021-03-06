<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\Request\Wishlist;

use DH\ArtisWishlistPlugin\Command\Wishlist\AddProductToWishlist;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class AddProductToWishlistRequest implements RequestInterface
{
    /** @var string */
    protected $wishlistToken;

    /** @var string */
    protected $productCode;

    public function __construct(
        Request $request
    ) {
        $this->wishlistToken = $request->attributes->get('token');
        $this->productCode = $request->attributes->get('code');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new AddProductToWishlist(
            $this->wishlistToken,
            $this->productCode
        );
    }
}
