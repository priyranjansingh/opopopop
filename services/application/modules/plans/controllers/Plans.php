<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Plans extends CI_Controller {

   public function listing()
   {
       authenticate();
       $plans_list = $this->generalmodel->getListValue('plans',array('status'=>1,'deleted'=>0),'','plan_serial','ASC');
       echo json_encode($plans_list);
   }  
   
   public function plandetail($id)
   {
       authenticate();
       $plan_detail = $this->generalmodel->getOneRow('plans',array('id'=>$id));
       echo json_encode($plan_detail);
   } 
   
        
   
}
