<?php 

class Zizio_Powershare_Model_RemoteData_Incentives_Collection extends Varien_Data_Collection
{
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) 
        {
            return $this;
        }
    	
        $args = array(
        	'page' => $this->_curPage,
        	'size' => $this->_pageSize,
        	'sort' => ""
        );
        if (count($this->_orders) > 0)
        {
	        foreach ($this->_orders as $field => $dir)
        		break;
	        $args['sort'] = $field . "|" . (strtolower(self::SORT_ORDER_ASC) == strtolower($dir) ? "1" : "0");
        }
        
        $response = Zizio_Powershare_Helper_Data::CallUrl('/powers/mage/admin/get_incentives', $args, null);
        //var_dump($response);
        
    	$this->_items = array();
    	for ($i=0; $i<$response['ret_count']; $i++)
    	{
    		$item = new Zizio_Powershare_Model_Powershare();
    		$item->setData($response['items'][$i]);
    		$this->_items[$i] = $item; 
    	}
    	 
    	$this->_totalRecords = $response['total_count'];
    	$this->_setIsLoaded();
    	return $this;
    }
}
