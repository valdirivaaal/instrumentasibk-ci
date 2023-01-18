<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold">Event Key</h1>
		</div>
		<div class="alert alert-danger" role="alert">
			<strong>Halo, <?= $get_profil[0]['nama_lengkap'] ?>!</strong> <br>Kamu perlu berlangganan layanan Instrumentasi BK sebelum menggunakan fitur ini. Silahkan hubungi Aviv (082213069196) untuk informasi lebih lanjut.</a>
		</div>
		<div class="card">
			<div class="card-header">
				Masukkan Event Key Anda
			</div>
			<div class="card-body">
				<?php
				if ($this->session->flashdata('error')) {
				?>
					<div class="alert alert-danger" role="alert">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Gagal!</strong> <?= $this->session->flashdata('msg') ?>.
					</div>
				<?php
				}
				?>
				<form action="<?= base_url('ticket/save_sociometri') ?>" method="post">
					<div class="form-group">
						<label class="col-lg-12 control-label">Event Key</label>
						<div class="col-md-12">
							<div class="form-group">
								<input type="hidden" name="controller" value="<?= getUrlCurrently() ?>">
								<input type="text" class="form-control" name="event_key" placeholder="Masukkan event key anda">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-10">
							<button type="submit" class="btn btn-success">Simpan</button>
							<button type="button" class="btn btn-default">Batal</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
