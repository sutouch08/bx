<?php
class Stock_balance_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_current_stock_zone($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse, $allZone, $zoneCode)
  {
    $this->db
    ->select('s.product_code, s.zone_code, s.qty')
    ->select('p.name AS product_name, p.cost AS price')
    ->select('z.warehouse_code, z.name AS zone_name')
    ->from('stock AS s')
    ->join('products AS p', 's.product_code = p.code', 'left')
    ->join('zone AS z', 's.zone_code = z.code', 'left')
    ->where('s.qty !=', 0, FALSE);

    if($allProduct == 0 && ! empty($pdFrom) && !  empty($pdTo))
    {
      $this->db->where('s.product_code >=', $pdFrom)->where('s.product_code <=', $pdTo);
    }

    if($allZone == 1 && empty($zoneCode))
    {
      if($allWhouse == 0 && ! empty($warehouse))
      {
        $this->db->where_in('z.warehouse_code', $warehouse);
      }
    }

    if($allZone == 0 && ! empty($zoneCode))
    {
      $this->db->where('s.zone_code', $zoneCode);
    }

    $rs = $this->db
    ->order_by('s.product_code', 'ASC')
    ->order_by('z.warehouse_code', 'ASC')
    ->order_by('s.zone_code', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_prev_stock_zone($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse, $allZone, $zoneCode, $date)
  {
    $qr  = "SELECT s.product_code, s.warehouse_code, s.zone_code ";
    $qr .= ", p.name AS product_name, p.cost AS price ";
    $qr .= ",z.name AS zone_name, (SUM(move_in) - SUM(move_out)) AS qty ";
    $qr .= "FROM stock_movement AS s ";
    $qr .= "LEFT JOIN products AS p ON s.product_code = p.code ";
    $qr .= "LEFT JOIN zone AS z ON s.zone_code = z.code ";
    $qr .= "WHERE s.date_add <= '{$date}' ";

    if($allProduct == 0 && ! empty($pdFrom) && !  empty($pdTo))
    {
      $qr .= "AND s.product_code >= '{$pdFrom}' AND s.product_code <= '{$pdTo}' ";
    }

    if($allZone == 1 && empty($zoneCode))
    {
      if($allWhouse == 0 && ! empty($warehouse))
      {
        $wh_list = "";
        $i = 1;
        foreach($warehouse as $wh)
        {
          $wh_list .= $i === 1 ? "'{$wh}'" : ", '{$wh}'";
          $i++;
        }

        $qr .= "AND s.warehouse_code IN({$wh_list}) ";
        $this->db->where_in('z.warehouse_code', $warehouse);
      }
    }

    if($allZone == 0 && ! empty($zoneCode))
    {
      $qr .= "AND s.zone_code = '{$zoneCode}' ";
    }

    $qr .= "GROUP BY s.product_code ";
    $qr .= "ORDER BY s.product_code ASC";
  
    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_stock_balance_zone($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse, $allZone, $zoneCode)
  {
    $this->db
    ->select('s.*')
    ->select('p.code AS product_code, p.name AS product_name, p.cost AS price, p.style_code')
    ->select('z.warehouse_code, z.name AS zone_name')
    ->from('stock AS s')
    ->join('products AS p', 's.product_code = p.code', 'left')
    ->join('zone AS z', 's.zone_code = z.code', 'left')
    ->where('s.qty !=', 0, FALSE);

    if($allProduct == 0 && ! empty($pdFrom) && !  empty($pdTo))
    {
      $this->db->where('p.style_code >=', $pdFrom)->where('p.style_code <=', $pdTo);
    }

    if($allZone == 1 && empty($zoneCode))
    {
      if($allWhouse == 0 && ! empty($warehouse))
      {
        $this->db->where_in('z.warehouse_code', $warehouse);
      }
    }

    if($allZone == 0 && ! empty($zoneCode))
    {
      $this->db->where('s.zone_code', $zoneCode);
    }

    $this->db->order_by('s.product_code', 'ASC')->order_by('z.warehouse_code', 'ASC')->order_by('s.zone_code', 'ASC');


    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


}
 ?>
