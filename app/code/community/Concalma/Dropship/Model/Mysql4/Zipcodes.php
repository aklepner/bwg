<?php

class Concalma_Dropship_Model_Mysql4_Zipcodes extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the brands_id refers to the key field in your database table.
        $this->_init('dropship/zipcodes', 'zip');
    }
}
