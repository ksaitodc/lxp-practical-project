<footer class="footer-section footer">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">

                <ul class="footer-menu">
                    <li> <a href="{{ route('accounts', ['tab' => 'profile']) }}">Your account</a>  </li>
                    <li> <a href="">Contact us</a>  </li>
                    <li> <a href="">Terms of service</a>  </li>
                </ul>

                <p>&copy; <a href="{{ config('app.url') }}">{{ config('app.name') }}</a> | All Rights Reserved</p>

            </div>
        </div>
    </div>
</footer>