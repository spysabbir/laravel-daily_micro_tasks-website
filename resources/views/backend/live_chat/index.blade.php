@extends('layouts.template_master')

@section('title', 'Live Chat')

@section('content')
<div class="row chat-wrapper">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row position-relative">
                    <div class="col-lg-4 chat-aside border-end-lg">
                        <div class="aside-content">
                            <div class="aside-header">
                                <div class="d-flex justify-content-between align-items-center pb-2 mb-2">
                                    <div class="d-flex align-items-center">
                                        <figure class="me-2 mb-0">
                                            <img src="{{ asset('uploads/profile_photo') }}/{{ Auth::user()->profile_photo }}" class="img-sm rounded-circle" alt="profile">
                                            <div class="status online"></div>
                                        </figure>
                                        <div>
                                            <h6>{{ Auth::user()->name }}</h6>
                                            <p class="text-muted tx-13">Software Developer</p>
                                        </div>
                                    </div>
                                </div>
                                <form class="search-form">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                        <i data-feather="search" class="cursor-pointer"></i>
                                        </span>
                                        <input type="text" class="form-control" id="searchForm" placeholder="Search here...">
                                    </div>
                                </form>
                            </div>
                            <div class="aside-body">
                                <ul class="nav nav-tabs nav-fill mt-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="chats-tab" data-bs-toggle="tab" data-bs-target="#chats" role="tab" aria-controls="chats" aria-selected="true">
                                        <div class="d-flex flex-row flex-lg-column flex-xl-row align-items-center justify-content-center">
                                            <i data-feather="message-square" class="icon-sm me-sm-2 me-lg-0 me-xl-2 mb-md-1 mb-xl-0"></i>
                                            <p class="d-none d-sm-block">Chats</p>
                                        </div>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="contacts-tab" data-bs-toggle="tab" data-bs-target="#contacts" role="tab" aria-controls="contacts" aria-selected="false">
                                        <div class="d-flex flex-row flex-lg-column flex-xl-row align-items-center justify-content-center">
                                            <i data-feather="users" class="icon-sm me-sm-2 me-lg-0 me-xl-2 mb-md-1 mb-xl-0"></i>
                                            <p class="d-none d-sm-block">Contacts</p>
                                        </div>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content mt-3">
                                    <div class="tab-pane fade show active" id="chats" role="tabpanel" aria-labelledby="chats-tab">
                                        <div>
                                            <p class="text-muted mb-1">Recent chats</p>
                                            <ul class="list-unstyled chat-list px-1">
                                                <li class="chat-item pe-1">
                                                    <a href="javascript:;" class="d-flex align-items-center">
                                                        <figure class="mb-0 me-2">
                                                            <img src="https://via.placeholder.com/37x37" class="img-xs rounded-circle" alt="user">
                                                            <div class="status online"></div>
                                                        </figure>
                                                        <div class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                            <div>
                                                                <p class="text-body fw-bolder">John Doe</p>
                                                                <p class="text-muted tx-13">Hi, How are you?</p>
                                                            </div>
                                                            <div class="d-flex flex-column align-items-end">
                                                                <p class="text-muted tx-13 mb-1">4:32 PM</p>
                                                                <div class="badge rounded-pill bg-primary ms-auto">5</div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li class="chat-item pe-1">
                                                    <a href="javascript:;" class="d-flex align-items-center">
                                                        <figure class="mb-0 me-2">
                                                            <img src="https://via.placeholder.com/37x37" class="img-xs rounded-circle" alt="user">
                                                            <div class="status offline"></div>
                                                        </figure>
                                                        <div class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                            <div>
                                                                <p class="text-body fw-bolder">Carl Henson</p>
                                                                <div class="d-flex align-items-center">
                                                                    <i data-feather="image" class="text-muted icon-md mb-2px"></i>
                                                                    <p class="text-muted ms-1">Photo</p>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex flex-column align-items-end">
                                                                <p class="text-muted tx-13 mb-1">05:24 PM</p>
                                                                <div class="badge rounded-pill bg-danger ms-auto">3</div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="contacts" role="tabpanel" aria-labelledby="contacts-tab">
                                        <p class="text-muted mb-1">Contacts</p>
                                        <ul class="list-unstyled chat-list px-1">
                                            <li class="chat-item pe-1">
                                                <a href="javascript:;" class="d-flex align-items-center">
                                                    <figure class="mb-0 me-2">
                                                        <img src="https://via.placeholder.com/37x37" class="img-xs rounded-circle" alt="user">
                                                        <div class="status offline"></div>
                                                    </figure>
                                                    <div class="d-flex align-items-center justify-content-between flex-grow-1 border-bottom">
                                                        <div>
                                                            <p class="text-body">Amiah Burton</p>
                                                            <div class="d-flex align-items-center">
                                                                <p class="text-muted tx-13">Front-end Developer</p>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-end text-body">
                                                            <i data-feather="message-square" class="icon-md text-primary me-2"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="chat-item pe-1">
                                                <a href="javascript:;" class="d-flex align-items-center">
                                                    <figure class="mb-0 me-2">
                                                        <img src="https://via.placeholder.com/37x37" class="img-xs rounded-circle" alt="user">
                                                        <div class="status online"></div>
                                                    </figure>
                                                    <div class="d-flex align-items-center justify-content-between flex-grow-1 border-bottom">
                                                        <div>
                                                            <p class="text-body">John Doe</p>
                                                            <div class="d-flex align-items-center">
                                                                <p class="text-muted tx-13">Back-end Developer</p>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-end text-body">
                                                            <i data-feather="message-square" class="icon-md text-primary me-2"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 chat-content">
                        <div class="chat-header border-bottom pb-2">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i data-feather="corner-up-left" id="backToChatList" class="icon-lg me-2 ms-n2 text-muted d-lg-none"></i>
                                    <figure class="mb-0 me-2">
                                        <img src="https://via.placeholder.com/43x43" class="img-sm rounded-circle" alt="image">
                                        <div class="status online"></div>
                                        <div class="status online"></div>
                                    </figure>
                                    <div>
                                        <p>Mariana Zenha</p>
                                        <p class="text-muted tx-13">Front-end Developer</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center me-n1">
                                    <a href="#" class="d-none d-sm-block">
                                        <i data-feather="user-plus" class="icon-lg text-muted" data-bs-toggle="tooltip" title="Add to contacts"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="chat-body">
                            <ul class="messages">
                                <li class="message-item friend">
                                    <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle" alt="avatar">
                                    <div class="content">
                                        <div class="message">
                                            <div class="bubble">
                                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                            </div>
                                            <span>8:12 PM</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="message-item me">
                                    <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle" alt="avatar">
                                    <div class="content">
                                        <div class="message">
                                            <div class="bubble">
                                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry printing and typesetting industry.</p>
                                            </div>
                                        </div>
                                        <div class="message">
                                            <div class="bubble">
                                                <p>Lorem Ipsum.</p>
                                            </div>
                                            <span>8:13 PM</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="message-item friend">
                                    <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle" alt="avatar">
                                    <div class="content">
                                        <div class="message">
                                            <div class="bubble">
                                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                            </div>
                                            <span>8:15 PM</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="message-item me">
                                    <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle" alt="avatar">
                                    <div class="content">
                                        <div class="message">
                                            <div class="bubble">
                                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry printing and typesetting industry.</p>
                                            </div>
                                            <span>8:15 PM</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="chat-footer d-flex">
                            <div class="d-none d-md-block">
                                <button type="button" class="btn border btn-icon rounded-circle me-2" data-bs-toggle="tooltip" title="Attatch files">
                                    <i data-feather="paperclip" class="text-muted"></i>
                                </button>
                            </div>
                            <form class="search-form flex-grow-1 me-2">
                                <div class="input-group">
                                    <input type="text" class="form-control rounded-pill" id="chatForm" placeholder="Type a message">
                                </div>
                            </form>
                            <div>
                                <button type="button" class="btn btn-primary btn-icon rounded-circle">
                                    <i data-feather="send"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection