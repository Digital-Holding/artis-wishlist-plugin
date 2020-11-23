<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\Handler\Wishlist;

use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use DH\ArtisWishlistPlugin\Command\Wishlist\RemoveProductFromWishlist;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

final class RemoveProductFromWishlistHandler
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var WishlistRepositoryInterface */
    private $wishlistRepository;

    /** @var RepositoryInterface */
    private $wishlistProductRepository;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        WishlistRepositoryInterface $wishlistRepository,
        RepositoryInterface $wishlistProductRepository,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->productRepository = $productRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistProductRepository = $wishlistProductRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(RemoveProductFromWishlist $command): void
    {
        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneBy(['code' => $command->productCode()]);
        Assert::notNull($product, sprintf('Product with code %s has not been found.', $command->productCode()));

        $token = $this->tokenStorage->getToken();
        $user = is_object($token->getUser()) ? $token->getUser() : null;

        $wishlist = $this->wishlistRepository->findByToken($command->wishlistToken());
        Assert::notNull($wishlist, 'Wishlist has not been found.');

        foreach ($wishlist->getWishlistProducts() as $wishlistProduct) {
            if ($product === $wishlistProduct->getProduct()) {
                $this->wishlistProductRepository->remove($wishlistProduct);
            }
        }
    }
}
