<?php

namespace Inkl\SystemMail\Model;

use Magento\Framework\Mail\MessageInterfaceFactory;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
	public function __construct(FactoryInterface $templateFactory,
	                            MessageInterfaceFactory $messageFactory,
	                            SenderResolverInterface $senderResolver,
	                            ObjectManagerInterface $objectManager,
	                            TransportInterfaceFactory $mailTransportFactory)
	{
		parent::__construct($templateFactory, $messageFactory->create(), $senderResolver, $objectManager, $mailTransportFactory);
	}

}