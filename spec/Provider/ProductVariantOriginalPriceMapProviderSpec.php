<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\ProductVariantDataMapProviderInterface;

final class ProductVariantOriginalPriceMapProviderSpec extends ObjectBehavior
{
    function let(ProductVariantPricesCalculatorInterface $calculator): void
    {
        $this->beConstructedWith($calculator);
    }

    function it_implements_product_variant_options_map_data_provider_interface(): void
    {
        $this->shouldImplement(ProductVariantDataMapProviderInterface::class);
    }

    function it_does_not_support_variants_with_no_channel_pricing_in_channel(
        ChannelInterface $channel,
        ProductVariantInterface $variant,
    ): void {
        $variant->getChannelPricingForChannel($channel)->willReturn(null);

        $this->supports($variant, $channel)->shouldReturn(false);
    }

    function it_does_not_support_variants_with_price_equal_original_price(
        ProductVariantPricesCalculatorInterface $calculator,
        ChannelInterface $channel,
        ProductVariantInterface $variant,
        ChannelPricingInterface $channelPricing,
    ): void {
        $variant->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $calculator->calculate($variant, ['channel' => $channel])->willReturn(1000);
        $calculator->calculateOriginal($variant, ['channel' => $channel])->willReturn(1000);

        $this->supports($variant, $channel)->shouldReturn(false);
    }

    function it_does_not_support_variants_with_price_greater_than_original_price(
        ProductVariantPricesCalculatorInterface $calculator,
        ChannelInterface $channel,
        ProductVariantInterface $variant,
        ChannelPricingInterface $channelPricing,
    ): void {
        $variant->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $calculator->calculate($variant, ['channel' => $channel])->willReturn(1200);
        $calculator->calculateOriginal($variant, ['channel' => $channel])->willReturn(1000);

        $this->supports($variant, $channel)->shouldReturn(false);
    }

    function it_supports_variants_with_price_lower_than_original_price(
        ProductVariantPricesCalculatorInterface $calculator,
        ChannelInterface $channel,
        ProductVariantInterface $variant,
        ChannelPricingInterface $channelPricing,
    ): void {
        $variant->getChannelPricingForChannel($channel)->willReturn($channelPricing);
        $calculator->calculate($variant, ['channel' => $channel])->willReturn(1000);
        $calculator->calculateOriginal($variant, ['channel' => $channel])->willReturn(1200);

        $this->supports($variant, $channel)->shouldReturn(true);
    }

    function it_provides_original_price_of_variant_in_channel(
        ProductVariantPricesCalculatorInterface $calculator,
        ChannelInterface $channel,
        ProductVariantInterface $variant,
    ): void {
        $calculator->calculateOriginal($variant, ['channel' => $channel])->willReturn(1200);

        $this->provide($variant, $channel)->shouldReturn(['original-price' => 1200]);
    }
}
