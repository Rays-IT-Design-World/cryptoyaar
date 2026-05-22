@extends('frontend.layouts.main')
@section('content')

<div class="ms-breadcrumb-wrapper text-center ">
	<div class="container">
		<div class="row">
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
				<div class="ms-breadcrumb-container">
					<h1>&nbsp;testing</h1> 
					<ul> 
						<li><a href="/">Home</a></li><li>&nbsp;testing</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div> 

<?php 
  $videos = DB::table('videos')->latest()->get();

?>
@foreach ($videos as $item)
    <video class="video-player" 
           data-id="{{ $item->id }}" 
           width="600" controls>
        <source src="{{ asset('storage/'.$item->video_path) }}" type="video/mp4">
    </video>
@endforeach

{{--  --}}


<script>

let sessionIds = {};

document.querySelectorAll('.video-player').forEach(video => {

    let videoId = video.dataset.id;

    sessionIds[videoId] =
        Date.now() + "_" + videoId;

    video.addEventListener('pause', function () {
        sendWatchTime(video);
    });

    video.addEventListener('ended', function () {
        sendWatchTime(video);
    });

});

async function sendWatchTime(video) {

    let videoId = video.dataset.id;

    let watchTime =
        Math.floor(video.currentTime);

    console.log("Watch Time:", watchTime);

    if (watchTime < 30) {
        return;
    }

    try {

        let response = await fetch('/api/watch_time', {

            method: 'POST',

            headers: {

                'Content-Type': 'application/json',

                'X-CSRF-TOKEN':
                    document.querySelector('meta[name="csrf-token"]').content,

                'Accept': 'application/json'
            },

            body: JSON.stringify({

                video_id: videoId,

                watch_time: watchTime,

                session_id: sessionIds[videoId],

                device_id: 'test_device',

                traffic_source: 'organic'
            })
        });

        let data = await response.json();

        console.log(data);

    } catch (error) {

        console.log(error);
    }
}
</script>
@endsection
