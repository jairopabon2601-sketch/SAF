<script>
var win = navigator.platform.indexOf('Win') > -1;
if (win && document.querySelector('#sidenav-scrollbar')) {
    var options = {
    damping: '0.5'
    }
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
}
</script>

<script src="https://code.jquery.com/jquery-3.7.0.js"
integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM="
crossorigin="anonymous"></script>


<script src="assets/js/soft-ui-dashboard.min.js?v=1.0.7"></script>

<script src="../js/jquery.growl.js"></script>
<script src="js/app_funciones.js?v=20240423-1"></script>
