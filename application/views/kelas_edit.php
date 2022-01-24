 <div class="row">
 	<div class="col-md-12">
 		<div class="panel-body bio-graph-info">
 			<h1 class="font-weight-bold"> <?= (getField('user_info','status',array('user_id'=>$this->session->userdata('id')))=='Guru BK') ? 'Kelas' : 'Kelompok' ?></h1>
 		</div>
 		<div class="card">
 			<div class="card-header">
 				Sunting Kelas
 			</div>
 			<div class="card-body">
 				<form class="form-horizontal" role="form" action="<?= base_url('kelas/save') ?>" method="post">
 					<input type="hidden" name="id" value="<?= (isset($get_kelas[0]['id'])) ? $get_kelas[0]['id'] : '' ?>">
 					<div class="form-group">
 						<label  class="col-lg-12 control-label"><?= (getField('user_info','status',array('user_id'=>$this->session->userdata('id')))=='Guru BK') ? 'Kelas' : 'Kelompok' ?></label>
 						<div class="col-md-12">
 							<div class="form-group">
 								<input type="text" class="form-control" name="kelas" placeholder="Kelas" value="<?= (isset($get_kelas[0]['kelas'])) ? $get_kelas[0]['kelas'] : '' ?>" required>
 							</div>
 						</div>
 					</div>
 					<div class="form-group">
 						<label  class="col-lg-12 control-label">Jumlah Siswa</label>
 						<div class="col-md-12">
 							<div class="form-group">
 								<input type="number" class="form-control" name="jumlah_siswa" placeholder="Jumlah siswa" value="<?= (isset($get_kelas[0]['jumlah_siswa'])) ? $get_kelas[0]['jumlah_siswa'] : '' ?>" required>
 							</div>
 						</div>
 					</div>

 					<?php
 					if (getField('user_info','status',array('user_id'=>$this->session->userdata('id')))=='Guru BK') {
 						?>
 						<div class="form-group">
 							<label  class="col-lg-12 control-label">Guru BK</label>
 							<div class="col-md-12">
 								<div class="form-group">
 									<select class="form-control" name="konselor_id" required>
 										<option value="">Pilih guru BK</option>
 										<?php
 										foreach ($get_konselor as $key => $value) {
 											?>
 											<option value="<?= $value['id'] ?>" <?= (@$get_kelas[0]['konselor_id']==$value['id']) ? 'selected' : '' ?>><?= $value['nama_lengkap'] ?></option>
 											<?php
 										}
 										?>
 									</select>
 								</div>
 							</div>
 						</div>
 						<?php
 					} else {
 						?>
 						<div class="form-group">
 							<label  class="col-lg-12 control-label">Jenjang</label>
 							<div class="col-md-12">
 								<div class="form-group">
 									<select class="form-control" name="jenjang" required>
 										<option value="" selected>Pilih jenjang kelas</option>
 										<option value="SD">SD</option>
 										<option value="SMP">SMP</option>
 										<option value="SMA">SMA</option>
 										<option value="PT">Perguruan Tinggi</option>
 										<option value="Umum">UMUM</option>
 									</select>
 								</div>
 							</div>
 						</div>
 						<?php
 					}
 					?>
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