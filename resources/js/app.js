// Portal nav: toggle the mobile drawer on customer/tester/practitioner layouts.
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-portal-burger]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var nav = btn.closest('.portal-nav');
            if (nav) {
                nav.classList.toggle('portal-open');
            }
        });
    });
});
