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
			<button type="button" class="btn btn-sm btn-warning" onclick="leave()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-success" onclick="save()"><i class="fa fa-save"></i> บันทึก</button>
			<?php	endif; ?>
    </p>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-3 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm" id="code" value="" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center e" id="date-add" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>
	<div class="col-lg-3 col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
		<label>ผู้ยืม</label>
		<select class="width-100 filter e" id="employee" onchange="lend_code_init()">
			<option value="">เลือกพนักงาน</option>
			<?php echo select_employee(); ?>
		</select>
	</div>
	<div class="col-lg-6 col-md-5 col-sm-5 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
	</div>

	<div class="divider-hidden"></div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>ใบยืมสินค้า</label>
		<input type="text" class="form-control input-sm text-center e" id="lend_code" value="" placeholder="ระบุเลขที่ใบยืมสินค้า" required>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">doc</label>
		<button type="button" class="btn btn-xs btn-success btn-block" id="btn-set-code" onclick="load_lend_details()">ดึงข้อมูล</button>
		<button type="button" class="btn btn-xs btn-primary btn-block hide" id="btn-change-code" onclick="change_lend_code()">เปลี่ยน</button>
	</div>
	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-5 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm e" id="zone_code" value="" required />
	</div>
	<div class="col-lg-6-harf col-md-4-harf col-sm-4-harf col-xs-9 padding-5">
		<label>โซน[รับคืน]</label>
		<input type="text" class="form-control input-sm e" id="zone" value="" placeholder="กำหนดโซนที่จะรับสินค้าเข้า" required />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">chang</label>
		<button type="button" class="btn btn-xs btn-primary btn-block hide" id="btn-change-zone" onclick="changeZone()">เปลี่ยนโซน</button>
		<button type="button" class="btn btn-xs btn-success btn-block" id="btn-set-zone" onclick="setZone()">ตกลง</button>
	</div>
</div>
<div class="divider"></div>
<div class="row">
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>จำนวน</label>
		<input type="number" class="form-control input-sm text-center" id="qty" value="1">
	</div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>บาร์โค้ดสินค้า</label>
		<input type="text" class="form-control input-sm text-center" id="barcode" placeholder="ยิงบาร์โค้ดสินค้า">
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">barcode</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="doReceive()">ตกลง</button>
	</div>

	<div class="col-lg-5 col-md-3 col-sm-3 col-xs-4">&nbsp;</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">add</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="receiveAll()">คืนทั้งหมด</button>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">clear</label>
		<button type="button" class="btn btn-xs btn-danger btn-block" onclick="clearAll()">เคลียร์ทั้งหมด</button>
	</div>
</div>

<hr class="margin-top-15"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:1000px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-150 middle">บาร์โคด</th>
					<th class="min-width-200 middle">สินค้า</th>
					<th class="fix-width-100 middle text-center">ยืม</th>
					<th class="fix-width-100 middle text-center">คืนแล้ว</th>
					<th class="fix-width-100 middle text-center">ค้าง</th>
					<th class="fix-width-100 middle text-center">ครั้งนี้</th>
				</tr>
			</thead>
			<tbody id="result">

			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script id="template" type="text/x-handlebarsTemplate">
{{#each details}}
	{{#if nodata}}
		<tr>
			<td colspan="7" class="middle text-center">ไม่พบข้อมูล</td>
		</tr>
	{{else}}
		{{#if @last}}
			<tr class="font-size-14">
				<td colspan="3" class="middle text-right">รวม</td>
				<td class="middle text-center">{{totalLend}}</td>
				<td class="middle text-center">{{totalReceived}}</td>
				<td class="middle text-right">{{totalBacklogs}}</td>
				<td class="middle text-center" id="totalQty">0</td>
			</tr>
		{{else}}
			<tr>
				<input type="hidden" class="{{barcode}}" data-no="{{no}}" value="{{no}}">
				<input type="hidden" id="lendQty-{{no}}" value="{{lendQty}}" />
				<input type="hidden" id="receivedQty-{{no}}" value="{{received}}" />
				<input type="hidden" id="backlogs-{{no}}" value="{{backlogs}}" />

				<td class="middle text-center no">{{no}}</td>
				<td class="middle">
					{{#if barcode}}
					<span class="barcode" onClick="addToBarcode('{{barcode}}')">{{barcode}}</span>
					{{/if}}
				</td>
				<td class="middle">{{itemCode}} : {{itemName}}</td>
				<td class="middle text-center">{{lendQtyLabel}}</td>
				<td class="middle text-center">{{receivedLabel}}</td>
				<td class="middle text-center">{{backlogsLabel}}</td>
				<td class="middle text-center">
				{{#if backlogs}}
					<input type="number"
					class="form-control input-sm text-right qty"
					data-product="{{itemCode}}"
					data-name="{{itemName}}"
					id="receiveQty-{{no}}"
					data-no="{{no}}"
					value="" />
				{{/if}}
				</td>
			</tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>

<script>
	$('#employee').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend_control.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
