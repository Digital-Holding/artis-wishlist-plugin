<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\Controller\Api\Wishlist;

use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use DH\ArtisWishlistPlugin\Factory\ViewFactory\Wishlist\WishlistViewFactoryInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Http\RequestBasedLocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class PickUpWishlistAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var WishlistRepositoryInterface */
    private $wishlistRepository;

    /** @var WishlistFactoryInterface */
    private $wishlistFactory;

    /** @var WishlistViewFactoryInterface */
    private $wishlistViewFactory;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var RequestBasedLocaleProviderInterface */
    private $requestBasedLocaleProvider;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        WishlistViewFactoryInterface $wishlistViewFactory,
        ChannelContextInterface $channelContext,
        RequestBasedLocaleProviderInterface $requestBasedLocaleProvider,
        TokenStorageInterface $tokenStorage
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistViewFactory = $wishlistViewFactory;
        $this->channelContext = $channelContext;
        $this->requestBasedLocaleProvider = $requestBasedLocaleProvider;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(Request $request): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $localeCode = $this->requestBasedLocaleProvider->getLocaleCode($request);

        $token = $this->tokenStorage->getToken();
        $user = is_object($token->getUser()) ? $token->getUser() : null;
        $wishlist = null;

        if (null !== $user) {
            $wishlist = $this->wishlistRepository->findByShopUser($user) ?
                $this->wishlistRepository->findByShopUser($user) :
                $this->wishlistFactory->createForUser($user)
            ;
        }

        if (null === $wishlist) {
            $wishlist = $this->wishlistFactory->createNew();
        }
        $this->wishlistRepository->add($wishlist);

        $wishlistView = $this->wishlistViewFactory->create($wishlist, $channel, $localeCode);

        return $this->viewHandler->handle(View::create($wishlistView, Response::HTTP_OK));
    }
}
