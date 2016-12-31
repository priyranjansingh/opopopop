<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function add() {
        $data = json_decode(file_get_contents("php://input"));
        $data_arr['first_name'] = $data->first_name;
        $data_arr['last_name'] = $data->last_name;
        $data_arr['dj_name'] = $data->dj_name;
        $data_arr['dj_company_name'] = $data->dj_company_name;
        $data_arr['dj_year_count'] = $data->dj_year_count;
        $data_arr['address'] = $data->address;
        $data_arr['street'] = $data->street;
        $data_arr['zip'] = $data->zip;
        $data_arr['country_id'] = $data->country_id;
        $data_arr['state_id'] = $data->state_id;
        $data_arr['email'] = $data->email;
        $data_arr['phone'] = $data->mobile;
        $data_arr['password'] = md5($data->password);

        $_POST = json_decode(file_get_contents("php://input"), true);

        $dj_type_id_arr = $data->dj_type_id_arr;



        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[password]');


        if ($this->form_validation->run()) {


            $return_arr = array();

            // checking the user in the database
            $user = $this->generalmodel->getOneRow('users', array('email' => $data->email));
            if (empty($user)) {
                $user_id = create_guid();
                $data_arr['id'] = $user_id;
                $data_arr['created_by'] = $user_id;
                $data_arr['modified_by'] = $user_id;
                $data_arr['date_entered'] = date("Y-m-d H:i:s");
                $data_arr['date_modified'] = date("Y-m-d H:i:s");
                $data_arr['role_id'] = FRONT_USER_ROLE_ID;
                $this->generalmodel->insert_data("users", $data_arr);

                if (!empty($dj_type_id_arr)) {
                    foreach ($dj_type_id_arr as $dj_id) {
                        $data_user_dj_type = array();
                        $data_user_dj_type['id'] = create_guid();
                        $data_user_dj_type['user_id'] = $data_arr['id'];
                        $data_user_dj_type['dj_type_id'] = $dj_id;
                        $data_user_dj_type['created_by'] = $data_arr['id'];
                        $data_user_dj_type['modified_by'] = $data_arr['id'];
                        $data_user_dj_type['date_entered'] = date("Y-m-d H:i:s");
                        $data_user_dj_type['date_modified'] = date("Y-m-d H:i:s");
                        $this->generalmodel->insert_data("user_dj_type", $data_user_dj_type);
                    }
                }

                $user = $this->generalmodel->getOneRow('users', array('email' => $data->email));
            }

            $this->session->set_userdata('user_id', $user['id']);
            $this->session->set_userdata('user_name', $user['email']);
            $return_arr['user_detail'] = $user;
            //$return_arr['user_plan_detail'] = $data_user_plan;
            // sending mail 
            $data_email = array();
            $data_email['first_name'] = $data->first_name;
            $data_email['last_name'] = $data->last_name;
            $data_email['dj_name'] = $data->dj_name;
            $data_email['email'] = $data->email;

            $message = $this->load->view('registration_template', $data_email, true);
            send_mail($data_email['email'], 'Opopopop Registration', $message);
            // end of sending mail


            echo json_encode($return_arr);
        } else {
            $this->output->set_output(validation_errors());
        }
    }

    // function to check that whethe the email exists or not

    public function checkEmail() {
        $data = json_decode(file_get_contents("php://input"));
        $user_email = $data->user_email;
        $_POST = json_decode(file_get_contents("php://input"), true);

        $this->form_validation->set_rules('user_email', 'Email', 'trim|required|valid_email');
        if ($this->form_validation->run()) {
            // checking the user in the database
            $user = $this->generalmodel->getOneRow('users', array('email' => $user_email));
            $return_arr = array();
            if (empty($user)) {
                $return_arr['status'] = 'not_exists';
            } else {
                $return_arr['status'] = 'exists';
            }
            echo json_encode($return_arr);
        } else {
            $this->output->set_output(validation_errors());
        }
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"));
        $email = $data->email;
        $password = md5($data->password);
        $_POST = json_decode(file_get_contents("php://input"), true);

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run()) {
            // checking the user in the database
            $user = $this->generalmodel->getOneRow('users', array('email' => $email, 'password' => $password));
            $return_arr = array();
            if (empty($user)) {
                $return_arr['status'] = 'not_exists';
                $return_arr['user_detail'] = '';
                $return_arr['user_plan'] = '';
                $return_arr['user_payment_plan_obj'] = '';
            } else {
                $user_plan_detail = $this->generalmodel->getOneRow('user_plan', array('user_id' => $user['id'], 'status' => 1, 'deleted' => 0));
                $return_arr['status'] = 'exists';
                $return_arr['user_detail'] = $user;
                $return_arr['user_plan_detail'] = $user_plan_detail;
                $this->session->set_userdata('user_id', $user['id']);
                $this->session->set_userdata('user_name', $user['email']);
                // getting the user payment plan detail
                $user_payment_plan_obj = $this->getUserPlanForServer();
                $return_arr['user_payment_plan_obj'] = $user_payment_plan_obj;
            }
            echo json_encode($return_arr);
        } else {
            $this->output->set_output(validation_errors());
        }
    }

    public function logout() {
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('user_name');
        $return_arr = array();
        echo json_encode($return_arr);
    }

    // function for getting the current plan status of the user
    public function getUserPlan() {
        $return_array = array();
        $return_array['user_payment_status'] = '';
        $return_array['user_last_transaction'] = '';
        $return_array['user_last_plan'] = '';

        $user_id = $this->session->userdata('user_id');
        // getting user detail for getting the paid status
        $user_payment_detail = $this->generalmodel->getOneRow('users', array('id' => $user_id), array('is_paid'));
        if (!empty($user_payment_detail)) {
            if ($user_payment_detail['is_paid'] == 0) {
                $latest_user_plan = $this->generalmodel->getOneRow('user_plan', array('user_id' => $user_id), '', 'date_entered', 'DESC');
                $latest_user_transaction = $this->generalmodel->getOneRow('transactions', array('user_id' => $user_id), '', 'date_entered', 'DESC');
                $return_array['user_payment_status'] = 0;
                $return_array['user_last_transaction'] = $latest_user_transaction;
                $return_array['user_last_plan'] = $latest_user_plan;
            } else if ($user_payment_detail['is_paid'] == 1) {
                $latest_user_plan = $this->generalmodel->getOneRow('user_plan', array('user_id' => $user_id), '', 'date_entered', 'DESC');
                $latest_user_transaction = $this->generalmodel->getOneRow('transactions', array('user_id' => $user_id), '', 'date_entered', 'DESC');
                $return_array['user_payment_status'] = 1;
                $return_array['user_last_transaction'] = $latest_user_transaction;
                $return_array['user_last_plan'] = $latest_user_plan;
            }
        }
        echo json_encode($return_array);
    }

    // function for getting the current plan status of the user
    public function getUserPlanForServer() {
        $return_array = array();
        $return_array['user_payment_status'] = '';
        $return_array['user_last_transaction'] = '';
        $return_array['user_last_plan'] = '';

        $user_id = $this->session->userdata('user_id');
        // getting user detail for getting the paid status
        $user_payment_detail = $this->generalmodel->getOneRow('users', array('id' => $user_id), array('is_paid'));
        if (!empty($user_payment_detail)) {
            if ($user_payment_detail['is_paid'] == 0) {
                $latest_user_plan = $this->generalmodel->getOneRow('user_plan', array('user_id' => $user_id), '', 'date_entered', 'DESC');
                $latest_user_transaction = $this->generalmodel->getOneRow('transactions', array('user_id' => $user_id), '', 'date_entered', 'DESC');
                $return_array['user_payment_status'] = 0;
                $return_array['user_last_transaction'] = $latest_user_transaction;
                $return_array['user_last_plan'] = $latest_user_plan;
            } else if ($user_payment_detail['is_paid'] == 1) {
                $latest_user_plan = $this->generalmodel->getOneRow('user_plan', array('user_id' => $user_id), '', 'date_entered', 'DESC');
                $latest_user_transaction = $this->generalmodel->getOneRow('transactions', array('user_id' => $user_id), '', 'date_entered', 'DESC');
                $return_array['user_payment_status'] = 1;
                $return_array['user_last_transaction'] = $latest_user_transaction;
                $return_array['user_last_plan'] = $latest_user_plan;
            }
        }
        return $return_array;
    }

    public function forgotPassword() {
        $data = json_decode(file_get_contents("php://input"));
        $email = $data->email;
        $_POST = json_decode(file_get_contents("php://input"), true);

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        if ($this->form_validation->run()) {
            // checking the user in the database
            $user = $this->generalmodel->getOneRow('users', array('email' => $email, 'status' => 1, 'deleted' => 0));
            $return_arr = array();
            if (empty($user)) {
                $return_arr['status'] = 'not_exists';
            } else {
                $password = '123456';
                $user_update_arr = array();
                $user_update_arr['password'] = md5($password);
                $this->generalmodel->update_row_by_condition('users', array('id' => $user['id']), $user_update_arr);
                    $return_arr['status'] = 'exists';
                // sending mail 
                $data_email = array();
                $data_email['first_name'] = $user['first_name'];
                $data_email['last_name'] = $user['last_name'];
                $data_email['dj_name'] = $user['dj_name'];
                $data_email['email'] = $user['email'];
                $data_email['password'] = $password;

                $message = $this->load->view('forgotpassword_template', $data_email, true);
                send_mail($data_email['email'], 'Opopopop Password Recovery', $message);
                // end of sending mail
            }
            echo json_encode($return_arr);
        } else {
            $this->output->set_output(validation_errors());
        }
    }
    
    
     public function changePassword() {
        authenticate(); 
        $data = json_decode(file_get_contents("php://input"));
        $password = $data->password;
        $confirm_password = $data->confirm_password;
        $_POST = json_decode(file_get_contents("php://input"), true);

        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[password]');
        if ($this->form_validation->run()) {
             $user_id = $this->session->userdata('user_id');
            // checking the user in the database
            $user = $this->generalmodel->getOneRow('users', array('id' => $user_id, 'status' => 1, 'deleted' => 0));
            $return_arr = array();
            if (empty($user)) {
                $return_arr['status'] = 'not_exists';
            } else {
                $user_update_arr = array();
                $user_update_arr['password'] = md5($password);
                $this->generalmodel->update_row_by_condition('users', array('id' => $user['id']), $user_update_arr);
                    $return_arr['status'] = 'exists';
            }
            echo json_encode($return_arr);
        } else {
            $this->output->set_output(validation_errors());
        }
    }
    
    
    
    public function test()
    {
         echo send_mail('priyranjan.singh@rediffmail.com', 'Opopopop Password Recovery', 'hello testitng');
    }        

    

}
