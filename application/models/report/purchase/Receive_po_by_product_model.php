<?php
class Receive_po_by_product_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function get_data(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db
      ->select('rp.code, rp.po_code, rp.invoice_code')
      ->select('rp.vender_code, rp.vender_name, rp.date_add')
      ->select('rd.product_code, rd.qty, rd.amount')
      ->from('receive_product_detail AS rd')
      ->join('receive_product AS rp', 'rd.receive_code = rp.code', 'left')
      ->join('products AS pd', 'rd.product_code = pd.code', 'left')
      ->join('product_size AS si', 'pd.size_code = si.code', 'left')
      ->where('rp.date_add >=', $ds['fromDate'])
      ->where('rp.date_add <=', $ds['toDate'])
      ->where('rp.status', 'C');

      if(empty($ds['allProduct']))
      {
        if(!empty($ds['pdFrom']) && !empty($ds['pdTo']))
        {
          $this->db->where('rd.product_code >=', $ds['pdFrom']);
          $this->db->where('rd.product_code <=', $ds['pdTo']);
        }
      }

      if(empty($ds['allDoc']))
      {
        if(!empty($ds['docFrom']) && !empty($ds['docTo']))
        {
          $this->db->where('rp.code >=', $ds['docFrom']);
          $this->db->where('rp.code <=', $ds['docTo']);
        }
      }

      if(empty($ds['allVendor']))
      {
        if(!empty($ds['venderFrom']) && !empty($ds['venderTo']))
        {
          $this->db->where('rp.vender_code >=', $ds['venderFrom']);
          $this->db->where('rp.vender_code <=', $ds['venderTo']);
        }
      }

      if(empty($ds['allPO']))
      {
        if(!empty($ds['poFrom']) && !empty($ds['poTo']))
        {
          $this->db->where('rp.po_code >=', $ds['poFrom']);
          $this->db->where('rp.po_code <=', $ds['poTo']);
        }
      }


      if(empty($ds['allInvoice']))
      {
        if(!empty($ds['invoiceFrom']) && !empty($ds['invoiceTo']))
        {
          $this->db->where('rp.invoice_code >=', $ds['invoiceFrom']);
          $this->db->where('rp.invoice_code <=', $ds['invoiceTo']);
        }
      }

      $this->db
      ->order_by('rp.code', 'ASC')
      ->order_by('pd.style_code', 'ASC')
      ->order_by('pd.color_code', 'ASC')
      ->order_by('si.position', 'ASC');

      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return NULL;
    }

    return FALSE;
  }



} //--- end class
 ?>
