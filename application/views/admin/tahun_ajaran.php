<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold">Setting Tahun Ajaran</h1>
		</div>
		<div class="card">
			<div class="card-header">
				Setting Tahun Ajaran
			</div>
			<div class="card-body">
				<i class="mb-3"><b>Nb : Tahun ajaran yang aktif saat ini adalah <?= $this->Main_model->getTahunAjaran() ?></b></i>
				<?php
				if ($this->session->flashdata('success')) {
				?>
					<div class="alert alert-success" role="alert">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Sukses!</strong> Data <?= $this->session->userdata('success') ?> berhasil diubah.
						<?php unset($_SESSION['success']) ?>
					</div>
				<?php
				}
				?>
				<form action="<?= base_url('admin/tahun_ajaran_action') ?>" method="POST">
					<div class="mb-3">
						<label for="" class="form-label">Status</label>
						<select class="form-control select-status" id="select-status" name="status">
							<option value="Aktif" <?= $get_data[0]['status'] == 'Aktif' ? 'selected' : '' ?>>Setting Manual</option>
							<option value="Tidak Aktif" <?= $get_data[0]['status'] == 'Tidak Aktif' ? 'selected' : '' ?>>Setting Otomatis</option>
						</select>
					</div>
					<div class="mb-3" id="tahun_ajaran">
						<label for="" class="form-label">Tahun Ajaran</label>
						<input type="text" placeholder="yyyy/yyyy" data-mask="9999/9999" class="form-control" name="tahun_ajaran" value="<?= $get_data[0]['tahun_ajaran'] ?>">
						<span class="help-inline">Format : yyyy/yyyy</span><br>
						<span class="help-inline">Contoh : <?= $this->Main_model->getTahunAjaran() ?></span>
					</div>
					<div class="mb-3">
						<div class="alert alert-info">
							<b>Setting Otomatis </b> : Otomatis mencetak tahun ajaran dengan ketentuan, Jika Bulan saat ini berada Diatas atau sama dengan Bulan ke 7 (Juli) Maka Tahun ajarannya adalah "(Tahun ini)/(Tahun Depan)". Jika tidak sesuai dengan kondisi sebelumnya. maka "(Tahun Lalu)/(Tahun Ini)" <br><br>
							<b>Setting Manual</b> : Menampilkan Tahun ajaran Sesuai Yang di tulis
							<br><br>
							<i> Tahun ajaran digunakan pada pengisian instrumen sehingga Kelas yang tampil hanya kelas yang Berada pada tahun ajaran yang aktif saat ini saja.</i>
						</div>
					</div>
					<button type="submit" class="btn btn-primary container-fluid">Edit Tahun Ajaran</button>
				</form>
			</div>

		</div>
	</div>
</div>
<script type="text/javascript">
	<?php if ($get_data[0]['status'] == 'Aktif') { ?>
		$("#tahun_ajaran").show();
		$("#tahun_ajaran").prop('disabled', false);
	<?php } else { ?>
		$("#tahun_ajaran").hide();
		$("#tahun_ajaran").prop('disabled', true);
	<?php } ?>

	$('#select-status').change(function() {
		if ($(this).val() == 'Aktif') {
			$("#tahun_ajaran").show();
			$("#tahun_ajaran").prop('disabled', false);
		} else {
			$("#tahun_ajaran").hide();
			$("#tahun_ajaran").prop('disabled', true);
		}
	});
</script>
