<?php

namespace Endereco\Addressvalidation\Plugin;

use Magento\Sales\Model\Order;

class AddressOrderPlugin
{
	
	protected $configProvider;
	protected $config;
	
	public function __construct(
        \Endereco\Addressvalidation\Model\ConfigProvider $configProvider
    ) {
        
        $this->configProvider = $configProvider;
		$this->config = $this->configProvider->getConfig();
    }
    /**
     * Plugin to modify the order address format.
     *
     * @param OrderInterface $order
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $address
     * @return \Magento\Sales\Api\Data\OrderAddressInterface
     */
    public function aroundGetShippingAddress(Order $order, \Closure $proceed)
    {
        $address = $proceed();
        return $this->reorderStreet($address);
    }

    public function aroundGetBillingAddress(Order $order, \Closure $proceed)
    {
        $address = $proceed();
        return $this->reorderStreet($address);
    }
	
	protected function isUsingFullStreet() {
		return $this->config['cccc']['addressvalidation']['endereco']['mapping']['useStreetFull'];
	}
	
	protected function getCountriesWithHouseNumberFirst() {
		return $this->config['cccc']['addressvalidation']['endereco']['countries_with_house_number_first'];
	}

    /**
     * Reorder street address to show house number first.
     *
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $address
     * @return \Magento\Sales\Api\Data\OrderAddressInterface
     */
    protected function reorderStreet($address)
    {
		if (!$this->isUsingFullStreet()) {
			if (!$address || !$address->getStreet()) {
				return $address;
			}
			$street = $address->getStreet();
			if (count($street) == 2) {
				if (in_array($address->getCountryId(), $this->getCountriesWithHouseNumberFirst())) {
					$address->setData('street', $street[1]. ' '.$street[0]);
				} else {
					$address->setData('street', $street[0]. ' '.$street[1]);
				}
			}	

		}
		return $address;
    }
}
