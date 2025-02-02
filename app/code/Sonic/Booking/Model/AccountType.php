<?php
namespace Sonic\Booking\Model;

class AccountType implements \Magento\Framework\Option\ArrayInterface
{
	 public function toOptionArray()
	 {
	  $arr = $this->toArray();
	    $ret = [];
	    foreach ($arr as $key => $value) {
	        $ret[] = [
	            'value' => $key,
	            'label' => $value
	        ];
	    }
	    return $ret;
	 }
	 /*
	 * Get options in "key-value" format
	 * @return array
	 */
	public function toArray()
	{
	    $choose = [
	        '1' => 'Reimbursement Account',
	        '2' => 'Corporate Invoicing Account'
	    ];
	    return $choose;
	}
}