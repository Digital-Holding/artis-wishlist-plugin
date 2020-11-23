<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\Handler\Wishlist;

use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use DH\ArtisWishlistPlugin\Command\Wishlist\AddProductVariantToWishlist;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

final class AddProductVariantToWishlistHandler
{
    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

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
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistRepositoryInterface $wishlistRepository,
        RepositoryInterface $wishlistProductRepository,
        WishlistFactoryInterface $wishlistFactory,
        WishlistProductFactoryInterface $wishlistProductFactory,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->productVariantRepository = $productVariantRepository;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistProductRepository = $wishlistProductRepository;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(AddProductVariantToWishlist $command): void
    {
        $wishlistToken = $command->wishlistToken();

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->productVariantRepository->findOneBy(['code' => $command->productVariantCode()]);
        Assert::notNull($productVariant, sprintf('Product Variant with code %s has not been found.', $command->productVariantCode()));

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

        $wishlistProductVariant = $this->wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant);
        $wishlist->addWishlistProduct($wishlistProductVariant);

        $this->wishlistRepository->add($wishlist);
    }
}
