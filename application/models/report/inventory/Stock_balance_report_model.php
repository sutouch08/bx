<?php
class Stock_balance_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_stock_balance_zone($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse, $allZone, $zoneCode)
  {
    $this->db
    ->select('s.*')
    ->select('p.code AS product_code, p.name AS product_name, p.cost AS price, p.style_code')
    ->select('z.code AS warehouse_code, z.name AS zone_name')
    ->from('stock AS s')
    ->join('products AS p', 's.product_code = p.code', 'left')
    ->joinn('zone AS z', 's.zone_code = z.code', 'left')
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
