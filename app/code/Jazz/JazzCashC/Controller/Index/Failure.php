<?php

namespace Jazz\JazzCashC\Controller\Index;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Framework\App\Bootstrap;
 
class Failure extends Command
{
   protected function configure()
   {
       $this->setName('example:sayhello');
       $this->setDescription('Demo command line');
       
       parent::configure();
   }
   protected function execute(InputInterface $input, OutputInterface $output)
   {
       $output->writeln("Hello World");
   }
}
?>