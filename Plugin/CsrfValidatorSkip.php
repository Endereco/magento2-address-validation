<?php

namespace Endereco\Addressvalidation\Plugin;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\CsrfValidator;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

class CsrfValidatorSkip
{
    /**
     * @var UrlInterface
     */
    private $url;

    public function __construct(
        UrlInterface $url
    ) {
        $this->url = $url;
    }

    public function aroundValidate(
        CsrfValidator $subject,
        \Closure $proceed,
        RequestInterface $request,
        ActionInterface $action
    ) {
        if (strpos($this->url->getCurrentUrl(), 'endereco/proxy/proxy') !== false) {
            return null;
        }

        return $proceed($request, $action);
    }
}
