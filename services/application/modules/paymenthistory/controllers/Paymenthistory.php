<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Paymenthistory extends CI_Controller {

    public function getTotalCount() {
        authenticate();
        $user_id = $this->session->userdata('user_id');
        $payment_history_list = $this->generalmodel->getListValue('transactions', array('status' => 1, 'deleted' => 0,'user_id'=>$user_id), '', 'date_entered', 'DESC');
        echo json_encode(count($payment_history_list));
    }

    public function listing($limit = 0, $offset = ITEM_PER_PAGE) {
        authenticate();
        $user_id = $this->session->userdata('user_id');
        $this->db->select("t.invoice,t.transaction_id,t.amount,t.payment_status,DATE_FORMAT(t.date_modified, '%b %c %Y')as  modified_date,p.plan_name,p.plan_desc,p.plan_price,p.plan_duration");
        $this->db->where('t.user_id',$user_id);
        $this->db->limit($offset, $limit);
        $this->db->order_by('t.date_entered', 'DESC');
        $this->db->join('users u', 'u.id = t.user_id');
        $this->db->join('plans p', 'p.id = t.plan_id');
        $this->db->from('transactions t');
        $query = $this->db->get();
        $payment_history_list = $query->result_array();
        echo json_encode($payment_history_list);
    }


   

}
