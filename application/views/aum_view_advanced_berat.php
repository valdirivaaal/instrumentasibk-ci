<div class="row mt-5">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
		</div>
		<div class="card">
			<div class="card-header text-center">
				<h1 class="font-weight-bold"> Alat Ungkap Masalah</h1>
			</div>
			<div class="card-body">
				<form class="form-horizontal" role="form" action="<?= base_url('instrumen/aum_advanced_detail') ?>" method="get">
					<input type="hidden" name="instrumen_id" value="<?= $_GET['instrumen_id'] ?>">
					<input type="hidden" name="nama_lengkap" value="<?= $_GET['nama_lengkap'] ?>">
					<input type="hidden" name="jenis_kelamin" value="<?= $_GET['jenis_kelamin'] ?>">
					<input type="hidden" name="nis" value="<?= $_GET['nis'] ?>">
					<input type="hidden" name="kelas" value="<?= $_GET['kelas'] ?>">
					<input type="hidden" name="tanggal_lahir" value="<?= $_GET['tanggal_lahir'] ?>">
					<input type="hidden" name="jawaban" value='<?= $jawaban ?>'>

					<div class="col-md-12">
						<div class="alert alert-warning" role="alert">
							<h5 class="font-weight-bold mt-3">Petunjuk Pengisian :</h5> 
							<p>Lihatlah kembali masalah-masalah yang telah Anda tandai yang menjadi keluhan dan gangguan bagi Anda. Dari masalah-masalah tersebut, manasajakah yang Anda rasakan amat berat atau amat mengganggu dengan mengubah tombol berwarna merah menjadi hijau. Selamat mengerjakan.</p>
						</div>
					</div>

					<div class="form-group text-center col-lg-10 offset-1">
						<button type="submit" class="btn btn-success">Simpan</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>