<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\Handler\Wishlist;

use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use DH\ArtisWishlistPlugin\Command\Wishlist\RemoveProductVariantFromWishlist;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

final class RemoveProductVariantFromWishlistHandler
{
    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    /** @var WishlistRepositoryInterface */
    private $wishlistRepository;

    /** @var RepositoryInterface */
    private $wishlistProductRepository;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistRepositoryInterface $wishlistRepository,
        RepositoryInterface $wishlistProductRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->productVariantRepository = $productVariantRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistProductRepository = $wishlistProductRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(RemoveProductVariantFromWishlist $command): void
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->productVariantRepository->findOneBy(['code' => $command->productVariantCode()]);
        Assert::notNull($productVariant, sprintf('Product Variant with code %s has not been found.', $command->productVariantCode()));

        $token = $this->tokenStorage->getToken();
        $user = is_object($token->getUser()) ? $token->getUser() : null;

        $wishlist = $this->wishlistRepository->findByToken($command->wishlistToken());
        Assert::notNull($wishlist, 'Wishlist has not been found.');

        foreach ($wishlist->getWishlistProducts() as $wishlistProduct) {
            if ($productVariant === $wishlistProduct->getVariant()) {
                $this->wishlistProductRepository->remove($wishlistProduct);
            }
        }
    }
}
