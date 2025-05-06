<?php
class Current_stock_consignment_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_stock_summary()
  {
    $qr = "SELECT SUM(s.qty) AS qty, SUM(s.qty * p.cost) AS amount
          FROM stock AS s
          LEFT JOIN products AS p ON s.product_code = p.code
          WHERE s.qty != 0";

    $rs = $this->db->query($qr);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_stock_summary_by_group($code)
  {
    $qr = "SELECT SUM(s.qty) AS qty, SUM(s.qty * p.cost) AS amount
          FROM stock AS s
          LEFT JOIN products AS p ON s.product_code = p.code
          WHERE s.qty != 0 AND p.group_code = '{$code}'";

    $rs = $this->db->query($qr);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_style_summary($code)
  {
    $qr = "SELECT SUM(s.qty) AS qty, SUM(s.qty * p.cost) AS amount
          FROM stock AS s
          LEFT JOIN products AS p ON s.product_code = p.code
          WHERE s.qty != 0 AND p.style_code = '{$code}'";

    $rs = $this->db->query($qr);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_count_style()
  {
    return $this->db->count_all_results('product_style');
  }


  public function get_count_style_by_group($code)
  {
    return $this->db->where('group_code', $code)->count_all_results('product_style');
  }


  public function get_count_item()
  {
    return $this->db->count_all_results('products');
  }

  public function get_count_item_by_group($code)
  {
    return $this->db->where('group_code', $code)->count_all_results('products');
  }


  public function get_sum_stock_style_by_group($group_code)
  {
    $qr = "SELECT p.style_code AS code, SUM(s.qty) AS qty, SUM(s.qty * p.cost) AS amount
          FROM stock AS s
          LEFT JOIN products AS p ON s.product_code = p.code
          WHERE s.qty != 0 AND p.group_code = '{$group_code}'
          GROUP BY p.style_code
          ORDER BY s.qty DESC";

    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class


 ?>
