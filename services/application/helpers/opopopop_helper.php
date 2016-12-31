<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function pre($data, $exit = false) {
    echo "<pre>";
    print_r($data);
    if ($exit) {
        exit;
    }
}

function authenticate() {
    $CI = & get_instance();
    $user_details = $CI->session->userdata('user_name');
    if (empty($user_details)) {
        redirect('/');
    }
}

function view($data) {
    $CI = & get_instance();
    $CI->load->view($data['template'] . "/layout", $data);
}

function create_guid() {
    $microTime = microtime();
    list($a_dec, $a_sec) = explode(" ", $microTime);
    $dec_hex = dechex($a_dec * 1000000);
    $sec_hex = dechex($a_sec);
    ensure_length($dec_hex, 5);
    ensure_length($sec_hex, 6);
    $guid = "";
    $guid .= $dec_hex;
    $guid .= create_guid_section(3);
    $guid .= '-';
    $guid .= create_guid_section(4);
    $guid .= '-';
    $guid .= create_guid_section(4);
    $guid .= '-';
    $guid .= create_guid_section(4);
    $guid .= '-';
    $guid .= $sec_hex;
    $guid .= create_guid_section(6);
    return $guid;
}

function ensure_length(&$string, $length) {
    $strlen = strlen($string);
    if ($strlen < $length) {
        $string = str_pad($string, $length, "0");
    } else if ($strlen > $length) {
        $string = substr($string, 0, $length);
    }
}

function create_guid_section($characters) {
    $return = "";
    for ($i = 0; $i < $characters; $i++) {
        $return .= dechex(mt_rand(0, 15));
    }
    return $return;
}

function getPlanDurationLabelPaypal($duration) {
    $label = '';
    if ($duration == 'day') {
        $label = "D";
    } else if ($duration == 'week') {
        $label = "W";
    } else if ($duration == 'month') {
        $label = "M";
    } else if ($duration == 'year') {
        $label = "Y";
    }
    return $label;
}

function send_mail($to,$subject,$message) {
    $CI = & get_instance();
    $CI->load->library('email');
    $CI->email->set_newline("\r\n");

    $CI->email->from('info@dealrush.in', 'Opopopop');
    $CI->email->to($to);

    $CI->email->subject($subject);
    $CI->email->message($message);

    $CI->email->send();
}
