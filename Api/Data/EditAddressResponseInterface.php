<?php
/**
 * Module: Endereco\Addressvalidation\Api\Data
 * Copyright: (c) 2019 cccc.de
 * Date: 12.11.19 10:16
 *
 *
 */

namespace Endereco\Addressvalidation\Api\Data;

interface EditAddressResponseInterface
{
    /**
     * Returns if editing the address was a success
     *
     * @return bool
     */
    public function getSuccess();
}