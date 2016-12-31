<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Music extends CI_Controller {

    public function getTotalCount() {
//        authenticate();
//        $music_list = $this->generalmodel->getListValue('original_media', array('status' => 1, 'deleted' => 0, 'type' => 1), '', 'date_entered', 'DESC');
//        echo json_encode(count($music_list));
        
        authenticate();
        $this->db->select("*");
        $this->db->where(array('status' => 1, 'deleted' => 0));
        $this->db->group_by('original_song_id');
        $this->db->from('original_remix');
        $query = $this->db->get();
        $original_music_list = $query->result_array();
        echo json_encode(count($original_music_list));
    }

//    public function listing($limit = 0, $offset = ITEM_PER_PAGE) {
//        authenticate();
//        $this->db->select("om.*,g.name genre_name,g.folder_name genre_folder_name,sg.name subgenre_name,sg.folder_name subgenre_folder_name");
//        $this->db->limit($offset, $limit);
//        $this->db->where(array('om.status' => 1, 'om.deleted' => 0, 'om.type' => 1));
//        $this->db->order_by('date_entered', 'DESC');
//        $this->db->join('genres sg', 'sg.id = om.sub_genre', 'left');
//        $this->db->join('genres g', 'g.id = om.genre');
//        $this->db->from('original_media om');
//        $query = $this->db->get();
//        $music_list = $query->result_array();
//        echo json_encode($music_list);
//    }

    public function detail($slug) {
        authenticate();
        $music_detail = $this->generalmodel->getOneRow('original_media', array('slug' => $slug));
        echo json_encode($music_detail);
    }

    public function listing($limit = 0, $offset = ITEM_PER_PAGE) {
        $this->db->select("or.original_song_id,om.song_name,om.artist_name,DATE_FORMAT(om.date_entered, '%b %c %Y')as  date_entered");
        $this->db->limit($offset, $limit);
        $this->db->where(array('or.status' => 1, 'or.deleted' => 0));
        $this->db->order_by('or.date_entered','DESC');
        $this->db->group_by('or.original_song_id');
        $this->db->join('original_media om', 'om.id = or.original_song_id');
        $this->db->from('original_remix or');
        $query = $this->db->get();
        $original_music_list = $query->result_array();
        $return_arr = array();
        $temp_arr = array();
        foreach ($original_music_list as $key => $val) {
            $temp_arr['original_song_detail'] = $val;
            $this->db->select("rm.song_name,rm.file_name,DATE_FORMAT(rm.date_entered, '%b %c %Y')as  date_entered,g.name genre_name,g.folder_name genre_folder_name,sg.name subgenre_name,sg.folder_name subgenre_folder_name");
            $this->db->where('or.original_song_id', $val['original_song_id']);
            $this->db->join('remix_media rm', 'rm.id = or.remix_song_id');
            $this->db->join('genres sg', 'sg.id = rm.sub_genre', 'left');
            $this->db->join('genres g', 'g.id = rm.genre');
            $this->db->from('original_remix or');
            $query = $this->db->get();
            $remix_music_list = $query->result_array();
            $temp_arr['remix_songs_list'] = $remix_music_list;
            array_push($return_arr, $temp_arr);
            $temp_arr = array();
        }
        echo json_encode($return_arr);
    }

}
