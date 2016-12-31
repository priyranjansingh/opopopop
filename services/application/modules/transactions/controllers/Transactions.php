<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions extends CI_Controller {

    public function add() {
        authenticate();
        $data = json_decode(file_get_contents("php://input"));
        $data_arr['plan_detail'] = $data->plan_detail;
        $_POST = json_decode(file_get_contents("php://input"), true);
        $this->form_validation->set_rules('plan_id', 'Plan ID', 'trim|required');


        if ($this->form_validation->run()) {
            $return_arr = array();
            $user_id = $this->session->userdata('user_id');
            // getting the invoice id
            $invoice_detail = $this->generalmodel->getOneRow('invoice', array('id' => INVOICE_ID));
            $inv_no = $invoice_detail['invoice_text'] . '-' . $invoice_detail['invoice_count'];

            $data_trans['id'] = create_guid();
            $data_trans['invoice'] = $inv_no;
            $data_trans['user_id'] = $user_id;
            $data_trans['plan_id'] = $data->plan_detail->id;
            $data_trans['payment_method'] = 'paypal';
            $data_trans['amount'] = $data->plan_detail->plan_price;
            $data_trans['payment_status'] = 'pending';
            $data_trans['created_by'] = $user_id;
            $data_trans['modified_by'] = $user_id;
            $data_trans['date_entered'] = date("Y-m-d H:i:s");
            $data_trans['date_modified'] = date("Y-m-d H:i:s");
            $trans_id = $this->generalmodel->insert_data("transactions", $data_trans);
            if ($trans_id) {
                $new_invoice_count = str_pad(($invoice_detail['invoice_count'] + 1), 6, '0', STR_PAD_LEFT);
                $update_arr = array();
                $update_arr['invoice_count'] = $new_invoice_count;
                $update_arr['date_modified'] = date("Y-m-d H:i:s");
                $update_arr['modified_by'] = $user_id;
                $affetted_rows = $this->generalmodel->update_row_by_id('invoice', 'id', INVOICE_ID, $update_arr);
            }
            $data_trans['plan_name'] = $data->plan_detail->plan_name;
            $data_trans['plan_duration'] = $data->plan_detail->plan_duration;
            $data_trans['plan_duration_type'] = getPlanDurationLabelPaypal($data->plan_detail->plan_duration_type);
            $return_arr['trans_detail'] = $data_trans;
            // get the paypal details 
            $data_paypal = array();
            $data_paypal['PAYPAL_BUSINESS_EMAIL'] = PAYPAL_BUSINESS_EMAIL;
            $data_paypal['PAYPAL_NOTIFY_URL'] = PAYPAL_NOTIFY_URL;
            $data_paypal['PAYPAL_CANCEL_URL'] = PAYPAL_CANCEL_URL;
            $data_paypal['PAYPAL_RETURN_URL'] = PAYPAL_RETURN_URL;
            $return_arr['paypal_detail'] = $data_paypal;
            echo json_encode($return_arr);
        } else {
            $this->output->set_output(validation_errors());
        }
    }

    public function applycoupon() {
        //authenticate();
        $data = json_decode(file_get_contents("php://input"));
        $plan_id = $data->plan_id;
        $coupon_code = $data->coupon_code;
        $_POST = json_decode(file_get_contents("php://input"), true);
        $this->form_validation->set_rules('coupon_code', 'Coupon Code', 'trim|required');
        if ($this->form_validation->run()) {
            $result_array = array();
            if (!empty($coupon_code)) {
                $plan_detail = $this->generalmodel->getOneRow('plans', array('id' => $plan_id, 'status' => 1, 'deleted' => 0));
                if (!empty($plan_detail)) {
                    $plan_price = $plan_detail['plan_price'];
                }
                $cur_date = date("Y-m-d");
                $coupon_code_detail = $this->generalmodel->getOneRow('coupon', array('code' => $coupon_code, 'status' => 1, 'deleted' => 0, 'expiry_date>=' => $cur_date, 'begin_date<=' => $cur_date));
                if (!empty($coupon_code_detail)) {
                    $discount = $coupon_code_detail['discount'];
                    $discount_type = $coupon_code_detail['discount_type'];
                    if ($discount_type == 'number') {
                        $final_payable_amount = $plan_price - $discount;
                    } else if ($discount_type == 'percent') {
                        $final_payable_amount = $plan_price - ($plan_price * $discount / 100);
                    }
                    $result_array['status'] = 'success';
                    $result_array['message'] = 'Your final amount payable is $' . $final_payable_amount;
                    $result_array['amount'] = $final_payable_amount;
                } else {
                    $result_array['status'] = 'failure';
                    $result_array['message'] = 'Sorry, there is no coupon by this code';
                }
            } else {
                $result_array['status'] = 'failure';
                $result_array['message'] = 'Couponcode can not be blank';
            }
            echo json_encode($result_array);
        } else {
            $this->output->set_output(validation_errors());
        }
    }

    public function notify() {
        if ($_POST) {
            // saving in log file
            $fp = fopen('./assets/payment_log.txt', 'w');
            fwrite($fp, json_encode($_POST));
            fclose($fp);
            // end of saving in the log file 
            if (!empty($_POST['payment_status'])) {
                if ($_POST['payment_status'] == "Completed") {

                    $custom = $_POST['custom'];
                    $custom_arr = explode("#", $custom);
                    $user_id = $custom_arr[0];
                    $plan_id = $custom_arr[1];
                    $transaction = $this->generalmodel->getOneRow('transactions', array('user_id' => $user_id, 'plan_id' => $plan_id, 'payment_status' => 'pending'));
                    if (!empty($transaction)) {
                        $transaction_update_arr = array();
                        $transaction_update_arr['payment_status'] = 'paid';
                        $transaction_update_arr['transaction_id'] = $_POST['txn_id'];
                        $transaction_update_arr['details'] = json_encode($_POST);
                        $transaction_update_arr['date_modified'] = date("Y-m-d H:i:s", strtotime($_POST['payment_date']));
                        $this->generalmodel->update_row_by_condition('transactions', array('id' => $transaction['id']), $transaction_update_arr);
                    }

                    // making entry in the user_plan table 
                    // first check whether there is any record or not by that user_id and plan_id and if there is any record then update 
                    // that otherwise make fresh entry 
                    // Before doing this if there is any entry of this user in the user_plan table of another plan id just delete that

                    $old_user_plan = $this->generalmodel->getOneRow('user_plan', array('user_id' => $user_id, 'plan_id !=' => $plan_id, 'status' => 1, 'deleted' => 0));
                    if (!empty($old_user_plan)) {
                        $user_plan_update_arr = array();
                        $user_plan_update_arr['status'] = 0;
                        $user_plan_update_arr['deleted'] = 1;
                        $this->generalmodel->update_row_by_condition('user_plan', array('id' => $old_user_plan['id']), $user_plan_update_arr);
                    }




                    $user_plan = $this->generalmodel->getOneRow('user_plan', array('user_id' => $user_id, 'plan_id' => $plan_id, 'status' => 1, 'deleted' => 0));
                    if (empty($user_plan)) {
                        $user_plan_data_arr = array();
                        $user_plan_data_arr['id'] = create_guid();
                        $user_plan_data_arr['user_id'] = $user_id;
                        $user_plan_data_arr['plan_id'] = $plan_id;
                        // getting plan detail
                        $plan_detail = $this->generalmodel->getOneRow('plans', array('id' => $plan_id));
                        $user_plan_data_arr['plan_start_date'] = date("Y-m-d");
                        $nxt_date_string = "+ " . $plan_detail['plan_duration'] . " " . $plan_detail['plan_duration_type'];
                        $user_plan_data_arr['plan_end_date'] = date("Y-m-d", strtotime($nxt_date_string));
                        $user_plan_data_arr['created_by'] = $user_id;
                        $user_plan_data_arr['modified_by'] = $user_id;
                        $user_plan_data_arr['date_entered'] = date("Y-m-d H:i:s");
                        $user_plan_data_arr['date_modified'] = date("Y-m-d H:i:s");
                        $this->generalmodel->insert_data('user_plan', $user_plan_data_arr);
                    } else {
                        // only update the current  record
                        // getting plan detail
                        $plan_detail = $this->generalmodel->getOneRow('plans', array('id' => $plan_id));
                        $user_plan_update_arr = array();
                        $nxt_date_string = "+ " . $plan_detail['plan_duration'] . " " . $plan_detail['plan_duration_type'];
                        $user_plan_update_arr['plan_end_date'] = date("Y-m-d", strtotime($nxt_date_string));
                        $user_plan_update_arr['modified_by'] = $user_id;
                        $user_plan_update_arr['date_modified'] = date("Y-m-d H:i:s");
                        $this->generalmodel->update_row_by_condition('user_plan', array('user_id' => $user_id, 'plan_id' => $plan_id, 'status' => 1, 'deleted' => 0), $user_plan_update_arr);
                    }

                    // making entry of fresh row in the transaction table i.e. invoice
                    $invoice = $this->generalmodel->getOneRow('invoice', array('id' => INVOICE_ID));
                    $inv_no = $invoice['invoice_text'] . '-' . $invoice['invoice_count'];
                    $transaction_data_arr = array();
                    $transaction_data_arr['id'] = create_guid();
                    $transaction_data_arr['invoice'] = $inv_no;
                    $transaction_data_arr['user_id'] = $user_id;
                    $transaction_data_arr['plan_id'] = $plan_id;
                    $transaction_data_arr['payment_method'] = 'paypal';
                    $transaction_data_arr['amount'] = $plan_detail['plan_price'];
                    $transaction_data_arr['payment_status'] = 'pending';
                    $transaction_data_arr['created_by'] = $user_id;
                    $transaction_data_arr['modified_by'] = $user_id;
                    $transaction_data_arr['date_entered'] = date("Y-m-d H:i:s");
                    $transaction_data_arr['date_modified'] = date("Y-m-d H:i:s");
                    $tran_add_status = $this->generalmodel->insert_data('transactions', $transaction_data_arr);
                    if ($tran_add_status) {
                        $new_invoice_count = str_pad(($invoice['invoice_count'] + 1), 6, '0', STR_PAD_LEFT);
                        $this->generalmodel->update_row_by_condition('invoice', array('id' => INVOICE_ID), array('invoice_count' => $new_invoice_count));
                    }

                    // end of making entry of fresh row in the transaction table i.e. invoice

                    $this->generalmodel->update_row_by_condition('users', array('id' => $user_id), array('is_paid' => 1));
                }
            }
        }
    }

    public function cancel() {
        redirect(FRONTEND_BASE_URL . "#/cancel");
    }

    public function thank() {
        redirect(FRONTEND_BASE_URL . "#/thankyou");
    }

    public function process() {
        require('./assets/stripe/init.php');
        $data = json_decode(file_get_contents("php://input"));
        $token = $data->stripe_token;
        $secret_key = stripe_secret_key;
        \Stripe\Stripe::setApiKey($secret_key);
        $user_id = $this->session->userdata('user_id');
        $customer_metadeta_arr = array('db_plan_id' => $data->plan_detail->id);
        $customer = \Stripe\Customer::create(array(
                    "source" => $token,
                    "plan" => $data->plan_detail->stripe_plan,
                    "email" => $this->session->userdata('user_name'),
                    "id" => $this->session->userdata('user_id'),
                    "metadata" => $customer_metadeta_arr,
                        )
        );
        $customer = json_decode(substr($customer, 22));

        $invoice = $this->generalmodel->getOneRow('invoice', array('id' => INVOICE_ID));
        $inv_no = $invoice['invoice_text'] . '-' . $invoice['invoice_count'];
        $transaction_data_arr = array();
        $transaction_data_arr['id'] = create_guid();
        $transaction_data_arr['invoice'] = $inv_no;
        $transaction_data_arr['user_id'] = $customer->id;
        $transaction_data_arr['plan_id'] = $data->plan_detail->id;
        $transaction_data_arr['payment_method'] = 'stripe';
        $transaction_data_arr['amount'] = ($customer->subscriptions->data[0]->plan->amount / 100);
        $transaction_data_arr['details'] = json_encode($customer);
        $transaction_data_arr['payment_status'] = 'pending';
        $transaction_data_arr['created_by'] = $customer->id;
        $transaction_data_arr['modified_by'] = $customer->id;
        $transaction_data_arr['date_entered'] = date("Y-m-d H:i:s");
        $transaction_data_arr['date_modified'] = date("Y-m-d H:i:s");
        $tran_add_status = $this->generalmodel->insert_data('transactions', $transaction_data_arr);
        if ($tran_add_status) {
            $new_invoice_count = str_pad(($invoice['invoice_count'] + 1), 6, '0', STR_PAD_LEFT);
            $this->generalmodel->update_row_by_condition('invoice', array('id' => INVOICE_ID), array('invoice_count' => $new_invoice_count));
        }
        echo json_encode($transaction_data_arr);
    }

    public function webhook($listner = '') {

        if (isset($listner) && $listner == 'stripe') {

            global $stripe_options;

            require('./assets/stripe/init.php');

            $secret_key = stripe_secret_key;

            \Stripe\Stripe::setApiKey($secret_key);

            // retrieve the request's body and parse it as JSON
            $body = @file_get_contents('php://input');
            // grab the event information
            $event_json = json_decode($body);

            // this will be used to retrieve the event from Stripe
            $event_id = $event_json->id;
            // pre($event_id, true);
            if (isset($event_json->id)) {

                try {
                    // to verify this is a real event, we re-retrieve the event from Stripe 
                    $event = \Stripe\Event::retrieve($event_json->id);
                    $test_data_arr = array();
                    $test_data_arr['id'] = create_guid();
                    $test_data_arr['response'] = $event;
                    $test_data_arr['date_entered'] = date("Y-m-d H:i:s");
                    // $test_data_add_status = $this->generalmodel->insert_data('test', $test_data_arr);
                    $event = substr($event, 19);
                    $event = json_decode($event);
                    // pre($event,true);
                    $data = $event->data->object;
                    // successful payment
                    if ($event->type == 'invoice.payment_succeeded') {
                        $invoice = $data->lines->data[0];
                        // send a payment receipt email here
                        // retrieve the payer's information
                        $customer = \Stripe\Customer::retrieve($data->customer);
                        // pre($customer);
                        // echo "--------------------------------------------------";
                        //$customer = json_decode($customer);
                        // pre($customer,true);
                        $email = $customer->email;
                        $user_id = $customer->id;
                        $plan_id = $customer->metadata->db_plan_id;
                        $test_data_arr1 = array();
                        $test_data_arr1['id'] = create_guid();
                        $test_data_arr1['response'] = $user_id . "#" . $plan_id;
                        $test_data_arr1['date_entered'] = date("Y-m-d H:i:s");
                        $this->generalmodel->insert_data('test', $test_data_arr1);


                        $transaction_id = $invoice->id;
                        $transaction = $this->generalmodel->getOneRow('transactions', array('user_id' => $user_id, 'plan_id' => $plan_id, 'payment_status' => 'pending'));
                        if (!empty($transaction)) {
                            $transaction_update_arr = array();
                            $transaction_update_arr['payment_status'] = 'paid';
                            $transaction_update_arr['transaction_id'] = $transaction_id;
                            $transaction_update_arr['details'] = $body;
                            $transaction_update_arr['date_modified'] = date("Y-m-d H:i:s");
                            $this->generalmodel->update_row_by_condition('transactions', array('id' => $transaction['id']), $transaction_update_arr);
                        }

                        // making entry in the user_plan table 
                        // first check whether there is any record or not by that user_id and plan_id and if there is any record then update 
                        // that otherwise make fresh entry 
                        // Before doing this if there is any entry of this user in the user_plan table of another plan id just delete that

                        $old_user_plan = $this->generalmodel->getOneRow('user_plan', array('user_id' => $user_id, 'plan_id !=' => $plan_id, 'status' => 1, 'deleted' => 0));
                        if (!empty($old_user_plan)) {
                            $user_plan_update_arr = array();
                            $user_plan_update_arr['status'] = 0;
                            $user_plan_update_arr['deleted'] = 1;
                            $this->generalmodel->update_row_by_condition('user_plan', array('id' => $old_user_plan['id']), $user_plan_update_arr);
                        }




                        $user_plan = $this->generalmodel->getOneRow('user_plan', array('user_id' => $user_id, 'plan_id' => $plan_id, 'status' => 1, 'deleted' => 0));
                        if (empty($user_plan)) {
                            $user_plan_data_arr = array();
                            $user_plan_data_arr['id'] = create_guid();
                            $user_plan_data_arr['user_id'] = $user_id;
                            $user_plan_data_arr['plan_id'] = $plan_id;
                            // getting plan detail
                            $plan_detail = $this->generalmodel->getOneRow('plans', array('id' => $plan_id));
                            $user_plan_data_arr['plan_start_date'] = date("Y-m-d");
                            $nxt_date_string = "+ " . $plan_detail['plan_duration'] . " " . $plan_detail['plan_duration_type'];
                            $user_plan_data_arr['plan_end_date'] = date("Y-m-d", strtotime($nxt_date_string));
                            $user_plan_data_arr['created_by'] = $user_id;
                            $user_plan_data_arr['modified_by'] = $user_id;
                            $user_plan_data_arr['date_entered'] = date("Y-m-d H:i:s");
                            $user_plan_data_arr['date_modified'] = date("Y-m-d H:i:s");
                            $this->generalmodel->insert_data('user_plan', $user_plan_data_arr);
                        } else {
                            // only update the current  record
                            // getting plan detail
                            $plan_detail = $this->generalmodel->getOneRow('plans', array('id' => $plan_id));
                            $user_plan_update_arr = array();
                            $nxt_date_string = "+ " . $plan_detail['plan_duration'] . " " . $plan_detail['plan_duration_type'];
                            $user_plan_update_arr['plan_end_date'] = date("Y-m-d", strtotime($nxt_date_string));
                            $user_plan_update_arr['modified_by'] = $user_id;
                            $user_plan_update_arr['date_modified'] = date("Y-m-d H:i:s");
                            $this->generalmodel->update_row_by_condition('user_plan', array('user_id' => $user_id, 'plan_id' => $plan_id, 'status' => 1, 'deleted' => 0), $user_plan_update_arr);
                        }

                        // making entry of fresh row in the transaction table i.e. invoice
                        $invoice = $this->generalmodel->getOneRow('invoice', array('id' => INVOICE_ID));
                        $inv_no = $invoice['invoice_text'] . '-' . $invoice['invoice_count'];
                        $transaction_data_arr = array();
                        $transaction_data_arr['id'] = create_guid();
                        $transaction_data_arr['invoice'] = $inv_no;
                        $transaction_data_arr['user_id'] = $user_id;
                        $transaction_data_arr['plan_id'] = $plan_id;
                        $transaction_data_arr['payment_method'] = 'stripe';
                        $transaction_data_arr['amount'] = $plan_detail['plan_price'];
                        $transaction_data_arr['payment_status'] = 'pending';
                        $transaction_data_arr['created_by'] = $user_id;
                        $transaction_data_arr['modified_by'] = $user_id;
                        $transaction_data_arr['date_entered'] = date("Y-m-d H:i:s");
                        $transaction_data_arr['date_modified'] = date("Y-m-d H:i:s");
                        $tran_add_status = $this->generalmodel->insert_data('transactions', $transaction_data_arr);
                        if ($tran_add_status) {
                            $new_invoice_count = str_pad(($invoice['invoice_count'] + 1), 6, '0', STR_PAD_LEFT);
                            $this->generalmodel->update_row_by_condition('invoice', array('id' => INVOICE_ID), array('invoice_count' => $new_invoice_count));
                        }
                        // end of making entry of fresh row in the transaction table i.e. invoice
                        
                        $this->generalmodel->update_row_by_condition('users', array('id' => $user_id), array('is_paid' => 1));
                        
                    } else {
                        echo $event->type;
                    }
                } catch (Exception $e) {
                    $headers = 'From: <info@dealrush.in>';
                    mail('singh.priyranjan@gmail.com', 'Jockdrive Payment Exception', $e, $headers);
                }
            }
        }
    }

    // for testing purpose . No use in code 
//    public function test_customer() {
//
//        global $stripe_options;
//
//        require('./assets/stripe/init.php');
//
//        $secret_key = stripe_secret_key;
//
//        \Stripe\Stripe::setApiKey($secret_key);
//        $customer = \Stripe\Customer::retrieve('dbffc057-8d4c-7649-0301-5856f6ad4e47');
//        pre(json_decode($customer));
//        pre($customer->metadata, true);
//        // pre($customer->id,true);
////        $test_data_arr1 = array();
////                        $test_data_arr1['id'] = create_guid();
////                        $test_data_arr1['response'] = $customer->metadata->db_plan_id."#".$customer->id;
////                        $test_data_arr1['date_entered'] = date("Y-m-d H:i:s");
////                        $this->generalmodel->insert_data('test', $test_data_arr1);
//    }

//    public function update_customer() {
//        require('./assets/stripe/init.php');
//
//        $secret_key = stripe_secret_key;
//
//        \Stripe\Stripe::setApiKey($secret_key);
//
//        $cu = \Stripe\Customer::retrieve("dbffc057-8d4c-7649-0301-5856f6ad4e47");
//        $cu->metadata = array('db_plan_id'=>'51c61b4d-7f33-708f-1c02-585226b6d301');
//        $cu->save();
//    }

}
