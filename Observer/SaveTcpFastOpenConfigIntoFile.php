<?php

namespace Endereco\Addressvalidation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Psr\Log\LoggerInterface;

class SaveTcpFastOpenConfigIntoFile implements ObserverInterface
{
	protected $configPrefix = 'cccc_addressvalidation_endereco_section';
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ConfigSaveObserver constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param File $file
     * @param DirectoryList $directoryList
     * @param LoggerInterface $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        File $file,
        DirectoryList $directoryList,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->file = $file;
        $this->directoryList = $directoryList;
        $this->logger = $logger;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            $useTcpFastOpen = $this->scopeConfig->getValue(
                $this->configPrefix . '/connection/use_tcp_fast_open',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            );

            $filePath = $this->directoryList->getPath(DirectoryList::VAR_DIR) . '/tcp_config_value.txt';

            $this->file->write($filePath, "use_tcp_fast_open=" . ($useTcpFastOpen ? 1 : 0));

        } catch (\Exception $e) {
            $this->logger->error(__('Error while saving config to file: %1', $e->getMessage()));
        }
    }
}
