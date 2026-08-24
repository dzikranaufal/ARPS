{{-- resources/views/partials/public/footer.blade.php --}}
<footer class="bg-dark text-light py-4 mt-5">
    <div class="container">
        <div class="row gy-3">
            <div class="col-md-4">
                <h5 class="fw-bold">ARPS</h5>
                <p class="small text-white-50 mb-0">
                    Academics, Researchers, and Practitioners Society — connecting
                    academics, researchers, and practitioners nationally and internationally.
                </p>
            </div>
            <div class="col-md-4">
                <h6>Quick Links</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('programs.index') }}" class="link-light text-decoration-none">Programs</a>
                    </li>
                    <li><a href="{{ route('organization.index') }}" class="link-light text-decoration-none">Organization</a>
                    </li>
                    <li><a href="{{ route('journals.index') }}" class="link-light text-decoration-none">Journals</a>
                    </li>
                    <li><a href="{{ route('contact.index') }}" class="link-light text-decoration-none">Contact</a>
                    </li>
                    <li><a href="{{ route('about') }}" class="link-light text-decoration-none">About</a></li>
                    <li><a href="{{ route('register') }}" class="link-light text-decoration-none">Register</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6>Contact</h6>
                {{-- STATIC SAMPLE — ganti dengan data organization_profile di fase backend --}}
                <p class="small text-white-50 mb-0">
                    info@arps.org<br>+62 812-3456-7890<br>
                    Universitas Pendidikan Indonesia<br>Bandung, Indonesia
                </p>
            </div>
        </div>
        <hr class="border-secondary">
        <p class="small text-white-50 mb-0">&copy; {{ date('Y') }} ARPS. All rights reserved.</p>
    </div>
</footer>
