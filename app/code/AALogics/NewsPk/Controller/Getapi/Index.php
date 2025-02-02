<?php
namespace AALogics\NewsPk\Controller\Getapi;

use AALogics\NewsPk\Helper\Data;
use AALogics\NewsPk\Model\Newspk;
use AALogics\NewsPk\Model\ResourceModel\Newspk as NewspkResource;

class Index extends \Magento\Framework\App\Action\Action
{
    
    /**
     * @var Newspk
     */
    private $newspk;
    /**
     * @var NewspkResource
     */
    private $newspkResource;

    private $_helper;
    /**
     * Add constructor.
     * @param Newspk $newspk
     * @param NewspkResource $newspkResource
     */

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		Newspk $newspk,
        NewspkResource $newspkResource,
        Data $data)
	{
        parent::__construct($context);
        $this->newspk = $newspk;
        $this->newspkResource = $newspkResource;
        $this->_helper = $data;
	}

	public function execute()
	{
        $date = date('Y-m-d H:i:s', strtotime('-7 days'));
        $current_date = date('Y-m-d')."T00:00:00Z";

        // Delete last 7 day date & delete all of this date
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('newspk');
        $sql = "DELETE FROM ".$tableName." WHERE created_at <= '".$date."'";
        $connection->query($sql);
        /**/

        $client = new \GuzzleHttp\Client();
        $url =  $this->_helper->getAdminField('api_url') . $current_date;
        $response = $client->get($url);
        $res = json_decode($response->getBody(), true);

        $data = []; $count = 0;
        foreach ($res['articles'] as $key => $value) 
        {
            if($count == 30){
                break;
            }

            $source = null;
            if(isset($value['source']['name'])){   
                $source = $value['source']['name'];
            }

            $data[$key] = [
                'title'=>$value['title'],
                'description'=>$value['description'],
                'content'=>$value['content'],
                'url'=>$value['url'],
                'source'=> $source,
                'image'=> $value['image'],
                'published_at'=> $value['publishedAt']
            ];

            /* Set the data in the model */
            $newspkModel = $this->newspk;
            $newspkModel->setData($data[$key]);
            try {
                /* Use the resource model to save the model in the DB */
                $this->newspkResource->save($newspkModel);
                // 
                $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/newscron.log');
                $logger = new \Laminas\Log\Logger();
                $logger->addWriter($writer);
                $logger->info('News Added Successfully. Title : '.$data[$key]['title']);

            } catch (\Exception $exception) {
                $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/newscron.log');
                $logger = new \Laminas\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($exception);
            }

            $count = $count+1;
        
        }

        echo "check var/log/newscron.log file";
		exit;
	}
}
