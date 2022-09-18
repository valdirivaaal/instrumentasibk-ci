  <aside>
  	<div id="sidebar" class="nav-collapse ">
  		<!-- sidebar menu start-->
  		<ul class="sidebar-menu" id="nav-accordion">
  			<li>
  				<a class="<?= (uri_string() == 'admin/dashboard') ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>">
  					<i class="fa fa-dashboard"></i>
  					<span> Dashboard</span>
  				</a>
  			</li>

  			<li>
  				<a class="<?= (uri_string() == 'admin/user') ? 'active' : '' ?>" href="<?= base_url('admin/user') ?>">
  					<i class="fa fa-users"></i>
  					<span>Data User</span>
  				</a>
  			</li>

  			<li>
  				<a class="<?= (uri_string() == 'admin/narasumber') ? 'active' : '' ?>" href="<?= base_url('admin/narasumber') ?>">
  					<i class="fa fa-users"></i>
  					<span>Data Narasumber</span>
  				</a>
  			</li>

  			<li class="sub-menu">
  				<a href="javascript:;" class="<?= (uri_string() == '' || uri_string() == 'admin/key_available' || uri_string() == 'admin/key_used') ? 'active' : '' ?>">
  					<i class="fa fa-key"></i>
  					<span>Event Key</span>
  				</a>
  				<ul class="sub">
  					<li class="<?= (uri_string() == 'admin/key_available') ? 'active' : '' ?>"><a href="<?= base_url('admin/key_available') ?>">Tersedia</a></li>
  					<li class="<?= (uri_string() == 'admin/key_used') ? 'active' : '' ?>"><a href="<?= base_url('admin/key_used') ?>">Terpakai</a></li>
  				</ul>
  			</li>

  			<!-- <li>
          <a class="<?= (uri_string() == 'kelas') ? 'active' : '' ?>" href="<?= base_url('admin/manage') ?>">
            <i class="fa fa-th"></i>
            <span>Data Admin</span>
          </a>
        </li> -->
  			<li>
  				<a class="<?= (uri_string() == 'admin/logo') ? 'active' : '' ?>" href="<?= base_url('') ?>admin/logo">
  					<i class="fa fa-image"></i>
  					<span>Manajer Logo</span>
  				</a>
  			</li>
  			<li>
  				<a class="<?= (uri_string() == 'admin/pengumuman') ? 'active' : '' ?>" href="<?= base_url('') ?>admin/pengumuman">
  					<i class="fa fa-bullhorn"></i>
  					<span>Ubah Pengumuman</span>
  				</a>
  			</li>
  			<li>
  				<a class="<?= (uri_string() == 'admin/tahun_ajaran') ? 'active' : '' ?>" href="<?= base_url('') ?>admin/tahun_ajaran">
  					<i class="fa fa-calendar"></i>
  					<span>Ubah Tahun Ajaran</span>
  				</a>
  			</li>
  			<li>
  				<a class="<?= (uri_string() == 'dashboard') ? 'active' : '' ?>" href="<?= base_url('') ?>">
  					<i class="fa fa-arrow-left"></i>
  					<span>Back to Instrumentasi BK</span>
  				</a>
  			</li>
  		</ul>
  		<!-- sidebar menu end-->
  	</div>
  </aside>
