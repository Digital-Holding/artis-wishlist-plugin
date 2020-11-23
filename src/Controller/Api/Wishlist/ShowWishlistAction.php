<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\Controller\Api\Wishlist;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use DH\ArtisWishlistPlugin\Factory\ViewFactory\Wishlist\WishlistViewFactoryInterface;
use DH\ArtisWishlistPlugin\View\Wishlist\WishlistView;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Http\RequestBasedLocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ShowWishlistAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ValidatorInterface */
    private $validator;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var RequestBasedLocaleProviderInterface */
    private $requestBasedLocaleProvider;

    /** @var WishlistRepositoryInterface */
    private $wishlistRepository;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var WishlistViewFactoryInterface */
    private $wishlistViewFactory;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ValidatorInterface $validator,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        RequestBasedLocaleProviderInterface $requestBasedLocaleProvider,
        WishlistRepositoryInterface $wishlistRepository,
        ChannelContextInterface $channelContext,
        WishlistViewFactoryInterface $wishlistViewFactory,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->viewHandler = $viewHandler;
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->requestBasedLocaleProvider = $requestBasedLocaleProvider;
        $this->wishlistRepository = $wishlistRepository;
        $this->channelContext = $channelContext;
        $this->wishlistViewFactory = $wishlistViewFactory;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(Request $request): Response
    {
        $wishlistToken = $request->attributes->get('token');

        $token = $this->tokenStorage->getToken();
        $user = is_object($token->getUser()) ? $token->getUser() : null;
        $wishlist = null;

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $localeCode = $this->requestBasedLocaleProvider->getLocaleCode($request);

        if (null !== $wishlistToken){
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistRepository->findByToken($wishlistToken);
        }

        if (null !== $user) {
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistRepository->findByShopUser($user);
        }

        $wishlistView = null !== $wishlist ? $this->buildFavoriteView($wishlist, $channel, $localeCode) : [];

        return $this->viewHandler->handle(View::create($wishlistView, Response::HTTP_OK));
    }

    private function buildFavoriteView(WishlistInterface $wishlist, ChannelInterface $channel, string $localeCode): WishlistView
    {
        return $this->wishlistViewFactory->create($wishlist, $channel, $localeCode);
    }
}
