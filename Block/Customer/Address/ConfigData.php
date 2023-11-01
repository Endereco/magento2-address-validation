<?php
/**
 * Module: Endereco\Addressvalidation\Block\Customer\Address
 * Copyright: (c) 2021 cccc.de
 * Date: 22.12.21 10:16
 *
 *
 */

namespace Endereco\Addressvalidation\Block\Customer\Address;

use Endereco\Addressvalidation\Model\ConfigProvider;
use Magento\Framework\View\Element\Template;

class ConfigData extends \Magento\Framework\View\Element\Template
{

    /** @var ConfigProvider */
    protected $configProvider;
	/**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    public function __construct(
		ConfigProvider $configProvider, 
		Template\Context $context, 
		\Magento\Framework\Serialize\Serializer\Json $serializer = null,
        \Magento\Framework\Serialize\SerializerInterface $serializerInterface = null,
		array $data = []
	)
    {
        $this->configProvider = $configProvider;
        $this->serializer = $serializerInterface ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\JsonHexTag::class);
        parent::__construct($context, $data);
    }

    public function getConfig()
    {
        return $this->configProvider->getConfig();
    }
	
	public function jsonEncode($array) {
		$jsonString = $this->serializer->serialize($array);
		return $jsonString;
	}
}
