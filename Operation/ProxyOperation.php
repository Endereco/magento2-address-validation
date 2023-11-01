<?php
/**
 * Module: Endereco\Addressvalidation\Operation
 * Copyright: (c) 2021 cccc.de
 * Date: 17.06.21 15:25
 *
 *
 */

namespace Endereco\Addressvalidation\Operation;

class ProxyOperation extends BaseOperation
{
    public function doRequest(array $data) {
        $result =  $this->doApiRequest($data);
        return $result;
    }
}
