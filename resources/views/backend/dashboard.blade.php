@extends('backend.layouts.main')
@section('content')
    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <div class="row">
                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-end">
                                    <div class="avatar-sm mx-auto mb-4">
                                        <span class="avatar-title rounded-circle bg-light font-size-24">
                                            <i class="mdi mdi-cash-multiple text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-muted text-uppercase fw-semibold">Total Users</p>
                                    <h4 class="mb-1 mt-1"><span class="counter-value"
                                            data-target="{{ $totalUsers }}">0</span></h4>
                                </div>
                                </p>
                            </div>
                        </div>
                    </div> <!-- end col-->

                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-end">
                                    <div class="avatar-sm mx-auto mb-4">
                                        <span class="avatar-title rounded-circle bg-light font-size-24">
                                            <i class="mdi mdi-refresh-circle text-success"></i>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-muted text-uppercase fw-semibold">Total Category</p>
                                    <h4 class="mb-1 mt-1"><span class="counter-value"
                                            data-target="{{ $totalCategory }}">0</span></h4>
                                </div>
                                </p>
                            </div>
                        </div>
                    </div> <!-- end col-->

                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-end">
                                    <div class="avatar-sm mx-auto mb-4">
                                        <span class="avatar-title rounded-circle bg-light font-size-24">
                                            <i class="mdi mdi-account-group text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-muted text-uppercase fw-semibold">Total Staff</p>
                                    <h4 class="mb-1 mt-1"><span class="counter-value"
                                            data-target="{{ $totalStaff }}">0</span></h4>
                                </div>
                                </p>
                            </div>
                        </div>
                    </div> <!-- end col-->

                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-end">
                                    <div class="avatar-sm mx-auto mb-4">
                                        <span class="avatar-title rounded-circle bg-light font-size-24">
                                            <i class="mdi mdi-cart-check text-success"></i>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-muted text-uppercase fw-semibold">Total Enquiry</p>
                                    <h4 class="mb-1 mt-1"><span class="counter-value" data-target="">0</span></h4>
                                </div>
                                </p>
                            </div>
                        </div>
                    </div> <!-- end col-->
                </div>
                <!-- end row-->

                <div class="row">
                    <div class="col-xl-10">
                        <div class="card card-height-100">
                            <div class="card-body">
                                {{-- <div class="float-end">
                                            <div class="dropdown">
                                                <a class="dropdown-toggle text-reset" href="#" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="fw-semibold">Sort By:</span> <span class="text-muted">Yearly<i class="mdi mdi-chevron-down ms-1"></i></span>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                                                    <a class="dropdown-item" href="#">Yearly</a>
                                                    <a class="dropdown-item" href="#">Monthly</a>
                                                    <a class="dropdown-item" href="#">Weekly</a>
                                                    <a class="dropdown-item" href="#">Today</a>
                                                </div>
                                            </div>
                                        </div> --}}
                                <h4 class="card-title mb-4">Subscription Revenue</h4>

                                <div class="mt-1">
                                    <ul class="list-inline main-chart mb-0">
                                        <li class="list-inline-item chart-border-left me-0 border-0">
                                            <h3 class="text-primary">₹<span
                                                    data-plugin="counterup">{{ $totalCompanyRevenue }}</span><span
                                                    class="text-muted d-inline-block fw-normal font-size-15 ms-3">Company
                                                    Revenue</span></h3>
                                        </li>
                                        <li class="list-inline-item chart-border-left me-0">

                                            <h3 class="text-primary">₹<span
                                                    data-plugin="counterup">{{ $totalGST }}</span><span
                                                    class="text-muted d-inline-block fw-normal font-size-15 ms-3">GST
                                                    Amount</span></h3>

                                        </li>
                                        <li class="list-inline-item chart-border-left me-0">
                                            <h3 class="text-primary">₹<span
                                                    data-plugin="counterup">{{ $totalCreatorPool }}</span><span
                                                    class="text-muted d-inline-block fw-normal font-size-15 ms-3">Creator
                                                    Pool</span></h3>
                                        </li>
                                        <li class="list-inline-item chart-border-left me-0">
                                            <h3 class="text-primary">₹<span
                                                    data-plugin="counterup">{{ $totalReferral }}</span><span
                                                    class="text-muted d-inline-block fw-normal font-size-15 ms-3">Referral
                                                    Pool</span></h3>
                                        </li>
                                    </ul>
                                </div>

                                <div class="mt-3">
                                    <div id="sales-analytics-chart" class="apex-charts" dir="ltr"></div>
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    {{-- <div class="col-xl-4">
                                <div class="card bg-pattern">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-sm-8">
                                                <div class="avatar-xs mb-3">
                                                    <span class="avatar-title rounded-circle bg-light font-size-24">
                                                        <i class="mdi mdi-bullhorn-outline text-primary"></i>
                                                    </span>
                                                </div>
                                                <p class="font-size-18">Enhance your <b>Campaign</b> for better outreach <i class="mdi mdi-arrow-right"></i></p>
                                                <div class="mt-4">
                                                    <a href="pages-pricing.html" class="btn btn-success waves-effect waves-light">Upgrade Account!</a>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="mt-4 mt-sm-0">
                                                    <img src="assets/images/widget-img.png" class="img-fluid" alt="widget-img">
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end card-body-->
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <div class="float-end">
                                            <div class="dropdown">
                                                <a class="dropdown-toggle text-reset" href="#" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="fw-semibold">Report By:</span> <span class="text-muted">Monthly<i class="mdi mdi-chevron-down ms-1"></i></span>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton2">
                                                    <a class="dropdown-item" href="#">Yearly</a>
                                                    <a class="dropdown-item" href="#">Monthly</a>
                                                    <a class="dropdown-item" href="#">Weekly</a>
                                                    <a class="dropdown-item" href="#">Today</a>
                                                </div>
                                            </div>
                                        </div>

                                        <h4 class="card-title mb-4">Earning Reports</h4>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <p class="text-muted mb-1">This Month</p>
                                                        <h5 class="">$12,582<small class="badge badge-light-success font-13">+15%</small></h5>
                                                    </div>

                                                    <div class="col-6">
                                                        <p class="text-muted mb-1">Last Month</p>
                                                        <h5 class="">$98,741 <small class="badge badge-light-danger font-13">-5%</small></h5>
                                                    </div>
                                                </div>
                                                <p class="text-muted"><span class="text-success me-1"> 12%<i class="mdi mdi-arrow-up"></i></span>From previous period</p>

                                                <div class="mt-4">
                                                    <a href="" class="btn btn-primary waves-effect waves-light btn-sm">Generate Reports <i class="mdi mdi-arrow-right ms-1"></i></a>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mt-4 mt-sm-0">
                                                    <div id="radialBar-chart" class="apex-charts"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end card -->

                            </div> --}}
                </div>


            </div>
            <!-- container-fluid -->
        </div>
    @endsection
