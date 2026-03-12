/*============================================
        EVENTOS DEL SOFTWARE
============================================*/
/*========================================
	MOSTRAR BOTON PARA AGREGAR PRODUCTOS
========================================*/
$('#formCompra').on('change','select.tipo_cantidad', function(){
    if($('.tipo_cantidad').val()!='')$('.btnAgregarProducto').removeAttr('hidden');
    // SUMAR EL TOTAL DE LOS PRECIOS
	sumarTotalPrecios()
    // AGRUPAR PRODUCTOS EN FORMATO JSON
	listarProductos()
});
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
            '<div class="row todo" style="padding:5px 15px">'+
                '<div class="col-10 col-md-4">'+
                    '<div class="form-group">'+
                        '<input type="hidden" class="idSeleccionado" name="idSeleccionado">'+
                        '<label for="nuevaDescripcion">Seleccione un producto</label>'+
                        '<select class="form-control select2 nuevaDescripcion" id="producto'+numProducto+'" idProducto name="nuevaDescripcion" required>'+
                            '<option>Seleccione el producto</option>'+
                        '</select>'+
                    '</div>'+
                '</div>'+
                '<div class="col-12 col-md-4">'+
                    '<div class="form-group ingresoCantidad">'+
                        '<label for="nuevaCantidad">Ingrese la cantidad a vender</label>'+
                        '<input type="number" class="form-control nuevaCantidad" name="nuevaCantidad" peso value="0.04" step="any" required>'+
                    '</div>'+
                ' </div>'+
                '<div class="col-12 col-md-3" style="display: none;">'+
                    '<div class="form-group ingresoLibras">'+
                        '<label for="cantidadLibras">Ingrese las libras a comprar</label>'+
                        '<input type="number" class="form-control cantidadLibras" name="cantidadLibras" decimales min="0" step="any" value="0" required>'+
                    '</div>'+
                ' </div>'+
                '<div class="col-12 col-md-4 ingresoPrecio">'+
                    '<label for="nuevoPrecio">Ingrese el precio del producto</label>'+
                    '<div class="input-group">'+
                        '<input type="number" class="form-control nuevoPrecio" name="nuevoPrecio" min="0" value="0" required>'+
                        '<button type="button" class="btn btn-danger quitarProducto" idProducto><i class="fa fa-times"></i></button>'+
                    '</div>'+
                ' </div>'+
            '</div>'
        );
        // AGREGAMOS LOS PRODUCTOS AL SELECT
        arreglo.forEach(funcionForEach);
        function funcionForEach(item, index){
            if(item.opcion != 1){
                $("#producto"+numProducto).append(
                    '<option idProducto="'+item.id+'" value="'+item.combi_nombre+'">'+item.combi_nombre+'</option>'
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
$('#formCompra').on('change', "select.nuevaDescripcion", function(){

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
            $(nuevaDescripcion[index]).attr("idProducto", item['id']);
            $(nuevaCantidad[index]).attr("peso", item['peso']);
        }
        // AGRUPAR PRODUCTOS EN FORMATO JSON
        listarProductos()
    });
});
/*==========================================================
			QUITAMOS EL PRODUCTO EN CUESTION
==========================================================*/
var idQuitarProducto = [];
$("#formCompra").on("click", "button.quitarProducto", function(){
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
			MODIFICAR LA CANTIDAD Y SUMAR TOTALES
======================================================================*/
$("#formCompra").on("change", "input.nuevoPrecio", function(){
	// SUMAR EL TOTAL DE LOS PRECIOS
	sumarTotalPrecios()
	// AGRUPAR PRODUCTOS EN FORMATO JSON
	listarProductos()
});
/*======================================================================
			MODIFICAR LA CANTIDAD Y SU TOTAL
======================================================================*/
$("#formCompra").on("change", "input.nuevaCantidad", function(){
	// SUMAR EL TOTAL DE LOS PRECIOS
	sumarTotalPrecios()
	// AGRUPAR PRODUCTOS EN FORMATO JSON
	listarProductos()
});
/*======================================================================
			MODIFICAR LAS LIBRAS Y SUMAR PRECIOS
======================================================================*/
$("#formCompra").on("change", "input.cantidadLibras", function(){
    // MULTIPLICAR LIBRAS Y DIVIDIR POR 100 PARA OBTENER EL PORCENTAJE
    var cantidadLibraItem = $('.cantidadLibras');
    $(cantidadLibraItem).attr('decimales',($(cantidadLibraItem).val()*4)/100);
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
    var cantidadLibraItem = $('.cantidadLibras');
    var arraySumaPrecios = [];
    var totalSuma = 0;

    for (var i = 0; i < cantidadItem.length; i++) {
        var totalCantidad = $(cantidadItem[i]).val();
        if($(cantidadLibraItem).val() > 0){
            totalCantidad =  parseFloat($(cantidadItem[i]).val()) + parseFloat($(cantidadLibraItem[i]).attr('decimales'));
        }
        arraySumaPrecios.push(
            Number($(precioItem[i]).val()) * Number(totalCantidad)
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

    var tipoCantidad = $('.tipo_cantidad');

    var cantidadLibra = $('.cantidadLibras');

	var cantidad 	= $(".nuevaCantidad");

	var precio 		= $(".nuevoPrecio");

	for (var i = 0; i < descripcion.length; i++) {
		listaProductos.push({ "id" : $(descripcion[i]).attr("idProducto"),
							  "descripcion" : $(descripcion[i]).val(),
							  "tipo_cantidad" : $(tipoCantidad).val(),
                              "peso" : $(cantidad[i]).attr('peso'),
							  "cantidad" : $(cantidad[i]).val(),
                              "libras" : $(cantidadLibra[i]).val(),
                              "precio" : $(precio[i]).val()
                            })

	}

	$("#listaProductos").val(JSON.stringify(listaProductos));

}
