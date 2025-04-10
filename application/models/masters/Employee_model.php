<?php
class Employee_model extends CI_Model
{
  private $tb = "employee";

  public function __construct()
  {
    parent::__construct();
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_name($id)
  {
    $rs = $this->db->select('first_name, last_name')->where('id', $id)->get($this->tb);
    if($rs->num_rows() === 1)
    {
      return $rs->row()->first_name.' '.$rs->row()->last_name;
    }

    return NULL;
  }

}//--- end class
 ?>
