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
        $ids = explode(',', $this->EE->input->post('ids', TRUE));
        $dragged = $this->EE->input->post('dragged', TRUE);
        $sort_order = $this->EE->input->post('sort_order', TRUE);
        
        if(count($ids) == 0 || $ids[0]=='')
        {
            echo 'empty';
            exit;
        }
        
        $query = $this->EE->db->where_in('entry_id', $ids)
                              ->order_by('entry_date','asc')
                              ->get('channel_titles');
        
        $entries = array();                      
        foreach($query->result_array() as $entry)
        {
            $entries[$entry['entry_id']] = $entry;
        }
        
        // Now reverse our IDs so we're adding minutes from the bottom up if viewing entries by ascending order
        // $sorted_ids = $sort_order == 'asc' ? array_reverse($ids) : $ids;
        $sorted_ids = $sort_order == 'desc' ? array_reverse($ids) : $ids; 
        
        
        // Get the entry_date for the oldest entry in the list
        $last_entry = current($entries);
        $last_date = $last_entry['entry_date'];
        $i = 0;
        
        // Sort our entries by our newly reversed ID string
        $entries = $this->_sort_entries($entries, $sorted_ids);
        
        foreach($entries as $entry_id => $entry)
        {
            $new_date = strtotime("+". $i ." minute", $last_date);
            
            // echo $entry_id .' => '. date('m/d/Y h:i:s', $new_date) . "\n";
            
            $this->EE->db->where('entry_id', $entry_id)
                         ->update('channel_titles', array('entry_date' => $new_date));
            $i++;
        }
        
        exit;
    }
    
    private function _sort_entries($array, $order_array)
    {
        $ordered = array();
        foreach($order_array as $key) 
        {
            if(array_key_exists($key, $array)) 
            {
                $ordered[$key] = $array[$key];
                unset($array[$key]);
            }
        }
        
        return $ordered + $array;
    }
    
    private function debug($str, $die = false)
    {
        echo '<pre>';
        var_dump($str);
        echo '</pre>';
        
        if($die) die;
    }
    
}