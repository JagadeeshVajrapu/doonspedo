<footer class="admin-footer mt-auto">
    <p class="mb-0">&copy; {{ date('Y') }} <span class="text-brand fw-bold">{{ $sys_settings['app_name'] ?? 'Doonspedo' }}</span></p>
    @include('partials.ui.developer-credit')
</footer>
