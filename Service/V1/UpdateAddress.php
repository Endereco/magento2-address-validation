<?php
/**
 * Module: Endereco\Addressvalidation\Service\V1
 * Copyright: (c) 2019 cccc.de
 * Date: 2019-07-02 11:23
 *
 *
 */

namespace Endereco\Addressvalidation\Service\V1;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\AddressRepositoryInterface;
use Endereco\Addressvalidation\Service\V1\Data\EditAddressResponseFactory;
use Endereco\Addressvalidation\Api\UpdateAddressInterface;
use Magento\Quote\Model\Quote;

class UpdateAddress implements UpdateAddressInterface
{
    /**
     * Factory for the response object
     *
     * @var \Endereco\Addressvalidation\Service\V1\Data\EditAddressResponseFactory
     */
    protected $responseFactory;

    /**
     * Address repository
     *
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /** @var \Magento\Quote\Model\QuoteFactory  */
    protected $quoteFactory;

    /** @var \Magento\Quote\Model\ResourceModel\Quote  */
    protected $quoteRm;

    /** @var AddressInterfaceFactory  */
    protected $addressInterfaceFactory;

    public function __construct(
        EditAddressResponseFactory $responseFactory,
        AddressRepositoryInterface $addressRepository,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\ResourceModel\Quote $quoteRm,
        AddressInterfaceFactory $addressInterfaceFactory
    ) {
        $this->responseFactory = $responseFactory;
        $this->addressRepository = $addressRepository;
        $this->quoteFactory = $quoteFactory;
        $this->quoteRm = $quoteRm;
        $this->addressInterfaceFactory = $addressInterfaceFactory;
    }

    /**
     * @param  mixed $cartId
     * @param  AddressInterface $addressData
     * @return \Endereco\Addressvalidation\Service\V1\Data\UpdateAddressResponse
     */
    public function updateAddress($cartId, AddressInterface $addressData)
    {
        try {
            $address = $this->addressRepository->getById($addressData->getCustomerAddressId());
        } catch (\Exception $e) {
            /** @var AddressInterface $address */
            $address = $this->addressInterfaceFactory->create();
        }

        $address->setPostcode($addressData->getPostcode());
        $address->setCity($addressData->getCity());

        $street = $addressData->getStreet();
        if (!is_array($street)) {
            $street = [$street];
        }
        $address->setStreet($street);

        $address->setFirstname($addressData->getFirstname());
        $address->setLastname($addressData->getLastname());
        $address->setPrefix($addressData->getPrefix());
        $address->setCountryId($addressData->getCountryId());

        if ($addressData->getCustomerAddressId()) {
            $this->addressRepository->save($address);
        }

        /** @var Quote $quote */
        $quote = $this->quoteFactory->create()->loadActive($cartId);
        $quoteAddress = $quote->getShippingAddress();
        $quoteAddress->setStreet($address->getStreet());
        $quoteAddress->setPostcode($address->getPostcode());
        $quoteAddress->setCity($address->getCity());
        $quoteAddress->setCountryId($address->getCountryId());
        $quoteAddress->setFirstname($address->getFirstname());
        $quoteAddress->setLastname($address->getLastname());
        $quoteAddress->setPrefix($address->getPrefix());
        $quote->setShippingAddress($quoteAddress);
        $this->quoteRm->save($quote);

        $oResponse = $this->responseFactory->create();
        $oResponse->setData('success', true);
        return $oResponse;
    }
}
