<?php
/**
 * Module: Endereco\Addressvalidation\Api
 * Copyright: (c) 2019 cccc.de
 * Date: 2019-07-02 11:19
 *
 *
 */

namespace Endereco\Addressvalidation\Api;


interface UpdateAddressInterface
{
    /**
     * @param mixed $cartId
     * @param \Magento\Quote\Api\Data\AddressInterface $addressData
     * @return \Endereco\Addressvalidation\Service\V1\Data\UpdateAddressResponse
     */
    public function updateAddress($cartId, \Magento\Quote\Api\Data\AddressInterface $addressData);
}
