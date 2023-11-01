<?php
/**
 * Module: Endereco\Addressvalidation\Service\V1\Data
 * Copyright: (c) 2019 cccc.de
 * Date: 2019-07-02 11:25
 *
 *
 */

namespace Endereco\Addressvalidation\Service\V1\Data;

use Endereco\Addressvalidation\Api\Data\UpdateAddressResponseInterface;

class UpdateAddressResponse extends \Magento\Framework\Api\AbstractExtensibleObject implements UpdateAddressResponseInterface
{
    /**
     * Returns if editing the address was a success
     *
     * @return bool
     */
    public function getSuccess()
    {
        return $this->_get('success');
    }
}
