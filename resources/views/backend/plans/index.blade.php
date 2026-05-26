@extends('backend.layouts.main')
@section('content')

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

         
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Plan Section</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item active">Plan List</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
         

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <h4 class="card-title">Plan List</h4>

                            <table id="datatable" class="table table-bordered dt-responsive w-100">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Plan Name</th>
                                        <th>Validity (Days)</th>
                                        <th>Price</th>
                                        <th>Benefits</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($plans as $plan)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $plan->plan_name }}</td>
                                            <td>{{ $plan->validity }}</td>
                                            <td>₹ {{ $plan->price }}</td>

                                            <td>
                                                @if(!empty($plan->benefits))
                                                    <ul class="mb-0 ps-3">
                                                        @foreach($plan->benefits as $benefit)
                                                            <li>{{ $benefit }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">No benefits</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if($plan->status === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>

                                            <td>
                                                <a href="{{ route('plan.edit', $plan->id)}}"
                                                   class="btn btn-sm btn-warning">
                                                    Edit
                                                </a>

                                                <form action="{{route('plan.destroy' , $plan->id)}}"
                                                      method="post"
                                                      style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
            
                                                    <button class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    
                    </div>
               
                </div>
            </div>

        </div>
    </div>

    <div class="container py-4">

    <h2 class="text-center mb-4">Learn Cryptocurrency with CryptoYaar</h2>

    <div class="row">
        @foreach($plans as $plan)
        <div class="col-md-6 mb-4">

            <div class="card shadow-lg border-0" style="background:#0b0b0b; color:white; border-radius:15px;">

                <!-- Price Section -->
                <div class="d-flex justify-content-between p-3" style="background:linear-gradient(90deg,#6a11cb,#2575fc); border-radius:15px 15px 0 0;">
                    <h4>₹{{ $plan->price }} <small>/ month</small></h4>
                    <h4>₹{{ $plan->price * 12 }} <small>/ year</small></h4>
                </div>

                <!-- Benefits -->
                <div class="p-3">
                    <h5 class="mb-3">🎁 Included Benefits</h5>

                    <ul class="list-unstyled">
                        @if($plan->benefits)
                            @foreach($plan->benefits as $benefit)
                                <li class="mb-2">✅ {{ $benefit }}</li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                <!-- Button -->
                <div class="p-3 text-center">
                    <button class="btn btn-lg text-white w-100 pay-btn"
                            data-plan-id="{{ $plan->id }}"
                            data-amount="{{ $plan->price }}"
                            style="background:linear-gradient(90deg,#ff00cc,#3333ff); border-radius:10px;">
                        Pay ₹{{ $plan->price }}
                    </button>
                </div>

            </div>

        </div>
        @endforeach
    </div>
</div>

    {{-- <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.querySelectorAll('.pay-btn').forEach(button => {
            button.addEventListener('click', function () {

                let planId = this.dataset.planId;
                let amount = this.dataset.amount;

                fetch('/api/create-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer 2|6Hmx71AEW0IexRoCmoWgzfZpgA8fXeJsEoVicDEodbc0d04f'
                    },
                    body: JSON.stringify({
                        plan_id: planId
                    })
                })
                .then(res => res.json())
                .then(data => {

                    if (data.status) {

                        var options = {
                            "key": "{{ env('RAZORPAY_KEY') }}",
                            "amount": data.amount * 100,
                            "currency": "INR",
                            "name": "{{ auth()->user()->name }}",
                            "description": "Plan Purchase",
                            "order_id": data.order_id,

                            "handler": function (response){
                                // console.log(response);

                                fetch('/api/verify-payment', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Authorization': 'Bearer 2|6Hmx71AEW0IexRoCmoWgzfZpgA8fXeJsEoVicDEodbc0d04f'
                                    },
                                    body: JSON.stringify({
                                        razorpay_order_id: response.razorpay_order_id,
                                        razorpay_payment_id: response.razorpay_payment_id,
                                        razorpay_signature: response.razorpay_signature,
                                        plan_id: planId
                                    })
                                })
                                .then(res => res.json())
                                .then(result => {

                                    if(result.status){
                                        alert("Payment Successful 🎉");
                                        location.reload();
                                    } else {
                                        alert("Payment verification failed");
                                    }

                                });

                            },

                            "theme": {
                                "color": "#3399cc"
                            }
                        };

                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    }

                });

            });
        });
    </script> --}}

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.querySelectorAll('.pay-btn').forEach(button => {

            button.addEventListener('click', function () {

                let planId = this.dataset.planId;

                fetch('/api/create-order', {

                    method: 'POST',

                    headers: {

                        'Content-Type': 'application/json',

                        'Accept': 'application/json',

                        'X-CSRF-TOKEN':
                        document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content
                    },

                    body: JSON.stringify({

                        plan_id: planId
                    })
                })

                .then(res => res.json())

                .then(data => {

                    if (data.status) {

                        let options = {

                            key: data.key,

                            amount: data.amount * 100,

                            currency: "INR",

                            name:
                                "{{ auth()->user()->name }}",

                            description: "Plan Purchase",

                            order_id: data.order_id,

                            handler: function (response)
                            {
                                fetch('/api/verify-payment', {

                                    method: 'POST',

                                    headers: {

                                        'Content-Type':
                                            'application/json',

                                        'Accept':
                                            'application/json',

                                        'X-CSRF-TOKEN':
                                        document.querySelector(
                                            'meta[name="csrf-token"]'
                                        ).content
                                    },

                                    body: JSON.stringify({

                                        razorpay_order_id:
                                        response.razorpay_order_id,

                                        razorpay_payment_id:
                                        response.razorpay_payment_id,

                                        razorpay_signature:
                                        response.razorpay_signature,

                                        plan_id: planId
                                    })
                                })

                                .then(res => res.json())

                                .then(result => {

                                    if (result.status) {

                                        alert(
                                            "Payment Successful 🎉"
                                        );

                                        location.reload();

                                    } else {

                                        alert(result.message);
                                    }
                                });
                            },

                            theme: {
                                color: "#3399cc"
                            }
                        };

                        let rzp1 =
                            new Razorpay(options);

                        rzp1.open();
                    }
                });
            });
        });
    </script>

@endsection
