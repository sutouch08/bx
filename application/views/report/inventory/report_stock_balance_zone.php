<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5 padding-top-5">
    <h3 class="title">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5 text-right">
		<button type="button" class="btn btn-white btn-success top-btn" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
	</div>
</div><!-- End Row -->
<hr class="hidden-print"/>
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>สินค้า</label>
		<div class="btn-group width-100">
			<button type="button" class="btn btn-sm btn-primary width-50" id="btn-pd-all" onclick="toggleAllProduct(1)">ทั้งหมด</button>
			<button type="button" class="btn btn-sm width-50" id="btn-pd-range" onclick="toggleAllProduct(0)">เลือก</button>
		</div>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>เริ่มต้น</label>
		<input type="text" class="form-control input-sm text-center e" id="pdFrom" name="pdFrom" disabled>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>สิ้นสุด</label>
		<input type="text" class="form-control input-sm text-center e" id="pdTo" name="pdTo" disabled>
	</div>
	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>คลัง</label>
		<div class="btn-group width-100">
			<button type="button" class="btn btn-sm btn-primary width-50" id="btn-wh-all" onclick="toggleAllWarehouse(1)">ทั้งหมด</button>
			<button type="button" class="btn btn-sm width-50" id="btn-wh-range" onclick="toggleAllWarehouse(0)">เลือก</button>
		</div>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>โซน</label>
		<div class="btn-group width-100">
			<button type="button" class="btn btn-sm btn-primary width-50" id="btn-zone-all" onclick="toggleAllZone(1)">ทั้งหมด</button>
			<button type="button" class="btn btn-sm width-50" id="btn-zone-range" onclick="toggleAllZone(0)">เลือก</button>
		</div>
	</div>

	<div class="col-lg-2-harf col-md-3 col-sm-6 col-xs-6 padding-5">
		<label class="not-show">zone</label>
		<input type="text" class="form-control input-sm e" name="zoneCode" id="zone-code" disabled>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
		<label>ณ วันที่</label>
		<input type="text" class="form-control input-sm text-center" id="date" value="<?php echo date('Y-m-d'); ?>" readonly />
	</div>

	<input type="hidden" id="allProduct" name="allProduct" value="1">
	<input type="hidden" id="allWarehouse" name="allWhouse" value="1">
	<input type="hidden" id="allZone" name="allZone" value="1">	
</div>
<hr>

<div class="modal fade" id="wh-modal" tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal' style="width:500px;">
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
				<h4 class='title' id='modal_title'>เลือกคลัง</h4>
			</div>
			<div class='modal-body' id='modal_body' style="padding:0px; height:600px; max-height:70vh; overflow:auto;">
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


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive padding-5" id="rs">

	</div>
</div>

<form id="exportForm" method="post" action="<?php echo $this->home; ?>/do_export">
	<input type="hidden" name="data" id="data" value="" />
	<input type="hidden" name="token" id="token" value="" />
</form>

<script id="template" type="text/x-handlebars-template">
  <table class="table table-bordered table-striped" style="min-width:940px;">
    <tr class="font-size-11">
      <td colspan="8" class="text-center">รายงานสินค้าคงเหลือแยกตามโซน ณ วันที่ {{ reportDate }}</td>
    </tr>
    <tr class="font-size-11">
      <td colspan="8" class="text-center"> คลัง : {{ whList }} </td>
    </tr>
		<tr class="font-size-11">
      <td colspan="8" class="text-center"> โซน : {{ zoneList }} </td>
    </tr>
    <tr class="font-size-11">
      <td colspan="8" class="text-center"> สินค้า : {{ productList }} </td>
    </tr>
    <tr class="font-size-11">
      <td class="fix-width-40 middle text-center">ลำดับ</td>
			<td class="fix-widtd-100 middle text-center">คลัง</td>
      <td class="fix-width-150 middle text-center">โซน</td>
      <td class="fix-width-150 middle text-center">รหัส</td>
      <td class="min-width-200 middle text-center">สินค้า</td>
			<td class="fix-width-80 middle text-right">ราคา</td>
      <td class="fix-width-100 text-right middle">คงเหลือ</td>
			<td class="fix-width-100 text-right middle">มูลค่า</td>
    </tr>
		{{#each items}}
		  {{#if nodata}}
		    <tr class="font-size-11">
		      <td colspan="8" class="text-center">-----  ไม่พบสินค้าคงเหลือตามเงื่อนไขที่กำหนด  -----</td>
		    </tr>
		  {{else}}
				<tr class="font-size-11">
					<td class="middle text-center">{{no}}</td>
					<td class="middle">{{ warehouse }}</td>
					<td class="middle">{{ zone }}</td>
					<td class="middle">{{ pdCode }}</td>
					<td class="middle">{{ pdName }}</td>
					<td class="middle text-right">{{ price }}</td>
					<td class="middle text-right">{{ qty }}</td>
					<td class="middle text-right">{{ amount }}</td>
				</tr>
		  {{/if}}
		{{/each}}
		<tr class="font-size-11">
			<td colspan="6" class="text-right">รวม</td>
			<td class="text-right">{{ totalQty }}</td>
			<td class="text-right">{{ totalAmount }}</td>
		</tr>
  </table>
</script>

<script src="<?php echo base_url(); ?>scripts/report/inventory/stock_balance_zone.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
