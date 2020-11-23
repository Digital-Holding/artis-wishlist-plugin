<?php

declare(strict_types=1);

namespace DH\ArtisWishlistPlugin\Controller\Api\Wishlist;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class AddProductVariantToWishlistAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var CommandProviderInterface */
    private $addProductVariantToWishlistCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CommandProviderInterface $addProductVariantToWishlistCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->addProductVariantToWishlistCommandProvider = $addProductVariantToWishlistCommandProvider;
    }

    public function __invoke(Request $request): Response
    {
        $validationResults = $this->addProductVariantToWishlistCommandProvider->validate($request);

        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(
                View::create($this->validationErrorViewFactory->create($validationResults),
                    Response::HTTP_BAD_REQUEST
                )
            );
        }

        $this->bus->dispatch($this->addProductVariantToWishlistCommandProvider->getCommand($request));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_CREATED));
    }
}
