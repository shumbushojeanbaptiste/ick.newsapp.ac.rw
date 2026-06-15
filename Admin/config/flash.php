<?php
/**
 * Renders any queued flash messages as SweetAlert2 toasts.
 * Include this once near the end of a page (after the SweetAlert script is loaded).
 * Requires assets/libs/sweetalert2/sweetalert2.min.js to be present.
 */
$__flashes = get_flashes();
if ($__flashes):
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swal === 'undefined') { return; }
    const toast = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false,
        timer: 3500, timerProgressBar: true
    });
    const messages = <?= json_encode($__flashes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    messages.forEach(function (m) {
        toast.fire({ icon: m.type === 'danger' ? 'error' : m.type, title: m.message });
    });
});
</script>
<?php endif; ?>
