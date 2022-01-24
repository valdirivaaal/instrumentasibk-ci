 <div class="row">
 	<div class="col-md-12">
 		<div class="panel-body bio-graph-info">
 			<h1 class="font-weight-bold"> Formulir Transaksi</h1>
 		</div>
 		<div class="card">
 			<div class="card-header">
 				<?= ($get_transaksi) ? 'Sunting' : 'Tambah' ?> Transaksi
 			</div>
 			<div class="card-body">
 				<?php
 				if ($this->session->flashdata('error')) {
 					?>
 					<div class="alert alert-danger" role="alert">
 						<strong>Gagal!</strong> Event key tidak tersedia.
 					</div>
 					<?php
 				}
 				?>
 				<form class="form-horizontal" role="form" action="<?= base_url('admin/transaksi_save') ?>" method="post">
 					<input type="hidden" name="id" value="<?= (isset($get_transaksi[0]['id'])) ? $get_transaksi[0]['id'] : '' ?>">
 					<div class="form-group">
 						<label  class="col-lg-12 control-label">Nama Pembeli</label>
 						<div class="col-md-12">
 							<div class="form-group">
 								<input type="text" class="form-control" name="nama_pembeli" placeholder="Nama Pembeli" value="<?= (isset($get_transaksi[0]['nama_pembeli'])) ? $get_transaksi[0]['nama_pembeli'] : '' ?>" required>
 							</div>
 						</div>
 					</div>
 					
 					<div class="form-group">
 						<label  class="col-lg-12 control-label">Instansi Pembeli</label>
 						<div class="col-md-12">
 							<div class="form-group">
 								<input type="text" class="form-control" name="instansi_pembeli" placeholder="Instansi Pembeli" value="<?= (isset($get_transaksi[0]['instansi_pembeli'])) ? $get_transaksi[0]['instansi_pembeli'] : '' ?>" required>
 							</div>
 						</div>
 					</div>

 					<div class="form-group">
 						<label  class="col-lg-12 control-label">Jenis Akun</label>
 						<div class="col-md-12">
 							<div class="form-group">
 								<select class="form-control" name="jenis_akun" required>
 									<?php
 									if (!$get_transaksi[0]['jenis_akun']) {
 										?>
 										<option value="" selected>Pilih jenis akun pembeli</option>
 										<?php
 									}
 									?>
 									<option value="1" <?= @$get_transaksi[0]['jenis_akun']=='SMP' ? 'selected' : '' ?>>SMP</option>
 									<option value="2" <?= @$get_transaksi[0]['jenis_akun']=='SMA' ? 'selected' : '' ?>>SMA</option>
 									<option value="3" <?= @$get_transaksi[0]['jenis_akun']=='Konselor' ? 'selected' : '' ?>>Konselor</option>
 								</select>
 							</div>
 						</div>
 					</div>

 					<div class="form-group">
 						<label  class="col-lg-12 control-label">Event Key</label>
 						<div class="col-md-12">
 							<div class="form-group">
 								<input type="text" class="form-control" name="event_key" placeholder="Event Key" value="<?= (isset($get_transaksi[0]['event_key'])) ? $get_transaksi[0]['event_key'] : '' ?>" required>
 							</div>
 						</div>
 					</div>

 					<div class="form-group">
 						<label  class="col-lg-12 control-label">Potongan Harga</label>
 						<div class="col-md-12">
 							<div class="form-group input-group">
 								<div class="input-group-prepend">
 									<span class="input-group-text">Rp</span>
 								</div>
 								<input type="text" class="form-control money" name="potongan_harga" placeholder="Potongan Harga" value="<?= (isset($get_transaksi[0]['potongan_harga'])) ? $get_transaksi[0]['potongan_harga'] : 0 ?>" required>
 							</div>
 						</div>
 					</div>

 					<div class="form-group">
 						<label  class="col-lg-12 control-label">Tanggal Transaksi</label>
 						<div class="col-md-12">
 							<div class="form-group">
 								<input type="text" name="tanggal_transaksi" class="form-control date-picker-input" placeholder="Tanggal Transaksi" value=
 								"<?= (isset($get_transaksi[0]['tanggal_transaksi'])) ? date('d-m-Y',strtotime($get_transaksi[0]['tanggal_transaksi'])) : date('d-m-Y') ?>"> 
 							</div>
 						</div>
 					</div>

 					<div class="form-group-append">
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

 <script type="text/javascript">
 	$(document).ready(function () {
 		$('.money').mask('000.000.000.000.000,00', {reverse: true});
 	});
 </script>