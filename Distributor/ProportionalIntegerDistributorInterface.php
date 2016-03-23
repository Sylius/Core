<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Distributor;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface ProportionalIntegerDistributorInterface
{
    /**
     * @param int $total
     * @param array $elements
     * @param int $amount
     *
     * @return array
     */
    public function distribute($total, array $elements, $amount);
}