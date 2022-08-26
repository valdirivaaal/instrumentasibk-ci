<div class="row mt-5">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
		</div>
		<div class="card">
			<div class="card-header text-center">
				<h1 class="font-weight-bold"> Daftar Cek Masalah</h1>
			</div>
			<div class="card-body">
				<form class="form-horizontal" role="form" action="<?= base_url('instrumen/dcm_advanced_detail') ?>" method="get">
					<input type="hidden" name="instrumen_id" value="<?= $_GET['instrumen_id'] ?>">
					<input type="hidden" name="nama_lengkap" value="<?= $_GET['nama_lengkap'] ?>">
					<input type="hidden" name="jenis_kelamin" value="<?= $_GET['jenis_kelamin'] ?>">
					<input type="hidden" name="email" value="<?= $_GET['email'] ?>">
					<input type="hidden" name="whatsapp" value="<?= $_GET['whatsapp'] ?>">
					<input type="hidden" name="nis" value="<?= $_GET['nis'] ?>">
					<input type="hidden" name="kelas" value="<?= $_GET['kelas'] ?>">
					<input type="hidden" name="tanggal_lahir" value="<?= $_GET['tanggal_lahir'] ?>">

					<div class="col-md-12">
						<div class="alert alert-warning" role="alert">
							<h5 class="font-weight-bold mt-3">Petunjuk Pengisian :</h5>
							<p>Bacalah dengan seksama pernyataan-pernyataan berikut. Tandailah masalah-masalah yang menjadi keluhan dan mengganggu Anda sekarang dengan mengubah tombol merah menjadi tombol hijau. Tidak ada salah atau benar dalam pernyataan ini, pilihlah yang sesuai dengan kondisi kamu. Selamat mengerjakan.</p>
						</div>
					</div>
					<ul class="nav nav-tabs d-none" id="myTab" role="tablist">
						<?php
						for ($i = 0; $i <= $jumlah_tab; $i++) {
						?>
							<li class="nav-item">
								<a class="nav-link <?= ($i == 0) ? 'active' : '' ?>" id="step-1" data-toggle="tab" href="#tab<?= $i ?>" role="tab" aria-controls="home" aria-selected="true">Tahap <?= $i ?></a>
							</li>
						<?php
						}
						?>
					</ul>
					<div class="tab-content" id="myTabContent">
						<?php
						for ($i = 0; $i <= $jumlah_tab; $i++) {
						?>
							<div class="tab-pane <?= ($i == 0) ? 'active' : '' ?>" id="tab<?= $i ?>">
								<?php
								foreach ($get_instrumen[$i] as $key => $value) {
								?>
									<hr>
									<div class="row container">
										<div class="col-md-10">
											<h5><?= $value['pernyataan'] ?></h5>
										</div>
										<div class="col-md-2 text-center">
											<div class="make-switch mt-3" data-on="success" data-off="danger">
												<input type="checkbox" name="jawaban[<?= $value['id'] ?>]" value="Ya">
											</div>
										</div>
									</div>
									<hr>
								<?php
								}
								if ($i == $jumlah_tab) {
								?>
									<div class="form-group text-center col-lg-10 offset-1">
										<button type="submit" class="btn btn-success">Simpan</button>
									</div>
								<?php
								} else {
								?>
									<div class="form-group text-center col-lg-10 offset-1">
										<button type="button" class="btn btn-success btnStep<?= $i ?>">Selanjutnya</button>
									</div>
								<?php
								}
								?>
							</div>
						<?php
						}
						?>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var i;
	var jumlah_tab = <?= $jumlah_tab ?>;
	var total_tab = jumlah_tab - 1;

	for (i = 0; i <= total_tab; i++) {
		$('.btnStep' + i).click(function() {
			var $id = $('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
			jQuery('html,body').animate({
				scrollTop: 0
			}, 0);
		});

	}

	// $('.btnStep1').click(function(){
	// 	var $id = $('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
	// 	jQuery('html,body').animate({scrollTop:0},0);
	// });

	// $('.btnStep2').click(function(){
	// 	var $id = $('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
	// 	jQuery('html,body').animate({scrollTop:0},0);
	// });

	// $('.btnStep3').click(function(){
	// 	var $id = $('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
	// 	jQuery('html,body').animate({scrollTop:0},0);
	// });

	// $('.btnStep4').click(function(){
	// 	var $id = $('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
	// 	jQuery('html,body').animate({scrollTop:0},0);
	// });

	// $('.btnStep5').click(function(){
	// 	var $id = $('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
	// 	jQuery('html,body').animate({scrollTop:0},0);
	// });

	// $('.btnStep6').click(function(){
	// 	var $id = $('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
	// 	jQuery('html,body').animate({scrollTop:0},0);
	// });

	// $('.btnStep7').click(function(){
	// 	var $id = $('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
	// 	jQuery('html,body').animate({scrollTop:0},0);
	// });

	// $('.btnStep8').click(function(){
	// 	var $id = $('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
	// 	jQuery('html,body').animate({scrollTop:0},0);
	// });


	$('.make-switch').change(function() {
		if ($("#question-cerita").is(":checked") == true) {
			$("#cerita").attr("placeholder", "Yuk cerita yuk");
		} else {
			$("#cerita").attr("placeholder", "Males ah");
		}
	})
</script>
