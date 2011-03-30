<?php 

/**
 * ExpressionEngine Disposition Module Class
 *
 * @package     ExpressionEngine
 * @subpackage  Modules
 * @category    Disposition
 * @author      Brian Litzinger
 * @copyright   Copyright 2011 - Brian Litzinger
 * @link        http://boldminded.com/add-ons/disposition
 */
 
class disposition {
    
    function __construct()
    {
        $this->EE =& get_instance();
    }
    
    function update_entry_date()
    {
        $id = $this->EE->input->post('entry_id');
        $time = $this->EE->input->post('time');
        
        $time = date('Y-m-d H:i:s', strtotime($time));
        $time_stamp = $this->EE->localize->convert_human_date_to_gmt($time);
        
        $this->EE->db->where('entry_id', $id)
                     ->update('channel_titles', array('entry_date' => $time_stamp));
        
        exit;
    }
    
    private function debug($str, $die = false)
    {
        echo '<pre>';
        var_dump($str);
        echo '</pre>';
        
        if($die) die;
    }
    
}