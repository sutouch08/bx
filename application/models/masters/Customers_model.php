<?php
class Customers_model extends CI_Model
{
  private $tb = "customers";

  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert('customers', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('customers', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    $rs = $this->db->where('code', $code)->delete('customers');
    if(!$rs)
    {
      return $this->db->error();
    }

    return TRUE;
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if($ds['code'] != "" && $ds['code'] !== NULL)
    {
      $this->db
      ->group_start()
      ->like('code', $ds['code'])
      ->or_like('name', $ds['code'])
      ->group_end();
    }

    if($ds['group'] != 'all')
    {
      if($ds['group'] === "NULL")
      {
        $this->db->where('group_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('group_code', $ds['group']);
      }
    }

    if($ds['kind'] != "all")
    {
      if($ds['kind'] === "NULL")
      {
        $this->db->where('kind_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('kind_code', $ds['kind']);
      }
    }

    if($ds['type'] != "all")
    {
      if($ds['type'] === "NULL")
      {
        $this->db->where('type_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('type_code', $ds['type']);
      }

    }

    if($ds['class'] != "all")
    {
      if($ds['class'] === 'NULL')
      {
        $this->db->where('class_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('class_code', $ds['class']);
      }

    }

    if($ds['area'] != 'all')
    {
      if($ds['area'] === "NULL")
      {
        $this->db->where('area_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('area_code', $ds['area']);
      }
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('active', $ds['status']);
    }

    return $this->db->count_all_results('customers');
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('cu.*, cg.name AS group, ck.name AS kind, ct.name AS type, cc.name AS class, ca.name AS area')
    ->from('customers AS cu')
    ->join('customer_group AS cg', 'cu.group_code = cg.code', 'left')
    ->join('customer_kind AS ck', 'cu.kind_code = ck.code', 'left')
    ->join('customer_type AS ct', 'cu.type_code = ct.code', 'left')
    ->join('customer_class AS cc', 'cu.class_code = cc.code', 'left')
    ->join('customer_area AS ca', 'cu.area_code = ca.code', 'left');

    if($ds['code'] != "" && $ds['code'] !== NULL)
    {
      $this->db
      ->group_start()
      ->like('cu.code', $ds['code'])
      ->or_like('cu.name', $ds['code'])
      ->group_end();
    }

    if($ds['group'] != 'all')
    {
      if($ds['group'] === "NULL")
      {
        $this->db->where('cu.group_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('cu.group_code', $ds['group']);
      }
    }

    if($ds['kind'] != "all")
    {
      if($ds['kind'] === "NULL")
      {
        $this->db->where('cu.kind_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('cu.kind_code', $ds['kind']);
      }
    }

    if($ds['type'] != "all")
    {
      if($ds['type'] === "NULL")
      {
        $this->db->where('cu.type_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('cu.type_code', $ds['type']);
      }

    }

    if($ds['class'] != "all")
    {
      if($ds['class'] === 'NULL')
      {
        $this->db->where('cu.class_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('cu.class_code', $ds['class']);
      }

    }

    if($ds['area'] != 'all')
    {
      if($ds['area'] === "NULL")
      {
        $this->db->where('cu.area_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('cu.area_code', $ds['area']);
      }
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('cu.active', $ds['status']);
    }

    $rs = $this->db->order_by('cu.code', 'ASC')->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists($code, $old_code = '')
  {
    if($old_code != '')
    {
      $this->db->where('code !=', $old_code);
    }

    $count = $this->db->where('code', $code)->count_all_results('customers');

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists_name($name, $old_name = '')
  {
    if($old_name != '')
    {
      $this->db->where('name !=', $old_name);
    }

    $rs = $this->db->where('name', $name)->get('customers');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_sale_code($code)
  {
    $rs = $this->db->select('sale_code')->where('code', $code)->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->sale_code;
    }

    return NULL;
  }

}
?>
