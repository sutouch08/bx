<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_brand extends PS_Controller
{
  public $menu_code = 'DBPDBR';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'เพิ่ม/แก้ไข ยี่ห้อสินค้า';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/product_brand';
    $this->load->model('masters/product_brand_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'code', ''),
      'name' => get_filter('name', 'name', '')
    );

    $perpage = get_rows();

		$segment = 4; //-- url segment
		$rows = $this->product_brand_model->count_rows($filter);
    $filter['data'] = $this->product_brand_model->get_list($filter, $perpage, $this->uri->segment($segment));
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
    $this->pagination->initialize($init);

    $data = array();

    if( ! empty($filter['data']))
    {
      foreach($filter['data'] as $rs)
      {
        $rs->menber = $this->product_brand_model->count_members($rs->code);
      }
    }

    $this->load->view('masters/product_brand/product_brand_view', $filter);
  }


  public function add_new()
  {
    $this->load->view('masters/product_brand/product_brand_add_view');
  }


  public function add()
  {
    if($this->input->post('code'))
    {
      $sc = TRUE;
      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $ds = array(
        'code' => $code,
        'name' => $name
      );

      if($this->product_brand_model->is_exists($code) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$code."' มีในระบบแล้ว");
      }

      if($this->product_brand_model->is_exists_name($name) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$name."' มีในระบบแล้ว");
      }

      if($sc === TRUE)
      {
        if( ! $this->product_brand_model->add($ds))
        {
          $sc = FALSE;
          set_error('เพิ่มข้อมูลไม่สำเร็จ');
        }
      }


      if($sc === FALSE)
      {
        $this->session->set_flashdata('code', $code);
        $this->session->set_flashdata('name', $name);
      }
    }
    else
    {
      set_error('ไม่พบข้อมูล');
    }

    redirect($this->home.'/add_new');
  }



  public function edit($code)
  {
    $this->title = 'แก้ไข ยี่ห้อสินค้า';
    $rs = $this->product_brand_model->get($code);
    $data = array(
      'code' => $rs->code,
      'name' => $rs->name
    );

    $this->load->view('masters/product_brand/product_brand_edit_view', $data);
  }



  public function update()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      $name = $this->input->post('name');

      if($this->product_brand_model->is_exists_name($name, $code))
      {
        $sc = FALSE;
        $this->error = "ชื่อซ้ำ โปรดใช้ชื่ออื่น";
      }

      if($sc === TRUE)
      {
        if( ! $this->product_brand_model->update($code, ['name' => $name]))
        {
          $sc = FALSE;
          set_error('ปรับปรุงข้อมูลไม่สำเร็จ');
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('ไม่พบข้อมูล');
    }

    $this->_response($sc);
  }



  public function delete($code)
  {
    if($code != '')
    {
      if($this->product_brand_model->delete($code))
      {
        set_message('ลบข้อมูลเรียบร้อยแล้ว');
      }
      else
      {
        set_error('ลบข้อมูลไม่สำเร็จ');
      }
    }
    else
    {
      set_error('ไม่พบข้อมูล');
    }

    redirect($this->home);
  }



  public function export_to_sap($code, $old_code = NULL)
  {
    $rs = $this->product_brand_model->get($code);
    if(!empty($rs))
    {
      $ext = $this->product_brand_model->is_sap_exists($old_code);

      $arr = array(
        'Code' => $rs->code,
        'Name' => $rs->name,
        'UpdateDate' => sap_date(now(), TRUE),
        'OLDCODE' => $ext ? $old_code : NULL,
        'Flag' => $ext ? 'U' : 'A'
      );

      return $this->product_brand_model->add_sap_brand($arr);

      // if($ext)
      // {
      //   $arr['Flag'] = 'U';
      //   if($code !== $old_code)
      //   {
      //     $arr['OLDCODE'] = $old_code;
      //   }
      //
      //   return $this->product_brand_model->update_sap_brand($old_code, $arr);
      // }
      // else
      // {
      //   $arr['Flag'] = 'A';
      //
      //   return $this->product_brand_model->add_sap_brand($arr);
      // }
    }

    return FALSE;
  }

  public function clear_filter()
	{
		clear_filter(array('code', 'name'));
	}

}//--- end class
 ?>
