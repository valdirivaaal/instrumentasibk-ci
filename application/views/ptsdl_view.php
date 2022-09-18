<div class="row mt-5">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
		</div>
		<div class="card">
			<div class="card-header text-center">
				<h1 class="font-weight-bold"> Alat Ungkap Masalah Kegiatan Belajar</h1>
			</div>
			<div class="card-body">
				<form class="form-horizontal" role="form" action="<?= base_url('instrumen/ptsdl_advanced') ?>" method="get">
					<input type="hidden" value="<?= $get_kode[0]['id'] ?>" name="instrumen_id">
					<div class="form-group">
						<label class="col-lg-12 control-label">Nama Lengkap</label>
						<div class="col-lg-12">
							<input type="text" class="form-control" placeholder="Nama Lengkap" name="nama_lengkap" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-12 control-label">Email</label>
						<div class="col-lg-12">
							<input type="email" class="form-control" placeholder="Email" name="email" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-12 control-label">Nomor WA</label>
						<div class="col-lg-12">
							<input type="number" class="form-control" placeholder="Nomor WA" name="whatsapp" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-12 control-label">Jenis Kelamin</label>
						<div class="col-lg-12">
							<div class="custom-control custom-radio custom-control-inline">
								<input type="radio" id="customRadioInline1" name="jenis_kelamin" class="custom-control-input" value="1" required>
								<label class="custom-control-label" for="customRadioInline1">Laki-laki</label>
							</div>
							<div class="custom-control custom-radio custom-control-inline">
								<input type="radio" id="customRadioInline2" name="jenis_kelamin" class="custom-control-input" value="2" required>
								<label class="custom-control-label" for="customRadioInline2">Perempuan</label>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-12 control-label">NIS</label>
						<div class="col-lg-12">
							<input type="text" class="form-control" placeholder="NIS" name="nis">
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-12 control-label">Kelas</label>
						<div class="col-lg-12">
							<select class="js-example-basic-single" name="kelas" required>
								<option value="">Pilih kelas kamu</option>
								<?php
								foreach ($get_kelas as $key => $value) {
								?>
									<option value="<?= $value['id'] ?>"><?= $value['kelas'] ?></option>
								<?php
								}
								?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-12 control-label">Tanggal Lahir</label>
						<div class="col-lg-12">
							<input type="text" placeholder="dd-mm-yyyy" data-mask="99-99-9999" class="form-control" name="tanggal_lahir" value="">
							<span class="help-inline">Format : dd-mm-yyyy</span>
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
