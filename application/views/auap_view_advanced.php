<div class="row mt-5">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
		</div>
		<div class="card">
			<div class="card-header text-center">
				<h1 class="font-weight-bold"> Alat Ungkap Arah Peminatan</h1>
			</div>
			<div class="card-body">
				<form class="form-horizontal" role="form" action="<?= base_url('instrumen/auap_save') ?>" method="get">
					<input type="hidden" name="instrumen_id" value="<?= $_GET['instrumen_id'] ?>">
					<input type="hidden" name="nama_lengkap" value="<?= $_GET['nama_lengkap'] ?>">
					<input type="hidden" name="jenis_kelamin" value="<?= $_GET['jenis_kelamin'] ?>">
					<input type="hidden" name="email" value="<?= $_GET['email'] ?>">
					<input type="hidden" name="whatsapp" value="<?= $_GET['whatsapp'] ?>">
					<input type="hidden" name="nis" value="<?= $_GET['nis'] ?>">
					<input type="hidden" name="kelas" value="<?= $_GET['kelas'] ?>">
					<input type="hidden" name="tanggal_lahir" value="<?= $_GET['tanggal_lahir'] ?>">

					<div class="col-md-12">
						<h5 class="font-weight-bold mt-3">Petunjuk Pengisian :</h5>
						<p>1. Untuk setiap pernyataan disediakan lima kemungkinan jawaban</br>
							2. Pilihlah salah satu kemungkinan jawaban yang sesuai dengan apa yang terjadi/terdapat pada diri anda
						</p>
						<p>Catatan<br>SD = Sangat Disukai<br>DS = Disukai<br>KD = Kurang Disukai<br>TD = Tidak Disukai<br>TP = Tidak Dipahami</p>
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
										<div class="col-lg-8 col-md-12">
											<h5><?= $value['pernyataan'] ?></h5>
										</div>
										<div class="col-lg-4 text-center">
											<div id="checkboxes text-center">
												<div class="checkboxgroup">
													<label for="my_radio_button_id1">SD</label>
													<input type="radio" class="pernyataan" name="jawaban[<?= $value['id'] ?>]" value="SD" id="my_radio_button_id1" data-id="<?= $value['id'] ?>" required />
												</div>
												<div class="checkboxgroup">
													<label for="my_radio_button_id2">DS</label>
													<input type="radio" class="pernyataan" name="jawaban[<?= $value['id'] ?>]" value="DS" id="my_radio_button_id2" data-id="<?= $value['id'] ?>" required />
												</div>
												<div class="checkboxgroup">
													<label for="my_radio_button_id3">KD</label>
													<input type="radio" class="pernyataan" name="jawaban[<?= $value['id'] ?>]" value="KD" id="my_radio_button_id3" data-id="<?= $value['id'] ?>" required />
												</div>
												<div class="checkboxgroup">
													<label for="my_radio_button_id3">TD</label>
													<input type="radio" class="pernyataan" name="jawaban[<?= $value['id'] ?>]" value="TD" id="my_radio_button_id3" data-id="<?= $value['id'] ?>" required />
												</div>
												<div class="checkboxgroup">
													<label for="my_radio_button_id3">TPH</label>
													<input type="radio" class="pernyataan" name="jawaban[<?= $value['id'] ?>]" value="TPH" id="my_radio_button_id3" data-id="<?= $value['id'] ?>" required />
												</div>
											</div>
											<input type="hidden" id="jawaban<?= $value['id'] ?>" class="pernyataan<?= $i ?>">
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
										<button type="button" class="btn btn-success btnNext<?= $i ?>">Selanjutnya</button>
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
	$('.pernyataan').on('click', function() {
		var id = $(this).data('id');
		$("#jawaban" + id).val(id);
	});

	var i;
	var jumlah_tab = <?= $jumlah_tab ?>;
	var total_tab = jumlah_tab - 1;

	$('.btnNext0').click(function() {
		var length0 = $('.pernyataan0').length;
		var pernyataan0 = $('.pernyataan0').filter(function() {
			return this.value != '';
		});

		if (pernyataan0.length >= 0 && (pernyataan0.length !== length0)) {
			alert('Silahkan isi semua pernyataan yang telah disebutkan.');
		} else {
			$('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
			$('html, body').animate({
				scrollTop: $(".form-horizontal").offset().top
			}, 2000);
		}
	});

	$('.btnNext1').click(function() {
		var length1 = $('.pernyataan1').length;
		var pernyataan1 = $('.pernyataan1').filter(function() {
			return this.value != '';
		});

		if (pernyataan1.length >= 0 && (pernyataan1.length !== length1)) {
			alert('Silahkan isi semua pernyataan yang telah disebutkan.');
		} else {
			$('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
			$('html, body').animate({
				scrollTop: $(".form-horizontal").offset().top
			}, 2000);
		}
	});

	$('.btnNext2').click(function() {
		var length2 = $('.pernyataan2').length;
		var pernyataan2 = $('.pernyataan2').filter(function() {
			return this.value != '';
		});

		if (pernyataan2.length >= 0 && (pernyataan2.length !== length2)) {
			alert('Silahkan isi semua pernyataan yang telah disebutkan.');
		} else {
			$('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
			$('html, body').animate({
				scrollTop: $(".form-horizontal").offset().top
			}, 2000);
		}
	});

	$('.btnNext3').click(function() {
		var length3 = $('.pernyataan3').length;
		var pernyataan3 = $('.pernyataan3').filter(function() {
			return this.value != '';
		});

		if (pernyataan3.length >= 0 && (pernyataan3.length !== length3)) {
			alert('Silahkan isi semua pernyataan yang telah disebutkan.');
		} else {
			$('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
			$('html, body').animate({
				scrollTop: $(".form-horizontal").offset().top
			}, 2000);
		}
	});

	$('.btnNext4').click(function() {
		var length4 = $('.pernyataan4').length;
		var pernyataan4 = $('.pernyataan4').filter(function() {
			return this.value != '';
		});

		if (pernyataan4.length >= 0 && (pernyataan4.length !== length4)) {
			alert('Silahkan isi semua pernyataan yang telah disebutkan.');
		} else {
			$('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
			$('html, body').animate({
				scrollTop: $(".form-horizontal").offset().top
			}, 2000);
		}
	});

	$('.btnNext5').click(function() {
		var length5 = $('.pernyataan5').length;
		var pernyataan5 = $('.pernyataan5').filter(function() {
			return this.value != '';
		});

		if (pernyataan5.length >= 0 && (pernyataan5.length !== length5)) {
			alert('Silahkan isi semua pernyataan yang telah disebutkan.');
		} else {
			$('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
			$('html, body').animate({
				scrollTop: $(".form-horizontal").offset().top
			}, 2000);
		}
	});

	$('.btnNext6').click(function() {
		var length6 = $('.pernyataan6').length;
		var pernyataan6 = $('.pernyataan6').filter(function() {
			return this.value != '';
		});

		if (pernyataan6.length >= 0 && (pernyataan6.length !== length6)) {
			alert('Silahkan isi semua pernyataan yang telah disebutkan.');
		} else {
			$('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
			$('html, body').animate({
				scrollTop: $(".form-horizontal").offset().top
			}, 2000);
		}
	});

	$('.btnNext7').click(function() {
		var length7 = $('.pernyataan7').length;
		var pernyataan7 = $('.pernyataan7').filter(function() {
			return this.value != '';
		});

		if (pernyataan7.length >= 0 && (pernyataan7.length !== length7)) {
			alert('Silahkan isi semua pernyataan yang telah disebutkan.');
		} else {
			$('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
			$('html, body').animate({
				scrollTop: $(".form-horizontal").offset().top
			}, 2000);
		}
	});

	$('.btnNext8').click(function() {
		var length8 = $('.pernyataan8').length;
		var pernyataan8 = $('.pernyataan8').filter(function() {
			return this.value != '';
		});

		if (pernyataan8.length >= 0 && (pernyataan8.length !== length8)) {
			alert('Silahkan isi semua pernyataan yang telah disebutkan.');
		} else {
			$('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
			$('html, body').animate({
				scrollTop: $(".form-horizontal").offset().top
			}, 2000);
		}
	});

	$('.btnNext9').click(function() {
		var length9 = $('.pernyataan9').length;
		var pernyataan9 = $('.pernyataan9').filter(function() {
			return this.value != '';
		});

		if (pernyataan9.length >= 0 && (pernyataan9.length !== length9)) {
			alert('Silahkan isi semua pernyataan yang telah disebutkan.');
		} else {
			$('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
			$('html, body').animate({
				scrollTop: $(".form-horizontal").offset().top
			}, 2000);
		}
	});

	$('.btnNext10').click(function() {
		var length10 = $('.pernyataan10').length;
		var pernyataan10 = $('.pernyataan10').filter(function() {
			return this.value != '';
		});

		if (pernyataan10.length >= 0 && (pernyataan10.length !== length10)) {
			alert('Silahkan isi semua pernyataan yang telah disebutkan.');
		} else {
			$('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
			$('html, body').animate({
				scrollTop: $(".form-horizontal").offset().top
			}, 2000);
		}
	});


	$('.make-switch').change(function() {
		if ($("#question-cerita").is(":checked") == true) {
			$("#cerita").attr("placeholder", "Yuk cerita yuk");
		} else {
			$("#cerita").attr("placeholder", "Males ah");
		}
	})
</script>
