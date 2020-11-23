<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\Handler\Wishlist;

use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use DH\ArtisWishlistPlugin\Command\Wishlist\AddProductToWishlist;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

final class AddProductToWishlistHandler
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var WishlistRepositoryInterface */
    private $wishlistRepository;

    /** @var RepositoryInterface */
    private $wishlistProductRepository;

    /** @var WishlistFactoryInterface */
    private $wishlistFactory;

    /** @var WishlistProductFactoryInterface */
    private $wishlistProductFactory;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        WishlistRepositoryInterface $wishlistRepository,
        RepositoryInterface $wishlistProductRepository,
        WishlistFactoryInterface $wishlistFactory,
        WishlistProductFactoryInterface $wishlistProductFactory,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->productRepository = $productRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistProductRepository = $wishlistProductRepository;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(AddProductToWishlist $command): void
    {
        $wishlistToken = $command->wishlistToken();

        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneBy(['code' => $command->productCode()]);
        Assert::notNull($product, sprintf('Product with code %s has not been found.', $command->productCode()));

        $token = $this->tokenStorage->getToken();
        $user = is_object($token->getUser()) ? $token->getUser() : null;

        $wishlist = $this->wishlistRepository->findByToken($wishlistToken);

        if (null === $wishlist && null === $user) {
            $wishlist =  $this->wishlistFactory->createNew();
        }

        if (null === $wishlist && !$user instanceof ShopUserInterface) {
            $wishlist = $this->wishlistRepository->findByToken($wishlistToken) ?
                $this->wishlistRepository->findByToken($wishlistToken) :
                $this->wishlistFactory->createNew();
        }

        if ($user instanceof ShopUserInterface) {
            $wishlist = $this->wishlistRepository->findByShopUser($user) ?
                $this->wishlistRepository->findByShopUser($user) :
                $this->wishlistFactory->createForUser($user);
        }

        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);
        $wishlist->addWishlistProduct($wishlistProduct);

        $this->wishlistRepository->add($wishlist);
    }
}
