<?php

class PeepSo3_REST_V1_Endpoint_File_Download extends PeepSo3_REST_V1_Endpoint {

    private $current_user;
    private $id;
    private $files_model;

    public function __construct() {

        parent::__construct();

        $this->current_user = get_current_user_id();
        $this->id = $this->input->int('id', 0); // the file id of profile being viewed
        $this->files_model = new PeepSoFilesModel();
    }

    public function read() {
        if ($this->id) {
            $post = get_post($this->id);
            $file = get_attached_file($post->ID);
            $wp_filetype = wp_check_filetype($file);
            $file_mime = $wp_filetype['ext'];
            
            //@TODO: Implementation permission

            nocache_headers();
            header("Content-type: $file_mime");
            header('Content-Disposition: attachment; filename="' . $post->post_title . '"');
            readfile($file);
            exit;
        }

        return [
            'error' => 'file_not_downloaded'
        ];
    }

protected function can_read() {
    //@TODO: Access validation
    return TRUE;
}

}
