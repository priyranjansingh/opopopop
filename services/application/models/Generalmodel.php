<?php

class Generalmodel extends CI_Model {
    /*
     * Insert value in table
     * @param $table_name and $parameter
     * return true or false
     */

    public function insert_data($table_name, $dataArray) {

        if ($this->db->insert($table_name, $dataArray)) {
            $affected_rows = $this->db->affected_rows();
            return $affected_rows;
        } else {
            return false;
        }
    }

    /*
     * Update value in table row wise
     * @param $table_name,$field_name,$id and $dataArray
     * return number of effected rows
     */

    public function update_row_by_id($table_name, $field_name, $field_id, $dataArray) {
        $this->db->where($field_name, $field_id);
        $this->db->update($table_name, $dataArray);
        return $this->db->affected_rows();
    }
    
    /*
     * Update value in table row wise
     * @param $table_name,$condition,and $data_array
     * return number of effected rows
     */
    
    public function update_row_by_condition($table_name,$condition,$data_array)
    {
        $this->db->where($condition);
        $this->db->update($table_name, $data_array); 
        return $this->db->affected_rows();
    }        
    

    /*
     * Delete row by id
     * @param $table_name,$field_name and $id
     * return true or false
     */

    public function delete_row_by_id($table_name, $field_name, $field_id) {
        $this->db->where($field_name, $field_id);
        return $this->db->delete($table_name);
    }
    
    
    /*
     * Delete row by condition
     * @param $table_name,$field_name and $id
     * return true or false
     */

    public function delete_row_by_condition($table_name, $where_array) {
        $this->db->where($where_array);
        return $this->db->delete($table_name);
    }
    
    

    /*
     * get value from table
     * @param $table_name, $where_array_parameter and $select_array
     * return array
     */

    public function getListValue($table_name, $where_array_parameter = NULL, $select_array = NULL, $orderFieldName = NULL, $orderType = NULL, $limit = NULL, $offset = NULL) {
        if ($select_array != NULL) {
            $this->db->select($select_array);
        } else {
            $this->db->select("*");
        }

        if ($orderFieldName != NULL && $orderType != NULL) {
            $this->db->order_by($orderFieldName, $orderType);
        }


        if ($where_array_parameter != NULL) {
            $this->db->where($where_array_parameter);
        }
        
        if (($limit != NULL && $offset != NULL) || ($offset != NULL)) {
            $this->db->limit($offset, $limit);
        }

        $this->db->from($table_name);

        $query = $this->db->get();

        return $query->result_array();
    }

    public function getOneRow($table_name, $where_array_param, $select_array = NULL, $orderFieldName = NULL, $orderType = NULL) {
         if ($select_array != NULL) {
            $this->db->select($select_array);
        } else {
            $this->db->select("*");
        }
        if ($orderFieldName != NULL && $orderType != NULL) {
            $this->db->order_by($orderFieldName, $orderType);
        }
        $this->db->where($where_array_param);
        $this->db->from($table_name);
        $query = $this->db->get();
        return $query->row_array();
    }

    /*
     * Get no of row in table
     * @param table_name, where_param
     * @return integer
     */

    public function get_no_row($table_name, $where_param = NULL) {
        if ($where_param != NULL) {
            $this->db->where($where_param);
        }
        $this->db->from($table_name);
        $query = $this->db->get();
        return $query->num_rows();
    }

    /*
     * Get Maximum value form field 
     * @param table_name, field name, where param
     * @return string
     */

    public function get_max_value($table_name, $field_name, $where_array = NULL) {
        $this->db->select_max($field_name);

        if ($where_array != NULL) {
            $this->db->where($where_array);
        }
        $this->db->from($table_name);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result[$field_name];
    }
    
    // function to run the custom query
    
    public function run_custom_query($query_string)
    {
        $query = $this->db->query($query_string);
        return $query->result_array();
    } 
    
   
    
   
   
}

?>
