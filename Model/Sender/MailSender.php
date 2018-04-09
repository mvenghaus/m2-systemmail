<?php

namespace Inkl\SystemMail\Model\Sender;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\Store;

class MailSender
{
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_CRITICAL = 'critical';

    /** @var TransportBuilder */
    private $transportBuilder;
    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(TransportBuilder $transportBuilder,
                                ScopeConfigInterface $scopeConfig)
    {
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    public function send($type, $subject, $body)
    {
        $toEmails = trim($this->scopeConfig->getValue(sprintf('inkl_systemmail/emails/%s', $type)));
        if (!$toEmails)
        {
            return;
        }
        $toEmails = explode("\n", $toEmails);

        $transport = $this->transportBuilder
            ->setTemplateOptions([
                'area' => Area::AREA_ADMINHTML,
                'store' => Store::DEFAULT_STORE_ID
            ])
            ->setTemplateIdentifier('system_mail')
            ->setTemplateVars([
                'type' => strtoupper($type),
                'subject' => $subject,
                'body' => $body
            ])
            ->setFrom('general')
            ->addTo($toEmails)
            ->getTransport();

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

}