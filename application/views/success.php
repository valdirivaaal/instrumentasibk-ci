<div class="row mt-5">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
		</div>
		<div class="card">
			<div class="card-header text-center">
				<?php
				if ($this->session->flashdata('success')) {
					?>
					<div class="alert alert-success" role="alert">
						<h1 class="font-weight-bold"> Pengisian Sukses</h1>
						<p><i class="fa fa-check-circle fa-5x"></i></p>
						<p><strong><?= $this->session->flashdata('success') ?></strong></p>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</div>