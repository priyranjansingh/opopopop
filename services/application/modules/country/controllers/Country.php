<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Country extends CI_Controller {


        
        public function listing()
	{
                $countries = $this->generalmodel->getListValue("countries",'','','name','ASC');
                echo json_encode($countries);
	}
        
        
        public function getStateByCountry($country_id)
        {
            $states = $this->generalmodel->getListValue("states",array('country_id'=>$country_id));
            echo json_encode($states);
        }        
        
        
}
