<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stock_balance extends PS_Controller
{
  public $menu_code = 'RICSTB';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REINVT';
	public $title = 'รายงานสินค้าคงเหลือ';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/inventory/stock_balance';
    $this->load->model('report/inventory/inventory_report_model');
    $this->load->model('masters/products_model');
  }

  public function index()
  {
    $this->load->model('masters/warehouse_model');
    $whList = $this->warehouse_model->get_all();
    $ds['whList'] = $whList;
    $this->load->view('report/inventory/report_stock_balance', $ds);
  }


  public function get_report()
  {
    ini_set('memory_limit','512M');

    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $whs = "";

      if( ! empty($ds->warehouse))
      {
        $i = 1;

        foreach($ds->warehouse as $wh)
        {
          $whs .= $i === 1 ? $wh : ', '.$wh;
          $i++;
        }
      }

      $bs = [];
      $bs['items'] = [];

      $bs['reportDate'] = thai_date($ds->date, FALSE, '/');
      $bs['whList'] = $ds->allWhouse == 1 ? 'ทั้งหมด' : $whs;
      $bs['productList'] = $ds->allProduct == 1 ? 'ทั้งหมด' : "({$ds->pdFrom}) - ({$ds->pdTo})";

      $date = from_date($ds->date);
      $today = from_date(now());

      if($date == $today)
      {
        $res = $this->inventory_report_model->get_current_stock_balance($ds->allProduct, $ds->pdFrom, $ds->pdTo, $ds->allWhouse, $ds->warehouse);
      }
      else
      {
        $res = $this->inventory_report_model->get_prev_stock_balance($ds->allProduct, $ds->pdFrom, $ds->pdTo, $ds->allWhouse, $ds->warehouse, $date);
      }

      if( ! empty($res))
      {
        $no = 1;
        $totalQty = 0;
        $totalAmount = 0;

        foreach($res as $rs)
        {
          $bs['items'][] = array(
            'no' => number($no),
            'barcode' => $rs->barcode,
            'pdCode' => $rs->code,
            'pdName' => $rs->name,
            'cost' => number($rs->cost, 2),
            'qty' => number($rs->qty),
            'amount' => number($rs->cost * $rs->qty, 2)
          );

          $no++;
          $totalQty += $rs->qty;
          $totalAmount += ($rs->qty * $rs->cost);
        }

        $bs['totalQty'] = number($totalQty);
        $bs['totalAmount'] = number($totalAmount, 2);
      }
      else
      {
        $bs['items'][] = ['nodata' => 'nodata'];
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    echo $sc == TRUE ? json_encode($bs) : $this->error;
  }


  public function do_export()
  {
    ini_set('memory_limit','512M');
    $ds = json_decode($this->input->post('data'));
    $token = $this->input->post('token');

    $whs = "";

    if( ! empty($ds->warehouse))
    {
      $i = 1;

      foreach($ds->warehouse as $wh)
      {
        $whs .= $i === 1 ? $wh : ', '.$wh;
        $i++;
      }
    }

    //---  Report title
    $report_title = 'รายงานสินค้าคงเหลือ ณ วันที่  '.thai_date($ds->date, FALSE, '/');
    $wh_title = 'คลัง :  '. ($ds->allWhouse == 1 ? 'ทั้งหมด' : $whs);
    $pd_title = 'สินค้า :  '. ($ds->allProduct == 1 ? 'ทั้งหมด' : "({$ds->pdFrom}) - ({$ds->pdTo})");

    $date = from_date($ds->date);
    $today = from_date(now());

    if($date == $today)
    {
      $res = $this->inventory_report_model->get_current_stock_balance($ds->allProduct, $ds->pdFrom, $ds->pdTo, $ds->allWhouse, $ds->warehouse);
    }
    else
    {
      $res = $this->inventory_report_model->get_prev_stock_balance($ds->allProduct, $ds->pdFrom, $ds->pdTo, $ds->allWhouse, $ds->warehouse, $date);
    }

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Stock Balance Report');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->mergeCells('A1:G1');
    $this->excel->getActiveSheet()->setCellValue('A2', $wh_title);
    $this->excel->getActiveSheet()->mergeCells('A2:G2');
    $this->excel->getActiveSheet()->setCellValue('A3', $pd_title);
    $this->excel->getActiveSheet()->mergeCells('A3:G3');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A4', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B4', 'บาร์โค้ด');
    $this->excel->getActiveSheet()->setCellValue('C4', 'รหัส');
    $this->excel->getActiveSheet()->setCellValue('D4', 'สินค้า');
    $this->excel->getActiveSheet()->setCellValue('E4', 'ทุน');
    $this->excel->getActiveSheet()->setCellValue('F4', 'จำนวน');
    $this->excel->getActiveSheet()->setCellValue('G4', 'มูลค่า');

    $row = 5;

    if( ! empty($res))
    {
      $no = 1;

      foreach($res as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->barcode);
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->name);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->cost);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->qty);
        $this->excel->getActiveSheet()->setCellValue('G'.$row, ($rs->cost * $rs->qty));
        $no++;
        $row++;
      }

      $re = $row -1;

      $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
      $this->excel->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
      $this->excel->getActiveSheet()->setCellValue('F'.$row, '=SUM(F5:F'.$re.')');
      $this->excel->getActiveSheet()->setCellValue('G'.$row, '=SUM(G5:G'.$re.')');

      $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('B5:B'.$re)->getNumberFormat()->setFormatCode('0');
      $this->excel->getActiveSheet()->getStyle('F5:G'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('F5:F'.$row)->getNumberFormat()->setFormatCode('#,##0');
      $this->excel->getActiveSheet()->getStyle('G5:G'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
    }

    setToken($token);
    $file_name = "Report Stock Balance.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }

} //--- end class
?>
