<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="Mosaddek">
	<meta name="keyword" content="Instrumentasi Bimbingan dan Konseling">
	<link rel="shortcut icon" href="img/favicon.png">

	<title>Instrumentasi Bimbingan dan Konseling</title>

	<!-- Bootstrap core CSS -->
	<link href="<?= base_url('assets/member/') ?>css/bootstrap.min.css" rel="stylesheet">
	<link href="<?= base_url('assets/member/') ?>css/bootstrap-reset.css" rel="stylesheet">
	<!--external css-->

	<link rel="stylesheet" type="text/css" href="<?= base_url('assets/member/') ?>vendor/bootstrap-fileupload/bootstrap-fileupload.css" />
	<link rel="stylesheet" type="text/css" href="<?= base_url('assets/member/') ?>vendor/bootstrap-datepicker/css/datepicker.css" />
	<link rel="stylesheet" type="text/css" href="<?= base_url('assets/member/') ?>vendor/select2/css/select2.min.css" />
	<link href="<?= base_url('assets/member/') ?>vendor/advanced-datatable/media/css/demo_table.css" rel="stylesheet" />
	<link rel="stylesheet" href="<?= base_url('assets/member/') ?>vendor/data-tables/DT_bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="<?= base_url('assets/member/') ?>vendor/bootstrap-switch/static/stylesheets/bootstrap-switch.css" />
	<link rel="stylesheet" type="text/css" href="<?= base_url('assets/member/') ?>vendor/switchery/switchery.css" />
	<link rel="stylesheet" type="text/css" href="<?= base_url('assets/member/') ?>css/jquery.steps.css" />
	<!-- Custom styles for this template -->

	<link href="<?= base_url('assets/member/') ?>css/style.css" rel="stylesheet">
	<link href="<?= base_url('assets/member/') ?>css/style-responsive.css" rel="stylesheet" />

	<script src="<?= base_url('assets/member/') ?>js/jquery.js"></script>
	<script src="https://kit.fontawesome.com/05a0e1d41d.js" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>

	<style type="text/css">
		.select2-results__option[aria-selected=true] {
			display: none;
		}
	</style>

</head>

<body>

	<section id="container">
		<header class="header white-bg">
			<?php
			if (!isset($tipe_member)) {
			?>
				<div class="sidebar-toggle-box">
					<i class="fa fa-bars"></i>
				</div>
			<?php
			}
			?>
			<a href="index.html" class="logo">Admin Instrumentasi <span>BK</span></a>
			<div class="top-nav">
				<ul class="nav float-right top-menu">
					<li class="dropdown">
						<a data-toggle="dropdown" class="dropdown-toggle" href="#">
							<img alt="" src="img/avatar1_small.jpg">
							<span class="username"><?= getField("user_info", "nama_lengkap", array('user_id' => $this->session->userdata('id'))) ?></span>
							<b class="caret"></b>
						</a>
						<ul class="dropdown-menu extended logout dropdown-menu-right">
							<div class="log-arrow-up"></div>
							<li><a href="<?= base_url('auth/logout') ?>"><i class="fa fa-key"></i> Log Out</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</header>

		<?php
		if (!isset($tipe_member)) {
			echo $this->load->view('admin/sidebar.php', '', TRUE);
		}
		?>
		<!--sidebar end-->
		<!--main content start-->
		<?php
		if (!isset($tipe_member)) {
		?>
			<section id="main-content">
			<?php
		}
			?>
			<section class="wrapper">
				<?= $this->load->view($content, '', TRUE) ?>
			</section>

			<?php
			if (!isset($tipe_member)) {
			?>
			</section>
		<?php
			}
		?>
		<!--main content end-->

		<!--footer start-->
		<footer class="site-footer" style="<?= (!isset($tipe_member) ? '' : 'padding-left: 43px;position:relative;') ?>">
			<div class="text-center">
				2020 &copy; Instrumentasi Bimbingan dan Konseling
				<a href="#" class="go-top">
					<i class="fa fa-angle-up"></i>
				</a>
			</div>
		</footer>
		<!--footer end-->
	</section>

	<!-- js placed at the end of the document so the pages load faster -->
	<script src="<?= base_url('assets/member/') ?>js/bootstrap.bundle.min.js"></script>
	<script class="include" type="text/javascript" src="<?= base_url('assets/member/') ?>js/jquery.dcjqaccordion.2.7.js"></script>
	<script src="<?= base_url('assets/member/') ?>js/jquery.scrollTo.min.js"></script>
	<script src="<?= base_url('assets/member/') ?>js/jquery.nicescroll.js" type="text/javascript"></script>
	<script src="<?= base_url('assets/member/') ?>js/respond.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.3/croppie.js"></script>
	<script type="text/javascript" src="<?= base_url('assets/member/') ?>vendor/fuelux/js/spinner.min.js"></script>
	<script type="text/javascript" src="<?= base_url('assets/member/') ?>vendor/bootstrap-fileupload/bootstrap-fileupload.js"></script>
	<script type="text/javascript" src="<?= base_url('assets/member/') ?>vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
	<script type="text/javascript" language="javascript" src="<?= base_url('assets/member/') ?>vendor/advanced-datatable/media/js/jquery.dataTables.js"></script>
	<script type="text/javascript" src="<?= base_url('assets/member/') ?>vendor/data-tables/DT_bootstrap.js"></script>
	<script type="text/javascript" src="<?= base_url('assets/member/') ?>vendor/sweetalert/sweetalert2.all.min.js"></script>

	<script type="text/javascript" src="<?= base_url('assets/member/') ?>vendor/select2/js/select2.min.js"></script>
	<script src="<?= base_url('assets/member/') ?>vendor/bootstrap-switch/static/js/bootstrap-switch.js"></script>

	<!--bootstrap-switch-->
	<script src="<?= base_url('assets/member/') ?>vendor/switchery/switchery.js"></script>
	<!--Form Validation-->
	<script src="<?= base_url('assets/member/') ?>js/bootstrap-validator.min.js" type="text/javascript"></script>

	<!--Form Wizard-->
	<script src="<?= base_url('assets/member/') ?>js/jquery.validate.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="<?= base_url('assets/member/') ?>vendor/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>
	<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script> -->
	<script src="<?= base_url('assets/member/') ?>js/dynamic_table_init.js"></script>
	<script src="<?= base_url('assets/member/') ?>js/pickers/init-date-picker.js"></script>

	<!--common script for all pages-->
	<script src="<?= base_url('assets/member/') ?>js/common-scripts.js"></script>

	<!-- JS ZIP -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" integrity="sha512-XMVd28F1oH/O71fzwBnV7HucLxVwtxf26XV8P4wPk26EDxuGZ91N8bsOttmnomcCD3CS5ZMRL50H0GgOHvegtg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.js" integrity="sha512-3FKAKNDHbfUwAgW45wNAvfgJDDdNoTi5PZWU7ak3Xm0X8u0LbDBWZEyPklRebTZ8r+p0M2KIJWDYZQjDPyYQEA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.js"></script>

	<!-- swithery-->
	<script type="text/javascript">
		$(document).ready(function() {
			// Resets to the regular style
			$('#dimension-switch').bootstrapSwitch('setSizeClass', '');
			// Sets a mini switch
			$('#dimension-switch').bootstrapSwitch('setSizeClass', 'switch-mini');
			// Sets a small switch
			$('#dimension-switch').bootstrapSwitch('setSizeClass', 'switch-small');
			// Sets a large switch
			$('#dimension-switch').bootstrapSwitch('setSizeClass', 'switch-large');


			$('#change-color-switch').bootstrapSwitch('setOnClass', 'success');
			$('#change-color-switch').bootstrapSwitch('setOffClass', 'danger');

		});
		x
		$(document).ready(function() {
			//default
			var elem = document.querySelector('.js-switch');
			var init = new Switchery(elem);


			//small
			var elem = document.querySelector('.js-switch-small');
			var switchery = new Switchery(elem, {
				size: 'small'
			});

			//large
			var elem = document.querySelector('.js-switch-large');
			var switchery = new Switchery(elem, {
				size: 'large'
			});


			//blue color
			var elem = document.querySelector('.js-switch-blue');
			var switchery = new Switchery(elem, {
				color: '#7c8bc7',
				jackColor: '#9decff'
			});

			//green color
			var elem = document.querySelector('.js-switch-yellow');
			var switchery = new Switchery(elem, {
				color: '#FFA400',
				jackColor: '#ffffff'
			});

			//red color
			var elem = document.querySelector('.js-switch-red');
			var switchery = new Switchery(elem, {
				color: '#ff6c60',
				jackColor: '#ffffff'
			});


		});
	</script>

	<script type="text/javascript">
		$(document).ready(function() {
			$(".js-example-basic-single").select2({
				placeholder: "Pilih opsi yang tersedia",
			});

			$("#bidang_peminatan").select2({
				placeholder: "Pilih tiga bidang yang diminati",
				maximumSelectionLength: 3,
			});

			$('#spinner4').spinner({
				value: 0,
				step: 1,
				min: 0,
				max: 200
			});
		});
	</script>

</body>

</html>
