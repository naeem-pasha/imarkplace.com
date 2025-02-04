<?php
namespace Imark\ValidateCustomer\Controller\Validate;

use Magento\Framework\App\ResourceConnection;

class Validate extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /// its a table name where customer could be validate ///
    const FME_QUICKRFQ = 'fme_quickrfq';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    private $_connection;

    private $_msg;

    private $_records;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
       \Magento\Framework\View\Result\PageFactory $pageFactory,
       ResourceConnection $resourceConnection
    )
    {
        $this->resourceConnection = $resourceConnection;
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }
    /**
     * View page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->fetchAllQuery();
    }

    public function fetchAllQuery()
    {
        $this->_connection  = $this->resourceConnection->getConnection();
        $tableName = $this->_connection->getTableName(self::FME_QUICKRFQ);
        $msg = null;
        if (!empty(isset($_POST['username'])) && isset($_POST['username'])) 
        {
            
            $usernameInput = $_POST['username'];
           
           $query = $this->_connection->select()
                ->from($tableName, ['project_title'])
                ->where('project_title = ?', $usernameInput);

            $this->_records = $this->_connection->fetchAll($query);

            if (count($this->_records) <= 0) {
                echo $msg = "<span style='color:beige'>Incorrect CNIC#</span>";
            } else {
                echo "<span style='color:beige'>Varified!</span>";
                echo $msg = "<form action='https://members.almeezangroup.com/MembersLogin.aspx'><button type=submit id='widthdraw'>Click To Withdrawl</button></form>";
            }
        }

    }
}
