<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_logs extends PS_Controller
{
	public $title = 'API Logs';
	public $menu_code = 'IXAPILOG';
	public $menu_group_code = 'APILOG';
  public $menu_sub_group_code = '';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'rest/api/api_logs';
  	$this->load->model('rest/api/api_logs_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'logs_code', ''),
			'api_path' => get_filter('api_path', 'logs_path', 'all'),
      'status' => get_filter('status', 'logs_status', 'all'),
			'type' => get_filter('type', 'logs_type', 'all'),
			'action' => get_filter('action', 'logs_action', 'all'),
			'from_date' => get_filter('from_date', 'from_date', ''),
			'to_date' => get_filter('to_date', 'to_date', '')
    );

		if($this->input->post('search'))
		{
			redirect($this->home);
		}
		else
		{
			//--- แสดงผลกี่รายการต่อหน้า
			$perpage = get_rows();

			$segment  = 5; //-- url segment
			$rows     = $this->api_logs_model->count_rows($filter);
			//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
			$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
			$logs   = $this->api_logs_model->get_list($filter, $perpage, $this->uri->segment($segment));

			$filter['logs'] = $logs;			

			$this->pagination->initialize($init);
			$this->load->view('rest/api/api_logs_view', $filter);
		}
  }


	public function view_detail($id)
	{
		$ds = $this->api_logs_model->get_logs($id);

		$this->load->view('rest/api/api_logs_detail', $ds);
	}

	public function clear_filter()
	{
		$filter = array(
			'logs_code',
			'logs_path',
			'logs_status',
			'logs_type',
			'logs_action',
			'from_date',
			'to_date'
		);

		return clear_filter($filter);
	}

} //--- end classs
?>
