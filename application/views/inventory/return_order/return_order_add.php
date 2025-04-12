<?php $this->load->view('include/header'); ?>
<input type="hidden" id="required_remark" value="<?php echo $this->required_remark; ?>" />
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 visible-xs padding-5">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  	<p class="pull-right top-p">
		<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>เลขที่เอกสาร</label>
			<input type="text" class="form-control input-sm text-center" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center h" name="date_add" id="dateAdd" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
  	<label>Posting Date</label>
    <input type="text" class="form-control input-sm text-center edit" name="shipped_date" id="shipped-date" value=""/>
  </div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>เลขที่บิล[SAP]</label>
		<input type="text" class="form-control input-sm text-center h" name="invoice" id="invoice" value="" />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center h" name="customer_code" id="customer_code" value="" />
	</div>
	<div class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 padding-5">
		<label>ชื่อลูกค้า</label>
		<input type="text" class="form-control input-sm h" id="customer" value="" />
	</div>
	<div class="col-lg-2 col-md-3-harf col-sm-3-harf col-xs-4 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm text-center h" name="zone_code" id="zone_code" value="" />
	</div>
	<div class="col-lg-3-harf col-md-8-harf col-sm-8-harf col-xs-8 padding-5">
		<label>ชื่อโซน</label>
		<input type="text" class="form-control input-sm h" name="zone" id="zone" value="" />
	</div>

	<div class="col-lg-5-harf col-md-10-harf col-sm-10-harf col-xs-9 padding-5">
		<label>หมายเหตุ</label>
			<input type="text" class="form-control input-sm h" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">save</label>
		<?php 	if($this->pm->can_add) : ?>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่ม</button>
		<?php	endif; ?>
	</div>
</div>

<input type="hidden" name="warehouse_code" id="warehouse_code" value="" />
<hr class="margin-top-15"/>

<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_add.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
