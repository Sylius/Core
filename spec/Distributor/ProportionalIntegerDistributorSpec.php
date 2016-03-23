<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Distributor;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ProportionalIntegerDistributorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Distributor\ProportionalIntegerDistributor');
    }

    function it_implements_integer_proportional_distributor_interface()
    {
        $this->shouldImplement(ProportionalIntegerDistributorInterface::class);
    }

    function it_distributes_integer_based_on_elements_participation_in_total()
    {
        $this->distribute(8000, [4000, 2000, 2000], 300)->shouldReturn([150, 75, 75]);
    }

    function it_distributes_integer_based_on_elements_participation_in_total_even_if_it_can_be_divided_easily()
    {
        $this->distribute(8000, [4300, 1400, 2300], 300)->shouldReturn([162, 52, 86]);
    }

    function it_throws_exception_if_elements_sum_is_not_equal_with_total()
    {
        $this
            ->shouldThrow(new \InvalidArgumentException('Element sum should be equal with total.'))
            ->during('distribute', [1000, [500, 400], 200])
        ;
    }
}