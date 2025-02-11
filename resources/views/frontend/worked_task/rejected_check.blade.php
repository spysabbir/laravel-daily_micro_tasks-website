<div class="row">
    <div class="col-lg-8">
        <div class="mb-3">
            <h4 class="mb-2">Proof Answer:</h4>
            <div>
                {{ $proofTask->proof_answer }}
            </div>
        </div>
        <div class="mb-3">
            <h4 class="mb-2">Proof Image:</h4>
            @if (!json_decode($proofTask->proof_photos))
                <div class="alert alert-warning">This task does not require any proof photo.</div>
            @else
            <div class="my-2">
                <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                    <ol class="carousel-indicators">
                        @foreach (json_decode($proofTask->proof_photos) as $photo)
                            <li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        @foreach (json_decode($proofTask->proof_photos) as $photo)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <a href="{{ asset('uploads/task_proof_photo') }}/{{ $photo }}" data-lightbox="gallery" data-title="Proof Task Photo {{ $loop->iteration }}">
                                    <img src="{{ asset('uploads/task_proof_photo') }}/{{ $photo }}" style="max-height: 400px;" class="d-block w-100" alt="Proof Task Photo {{ $loop->iteration }}">
                                </a>
                                <div class="carousel-caption d-none d-md-block">
                                    <h5 class="mb-2"><strong class="badge bg-dark">Proof Task Photo {{ $loop->iteration }}</strong></h5>
                                    <strong><a href="{{ asset('uploads/task_proof_photo') }}/{{ $photo }}" target="_blank">View Full Image</a></strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" data-bs-target="#carouselExampleCaptions" role="button" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-primary" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </a>
                    <a class="carousel-control-next" data-bs-target="#carouselExampleCaptions" role="button" data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-primary" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="col-lg-4">
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Rejected!</h4>
            <p>Proof Task has been rejected.</p>
            <hr>
            <p class="mb-0"><strong>Rejected Reason:</strong> {{ $proofTask->rejected_reason }}</p>
            @if ($proofTask->rejected_reason_photo)
                <strong>Rejected Reason Photo: </strong>
                <a href="{{ asset('uploads/task_proof_rejected_reason_photo') }}/{{ $proofTask->rejected_reason_photo }}" target="_blank">
                    <img src="{{ asset('uploads/task_proof_rejected_reason_photo') }}/{{ $proofTask->rejected_reason_photo }}" class="img-fluid" alt="Rejected Reason Photo">
                </a>
            @endif
            <p><strong>Rejected Date:</strong> {{ date('d M Y h:i A', strtotime($proofTask->rejected_at)) }}</p>
            <p><strong>Rejected By:</strong> {{ $proofTask->rejectedBy->user_type =='Backend' ? 'Admin' : $proofTask->rejectedBy->name }}</p>
        </div>
    </div>
</div>
