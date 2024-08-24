<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnYmF6ZlZBc1RBMVZZMlVLakVURzc2RGtYejNJdlNQQ2F1SGVXQklHNnNRdDcyelBvZ0Z0eTdTa2o1ZFFVcTRvcE53REVzMk55Y1d3ZlF2SjdEeUZwZ1NQM3R4Sk1nODRxRWhUREpabUhsNXF4bDRoYllJV3QzOW5talpKYzN1cVdRPQ==*/

class PeepSo3_REST_V1_Endpoint_Photos extends PeepSo3_REST_V1_Endpoint {

    private $page;
    private $limit;

    public function __construct() {

        parent::__construct();

        $this->page = $this->input->int('page', 1);
        $this->limit = $this->input->int('limit', 1);
    }

    public function read() {
        $offset = ($this->page - 1) * $this->limit;

        if ($this->page < 1) {
            $offset = 0;
        }

        $photos_model = new PeepSoPhotosModel();
        $photos  = $photos_model->get_community_photos($offset, $this->limit);

        if (count($photos)) {
            $message = 'success';
        } else {
            $message = __('No photo', 'picso');
        }

        return [
            'photos' => $photos,
            'message' => $message
        ];
    }

    protected function can_read() {
        return TRUE;
    }

}
