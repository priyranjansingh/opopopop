<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Djtype extends CI_Controller {


        
        public function listing()
	{
                $djtype = $this->generalmodel->getListValue("dj_type",array('status'=>1,'deleted'=>0),'','name','ASC');
                echo json_encode($djtype);
	}
        
        
        
}
