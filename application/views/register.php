<div class="row mt-5">
	<div class="col-lg-10 col-md-12 offset-lg-1">
		<div class="panel-body bio-graph-info">
		</div>
		<div class="card">
			<div class="card-header text-center">
				<h3 class="font-weight-bold">Coba instrumentasi BK sekarang</h3>
			</div>
			<div class="card-body">
				<form class="form-horizontal" role="form" action="<?= base_url('auth/register_save') ?>" method="post">
					<?php
					if ($this->session->flashdata('error')) {
					?>
						<div class="alert alert-danger" role="alert">
							<strong>Gagal!</strong> <?= $this->session->flashdata('msg') ?>
						</div>
					<?php
					}
					?>
					<div class="form-group">
						<label class="col-lg-12 control-label">Nama Lengkap</label>
						<div class="col-lg-12">
							<input type="text" class="form-control step1" placeholder="Nama Lengkap" name="nama_lengkap" required value="<?= $this->session->flashdata('value')['nama_lengkap'] ? $this->session->flashdata('value')['nama_lengkap'] : '' ?>">
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-12 control-label">Email</label>
						<div class="col-lg-12">
							<input type="email" class="form-control step1" placeholder="Email" name="email" required value="<?= $this->session->flashdata('value')['email'] ? $this->session->flashdata('value')['email'] : '' ?>">
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-12 control-label">Nomor Whatsapp</label>
						<div class="col-lg-12">
							<input type="text" class="form-control step1" placeholder="Whatsapp" name="no_whatsapp" required value="<?= $this->session->flashdata('value')['no_whatsapp'] ? $this->session->flashdata('value')['no_whatsapp'] : '' ?>">
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-12 control-label">Kata Sandi</label>
						<div class="col-lg-12">
							<input type="password" id="password" class="form-control step1" placeholder="Masukkan kata sandi" name="password" required value="<?= ($this->session->flashdata('type') == 'email') ? $this->session->flashdata('value')['password'] : '' ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-12 control-label">Konfirmasi Kata Sandi</label>
						<div class="col-lg-12">
							<input type="password" id="password_confirmation" class="form-control step1" placeholder="Masukkan ulang kata sandi" name="password_conf" required>
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-12 control-label">Jenis Kelamin</label>
						<div class="col-lg-12">
							<select class="form-control" name="jenis_kelamin" required>
								<option value="" <?= ($this->session->flashdata('error')) || !$this->session->flashdata('value')['jenis_kelamin'] ? '' : 'selected' ?>>Pilih jenis kelamin anda</option>
								<option value="1" <?= ($this->session->flashdata('value')['jenis_kelamin'] == '1') ? 'selected' : '' ?>>Laki-laki</option>
								<option value="2" <?= ($this->session->flashdata('value')['jenis_kelamin'] == '2') ? 'selected' : '' ?>>Perempuan</option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-12 control-label">Tipe Akun</label>
						<div class="col-lg-12">
							<select class="form-control" name="jenjang" required>
								<option value="" selected>Pilih jenis akun anda</option>
								<option value="1">Guru BK SD</option>
								<option value="2">Guru BK SMP</option>
								<option value="3">Guru BK SMA</option>
								<option value="4">Konselor</option>
							</select>
						</div>
					</div>

					<!-- <div class="form-group">
						<label  class="col-lg-12 control-label">Jenjang</label>
						<div class="col-lg-12">
							<select class="form-control" name="jenjang" required>
								<option value="" <?= ($this->session->flashdata('error')) ? '' : 'selected' ?>>Pilih jenjang tempat anda mengajar</option>
								<option value="1" <?= ($this->session->flashdata('value')['jenjang'] == '1') ? 'selected' : '' ?>>SD</option>
								<option value="2" <?= ($this->session->flashdata('value')['jenjang'] == '2') ? 'selected' : '' ?>>SMP</option>
								<option value="3" <?= ($this->session->flashdata('value')['jenjang'] == '3') ? 'selected' : '' ?>>SMA</option>
								<option value="4" <?= ($this->session->flashdata('value')['jenjang'] == '4') ? 'selected' : '' ?>>Konselor</option>
							</select>
						</div>
					</div> -->

					<div class="form-group">
						<label class="col-lg-12 control-label">Instansi</label>
						<div class="col-lg-12">
							<input type="text" class="form-control" placeholder="Instansi" name="instansi" value="<?= $this->session->flashdata('value')['instansi'] ? $this->session->flashdata('value')['instansi'] : '' ?>" required>
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-12 control-label">Alamat Instansi</label>
						<div class="col-lg-12">
							<textarea class="form-control" placeholder="Tuliskan alamat instansi anda disini" name="alamat_instansi" required rows="7"><?= $this->session->flashdata('value')['alamat_instansi'] ? $this->session->flashdata('value')['alamat_instansi'] : ''  ?></textarea>
						</div>
					</div>

					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-10">
							<button type="submit" class="btn btn-success">Simpan</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
