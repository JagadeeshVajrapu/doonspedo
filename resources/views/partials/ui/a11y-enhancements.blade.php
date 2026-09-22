{{-- Presentation-only accessibility helpers. Does not alter form/API behavior. --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll(
        'a.nav-link.active, a.nav-link.is-active, a.is-active,' +
        '.rider-nav-item.is-active, .driver-nav-item.is-active'
    ).forEach(function (el) {
        if (!el.hasAttribute('aria-current')) {
            el.setAttribute('aria-current', 'page');
        }
    });

    document.querySelectorAll('table thead th:not([scope])').forEach(function (th) {
        th.setAttribute('scope', 'col');
    });

    document.querySelectorAll('.is-invalid').forEach(function (el) {
        el.setAttribute('aria-invalid', 'true');
        var feedback = el.parentElement && el.parentElement.querySelector('.invalid-feedback');
        if (feedback) {
            if (!feedback.id) {
                feedback.id = (el.id || ('field-' + Math.random().toString(36).slice(2, 8))) + '-error';
            }
            el.setAttribute('aria-describedby', feedback.id);
        }
    });

    document.querySelectorAll('button[title], a.btn[title]').forEach(function (el) {
        if (el.getAttribute('aria-label')) return;
        var text = (el.textContent || '').replace(/\s+/g, ' ').trim();
        if (text.length > 0) return;
        el.setAttribute('aria-label', el.getAttribute('title'));
    });

    document.querySelectorAll('i.bi').forEach(function (icon) {
        var parent = icon.parentElement;
        if (!parent) return;
        if (parent.getAttribute('aria-hidden') === 'true') return;
        if (parent.matches('button, a, [role="button"]')) {
            if (!parent.getAttribute('aria-label') && !(parent.textContent || '').replace(/\s+/g, '').replace(icon.textContent || '', '').trim()) {
                /* leave naming to aria-label / title handlers */
            }
            if (!icon.hasAttribute('aria-hidden')) {
                icon.setAttribute('aria-hidden', 'true');
            }
        }
    });
});
</script>
