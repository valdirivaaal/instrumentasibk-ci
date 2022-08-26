<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold"> Profil Anda</h1>
		</div>
		<div class="card">
			<div class="card-header">
				Atur Kop Surat
			</div>
			<div class="card-body">
				<?php
				if ($this->session->userdata('success')) {
				?>
					<div class="alert alert-success" role="alert">
						<strong>Sukses!</strong> <?= $this->session->userdata('success') ?>
					</div>
				<?php
				}
				?>
				<form class="form-horizontal" role="form" action="<?= base_url('profil/save_kop_surat') ?>" method="post" enctype="multipart/form-data">
					<div class="form-group">
						<label class="col-lg-12 control-label">Contoh Format</label>
						<div class="col-lg-12 text-center">
							<img src="<?= base_url('assets/img/kop_surat.png') ?>" class="img-fluid">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-12 control-label">Logo</label>
						<div class="col-lg-12">
							<select class="js-example-basic-single form-control select2" name="logo" required>
								<option value="">Pilih Logo daerah kamu</option>
								<?php foreach ($get_logo as $val) { ?>
									<option value="<?= $val['id'] ?>" <?= $get_kopsurat[0]['logo'] == $val['id'] ? 'selected' : '' ?>><?= $val['nama'] ?></option>
								<?php } ?>
								<option value="other" <?= $get_kopsurat[0]['logo'] == 'other' || !$get_kopsurat[0]['logo'] ?  'selected' :  '' ?>>Other</option>

							</select>

						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-12 control-label">Baris Pertama</label>
						<div class="col-lg-12">
							<input type="text" class="form-control" name="baris_pertama" placeholder="Contoh : Pemerintah Provinsi Daerah Khusus DKI Jakarta" value="<?= (@$get_kopsurat[0]['baris_pertama']) ? @$get_kopsurat[0]['baris_pertama'] : '' ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-12 control-label">Baris Kedua</label>
						<div class="col-lg-12">
							<input type="text" class="form-control" name="baris_kedua" placeholder="Contoh : Dinas Pendidikan" value="<?= (@$get_kopsurat[0]['baris_kedua']) ? @$get_kopsurat[0]['baris_kedua'] : '' ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-12 control-label">Baris Ketiga</label>
						<div class="col-lg-12">
							<input type="text" class="form-control" name="baris_ketiga" placeholder="Contoh : Sekolah Menengah Atas (SMA) Negeri 200 Jakarta" value="<?= (@$get_kopsurat[0]['baris_ketiga']) ? @$get_kopsurat[0]['baris_ketiga'] : '' ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-12 control-label">Baris Keempat</label>
						<div class="col-lg-12">
							<input type="text" class="form-control" name="baris_keempat" placeholder="Contoh : Jl.Kartika Eka Paksi, Cipinang Melayu, Kecamatan Makasar" value="<?= (@$get_kopsurat[0]['baris_keempat']) ? @$get_kopsurat[0]['baris_keempat'] : '' ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-12 control-label">Baris Kelima</label>
						<div class="col-lg-12">
							<input type="text" class="form-control" name="baris_kelima" placeholder="Kosongkan apabila memang tidak ada" value="<?= (@$get_kopsurat[0]['baris_kelima']) ? @$get_kopsurat[0]['baris_kelima'] : '' ?>">
						</div>
					</div>
					<div class="form-group" id="logo_daerah">
						<label class="col-lg-12 control-label">Logo Kiri</label>
						<div class="col-lg-12">
							<?php
							if (@$get_kopsurat[0]['logo_kiri']) {
							?>
								<p><img width="150" src="<?= base_url('uploads/logo/' . $this->session->userdata('id') . '/' . $get_kopsurat[0]['logo_kiri']) ?>" class="img-responsive" alt="Image"></p>

								<a href="<?= base_url('profil/hapus_logokiri/' . $get_kopsurat[0]['user_id']) ?>"><button type="button" class="btn btn-danger"><i class="fa fa-trash-o"></i> Hapus Foto</button></a>
							<?php
							} else {
							?>
								<input type="file" name="logo_kiri" class="form-control" accept="image/*">
							<?php
							}
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-12 control-label">Logo Kanan</label>
						<div class="col-lg-12">
							<?php
							if (@$get_kopsurat[0]['logo_kanan']) {
							?>
								<p><img width="150" src="<?= base_url('uploads/logo/' . $this->session->userdata('id') . '/' . $get_kopsurat[0]['logo_kanan']) ?>" class="img-responsive" alt="Image"></p>

								<a href="<?= base_url('profil/hapus_logokanan/' . $get_kopsurat[0]['user_id']) ?>"><button type="button" class="btn btn-danger"><i class="fa fa-trash-o"></i> Hapus Foto</button></a>
							<?php
							} else {
							?>
								<input type="file" name="logo_kanan" class="form-control" accept="image/*">
							<?php
							}
							?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-10">
							<button type="submit" class="btn btn-success">Save</button>
							<button type="button" class="btn btn-default">Cancel</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		<?php if ($get_kopsurat[0]['logo'] == 'other' || !$get_kopsurat[0]['logo']) { ?>
			$("#logo_daerah").show();
			$("#logo_daerah").prop('disabled', false);
		<?php } else { ?>
			$("#logo_daerah").hide();
			$("#logo_daerah").prop('disabled', true);
		<?php } ?>

		$('.select2').change(function() {
			if ($(this).val() == 'other') {
				$("#logo_daerah").show();
				$("#logo_daerah").prop('disabled', false);
			} else {
				$("#logo_daerah").hide();
				$("#logo_daerah").prop('disabled', true);
			}
		});
	</script>
