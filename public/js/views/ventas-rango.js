/*========================================
    SE CARGA POR DEFECTO RANGO 1 SEMANA
========================================*/
$(document).ready(function(){
  var start = moment().subtract(6, 'days');
  var end = moment();
  $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
  console.log('mensj');
});
/*========================================
			RANGO DE FECHAS
========================================*/
$('#daterange-btn').daterangepicker(
    {
      ranges   : {
        'Hoy'       : [moment(), moment()],
        'Ayer'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Últimos 7 días' : [moment().subtract(6, 'days'), moment()],
        'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
        'Este mes'  : [moment().startOf('month'), moment().endOf('month')],
        'Último mes'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      startDate: moment(),
      endDate  : moment()
    },
    function (start, end) {
      $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

      var fechaInicial = start.format('YYYY-MM-DD');

      var fechaFinal = end.format('YYYY-MM-DD');

      var capturarRango = $("#daterange-btn span").html();

      $("#fechaInicial").val(fechaInicial);
      $("#fechaFinal").val(fechaFinal);

    }

);
