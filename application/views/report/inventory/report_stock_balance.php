<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-success top-btn" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
		<button type="button" class="btn btn-white btn-info top-btn" onclick="print()"><i class="fa fa-print"></i> พิมพ์</button>
	</div>
</div><!-- End Row -->
<hr class="hidden-print"/>
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-12 padding-5">
		<label class="display-block">สินค้า</label>
		<div class="btn-group width-100">
			<button type="button" class="btn btn-sm btn-primary width-50" id="btn-pd-all" onclick="toggleAllProduct(1)">ทั้งหมด</button>
			<button type="button" class="btn btn-sm width-50" id="btn-pd-range" onclick="toggleAllProduct(0)">เลือก</button>
		</div>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12 padding-5">
		<label class="display-block not-show">start</label>
		<input type="text" class="form-control input-sm text-center e" id="pdFrom" name="pdFrom" disabled>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12 padding-5">
		<label class="display-block not-show">End</label>
		<input type="text" class="form-control input-sm text-center e" id="pdTo" name="pdTo" disabled>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label class="display-block">คลัง</label>
		<div class="btn-group width-100">
			<button type="button" class="btn btn-sm btn-primary width-50" id="btn-wh-all" onclick="toggleAllWarehouse(1)">ทั้งหมด</button>
			<button type="button" class="btn btn-sm width-50" id="btn-wh-range" onclick="toggleAllWarehouse(0)">เลือก</button>
		</div>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>ณ วันที่</label>
		<input type="text" class="form-control input-sm text-center e" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>
	<input type="hidden" id="allProduct" name="allProduct" value="1">
	<input type="hidden" id="allWarehouse" name="allWhouse" value="1">
</div>


<div class="modal fade" id="wh-modal" tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal' style="width:500px;">
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
				<h4 class='title' id='modal_title'>เลือกคลัง</h4>
			</div>
			<div class='modal-body' id='modal_body' style="padding:0px; height:600px; max-height: 70vh; overflow:auto;">
				<?php if(!empty($whList)) : ?>
					<?php foreach($whList as $rs) : ?>
						<div class="col-sm-12">
							<label>
								<input type="checkbox" class="chk" id="<?php echo $rs->code; ?>" name="warehouse[<?php echo $rs->code; ?>]" value="<?php echo $rs->code; ?>" style="margin-right:10px;" />
								<?php echo $rs->code; ?> | <?php echo $rs->name; ?>
							</label>
						</div>
					<?php endforeach; ?>
				<?php endif;?>

				<div class="divider" ></div>
			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-default btn-block' data-dismiss='modal'>ตกลง</button>
			</div>
		</div>
	</div>
</div>


<form id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
	<input type="hidden" name="data" id="data" value="" />
	<input type="hidden" name="token" id="token" value="" />
</form>
<hr>

<div class="row">
	<div class="col-sm-12" id="rs">

	</div>
</div>




<script id="template" type="text/x-handlebars-template">
  <table class="table table-bordered table-striped">
    <tr>
      <td colspan="7" class="text-center font-size-12">รายงานสินค้าคงเหลือ ณ วันที่ {{ reportDate }}</td>
    </tr>
    <tr>
      <td colspan="7" class="text-center font-size-12"> คลัง : {{ whList }} </td>
    </tr>
    <tr>
      <td colspan="7" class="text-center font-size-12"> สินค้า : {{ productList }} </td>
    </tr>
    <tr class="font-size-11">
      <th class="width-5 middle text-center">ลำดับ</th>
      <th class="width-15 middle text-center">บาร์โค้ด</th>
      <th class="width-15 middle text-center">รหัส</th>
      <th class="width-30 middle text-center">สินค้า</th>
      <th class="width-10 middle text-right">ทุน</th>
      <th class="width-10 text-right middle">คงเหลือ</th>
      <th class="width-15 text-right middle">มูลค่า</th>
    </tr>
{{#each items}}
  {{#if nodata}}
    <tr class="font-size-11">
      <td colspan="7" align="center">-----  ไม่พบสินค้าคงเหลือตามเงื่อนไขที่กำหนด  -----</td>
    </tr>
  {{else}}
		<tr class="font-size-11">
			<td class="middle text-center">{{no}}</td>
			<td class="middle text-center">{{ barcode }}</td>
			<td class="middle">{{ pdCode }}</td>
			<td class="middle">{{ pdName }}</td>
			<td class="middle text-right">{{ cost }}</td>
			<td class="middle text-right">{{ qty }}</td>
			<td class="middle text-right">{{ amount }}</td>
		</tr>
  {{/if}}
{{/each}}
		<tr class="font-size-12">
			<td colspan="5" class="text-right">รวม</td>
			<td class="text-right">{{ totalQty }}</td>
			<td class="text-right">{{ totalAmount }}</td>
		</tr>
  </table>
</script>

<script src="<?php echo base_url(); ?>scripts/report/inventory/stock_balance.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
