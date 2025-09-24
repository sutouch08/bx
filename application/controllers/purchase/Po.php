<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Po extends PS_Controller
{
  public $menu_code = 'PUPUOD';
	public $menu_group_code = 'PU';
  public $menu_sub_group_code = '';
	public $title;
  public $error;
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'purchase/po';
    $this->load->model('purchase/po_model');
    $this->load->model('masters/vender_model');
    $this->load->model('masters/products_model');
    $this->load->helper('po');
    $this->title = "ใบสั่งซื้อ";
  }


  public function index()
  {

    $filter = array(
      'code' => get_filter('code', 'po_code', ''),
      'vender' => get_filter('vender', 'po_vender', ''),
      'from_date' => get_filter('fromDate', 'po_from_date', ''),
      'to_date' => get_filter('toDate', 'po_to_date', ''),
      'status' => get_filter('status', 'po_status', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
      exit();
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->po_model->count_rows($filter);
      $filter['po'] = $this->po_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('purchase/po/po_list', $filter);
    }
  }


  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('purchase/po/po_add');
    }
    else
    {
      $this->deny_page();
    }
  }


  public function add()
  {
    $sc = TRUE;

    if($this->pm->can_add)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        if( ! empty($ds->doc_date) && ! empty($ds->vender_code) && ! empty($ds->vender_name))
        {
          $doc_date = db_date($ds->doc_date);
          $due_date = empty($ds->due_date) ? NULL : db_date($ds->due_date);
          $code = $this->get_new_code($doc_date);

          $arr = array(
            'code' => $code,
            'vender_code' => $ds->vender_code,
            'vender_name' => $ds->vender_name,
            'doc_date' => $doc_date,
            'due_date' => $due_date,
            'user' => $this->_user->uname,
            'remark' => get_null($ds->remark)
          );

          if( ! $this->po_model->add($arr))
          {
            $sc = FALSE;
            set_error('insert');
          }
        }
        else
        {
          $sc = FALSE;
          set_error('required');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  //--- add blunk details to po
  public function add_details()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->items))
    {
      $doc = $this->po_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 'P')
        {
          $this->db->trans_begin();

          foreach($ds->items as $rs)
          {
            if($sc === FALSE) { break; }

            $row = $this->po_model->get_detail_by_product($doc->code, $rs->code);

            if( ! empty($row))
            {
              $qty = $row->qty + $rs->qty;
              $open_qty = $row->open_qty + $rs->qty;

              $arr = array(
                'qty' => $qty,
                'open_qty' => $open_qty,
                'line_total' => $qty * $row->price,
                'valid' => 0
              );

              if( ! $this->po_model->update_detail($row->id, $arr))
              {
                $sc = FALSE;
                $this->error = "Update line item failed at {$rs->code}";
              }
            }
            else
            {
              $arr = array(
                'po_id' => $doc->id,
                'po_code' => $doc->code,
                'product_code' => $rs->code,
                'product_name' => $rs->name,
                'unit_code' => $rs->unit_code,
                'price' => $rs->price,
                'qty' => $rs->qty,
                'open_qty' => $rs->qty,
                'line_total' => $rs->qty * $rs->price,
                'update_user' => $this->_user->uname
              );

              if( ! $this->po_model->add_detail($arr))
              {
                $sc = FALSE;
                $this->error = "Insert line item failed at {$rs->code}";
              }
            }
          } //-- foreach

          if($sc === TRUE)
          {
            if( ! $this->po_model->recal_total($doc->code))
            {
              $sc = FALSE;
              $this->error = "Calculate document summary failed";
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  //--- add detail to po
  public function add_detail()
  {
    $sc = TRUE;

    $id = $this->input->post('id'); //-- po_id
    $code = $this->input->post('code'); //-- po_code
    $product_code = $this->input->post('product_code');
    $qty = $this->input->post('qty');
    $row_data = NULL; //--- เก็บข้อมูลสำหรับส่งกลับไป update row หลังจากทำงานเสร็จ
    $method = "add"; //--- วิธีการ update row หลังจากทำงานเสร็จ เพิ่มใหม่ หรือ update ของเก่า

    if( ! empty($id) && ! empty($code) && ! empty($product_code) && ! empty($qty))
    {
      if($qty > 0)
      {
        $item = $this->products_model->get($product_code);

        if( ! empty($item))
        {
          $doc = $this->po_model->get($code);

          if( ! empty($doc))
          {
            if($doc->status == 'P')
            {
              $row = $this->po_model->get_detail_by_product($code, $product_code);

              if( ! empty($row))
              {
                $method = "update";
                $new_qty = $row->qty + $qty;
                $open_qty = $row->open_qty + $qty;

                $arr = array(
                  'qty' => $new_qty,
                  'open_qty' => $open_qty,
                  'line_total' => $new_qty * $row->price,
                  'valid' => 0,
                  'update_user' => $this->_user->uname
                );

                if( ! $this->po_model->update_detail($row->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Update line item failed at {$item->code}";
                }

                if($sc === TRUE)
                {
                  $arr['id'] = $row->id;

                  $row_data = $arr;
                }
              }
              else
              {
                $arr = array(
                  'po_id' => $doc->id,
                  'po_code' => $doc->code,
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'unit_code' => $item->unit_code,
                  'price' => round($item->cost, 2),
                  'qty' => $qty,
                  'open_qty' => $qty,
                  'line_total' => $qty * $item->cost,
                  'update_user' => $this->_user->uname
                );

                $row_id = $this->po_model->add_detail($arr);

                if( ! $row_id)
                {
                  $sc = FALSE;
                  $this->error = "Insert line item failed at {$rs->code}";
                }

                if($sc === TRUE)
                {
                  $arr['id'] = $row_id;

                  $row_data = $arr;
                }
              }

              if($sc === TRUE)
              {
                $this->po_model->recal_total($doc->code);
              }
            }
            else
            {
              $sc = FALSE;
              set_error('status');
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Invalid document number";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid item code";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Qty must be more than 0";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'method' => $method,
      'data' => $row_data
    );

    echo json_encode($arr);
  }


  public function update_detail()
  {
    $sc = TRUE;
    $id = $this->input->post('id');
    $price = $this->input->post('price');
    $qty = $this->input->post('qty');

    if( ! empty($id) && ! empty($qty))
    {
      $row = $this->po_model->get_detail($id);

      if( ! empty($row))
      {
        $price = $price < 0 ? 0 : $price;
        $openQty = $row->open_qty;
        $diff = $qty - $row->qty; //--- ยอดต่างของจำนวนใหม่ กับ เก่า จะใช้ในการคำนวน openQty

        //---- ถ้าจำนวนใหม่น้อยกว่าจำนวนเก่า ต้องเอา openQty ออก
        if($diff < 0)
        {
          if($row->open_qty <= 0 || $row->open_qty < ($diff * -1))
          {
            $sc = FALSE;
            $this->error = "ไม่สามารถแก้ไขจำนวนได้เนื่องจากยอดค้างรับน้อยกว่ายอดที่ต้องการเปลี่ยนแปลง";
          }
          else
          {
            $openQty = $row->open_qty + $diff; //---- บวก diff ที่ติดลบ เพื่อลด openQty
          }
        }

        //--- ถ้าจำนวนใหม่มากกว่าจำนวนเก่า ต้องเพิ่ม openQty
        if($diff > 0)
        {
          $openQty = $row->open_qty + $diff;
        }

        if($sc === TRUE)
        {
          $arr = array(
            'qty' => $qty,
            'open_qty' => $openQty,
            'price' => $price,
            'line_total' => round($qty * $price, 2),
            'valid' => 0,
            'update_user' => $this->_user->uname
          );

          if( ! $this->po_model->update_detail($id, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update item row";
          }
        }

        if($sc === TRUE)
        {
          $this->po_model->recal_total($row->po_code);
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Item row not found";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function edit($code)
  {
    $po = $this->po_model->get($code);

    if( ! empty($po))
    {
      $ds = array(
        'po' => $po,
        'details' => $this->po_model->get_details($code)
      );

      $this->load->view('purchase/po/po_edit', $ds);
    }
    else
    {
      $this->error_page();
    }
  }


  public function get_product_grid($style_code)
  {
    $sc = TRUE;
    $this->load->library('purchase_grid');

    $grid = $this->purchase_grid->getProductGrid($style_code);

    if( ! empty($grid->data))
    {
      $tbs = '<table class="table table-bordered border-1" style="min-width:'.$grid->width.'px;">';
      $tbe = '</table>';
      $grid->data = $tbs.$grid->data.$tbe;
    }

    echo json_encode($grid);
  }


  public function remove_checked_details()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->rows))
    {
      $doc = $this->po_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status === 'P')
        {
          if( ! empty($ds->rows))
          {
            $this->db->trans_begin();

            foreach($ds->rows as $rs)
            {
              $row = $this->po_model->get_detail($rs->id);

              if( ! empty($row))
              {
                if($row->qty > $row->open_qty)
                {
                  $sc = FALSE;
                  $this->error = "ไม่สามารถลบรายการ {$row->product_code} เนื่องจากมีการรับเข้าแล้ว";
                }

                if($sc === TRUE)
                {
                  if( ! $this->po_model->delete_detail($rs->id))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to delete item row";
                  }
                }
              }

              if($sc === FALSE) { break; }
            }

            if($sc === TRUE)
            {
              if( ! $this->po_model->recal_total($doc->code))
              {
                $sc = FALSE;
                $this->error = "Calculate document summary failed";
              }
            }

            if($sc === TRUE)
            {
              $this->db->trans_commit();
            }
            else
            {
              $this->db->trans_rollback();
            }
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function update()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->doc_date) && ! empty($ds->vender_code))
    {
      $doc = $this->po_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 'P')
        {
          $doc_date = db_date($ds->doc_date);
          $due_date = empty($ds->due_date) ? NULL : db_date($ds->due_date);

          $arr = array(
            'vender_code' => $ds->vender_code,
            'vender_name' => $ds->vender_name,
            'doc_date' => $doc_date,
            'due_date' => $due_date,
            'update_user' => $this->_user->uname,
            'remark' => get_null($ds->remark)
          );

          if( ! $this->po_model->update($ds->code, $arr))
          {
            $sc = FALSE;
            set_error('update');
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function save()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->po_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'P')
        {
          $arr = array(
            'status' => 'O',
            'update_user' => $this->_user->uname
          );

          if( ! $this->po_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update document status";
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function unsave()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->po_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'O' OR $this->_SuperAdmin)
        {
          $this->db->trans_begin();

          $arr = array(
            'status' => 'P',
            'update_user' => $this->_user->uname
          );

          if( ! $this->po_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update document status";
          }

          if($sc === TRUE)
          {
            if( ! $this->po_model->un_close_details($code))
            {
              $sc = FALSE;
              $this->error = "Failed to update line status";
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function view_detail($code)
  {
    $doc = $this->po_model->get($code);

    if( ! empty($doc))
    {
      $ds = array(
        'po' => $doc,
        'details' => $this->po_model->get_details($code)
      );

      $this->load->view('purchase/po/po_view_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function close_po()
  {
    $sc = TRUE;

    if($this->pm->can_add OR $this->pm->can_edit)
    {
      $code = $this->input->post('code');

      if( ! empty($code))
      {
        $doc = $this->po_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status == 'O')
          {
            $this->db->trans_begin();

            $arr = array(
              'status' => 'C',
              'update_user' => $this->_user->uname
            );

            if( ! $this->po_model->update($code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update document status";
            }

            if($sc === TRUE)
            {
              $arr = array(
                'line_status' => 'C',
                'update_user' => $this->_user->uname
              );

              if( ! $this->po_model->update_details($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update line status";
              }
            }

            if($sc === TRUE)
            {
              $this->db->trans_commit();
            }
            else
            {
              $this->db->trans_rollback();
            }
          }
          else
          {
            $sc = FALSE;
            set_error('status');
          }
        }
        else
        {
          $sc = FALSE;
          set_error('notfound');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function cancle()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $reason = $this->input->post('reason');

    if($this->pm->can_delete)
    {
      if( ! empty($code))
      {
        $doc = $this->po_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status != 'D' && $doc->status != 'C')
          {
            if($this->po_model->is_received($code))
            {
              $sc = FALSE;
              $this->error = "ไม่สามารถยกเลิกได้ เนื่องจากมีการรับเข้าแล้ว";
            }

            if($sc === TRUE)
            {
              $this->db->trans_begin();

              $arr = array(
                'status' => 'D',
                'cancel_reason' => get_null($reason),
                'cancel_user' => $this->_user->uname,
                'cancel_date' => now()
              );

              if( ! $this->po_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update document status";
              }

              if($sc === TRUE)
              {
                $arr = array(
                  'line_status' => 'D',
                  'update_user' => $this->_user->uname
                );

                if( ! $this->po_model->update_details($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update item rows status";
                }
              }

              if($sc === TRUE)
              {
                $this->db->trans_commit();
              }
              else
              {
                $this->db->trans_rollback();
              }
            }
          }
          else
          {
            $sc = FALSE;
            set_error('status');
          }
        }
        else
        {
          $sc = FALSE;
          set_error('notfound');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function print_po($code)
  {
    $this->load->library('xprinter');

    $po = $this->po_model->get($code);

    $details = $this->po_model->get_details($code);

		$ds = array(
			'po' => $po,
			'details' => $details,
			'title' => "ใบสั่งซื้อ",
			'vender' => $this->vender_model->get($po->vender_code)
		);

    $this->load->view('print/print_po', $ds);
  }


  public function import_po()
  {
    ini_set('max_execution_time', 1200);
    ini_set('memory_limit','1000M');

    $sc = TRUE;

    $import = 0;
    $success = 0;
    $failed = 0;
    $skip = 0;
    $code = $this->input->post('code');
    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
    $path = $this->config->item('upload_path').'po/';
    $file	= 'uploadFile';
    $config = array(   // initial config for upload class
      "allowed_types" => "xlsx",
      "upload_path" => $path,
      "file_name"	=> "import-po-".date('YmdHis'),
      "max_size" => 5120,
      "overwrite" => TRUE
    );

    $this->load->library("upload", $config);

    if(! $this->upload->do_upload($file))
    {
      $sc = FALSE;
      $this->error = $this->upload->display_errors();
    }
    else
    {
      $this->load->library('excel');
      $info = $this->upload->data();
      $excel = PHPExcel_IOFactory::load($info['full_path']);
      $excel->setActiveSheetIndex(0);
      $sheet = $excel->getSheet(0);

      if( ! empty($sheet))
      {
        $rows = $sheet->getHighestRow();
        $limit = 5001;

        if($rows > $limit)
        {
          $sc = FALSE;
          $this->error = "ไฟล์มีจำนวนรายการเกิน {$limit} บรรทัด";
        }

        $doc = $this->po_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status != 'P')
          {
            $sc = FALSE;
            set_error('status');
          }
        }
        else
        {
          $sc = FALSE;
          set_error('notfound');
        }

        $ds = [];

        if($sc === TRUE)
        {
          $headCol = array(
            'A' => 'Item Code',
            'B' => 'Price',
            'C' => 'Qty'
          );

          $i = 1;

          while($i < $rows)
          {
            if($sc === FALSE)
            {
              break;
            }

            if($i == 1)
            {
              foreach($headCol as $col => $field)
              {
                $value = $sheet->getCell($col.$i)->getValue();

                if(empty($value) OR $value !== $field)
                {
                  $sc = FALSE;
                  $this->error .= 'Column '.$col.' Should be '.$field.'<br/>';
                }
              }

              if($sc === FALSE)
              {
                $this->error .= "<br/><br/>You should download new template !";
                break;
              }

              $i++;
            }
            else
            {
              $rs = [];

              foreach($headCol as $col => $field)
              {
                $column = $col.$i;

                $rs[$col] = $sheet->getCell($column)->getValue();
              }

              if($sc === TRUE && ! empty($rs['A']) && ! empty($rs['B']) && ! empty($rs['C']))
              {
                $item_code = trim($rs['A']);
                $price = empty($rs['B']) ? 0.00 : str_replace(",", "", $rs['B']);
                $price = is_numeric($price) ? $price : 0;
                $qty = empty(trim($rs['C'])) ? 1 : str_replace(',', '', $rs['C']);
                $qty = is_numeric($qty) ? $qty : 1;

                $item = $this->products_model->get($item_code);

                if( ! empty($item))
                {
                  $ds[] = (object) array(
                    'po_id' => $doc->id,
                    'po_code' => $doc->code,
                    'product_code' => $item->code,
                    'product_name' => $item->name,
                    'unit_code' => $item->unit_code,
                    'price' => $price,
                    'qty' => $qty,
                    'open_qty' => $qty,
                    'line_total' => $qty * $price,
                    'update_user' => $this->_user->uname
                  );
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "Invalid Item code '{$item_code}' at Line {$i} ";
                }
              }

              $i++;
            } // end if $i == 1
          } // end while


          if($sc === TRUE && ! empty($ds))
          {
            $this->db->trans_begin();

            if($this->po_model->delete_all_details($code))
            {
              foreach($ds as $rs)
              {
                if($sc === FALSE)
                {
                  break;
                }

                $arr = array(
                  'po_id' => $rs->po_id,
                  'po_code' => $rs->po_code,
                  'product_code' => $rs->product_code,
                  'product_name' => $rs->product_name,
                  'unit_code' => $rs->unit_code,
                  'price' => $rs->price,
                  'qty' => $rs->qty,
                  'open_qty' => $rs->qty,
                  'line_total' => $rs->line_total,
                  'update_user' => $rs->update_user
                );

                if( ! $this->po_model->add_detail($arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to insert item row";
                }
              }

              if($sc === TRUE)
              {
                $this->po_model->recal_total($code);
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Failed to delete previous item rows";
            }

            if($sc === TRUE)
            {
              $this->db->trans_commit();
            }
            else
            {
              $this->db->trans_rollback();
            }
          }
        } //--- if $sc == TRUE
      }
    }

    $this->_response($sc);
  }


  public function get_new_code($date = '')
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_PO');
    $run_digit = getConfig('RUN_DIGIT_PO');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->po_model->get_max_code($pre);
    if(! is_null($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


  public function get_template_file()
  {
    $path = $this->config->item('upload_path').'po/';
    $file_name = $path."import_po_template.xlsx";

    if(file_exists($file_name))
    {
      header('Content-Description: File Transfer');
      header('Content-Type:Application/octet-stream');
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: 0');
      header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
      header('Content-Length: '.filesize($file_name));
      header('Pragma: public');

      flush();
      readfile($file_name);
      die();
    }
    else
    {
      echo "File Not Found";
    }
  }

  public function clear_filter()
  {
    $filter = array('po_code', 'po_vender', 'po_from_date', 'po_to_date', 'po_status');
    clear_filter($filter);
  }
} //-- end class
?>
