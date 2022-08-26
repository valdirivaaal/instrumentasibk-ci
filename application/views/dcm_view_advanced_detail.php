<div class="row mt-5">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
		</div>
		<div class="card">
			<div class="card-header text-center">
				<h1 class="font-weight-bold"> Alat Ungkap Masalah</h1>
			</div>
			<div class="card-body">
				<form class="form-horizontal" role="form" action="<?= base_url('instrumen/dcm_advanced_save') ?>" method="get">

					<input type="hidden" name="instrumen_id" value="<?= $_GET['instrumen_id'] ?>">
					<input type="hidden" name="nama_lengkap" value="<?= $_GET['nama_lengkap'] ?>">
					<input type="hidden" name="jenis_kelamin" value="<?= $_GET['jenis_kelamin'] ?>">
					<input type="hidden" name="email" value="<?= $_GET['email'] ?>">
					<input type="hidden" name="whatsapp" value="<?= $_GET['whatsapp'] ?>">
					<input type="hidden" name="nis" value="<?= $_GET['nis'] ?>">
					<input type="hidden" name="kelas" value="<?= $_GET['kelas'] ?>">
					<input type="hidden" name="tanggal_lahir" value="<?= $_GET['tanggal_lahir'] ?>">
					<input type="hidden" name="jawaban" value='<?= serialize($_GET['jawaban']) ?>'>
					<div class="col-md-12">
						<div class="alert alert-danger" role="alert">
							<h3 class="font-weight-bold mt-3">Tahap 2, baca petunjuk pengisian :</h3>
							<p class="text-justify">Jawablah pertanyaan berikut ini.</p>
						</div>

					</div>
					<div class="row container">
						<div class="col-md-10">
							<h5>Apakah masalah-masalah yang anda tandai itu benar-benar menggambarkan keseluruhan masalah yang Anda hadapi sekarang?</h5>
						</div>
						<div class="col-md-2 text-center">
							<div class="make-switch mt-3" data-on="success" data-off="danger">
								<input type="checkbox" name="jawaban_deskriptif[221]" value="Ya">
							</div>
						</div>
					</div>
					<hr>
					<div class="row container">
						<div class="col-md-12">
							<h5>Jika anda masih ingin mengemukakan masalah-masalah lain yang belum tercakup dalam daftar yang baru saja ananda jawab. Ceritakanlah masalah-masalah lain tersebut!</h5>
						</div>
						<div class="col-md-12 text-center mt-3">
							<textarea name="jawaban_deskriptif[222]" id="" class="form-control" cols="30" rows="8" placeholder="Ceritakan masalahmu disini !"></textarea>
						</div>
					</div>
					<hr>
					<div class="row container">
						<div class="col-md-10">
							<h5>Kepada siapakah anda ingin memperoleh kesempatan untuk mengemukakan atau membicarakan masalah-masalah anda itu?</h5>
						</div>
						<div class="col-md-2 text-center">
							<select name="jawaban_deskriptif[223]" class="form-control" required>
								<option selected value="">Pilih salah satu</option>
								<option value="Guru Pembimbing">Guru Pembimbing</option>
								<option value="Teman">Teman</option>
								<option value="Guru Lain">Guru Lain</option>
								<option value="Orangtua">Orangtua</option>
								<option value="Ahli Lain">Ahli Lain</option>
								<option value="Lain-lain">Lain-Lain</option>
								<option value="Tidak Ingin">Tidak Ingin</option>
							</select>
						</div>
					</div>
					<hr>
					<div class="row container">
						<div class="col-md-10">
							<h5>Apakah anda ingin melakukan konseling untuk menyelesaikan masalah yang dimiliki?</h5>
						</div>
						<div class="col-md-2 text-center">
							<select name="jawaban_deskriptif[224]" class="form-control" required>
								<option selected value="">Pilih salah satu</option>
								<option value="Ya">Ya</option>
								<option value="Tidak">Tidak</option>
							</select>
						</div>
					</div>
					<hr>
					<div class="row container">
						<div class="col-md-6">
							<h5>Permasalahan ini...</h5>
						</div>
						<div class="col-md-6 text-center">
							<select name="jawaban_deskriptif[225]" class="form-control" required>
								<option selected value="">Pilih salah satu</option>
								<option value="1">Mendesak dan ingin saya selesaikan segera</option>
								<option value="2">Mendesak namun ingin saya selesaikan dengan perlahan</option>
								<option value="3">Tidak mendesak tapi ingin saya selesaikan segera</option>
								<option value="4">Tidak mendesak dan ingin saya selesaikan dengan perlahan</option>
							</select>
						</div>
					</div>
					<hr>
					<div class="form-group text-center col-lg-10 offset-1">
						<button type="submit" class="btn btn-success btnStep2">Simpan</button>
					</div>
			</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	var i;
	var jumlah_tab = <?= $jumlah_tab ?>;

	for (i = 0; i <= jumlah_tab; i++) {
		$('.btnStep' + i).click(function() {
			var $id = $('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
			jQuery('html,body').animate({
				scrollTop: 0
			}, 0);
		});

	}

	$('.make-switch').change(function() {
		if ($("#question-cerita").is(":checked") == true) {
			$("#cerita").attr("placeholder", "Kepada siapa biasanya kamu menceritakan masalahmu?");
		} else {
			$("#cerita").attr("placeholder", "Apa alasanmu tidak menceritakan masalahmu?");
		}
	})
</script>
