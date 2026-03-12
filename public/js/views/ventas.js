/*============================================
        EVENTOS DEL SOFTWARE
============================================*/
/*========================================
	LISTAR TODOS LOS PRODUCTOS AGREGADOS
========================================*/
var numProducto = 0;
$('.btnAgregarProducto').on('click',function(){
    numProducto++;
    $.ajax({
        url: 'traer/productos',
        method: 'POST',
        data:{
            _token: $('input[name="_token"]').val()
        }
    }).done(function(respuesta){
        var arreglo = JSON.parse(respuesta);
        $('.nuevoProducto').append(
            '<div class="row" style="padding:5px 15px">'+
                '<div class="col-10 col-md-5">'+
                    '<div class="form-group">'+
                        '<input type="hidden" class="idSeleccionado" name="idSeleccionado">'+
                        '<label for="nuevaDescripcion">Seleccione un producto</label>'+
                        '<select class="form-control select2 nuevaDescripcion" id="producto'+numProducto+'" idProducto name="nuevaDescripcion" required>'+
                            '<option>Seleccione el producto</option>'+
                        '</select>'+
                    '</div>'+
                '</div>'+
               '<div class="col-12 col-md-3">'+
                    '<div class="form-group ingresoCantidad">'+
                        '<label for="nuevaCantidad">Ingrese la cantidad a vender</label>'+
                        '<input type="number" class="form-control nuevaCantidad" name="nuevaCantidad" min="1" value="1" stock nuevoStock required>'+
                    '</div>'+
               ' </div>'+
                '<div class="col-12 col-md-4 ingresoPrecio">'+
                    '<label for="nuevoPrecio">Ingrese el precio del producto</label>'+
                    '<div class="input-group">'+
                        '<input type="number" class="form-control nuevoPrecio" name="nuevoPrecio" min="50"required>'+
                        '<button type="button" class="btn btn-danger quitarProducto" idProducto><i class="fa fa-times"></i></button>'+
                    '</div>'+
               ' </div>'+
            '</div>'
        );
        //AGREGAMOS LOS PRODUCTOS AL SELECT
        arreglo.forEach(funcionForEach);
        function funcionForEach(item, index){
            if(item.cantidad != 0){
                $("#producto"+numProducto).append(
                    '<option idProducto="'+item.id+'" value="'+item.nombre+'">'+item.nombre+'</option>'
                )
            }
        }
        // SUMAR EL TOTAL DE LOS PRECIOS
        sumarTotalPrecios()
        //APLICAMOS LOS ESTILOS PARA EL SELECT
        $('.select2').select2();
        // AGRUPAR PRODUCTOS EN FORMATO JSON
        listarProductos()
    });
});
/*==========================================================
				SELECCIONAR PRODUCTO
==========================================================*/
$('#formVenta').on('change', "select.nuevaDescripcion", function(){

	var nuevaDescripcion = $(this).parent().parent().parent().children().children().children(".nuevaDescripcion");

	var idProductoSeleccionado = $('option:selected', $(this).parent().parent().parent().children().children().children(".nuevaDescripcion")).attr('idProducto');

	var nuevaCantidad = $(this).parent().parent().parent().children().children(".ingresoCantidad").children(".nuevaCantidad");

    var inputIdProducto = $(this).parent().parent().parent().children().children().children(".idSeleccionado");
    //Guardamos el id del producto seleccionado
    inputIdProducto.val(idProductoSeleccionado);

    $.ajax({
		type: 'POST',
        url: 'traer/seleccionado',
        dataType: "JSON",
        data: {
            id: idProductoSeleccionado,
            _token: $('input[name="_token"]').val()
        }
	}).done(function(respuesta){
        //AGREGAMOS LOS ATRIBUTOS DEL PRODUCTO
        respuesta.forEach(funcionForEach);
        function funcionForEach(item, index){
            $(nuevaDescripcion).attr("idProducto", item['id']);
            $(nuevaCantidad).attr("stock", item['stock']);
            $(nuevaCantidad).attr("nuevoStock", Number(item['stock'])-1);
        }
        // AGRUPAR PRODUCTOS EN FORMATO JSON
        listarProductos()
    });

});
/*==========================================================
			QUITAMOS EL PRODUCTO EN CUESTION
==========================================================*/
var idQuitarProducto = [];
$("#formVenta").on("click", "button.quitarProducto", function(){
    //Eliminamos el elemento, hasta el div Nuevoproducto
	$(this).parent().parent().parent().remove();
    //Obtenemos el id del producto
	var idProducto = $(this).attr("idProducto");
	/*========================================================
	ALMACENAR EN EL LOCALSTORAGE EL ID DEL PRODUCTO A QUITAR
	========================================================*/
	if(localStorage.getItem("quitarProducto") == null){
		idQuitarProducto = [];
	}else{
		idQuitarProducto.concat(localStorage.getItem("quitarProducto"))
	}

	idQuitarProducto.push({"idProducto":idProducto});
	localStorage.setItem("quitarProducto", JSON.stringify(idQuitarProducto));
    /*========================================================
	CONTAMOS CUANTOS ELEMENTOS AGREGO
	========================================================*/
	if ($(".nuevoProducto").children().length == 0) {
		$("#nuevoTotalVenta").val(0);
	}else{
		// SUMAR EL TOTAL DE LOS PRECIOS
		sumarTotalPrecios()
		// AGRUPAR PRODUCTOS EN FORMATO JSON
		listarProductos()
	}
});
/*======================================================================
			MODIFICAR LA CANTIDAD Y VALIDAR STOCK
======================================================================*/
$("#formVenta").on("change", "input.nuevoPrecio", function(){
	// SUMAR EL TOTAL DE LOS PRECIOS
	sumarTotalPrecios()
	// AGRUPAR PRODUCTOS EN FORMATO JSON
	listarProductos()
});
/*======================================================================
			MODIFICAR LA CANTIDAD Y VALIDAR STOCK
======================================================================*/
$("#formVenta").on("change", "input.nuevaCantidad", function(){
    //Tremos el nuevo stock del producto
	var nuevoStock = Number($(this).attr("stock")) - $(this).val();
	$(this).attr("nuevoStock", nuevoStock);
    /*====================================================================
        SI LA CANTIDAD ES SUPERIOR AL STOCK REGRESAR VALORES INICIALES
    ====================================================================*/
	if(Number($(this).val()) > Number($(this).attr("stock"))){
		$(this).val(1);
		swal.fire({
	      title: "¡La cantidad que ingresó, supera a la registrada en el sistema!",
	      text: "¡Sólo hay "+$(this).attr("stock")+" unidades!",
	      icon: 'warning',
	    });
	}
	// SUMAR EL TOTAL DE LOS PRECIOS
	sumarTotalPrecios()
	// AGRUPAR PRODUCTOS EN FORMATO JSON
	listarProductos()
});
/*============================================
        FUNCIONES DEL SOFTWARE
============================================*/
/*========================================
		SUMAR TODOS LOS PRECIOS
========================================*/
function sumarTotalPrecios(){

	var precioItem = $(".nuevoPrecio");
	var cantidadItem = $(".nuevaCantidad");

    var arraySumaPrecios = [];
    var totalSuma = 0;

    for (var i = 0; i < cantidadItem.length; i++) {
        arraySumaPrecios.push(
            Number($(precioItem[i]).val()) * Number($(cantidadItem[i]).val())
        );
    }

    for (let i = 0; i < arraySumaPrecios.length; i++) {
        totalSuma += arraySumaPrecios[i];
    }

    $("#nuevoTotalVenta").val(totalSuma);

}
/*========================================
		LISTAR TODOS LOS PRODUCTOS
========================================*/

function listarProductos(){

	var listaProductos = [];

	var descripcion = $(".nuevaDescripcion");

	var cantidad 	= $(".nuevaCantidad");

	var precio 		= $(".nuevoPrecio");

	for (var i = 0; i < descripcion.length; i++) {

		listaProductos.push({ "id" : $(descripcion[i]).attr("idProducto"),
							  "descripcion" : $(descripcion[i]).val(),
							  "cantidad" : $(cantidad[i]).val(),
							  "stock" : $(cantidad[i]).attr("nuevoStock"),
                              "precio" : $(precio[i]).val()
                            })

	}

	$("#listaProductos").val(JSON.stringify(listaProductos));

}
