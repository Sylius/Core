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

namespace Sylius\Component\Core\Sales\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Sales\ValueObject\SalesInPeriod;
use Sylius\Component\Core\Sales\ValueObject\SalesPeriod;

interface SalesPerPeriodProviderInterface
{
    /** @return SalesInPeriod[] */
    public function provide(SalesPeriod $salesPeriod, ChannelInterface $channel): array;
}
