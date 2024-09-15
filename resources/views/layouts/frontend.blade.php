<!doctype html>
<html lang="zxx">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{ asset('frontend') }}/css/bootstrap.min.css">
        <!-- Owl Carousel CSS -->
        <link rel="stylesheet" href="{{ asset('frontend') }}/css/owl.carousel.min.css">
        <!-- Owl Carousel Theme Default CSS -->
        <link rel="stylesheet" href="{{ asset('frontend') }}/css/owl.theme.default.min.css">
        <!-- Box Icon CSS-->
        <link rel="stylesheet" href="{{ asset('frontend') }}/css/boxicon.min.css">
        <!-- Flaticon CSS-->
        <link rel="stylesheet" href="{{ asset('frontend') }}/fonts/flaticon/flaticon.css">
        <!-- Nice Select CSS -->
        <link rel="stylesheet" href="{{ asset('frontend') }}/css/nice-select.css">
        <!-- Meanmenu CSS -->
        <link rel="stylesheet" href="{{ asset('frontend') }}/css/meanmenu.css">
        <!-- Style CSS -->
        <link rel="stylesheet" href="{{ asset('frontend') }}/css/style.css">
		<!-- Dark CSS -->
        <link rel="stylesheet" href="{{ asset('frontend') }}/css/dark.css">
        <!-- Responsive CSS -->
        <link rel="stylesheet" href="{{ asset('frontend') }}/css/responsive.css">
        <!-- Title CSS -->
        <title>{{ config('app.name') }} - @yield('title')</title>
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('frontend') }}/img/favicon.png">
    </head>

    <body>
		<!-- Pre-loader Start -->
		<div class="loader-content">
            <div class="d-table">
                <div class="d-table-cell">
					<div class="sk-circle">
						<div class="sk-circle1 sk-child"></div>
						<div class="sk-circle2 sk-child"></div>
						<div class="sk-circle3 sk-child"></div>
						<div class="sk-circle4 sk-child"></div>
						<div class="sk-circle5 sk-child"></div>
						<div class="sk-circle6 sk-child"></div>
						<div class="sk-circle7 sk-child"></div>
						<div class="sk-circle8 sk-child"></div>
						<div class="sk-circle9 sk-child"></div>
						<div class="sk-circle10 sk-child"></div>
						<div class="sk-circle11 sk-child"></div>
						<div class="sk-circle12 sk-child"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- Pre-loader End -->

        <!-- Navbar Area Start -->
        <div class="navbar-area">
            <!-- Menu For Mobile Device -->
            <div class="mobile-nav">
                <a href="{{ route('index') }}" class="logo">
                    <img src="{{ asset('frontend') }}/img/logo.png" alt="logo">
                </a>
            </div>

            <!-- Menu For Desktop Device -->
            <div class="main-nav">
                <div class="container">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <a class="navbar-brand" href="{{ route('index') }}">
                            <img src="{{ asset('frontend') }}/img/logo.png" alt="logo">
                        </a>
                        <div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">
                            <ul class="navbar-nav m-auto">
                                <li class="nav-item">
                                    <a href="{{ route('index') }}" class="nav-link">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('about.us') }}" class="nav-link">About</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('contact.us') }}" class="nav-link">Contact Us</a>
                                </li>
                            </ul>

                            <div class="other-option">
                                @auth()
                                    <a href="{{ route('dashboard') }}" class="signin-btn">Dashboard</a>
                                @else
                                <a href="{{ route('register') }}" class="signup-btn">Register</a>
                                <a href="{{ route('login') }}" class="signin-btn">Login</a>
                                @endauth
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
        <!-- Navbar Area End -->

        @yield('content')

        <!-- Subscribe Section Start -->
        <section class="subscribe-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="section-title">
                            <h2>Get New Job Notifications</h2>
                            <p>Subscribe & get all related jobs notification</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <form class="newsletter-form" data-toggle="validator">
                            <input type="email" class="form-control" placeholder="Enter your email" name="EMAIL" required autocomplete="off">

                            <button class="default-btn sub-btn" type="submit">
                                Subscribe
                            </button>

                            <div id="validator-newsletter" class="form-result"></div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- Subscribe Section End -->

        <!-- Footer Section Start -->
		<footer class="footer-area pt-100 pb-70">
			<div class="container">
				<div class="row">
					<div class="col-lg-3 col-sm-6">
						<div class="footer-widget">
							<div class="footer-logo">
								<a href="{{ route('index') }}">
									<img src="{{ asset('frontend') }}/img/logo.png" alt="logo">
								</a>
							</div>

							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed eiusmod tempor incididunt ut labore et dolore magna. Sed eiusmod tempor incididunt ut.</p>

							<div class="footer-social">
								<a href="{{ get_site_settings('site_facebook_url') }}" target="_blank"><i class='bx bxl-facebook'></i></a>
								<a href="{{ get_site_settings('site_twitter_url') }}" target="_blank"><i class='bx bxl-twitter'></i></a>
								<a href="{{ get_site_settings('site_instagram_url') }}" target="_blank"><i class='bx bxl-instagram'></i></a>
								<a href="{{ get_site_settings('site_linkedin_url') }}" target="_blank"><i class='bx bxl-linkedin'></i></a>
                                <a href="{{ get_site_settings('site_pinterest_url') }}" target="_blank"><i class='bx bxl-pinterest'></i></a>
                                <a href="{{ get_site_settings('site_youtube_url') }}" target="_blank"><i class='bx bxl-youtube'></i></a>
							</div>
						</div>
					</div>

					<div class="col-lg-3 col-sm-6">
						<div class="footer-widget pl-60">
							<h3>For Candidate</h3>
							<ul>
								<li>
									<a href="javascript:void(0)">
										<i class='bx bx-chevrons-right bx-tada'></i>
										***
									</a>
								</li>
                                <li>
									<a href="javascript:void(0)">
										<i class='bx bx-chevrons-right bx-tada'></i>
										***
									</a>
								</li>
                                <li>
									<a href="javascript:void(0)">
										<i class='bx bx-chevrons-right bx-tada'></i>
										***
									</a>
								</li>
                                <li>
									<a href="javascript:void(0)">
										<i class='bx bx-chevrons-right bx-tada'></i>
										***
									</a>
								</li>
								<li>
									<a href="javascript:void(0)">
										<i class='bx bx-chevrons-right bx-tada'></i>
										***
									</a>
								</li>
							</ul>
						</div>
					</div>

					<div class="col-lg-3 col-sm-6">
						<div class="footer-widget pl-60">
							<h3>Quick Links</h3>
							<ul>
								<li>
									<a href="{{ route('how.it.works') }}">
										<i class='bx bx-chevrons-right bx-tada'></i>
										How It Works
									</a>
								</li>
								<li>
									<a href="{{ route('referral.program') }}">
										<i class='bx bx-chevrons-right bx-tada'></i>
										Referral Program
									</a>
								</li>
								<li>
									<a href="{{ route('faq') }}">
										<i class='bx bx-chevrons-right bx-tada'></i>
										FAQ
									</a>
								</li>
								<li>
									<a href="{{ route('privacy.policy') }}">
										<i class='bx bx-chevrons-right bx-tada'></i>
                                        Privacy Policy
									</a>
								</li>
								<li>
									<a href="{{ route('terms.and.conditions') }}">
										<i class='bx bx-chevrons-right bx-tada'></i>
										Terms And Conditions
									</a>
								</li>
							</ul>
						</div>
					</div>

					<div class="col-lg-3 col-sm-6">
						<div class="footer-widget footer-info">
							<h3>Information</h3>
							<ul>
								<li>
									<span>
										<i class='bx bxs-phone'></i>
										Phone:
									</span>
									<a href="tel:{{ get_site_settings('site_support_phone') }}">
										{{ get_site_settings('site_support_phone') }}
									</a>
								</li>

								<li>
									<span>
										<i class='bx bxs-envelope'></i>
										Email:
									</span>
									<a href="mailto:{{ get_site_settings('site_support_email') }}">
										{{ get_site_settings('site_support_email') }}
									</a>
								</li>

								<li>
									<span>
										<i class='bx bx-location-plus'></i>
										Address:
									</span>
									{{ get_site_settings('site_address') }}
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</footer>
        <div class="copyright-text text-center">
            <p>© {{ date('Y') }} <a href="{{ route('index') }}">{{ config('app.name') }}</a>. All Rights Reserved.</p>
        </div>
        <!-- Footer Section End -->

        <!-- Back To Top Start -->
		<div class="top-btn">
			<i class='bx bx-chevrons-up bx-fade-up'></i>
		</div>
		<!-- Back To Top End -->

		<!-- jQuery first, then Bootstrap JS -->
		<script src="{{ asset('frontend') }}/js/jquery.min.js"></script>
		<script src="{{ asset('frontend') }}/js/bootstrap.bundle.min.js"></script>
		<!-- Owl Carousel JS -->
		<script src="{{ asset('frontend') }}/js/owl.carousel.min.js"></script>
		<!-- Nice Select JS -->
		<script src="{{ asset('frontend') }}/js/jquery.nice-select.min.js"></script>
		<!-- Magnific Popup JS -->
		<script src="{{ asset('frontend') }}/js/jquery.magnific-popup.min.js"></script>
		<!-- Subscriber Form JS -->
		<script src="{{ asset('frontend') }}/js/jquery.ajaxchimp.min.js"></script>
		<!-- Form Velidation JS -->
		<script src="{{ asset('frontend') }}/js/form-validator.min.js"></script>
		<!-- Contact Form -->
		<script src="{{ asset('frontend') }}/js/contact-form-script.js"></script>
		<!-- Meanmenu JS -->
		<script src="{{ asset('frontend') }}/js/meanmenu.js"></script>
		<!-- Custom JS -->
		<script src="{{ asset('frontend') }}/js/custom.js"></script>
    </body>
</html>