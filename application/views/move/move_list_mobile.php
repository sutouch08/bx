<?php $this->load->view('include/header'); ?>
<?php $this->load->view('move/mobile/style'); ?>
<div class="row">
	<div class="col-lg-6 col-sm-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
</div><!-- End Row -->
<hr class=""/>
<?php $this->load->view('move/mobile/filter'); ?>

<div class="move-list">
	<?php if( ! empty($list)) : ?>
		<?php $no = $this->uri->segment(4) + 1; ?>
		<?php foreach($list as $rs) : ?>
			<div class="move-list-item">
				<div class="col-xs-2 text-center bold padding-5" style="height:60px; display:grid; align-items:center;">
					<span class="blue">#<?php echo $no;?></span>
					<?php if($rs->is_expire OR $rs->status == 2) : ?>
						<?php if($rs->status == 2) : ?>
							<span class="red">CN</span>
						<?php else : ?>
							<span class="dark">EXP</span>
						<?php endif; ?>
					<?php else : ?>
						<?php if($rs->status == 0) : ?>
							<span class="blue">NC</span>
						<?php endif; ?>
						<?php if($rs->status == 4) : ?>
							<span class="orange">WC</span>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<div class="col-xs-8 padding-5">
					<p class="move-list-line bold">
						<?php echo $rs->code; ?>
						<?php echo (empty($rs->reference) ? "" : "&nbsp;&nbsp;[".$rs->reference."]"); ?>
					</p>
					<p class="move-list-line bold">วันที่ : <?php echo thai_date($rs->date_add, FALSE,'/'); ?></p>
					<p class="move-list-line bold">คลัง : <?php echo $rs->warehouse_name; ?></p>
				</div>
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
					<a class="move-list-link font-size-24" href="javascript:edit('<?php echo $rs->code; ?>')"><i class="fa fa-angle-right"></i></a>
				<?php endif; ?>
			</div>
			<?php $no++; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<?php $this->load->view('move/mobile/list_menu'); ?>

<script src="<?php echo base_url(); ?>scripts/move/mobile/move_mobile.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
