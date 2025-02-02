<?php

namespace AALogics\NewsPk\Cron;

class Newspk
{

    protected $_helper;

    public function __construct(
        \AALogics\NewsPk\Helper\Data $_helper 
    ) 
    {
        $this->_helper = $_helper;
    }

	public function execute()
	{

        $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/newscron.log');
        $logger = new \Laminas\Log\Logger();
		$logger->addWriter($writer);
		$logger->info($this->_helper->newsPk());

		return $this->_helper->newsPk();
	}
}
