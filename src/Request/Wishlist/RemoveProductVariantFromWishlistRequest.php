<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\Request\Wishlist;

use DH\ArtisWishlistPlugin\Command\Wishlist\RemoveProductVariantFromWishlist;
use Sylius\ShopApiPlugin\Command\CommandInterface;
use Sylius\ShopApiPlugin\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class RemoveProductVariantFromWishlistRequest implements RequestInterface
{
    /** @var string */
    protected $wishlistToken;

    /** @var string */
    protected $productVariantCode;

    public function __construct(
        Request $request
    ) {
        $this->wishlistToken = $request->attributes->get('token');
        $this->productVariantCode = $request->attributes->get('code');
    }

    public static function fromHttpRequest(Request $request): RequestInterface
    {
        return new self($request);
    }

    public function getCommand(): CommandInterface
    {
        return new RemoveProductVariantFromWishlist(
            $this->wishlistToken,
            $this->productVariantCode
        );
    }
}
