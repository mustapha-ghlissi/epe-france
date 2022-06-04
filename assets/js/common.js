
import '@fortawesome/fontawesome-free/js/all.min';
import 'bootstrap';
import 'datatables.net-bs4/js/dataTables.bootstrap4.min';
import 'datatables.net-responsive-bs4/js/responsive.bootstrap4.min';
import 'datatables.net-buttons-bs4/js/buttons.bootstrap4.min';
import 'select2';
import 'select2/dist/js/i18n/fr';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'select2/dist/css/select2.min.css';
import 'datatables.net-bs4/css/dataTables.bootstrap4.min.css';
import 'datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css';
import 'datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css';


$(document).ready(function() {
    $("a#btn-back-top").click(function(e) {
        e.preventDefault();
        $("html, body").animate({ scrollTop: 0 }, "slow");
        return false;
    });
})

window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
    $("#btn-back-top").css('display', 'block');
  } else {
    $("#btn-back-top").css('display', 'none');
  }
}