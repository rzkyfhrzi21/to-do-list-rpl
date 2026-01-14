<!-- ================= CORE ================= -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
    crossorigin="anonymous"></script>

<!-- TEMPLATE CORE (WAJIB LOKAL) -->
<script src="assets/static/js/components/dark.js"></script>
<script src="assets/static/js/pages/horizontal-layout.js"></script>
<script src="assets/compiled/js/app.js"></script>
<script src="assets/static/js/pages/ui-todolist.js"></script>

<!-- ================= UI LIBRARIES (CDN) ================= -->

<!-- Dragula -->
<script src="https://cdn.jsdelivr.net/npm/dragula@3.7.3/dist/dragula.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include 'sweetalert.php'; ?>

<!-- Choices -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<!-- Parsley -->
<script src="https://cdn.jsdelivr.net/npm/parsleyjs@2.9.2/dist/parsley.min.js"></script>

<!-- Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- Toastify -->
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<!-- FilePond -->
<script src="https://cdn.jsdelivr.net/npm/filepond/dist/filepond.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>

<!-- Init FilePond (Template) -->
<script src="assets/static/js/pages/filepond.js"></script>

<!-- ================= DATATABLES (CDN) ================= -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    $(function() {
        $("#example1").DataTable({
            responsive: true,
            lengthChange: true,
            autoWidth: false
        });

        $("#example2").DataTable({
            paging: true,
            searching: true,
            ordering: true,
            responsive: true
        });
    });
</script>

<!-- ================= SCRIPT KECIL ================= -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const checkbox = document.getElementById("iaggree");
        const btn = document.getElementById("btn-delete-account");

        if (checkbox && btn) {
            checkbox.addEventListener("change", () => {
                checkbox.checked ?
                    btn.removeAttribute("disabled") :
                    btn.setAttribute("disabled", true);
            });
        }
    });
</script>