<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Tests\Unit\Bundle\CartBundle\Infrastructure\Validator\Rule;

use PHPUnit\Framework\TestCase;
use Shopware\Bundle\CartBundle\Domain\Cart\CalculatedCart;
use Shopware\Bundle\CartBundle\Domain\Validator\Data\RuleDataCollection;
use Shopware\Bundle\CartBundle\Infrastructure\Validator\Rule\BillingZipCodeRule;
use Shopware\Bundle\StoreFrontBundle\Address\Address;
use Shopware\Bundle\StoreFrontBundle\Context\ShopContext;
use Shopware\Bundle\StoreFrontBundle\Customer\Customer;

class BillingZipCodeRuleTest extends TestCase
{
    public function testEqualsWithSingleCode()
    {
        $rule = new BillingZipCodeRule(['ABC123']);

        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(ShopContext::class);

        $billing = new Address();
        $billing->setZipcode('ABC123');

        $customer = new Customer();
        $customer->setActiveBillingAddress($billing);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $this->assertTrue(
            $rule->match($cart, $context, new RuleDataCollection())
        );
    }

    public function testEqualsWithMultipleCodes()
    {
        $rule = new BillingZipCodeRule(['ABC1', 'ABC2', 'ABC3']);

        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(ShopContext::class);

        $billing = new Address();
        $billing->setZipcode('ABC2');

        $customer = new Customer();
        $customer->setActiveBillingAddress($billing);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $this->assertTrue(
            $rule->match($cart, $context, new RuleDataCollection())
        );
    }

    public function testNotMatchWithSingleCode()
    {
        $rule = new BillingZipCodeRule(['ABC1', 'ABC2', 'ABC3']);

        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(ShopContext::class);

        $billing = new Address();
        $billing->setZipcode('ABC4');

        $customer = new Customer();
        $customer->setActiveBillingAddress($billing);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $this->assertFalse(
            $rule->match($cart, $context, new RuleDataCollection())
        );
    }

    public function testWithoutShippingAddress()
    {
        $rule = new BillingZipCodeRule(['ABC1', 'ABC2', 'ABC3']);

        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(ShopContext::class);

        $context->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue(null));

        $this->assertFalse(
            $rule->match($cart, $context, new RuleDataCollection())
        );
    }
}