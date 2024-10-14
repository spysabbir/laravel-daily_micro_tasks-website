<div class="row d-flex justify-content-center">
    <div class="col-lg-8">
        <div class="mb-3">
            <h4>Proof Answer:</h4>
            <div>
                {{ $proofTask->proof_answer }}
            </div>
        </div>
        <div class="mb-3">
            <h4>Proof Image:</h4>
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
                                <img src="{{ asset('uploads/task_proof_photo') }}/{{ $photo }}" class="d-block w-100" alt="Proof Task Photo">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5 class="mb-2"><strong class="badge bg-dark">Proof Task Photo {{ $loop->iteration }}</strong></h5>
                                    <strong><a href="{{ asset('uploads/task_proof_photo') }}/{{ $photo }}" target="_blank">View Full Image</a></strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" data-bs-target="#carouselExampleCaptions" role="button" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </a>
                    <a class="carousel-control-next" data-bs-target="#carouselExampleCaptions" role="button" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
