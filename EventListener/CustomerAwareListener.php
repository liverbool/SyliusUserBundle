<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\EventListener;

use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\User\Context\CustomerContextInterface;
use Sylius\Component\User\Model\CustomerAwareInterface;
use Sylius\Component\User\Model\UserAwareInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CustomerAwareListener
{
    protected $customerContext;

    public function __construct(CustomerContextInterface $securityContext)
    {
        $this->customerContext = $securityContext;
    }

    public function setCustomer(GenericEvent $event)
    {
        if ($event instanceof CartEvent) {
            $resource = $event->getCart();
        } else {
            $resource = $event->getSubject();
        }

        if (!$resource instanceof CustomerAwareInterface) {
            throw new UnexpectedTypeException($resource, 'Sylius\Component\User\Model\CustomerAwareInterface');
        }

        if (null === $customer = $this->customerContext->getCustomer()) {
            return;
        }

        $resource->setCustomer($customer);
    }
}