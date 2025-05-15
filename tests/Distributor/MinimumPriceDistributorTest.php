<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Component\Core\Distributor;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Distributor\MinimumPriceDistributor;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class MinimumPriceDistributorTest extends TestCase
{
    private MockObject&ProportionalIntegerDistributorInterface $proportionalIntegerDistributor;

    private ChannelInterface&MockObject $channel;

    private MockObject&OrderItemInterface $tshirt;

    private MockObject&ProductVariantInterface $tshirtVariant;

    private ChannelPricingInterface&MockObject $tshirtVariantChannelPricing;

    private MinimumPriceDistributor $distributor;

    protected function setUp(): void
    {
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->tshirt = $this->createMock(OrderItemInterface::class);
        $this->tshirtVariant = $this->createMock(ProductVariantInterface::class);
        $this->tshirtVariantChannelPricing = $this->createMock(ChannelPricingInterface::class);
        $this->proportionalIntegerDistributor = $this->createMock(ProportionalIntegerDistributorInterface::class);
        $this->distributor = new MinimumPriceDistributor($this->proportionalIntegerDistributor);
    }

    public function shouldDistributePromotionTakingIntoAccountMinimumPrice()
    {
        $book = $this->createMock(OrderItemInterface::class);
        $bookVariant = $this->createMock(ProductVariantInterface::class);
        $bookVariantChannelPricing = $this->createMock(ChannelPricingInterface::class);
        $shoes = $this->createMock(OrderItemInterface::class);
        $shoesVariant = $this->createMock(ProductVariantInterface::class);
        $shoesVariantChannelPricing = $this->createMock(ChannelPricingInterface::class);
        $boardGame = $this->createMock(OrderItemInterface::class);
        $boardGameVariant = $this->createMock(ProductVariantInterface::class);
        $boardGameVariantChannelPricing = $this->createMock(ChannelPricingInterface::class);

        $this->tshirt->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->tshirt->expects($this->once())->method('getQuantity')->willReturn(1);
        $this->tshirt->expects($this->once())->method('getVariant')->willReturn($this->tshirtVariant);
        $this->tshirtVariant->expects($this->once())->method('getChannelPricingForChannel')->with($this->channel)->willReturn($this->tshirtVariantChannelPricing);
        $this->tshirtVariantChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);
        $this->tshirtVariant->expects($this->once())->method('getAppliedPromotionsForChannel')->with($this->channel)->willReturn([]);

        $book->expects($this->once())->method('getTotal')->willReturn(2000);
        $book->expects($this->once())->method('getQuantity')->willReturn(1);
        $book->expects($this->once())->method('getVariant')->willReturn($bookVariant);
        $bookVariant->expects($this->once())->method('getChannelPricingForChannel')->with($this->channel)->willReturn($bookVariantChannelPricing);
        $bookVariantChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(1900);
        $bookVariant->expects($this->once())->method('getAppliedPromotionsForChannel')->with($this->channel)->willReturn([]);
        $shoes->expects($this->once())->method('getTotal')->willReturn(5000);
        $shoes->expects($this->once())->method('getQuantity')->willReturn(1);
        $shoes->expects($this->once())->method('getVariant')->willReturn($shoesVariant);
        $shoesVariant->expects($this->once())->method('getChannelPricingForChannel')->with($this->channel)->willReturn($shoesVariantChannelPricing);
        $shoesVariantChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(5000);
        $shoesVariant->expects($this->once())->method('getAppliedPromotionsForChannel')->with($this->channel)->willReturn([]);
        $boardGame->expects($this->once())->method('getTotal')->willReturn(3000);
        $boardGame->expects($this->once())->method('getQuantity')->willReturn(1);
        $boardGame->expects($this->once())->method('getVariant')->willReturn($boardGameVariant);
        $boardGameVariant->expects($this->once())->method('getChannelPricingForChannel')->with($this->channel)->willReturn($boardGameVariantChannelPricing);
        $boardGameVariantChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(2600);
        $boardGameVariant->expects($this->once())->method('getAppliedPromotionsForChannel')->with($this->channel)->willReturn([]);
    }
}
