<?php
class Inventory_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function getStock($option, $limit = 100, $offset = 0)
  {
    $this->db
    ->select('s.product_code AS ItemCode, s.zone_code')
    ->select_sum('s.qty', 'OnHand')
    ->from('stock AS s')
    ->join('products AS p', 's.product_code = p.code', 'left')
    ->join('zone AS z', 's.zone_code = z.code', 'left')
    ->where('s.qty >', 0, FALSE);

    if($option->allProduct == 0 && ! empty($option->pdFrom) && ! empty($option->pdTo))
    {
      $this->db->where('p.style_code >=', $option->pdFrom)->where('p.style_code <=', $option->pdTo);
    }

    if($option->allWhouse == 0 && ! empty($option->whsList))
    {
      $this->db->where_in('z.warehouse_code', $option->whsList);
    }

    $rs = $this->db->group_by('s.product_code')->order_by('s.product_code', 'ASC')->limit($limit, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_current_stock_balance($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse)
  {
    $this->db
    ->select('s.*')
    ->select_sum('s.qty')
    ->from('stock AS s')
    ->join('products AS p', 's.product_code = p.code', 'left')
    ->join('zone AS z', 's.zone_code = z.code', 'left')
    ->where('s.qty >', 0, FALSE);

    if($allProduct == 0 && ! empty($pdFrom) && ! empty($pdTo))
    {
      $this->db
      ->where('p.stylel_code >=', $pdFrom)
      ->where('p.style_code <=', $pdTo);
    }

    if($allWhouse == 0 && ! empty($warehouse))
    {
      $this->db->where_in('z.warehouse_code', $warehouse);
    }

    $this->db->group_by('s.product_code')->order_by('s.product_code', 'ASC');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_reserv_stock($item_code, $warehouse = NULL)
  {
    $this->db
    ->select_sum('order_details.qty', 'qty')
    ->from('order_details')
    ->join('orders', 'order_details.order_code = orders.code', 'left')
    ->where('order_details.product_code', $item_code)
    ->where('order_details.is_complete', 0)
    ->where('order_details.is_expired', 0)
		->where('order_details.is_cancle', 0)
    ->where('order_details.is_count', 1);

    if($warehouse !== NULL)
    {
      $this->db->where_in('orders.warehouse_code', $warehouse);
    }

    $rs = $this->db->get();

    if($rs->num_rows() == 1)
    {
      return empty($rs->row()->qty) ? 0 : $rs->row()->qty;
    }

    return 0;
  }

}
 ?>
