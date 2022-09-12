<div class="row">
	<aside class="profile-nav col-lg-3">
		<section class="card">
			<div class="user-heading round">
				<img src="<?= (!empty($get_profil[0]['foto'])) ? base_url('uploads/foto_profil/' . $this->session->userdata('id') . '/' . $get_profil[0]['foto']) : base_url('assets/member/img/profile-avatar.jpg') ?>" class="img-fluid" alt="">
				<button type="button" class="btn btn-primary btn-sm mt-2" data-toggle="modal" data-target="#exampleModal">
					<i class="fas fa-file-image"></i> Ganti Foto Profil
				</button>
				<h1 class="mt-3"><?= $get_profil[0]['nama_lengkap'] ?></h1>
				<p><?= strtoupper(@$get_profil[0]['status']) ?> <?= strtoupper(@$get_profil[0]['instansi']) ?></p>
			</div>
		</section>
	</aside>
	<aside class="profile-info col-lg-9">
		<section class="card">
			<div class="bio-graph-heading">
				<?= (@$get_profil[0]['motto_hidup']) ? @$get_profil[0]['motto_hidup'] : 'Kamu belum mengisi motto hidup' ?>
			</div>
			<div class="card-body bio-graph-info">
				<?php
				if ($this->session->flashdata('success')) {
				?>
					<div class="alert alert-success" role="alert">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Sukses!</strong> Data <?= $this->session->flashdata('success') ?> berhasil disimpan.
						<?php unset($_SESSION["success"]) ?>
					</div>
				<?php
				}
				?>
				<?php
				if ($this->session->flashdata('error')) {
				?>
					<div class="alert alert-danger" role="alert">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Error!</strong><?= $this->session->flashdata('error') ?>
						<?php unset($_SESSION["error"]) ?>
					</div>
				<?php
				}
				?>
				<div class="row">
					<div class="col-md-6">
						<h1>Data Diri</h1>
					</div>
					<div class="col-md-6  text-right">
						<a class="btn btn-sm btn-primary" href="<?= base_url('profil/edit') ?>"><i class="fa fa-pencil"></i> Ubah Profil</a>
					</div>
				</div>
				<div class="row">
					<div class="bio-row">
						<p><span>Nama Lengkap </span>: <?= @$get_profil[0]['nama_lengkap'] ?></p>
					</div>
					<div class="bio-row">
						<p><span>Tanggal Lahir</span>: <?= date('d-m-Y', strtotime($get_profil[0]['tanggal_lahir']))  ?></p>
					</div>
					<div class="bio-row">
						<p><span>Telp. Sekolah </span>: <?= ($get_profil[0]['telp_instansi']) ? $get_profil[0]['telp_instansi'] : '-' ?></p>
					</div>
					<div class="bio-row">
						<p><span>No Ponsel </span>: <?= ($get_profil[0]['no_whatsapp']) ? $get_profil[0]['no_whatsapp'] : '-' ?></p>
					</div>
					<div class="bio-row">
						<p><span>Alamat Sekolah </span>: <?= ($get_profil[0]['alamat_instansi']) ? $get_profil[0]['alamat_instansi'] : '-' ?></p>
					</div>
					<div class="bio-row">
						<p><span>Alamat Rumah </span>: <?= ($get_profil[0]['alamat_rumah']) ? $get_profil[0]['alamat_rumah'] : '-' ?></p>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<a class="btn btn-round btn-primary" href="<?= base_url('profil/kop_surat') ?>"><i class="fa fa-edit"></i> Buat Kop Surat</a>
					</div>
				</div>
			</div>
		</section>
	</aside>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body text-center">
				<form action="<?= base_url() . 'profil/upload' ?>" method="post" enctype="multipart/form-data">
					<div class="input-group">
						<span class="input-group-btn">
							<span class="btn btn-primary btn-file">
								Browse… <input type="file" id="" name="uploaded_img">
							</span>
						</span>
						<input type="text" class="form-control" name="filename" readonly>
					</div>
					<img id='img-upload' class="img-fluid mt-2 w-50" />
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save changes</button>
			</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$(document).on('change', '.btn-file :file', function() {
			var input = $(this),
				label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
			input.trigger('fileselect', [label]);
		});

		$('.btn-file :file').on('fileselect', function(event, label) {

			var input = $(this).parents('.input-group').find(':text'),
				log = label;

			if (input.length) {
				input.val(log);
			} else {
				if (log) alert(log);
			}

		});

		function readURL(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();

				reader.onload = function(e) {
					$('#img-upload').attr('src', e.target.result);
				}

				reader.readAsDataURL(input.files[0]);
			}
		}

		$("#imgInp").change(function() {
			readURL(this);
		});
	});
</script>
