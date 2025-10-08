<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stock_balance_zone extends PS_Controller
{
  public $menu_code = 'RICSBZ';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REINVT';
	public $title = 'รายงานสินค้าคงเหลือแยกตามโซน';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/inventory/stock_balance_zone';
    $this->load->model('report/inventory/stock_balance_report_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/warehouse_model');
  }

  public function index()
  {
    $whList = $this->warehouse_model->get_all();
    $ds['whList'] = $whList;
    $this->load->view('report/inventory/report_stock_balance_zone', $ds);
  }


  public function get_report()
  {
    ini_set('memory_limit','512M');
    $sc = TRUE;
    $bs = [];
    $bs['items'] = [];
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $whs = "";

      if( ! empty($ds->warehouse) && empty($ds->zoneCode))
      {
        $i = 1;
        foreach($ds->warehouse as $wh)
        {
          $whs .= $i === 1 ? $wh : ', '.$wh;
          $i++;
        }
      }

      $zoneName = empty($ds->zoneCode) ? NULL : $this->zone_model->get_name($ds->zoneCode);

      //---  Report title
      $bs['reportDate'] = thai_date($ds->date, FALSE, '/');
      $bs['whList'] = $ds->allWhouse == 1 ? 'ทั้งหมด' : $whs;
      $bs['zoneList'] = $ds->allZone == 1 ? 'ทั้งหมด' : $ds->zoneCode." - ".$zoneName;
      $bs['productList'] = $ds->allProduct == 1 ? 'ทั้งหมด' : '('.$ds->pdFrom.') - ('.$ds->pdTo.')';

      $date = from_date($ds->date);
      $today = from_date(now());

      if($date == $today)
      {
        $res = $this->stock_balance_report_model->get_current_stock_zone($ds->allProduct, $ds->pdFrom, $ds->pdTo, $ds->allWhouse, $ds->warehouse, $ds->allZone, $ds->zoneCode);
      }
      else
      {
        $res = $this->stock_balance_report_model->get_prev_stock_zone($ds->allProduct, $ds->pdFrom, $ds->pdTo, $ds->allWhouse, $ds->warehouse, $ds->allZone, $ds->zoneCode, $date);
      }

      if( ! empty($res))
      {
        if(count($res) > 2000)
        {
          $sc = FALSE;
          $this->error = "ข้อมูลมีปริมาณมากเกินกว่าจะแสดงผลได้ กรุณาส่งออกข้อมูลแทนการแสดงผลหน้าจอ";
        }
        else
        {
          $no = 1;
          $totalQty = 0;
          $totalAmount = 0;

          foreach($res as $rs)
          {
            $amount = $rs->qty * $rs->price;
            $bs['items'][] = array(
              'no' => number($no),
              'warehouse' => $rs->warehouse_code,
              'zone' => $rs->zone_name,
              'pdCode' => $rs->product_code,
              'pdName' => $rs->product_name,
              'price' => number($rs->price, 2),
              'qty' => number($rs->qty),
              'amount' => number($amount, 2)
            );

            $totalQty += $rs->qty;
            $totalAmount += $amount;
            $no++;
          }

          $bs['totalQty'] = number($totalQty);
          $bs['totalAmount'] = number($totalAmount, 2);
        }
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

    echo $sc === TRUE ? json_encode($bs) : $this->error;
  }





  public function do_export()
  {
    ini_set('memory_limit','512M');    
    $ds = json_decode($this->input->post('data'));
    $token = $this->input->post('token');

    $whs = "";

    if( ! empty($ds->warehouse) && empty($ds->zoneCode))
    {
      $i = 1;
      foreach($ds->warehouse as $wh)
      {
        $whs .= $i === 1 ? $wh : ', '.$wh;
        $i++;
      }
    }

    $zoneName = empty($ds->zoneCode) ? NULL : $this->zone_model->get_name($ds->zoneCode);

    $report_title = "รายงานสินค้าคงเหลือแยกตามโซน ณ วันที่ ".thai_date($ds->date, FALSE, '/');
    $whList = $ds->allWhouse == 1 ? 'ทั้งหมด' : $whs;
    $zoneList = $ds->allZone == 1 ? 'ทั้งหมด' : $ds->zoneCode." - ".$zoneName;
    $productList = $ds->allProduct == 1 ? 'ทั้งหมด' : '('.$ds->pdFrom.') - ('.$ds->pdTo.')';

    $date = from_date($ds->date);
    $today = from_date(now());

    if($date == $today)
    {
      $res = $this->stock_balance_report_model->get_current_stock_zone($ds->allProduct, $ds->pdFrom, $ds->pdTo, $ds->allWhouse, $ds->warehouse, $ds->allZone, $ds->zoneCode);
    }
    else
    {
      $res = $this->stock_balance_report_model->get_prev_stock_zone($ds->allProduct, $ds->pdFrom, $ds->pdTo, $ds->allWhouse, $ds->warehouse, $ds->allZone, $ds->zoneCode, $date);
    }

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Stock Balance Report');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->mergeCells('A1:G1');
    $this->excel->getActiveSheet()->setCellValue('A2', 'คลัง');
    $this->excel->getActiveSheet()->setCellValue('B2', $whList);
    $this->excel->getActiveSheet()->mergeCells('B2:G2');
    $this->excel->getActiveSheet()->setCellValue('A3', 'โซน');
    $this->excel->getActiveSheet()->setCellValue('B3', $zoneList);
    $this->excel->getActiveSheet()->mergeCells('B3:G3');
    $this->excel->getActiveSheet()->setCellValue('A4', 'สินค้า');
    $this->excel->getActiveSheet()->setCellValue('B4', $productList);
    $this->excel->getActiveSheet()->mergeCells('B4:G4');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A5', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B5', 'รหัสคลัง');
    $this->excel->getActiveSheet()->setCellValue('C5', 'รหัสโซน');
    $this->excel->getActiveSheet()->setCellValue('D5', 'ชื่อโซน');
    $this->excel->getActiveSheet()->setCellValue('E5', 'รหัสสินค้า');
    $this->excel->getActiveSheet()->setCellValue('F5', 'ชื่อสินค้า');
    $this->excel->getActiveSheet()->setCellValue('G5', 'ราคา');
    $this->excel->getActiveSheet()->setCellValue('H5', 'จำนวน');
    $this->excel->getActiveSheet()->setCellValue('I5', 'มูลค่า');

    $row = 6;

    if( ! empty($res))
    {
      $no = 1;

      $totalQty = 0;

      foreach($res as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->warehouse_code);
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->zone_code);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->zone_name);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->product_code);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->product_name);
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->price);
        $this->excel->getActiveSheet()->setCellValue('H'.$row, $rs->qty);
        $this->excel->getActiveSheet()->setCellValue('I'.$row, "=G{$row}*H{$row}");
        $no++;
        $row++;
      }

      $ro = $row - 1;
      $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
      $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
      $this->excel->getActiveSheet()->setCellValue('H'.$row, "=SUM(H6:H{$ro})");
      $this->excel->getActiveSheet()->setCellValue('I'.$row, "=SUM(I6:I{$ro})");
      $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
    }

    setToken($token);
    $file_name = "Report Stock Zone.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }
} //--- end class








 ?>
