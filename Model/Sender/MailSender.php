<?php

namespace Inkl\SystemMail\Model\Sender;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilderByStore;
use Magento\Framework\Mail\Template\TransportBuilderFactory;
use Magento\Store\Model\Store;

class MailSender
{
	const TYPE_INFO = 'info';
	const TYPE_WARNING = 'warning';
	const TYPE_CRITICAL = 'critical';
	const TYPE_MARKETING = 'marketing';

	/** @var TransportBuilderFactory */
	private $transportBuilderFactory;
	/** @var TransportBuilderByStore */
	private $transportBuilderByStore;
	/** @var ScopeConfigInterface */
	private $scopeConfig;

	/**
	 * @param TransportBuilderFactory $transportBuilderFactory
	 * @param ScopeConfigInterface $scopeConfig
	 */
	public function __construct(TransportBuilderFactory $transportBuilderFactory,
	                            TransportBuilderByStore $transportBuilderByStore,
	                            ScopeConfigInterface $scopeConfig)
	{
		$this->transportBuilderFactory = $transportBuilderFactory;
		$this->transportBuilderByStore = $transportBuilderByStore;
		$this->scopeConfig = $scopeConfig;
	}

	public function send($type, $subject, $body)
	{
		$toEmails = trim($this->scopeConfig->getValue(sprintf('inkl_systemmail/emails/%s', $type)));
		if (!$toEmails)
		{
			return;
		}

		$toEmails = array_map('trim', explode("\n", $toEmails));

		$transportBuilder = $this->transportBuilderFactory->create()
			->setTemplateOptions([
				'area' => Area::AREA_ADMINHTML,
				'store' => Store::DEFAULT_STORE_ID
			])
			->setTemplateIdentifier('system_mail')
			->setTemplateVars([
				'type' => strtoupper($type),
				'subject' => $subject,
				'body' => $body
			]);

		foreach ($toEmails as $toEmail)
		{
			$transportBuilder->addTo($toEmail);
		}

		$transport = $transportBuilder->getTransport();

		$this->transportBuilderByStore->setFromByStore('general', Store::DEFAULT_STORE_ID);

		$transport->sendMessage();
	}

	public function sendInfo($subject, $body)
	{
		$this->send(self::TYPE_INFO, $subject, $body);
	}

	public function sendWarning($subject, $body)
	{
		$this->send(self::TYPE_WARNING, $subject, $body);
	}

	public function sendCritical($subject, $body)
	{
		$this->send(self::TYPE_CRITICAL, $subject, $body);
	}

	public function sendMarketing($subject, $body)
	{
		$this->send(self::TYPE_MARKETING, $subject, $body);
	}

}