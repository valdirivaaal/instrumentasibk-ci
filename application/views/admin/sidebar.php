  <aside>
    <div id="sidebar" class="nav-collapse ">
      <!-- sidebar menu start-->
      <ul class="sidebar-menu" id="nav-accordion">
        <li>
          <a class="<?= (getController() == 'dashboard') ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>">
            <i class="fa fa-dashboard"></i>
            <span> Dashboard</span>
          </a>
        </li>

        <li>
          <a class="<?= (getController() == 'profil') ? 'active' : '' ?>" href="<?= base_url('admin/transaksi/' . date('m')) ?>">
            <i class="fa fa-money-check"></i>
            <span>Data Transaksi</span>
          </a>
        </li>

        <li>
          <a class="<?= (getController() == 'kelas') ? 'active' : '' ?>" href="<?= base_url('admin/user') ?>">
            <i class="fa fa-users"></i>
            <span>Data Pengguna</span>
          </a>
        </li>

        <li class="sub-menu">
          <a href="javascript:;" class="<?= (getController() == 'aum' || getController() == 'ptsdl' || getController() == 'auap') ? 'active' : '' ?>">
            <i class="fa fa-key"></i>
            <span>Event Key</span>
          </a>
          <ul class="sub">
            <li class="<?= (getController() == 'aum') ? 'active' : '' ?>"><a href="<?= base_url('admin/key_available') ?>">Tersedia</a></li>
            <li class="<?= (getController() == 'ptsdl') ? 'active' : '' ?>"><a href="<?= base_url('admin/key_used') ?>">Terpakai</a></li>
          </ul>
        </li>

        <!-- <li>
          <a class="<?= (getController() == 'kelas') ? 'active' : '' ?>" href="<?= base_url('admin/manage') ?>">
            <i class="fa fa-th"></i>
            <span>Data Admin</span>
          </a>
        </li> -->
        <li>
          <a class="<?= (getController() == 'logo') ? 'active' : '' ?>" href="<?= base_url('') ?>admin/logo">
            <i class="fa fa-image"></i>
            <span>Manajer Logo</span>
          </a>
        </li>
        <li>
          <a class="<?= (getController() == 'dashboard') ? 'active' : '' ?>" href="<?= base_url('') ?>">
            <i class="fa fa-arrow-left"></i>
            <span>Back to Instrumentasi BK</span>
          </a>
        </li>
      </ul>
      <!-- sidebar menu end-->
    </div>
  </aside>