<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ setting('general.company_logo') ? Storage::url(setting('general.company_logo')) : asset('public/img/company.png') }}" class="img-circle" alt="@setting('general.company_name')">
            </div>
            <div class="pull-left info">
                <p>{{ str_limit(setting('general.company_name'), 22) }}</p>
            </div>
        </div>
        <!-- search form -->
        <!-- <form action="#" method="get" id="form-search" class="sidebar-form">
            <div id="search" class="input-group">
                <input type="text" name="search" value="<?php //echo $search; ?>" id="input-search" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </form> -->
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        {!! Menu::get('AdminMenu') !!}
    </section>
    <!-- /.sidebar -->
</aside>
