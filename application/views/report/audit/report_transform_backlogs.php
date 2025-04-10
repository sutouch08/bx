<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-sm-8 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
		<div class="col-sm-4 padding-5">
			<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
				<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
			</p>
		</div>
</div><!-- End Row -->
<hr class="hidden-print"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">
	<div class="col-sm-1 col-1-harf padding-5">
		<label class="display-block">ผู้เบิก</label>
		<div class="btn-group width-100">
			<button type="button" class="btn btn-sm btn-primary width-50" id="btn-user-all" onclick="toggleAllUser(1)">ทั้งหมด</button>
			<button type="button" class="btn btn-sm width-50" id="btn-user-use" onclick="toggleAllUser(0)">ระบุ</button>
		</div>
	</div>
	<div class="col-sm-3 padding-5">
		<label class="display-block not-show">&nbsp;</label>
		<input type="text" class="form-control input-sm" name="u_name" id="u_name" placeholder="ระบุชื่อผู้เบิก" disabled>
	</div>
	<div class="col-sm-1 col-1-harf padding-5">
		<label>สินค้า</label>
		<div class="btn-group width-100">
			<button type="button" class="btn btn-sm btn-primary width-50" id="btn-pd-all" onclick="toggleAllProduct(1)">ทั้งหมด</button>
			<button type="button" class="btn btn-sm width-50" id="btn-pd-range" onclick="toggleAllProduct(0)">ระบุ</button>
		</div>
	</div>
	<div class="col-sm-2 padding-5">
		<label>เริ่มต้น</label>
		<input type="text" class="form-control input-sm" id="pdFrom" name="pdFrom" placeholder="เริ่มต้น" disabled>
	</div>
	<div class="col-sm-2 padding-5">
		<label>สิ้นสุด</label>
		<input type="text" class="form-control input-sm" id="pdTo" name="pdTo" placeholder="สิ้นสุด" disabled>
	</div>
	<div class="col-sm-2 padding-5">
		<label>วันที่</label>
		<div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" readonly value="">
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" readonly value="">
    </div>
	</div>
</div>
<input type="hidden" id="allUser" name="allUser" value="1" />
<input type="hidden" id="dname" name="dname" value="" />
<input type="hidden" id="allProduct" name="allProduct" value="1" />
<input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>">
<hr>
</form>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="result-window" style="overflow:auto;">
		<table class="table table-bordered tableFixHead" style="min-width:1440px; font-size:11px;">
			<thead>
				<tr class="font-size-12">
					<th class="fix-width-40 text-center fix-header">#</th>
					<th class="fix-width-80 text-center fix-header">วันที่</th>
					<th class="fix-width-100 text-center fix-header">วันที่ต้องการของ</th>
					<th class="fix-width-100 text-center fix-header">เลขที่เอกสาร</th>
					<th class="fix-width-150 text-center fix-header">รหัสสินค้า(เบิก)</th>
					<th class="fix-width-150 text-center fix-header">รหัสสินค้า(รับ)</th>
					<th class="fix-width-80 text-center fix-header">ราคา</th>
					<th class="fix-width-80 text-center fix-header">เบิก</th>
					<th class="fix-width-80 text-center fix-header">รับ</th>
					<th class="fix-width-80 text-center fix-header">ค้างรับ</th>
					<th class="fix-width-100 text-center fix-header">มูลค่า</th>
					<th class="fix-width-150 text-center fix-header">ผู้เบิก(User)</th>
					<th class="fix-width-150 text-center fix-header">ผู้ทำรายการ</th>
				</tr>
			</thead>
			<tbody id="result">

			</tbody>
		</table>
  </div>
</div>

<script id="template" type="text/x-handlebarsTemplate">
	{{#each this}}
		{{#if @last}}
			<tr class="font-size-12">
				<td colspan="7" class="middle text-right">รวม</td>
				<td class="middle text-right">{{total_qty}}</td>
				<td class="middle text-right">{{total_return}}</td>
				<td class="middle text-right">{{total_balance}}</td>
				<td class="middle text-right">{{total_amount}}</td>
				<td colspan="2" class="middle text-right"></td>
			</tr>
		{{else}}
			<tr class="font-size-10">
				<td class="middle text-center">{{no}}</td>
				<td class="middle">{{date_add}}</td>
				<td class="middle">{{due_add}}</td>
				<td class="middle">{{order_code}}</td>
				<td class="middle">{{original_code}}</td>
				<td class="middle">{{product_code}}</td>
				<td class="middle text-right">{{price}}</td>
				<td class="middle text-right">{{qty}}</td>
				<td class="middle text-right">{{return}}</td>
				<td class="middle text-right">{{balance}}</td>
				<td class="middle text-right">{{amount}}</td>
				<td class="middle">{{user_ref}}</td>
				<td class="middle">{{user}}</td>
			</tr>
		{{/if}}
	{{/each}}
</script>



<script src="<?php echo base_url(); ?>scripts/report/audit/report_transform_backlogs.js?v=<?php echo date('Ymdh'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
