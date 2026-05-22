@extends('backend.layouts.main')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Revenue Section</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item active">Revenue List</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            {{-- <a href="{{ route('user.add') }}" class="btn btn-primary mb-3">Add User</a> --}}

                            <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Subscription Amount</th>
                                    <th>Company Revenue</th>
                                    <th>GST</th>
                                    <th>Creator Pool</th>
                                    <th>Referral Pool</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                    @forelse ($revenues as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->user_name ?? 'User' }}</td>
                                            <td>{{ $item->subscription_amount }}</td>

                                            <td>{{ $item->company_revenue }}</td>

                                            <td>{{ $item->gst}}</td>
                                            <td>{{ $item->creator_pool}}</td>
                                            <td>{{ $item->referral_amount}}</td>

                                            <td>

                                                <button type="button"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="confirmDelete({{ $item->id }})">
                                                    Delete
                                                </button>

                                                <form id="delete-form-{{ $item->id }}"
                                                      action="{{ route('revenue.destroy', $item->id) }}"
                                                      method="POST" style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No Users Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "User will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>

@endsection