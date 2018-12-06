<?php

namespace CoreShop2VueStorefrontBundle\Security\User;

use CoreShop\Bundle\CustomerBundle\Pimcore\Repository\CustomerRepository;
use CoreShop\Component\Customer\Model\CustomerInterface;
use Pimcore\Model\DataObject\CoreShopCustomer;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function loadUserByUsername($username)
    {
        /** @var CustomerInterface $pimcoreUser */
        $customer = $this->customerRepository->findCustomerByEmail($username);

        if ($customer) {
            return $customer;
        }

        throw new UsernameNotFoundException(sprintf('User %s was not found', $username));
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof CoreShopCustomer) {
            throw new UnsupportedUserException();
        }

        return $this->customerRepository->findCustomerByEmail($user->getEmail());
    }

    public function supportsClass($class)
    {
        return $class === CoreShopCustomer::class;
    }
}
