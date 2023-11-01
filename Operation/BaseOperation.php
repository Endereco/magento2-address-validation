<?php
/**
 * Module: Endereco\Addressvalidation\Operation
 * Copyright: (c) 2019 cccc.de
 * Date: 2019-05-22 15:33
 *
 *
 */

namespace Endereco\Addressvalidation\Operation;

use Endereco\Addressvalidation\Generator\RefererGenerator;
use Endereco\Addressvalidation\Logger\RequestLogger;
use Endereco\Addressvalidation\Model\ConfigProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\SerializerInterface;

class BaseOperation
{
    protected $configPrefix = 'cccc_addressvalidation_endereco_section';

    const MODULE_NAME = 'Endereco_Addressvalidation';

    /** @var ScopeConfigInterface */
    protected $config;

    /** @var string */
    protected $locale;

    protected $referer;

    protected $magentoVersion;

    protected $themeCode;

    /** @var LoggerInterface  */
    protected $logger;

    /** @var SerializerInterfac  */
    protected $serializer;

    /** @var ConfigProvider  */
    protected $configProvider;

    /** @var RequestLogger  */
    protected $requestLogger;

    /** @var string */
    protected $moduleVersion;

    /** @var RequestInterface */
    protected $request;

    public function __construct(ScopeConfigInterface $config, Resolver $localeResolver, RefererGenerator $refererGenerator,
                                ProductMetadataInterface $metaInterface, DesignInterface $design, ModuleListInterface $moduleList,
                                LoggerInterface $logger, SerializerInterface $serializer, ConfigProvider $configProvider,
                                RequestLogger $requestLogger, RequestInterface  $request)
    {
        $this->referer = $refererGenerator->getReferer();

        $this->config = $config;
        $this->locale = $localeResolver->getLocale() ?? $localeResolver->getDefaultLocale();

        $this->magentoVersion = $metaInterface->getVersion();
        $this->themeCode = $design->getDesignTheme()->getCode();

        $this->moduleVersion = $moduleList->getOne(self::MODULE_NAME)['setup_version'];

        $this->logger = $logger;
        $this->serializer = $serializer;

        $this->configProvider = $configProvider;
        $this->requestLogger = $requestLogger;
        $this->request = $request;
    }

    protected function getRequestHeaders() {
        $headers = [
            'Content-Type: application/json',
            'X-Auth-Key: ' . $this->config->getValue($this->configPrefix . '/connection/authkey',ScopeInterface::SCOPE_STORE),
            'X-Transaction-Referer: ' . $this->referer,
            'X-Transaction-Id: ' .  $this->request->getHeader('x-transaction-id'),
            'X-Agent: '. 'Magento:'.$this->magentoVersion.', Theme: '.$this->themeCode.', '.self::MODULE_NAME.': '.$this->moduleVersion
        ];
        if (empty($headers['X-Transaction-Id'])) {
            $headers['X-Transaction-Id'] = !empty($this->request->getHeader('X-Transaction-Id'))?$this->request->getHeader('X-Transaction-Id'):'n/a';
        }
        return $headers;
    }

    protected function getLanguage() {
        $parts = explode('_', $this->locale);
        return strtolower($parts[0]);
    }

    protected function doApiRequest(array $requestDataCompiled) {
        $url = $this->config->getValue($this->configPrefix . '/connection/baseurl', ScopeInterface::SCOPE_STORE);
        $ch = curl_init($url);

        $headers = $this->getRequestHeaders();
        curl_setopt_array(
            $ch,
            [
                CURLOPT_TCP_FASTOPEN => true,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($requestDataCompiled),
                CURLOPT_HTTPHEADER => $headers
            ]
        );

        if ($this->configProvider->shouldLogRequestsInSeparateFile()) {
            $logHash = hash('sha256', time().uniqid(rand(1, PHP_INT_MAX), true));

            $this->requestLogger->notice(
                sprintf('[%s ] Sending request to %s as POST, current request class: %s', $logHash, $url, get_class($this))
            );
            $this->requestLogger->notice(
                sprintf('[%s ] Sending request to %s as POST, request data: %s', $logHash, $url, print_r($requestDataCompiled, true))
            );
            $this->requestLogger->notice(
                sprintf('[%s ] Headers: %s', $logHash, implode(" | ", $headers))
            );
            $this->requestLogger->notice(
                sprintf('[%s ] Encoded data: %s', $logHash, json_encode($requestDataCompiled))
            );
        }

        $result = curl_exec($ch);

        $response = new ResponseObject(curl_errno($ch), curl_getinfo($ch, CURLINFO_HTTP_CODE), json_decode($result, true));

        if ($this->configProvider->shouldLogRequestsInSeparateFile()) {
            $this->requestLogger->notice(
                sprintf('[%s ] Response retrieved. CURL status: : %d - %s', $logHash, curl_errno($ch), curl_error($ch))
            );
            $this->requestLogger->notice(
                sprintf('[%s ] Response data %s', $logHash, $result)
            );
        }

        curl_close($ch);

        return $response;
    }

}
