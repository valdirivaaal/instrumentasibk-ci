<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Landing Page</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
	<link rel="stylesheet" href="<?= base_url('assets/landing/style.css') ?>">
</head>

<body>
	<section class="main">
		<nav class="navbar bg-transparent">
			<div class="container">
				<a class="navbar-brand" href="/">Instrumentasi BK</a>
			</div>
		</nav>
		<div class="container main-content">
			<div class="row">
				<div class="col-lg-7 order-lg-2">
					<div class="main-text d-flex flex-row text-uppercase">
						<span class="first">Instrumentasi</span>
						<span class="last"> BK</span>
					</div>
					<p>Sebuah website instrumentasi BK yang akan membantu guru BK dalam pengelolaan instrumen. Kami akan
						menyediakan AUM Umum, AUM PTSDL dan Alat Ungkap Arah Peminatan sebagai bahan pengambilan
						keputusan guru BK.</p>
					<a href="<?= base_url('auth/login') ?>" class="btn btn-pink rounded-pill px-5 login-main-btn">Login</a>
				</div>
				<div class="col-lg-5 col-sm-12 order-sm-last order-lg-first form-join">
					<div class="card rounded">
						<div class="card-body">
							<div class="card-title">
								Bergabung Sekarang
							</div>
							<form action="/" method="POST">
								<?php
								$post = $this->input->post();

								if (!empty($post)) {
									$this->session->set_flashdata('value', $post);
									redirect('auth/register');
								}
								?>
								<div class="form-floating mb-3">
									<input type="text" class="form-control rounded-pill" name="nama_lengkap" id="floatingInput" placeholder="Nama">
									<label for="floatingInput">Nama</label>
								</div>
								<div class="form-floating mb-3">
									<input type="email" class="form-control rounded-pill" name="email" id="floatingInput" placeholder="name@example.com">
									<label for="floatingInput">Email address</label>
								</div>
								<div class="form-floating mb-3">
									<input type="number" class="form-control rounded-pill" name="no_whatsapp" id="floatingInput" placeholder="08xxxxxxx">
									<label for="floatingInput">Nomor Whatsapp</label>
								</div>
								<button type="submit" class="btn btn-pink container-fluid rounded-pill p-2">Submit</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="footer">Copyright © 2022 Instrumentasi BK</div>
	</section>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
</body>

</html>
