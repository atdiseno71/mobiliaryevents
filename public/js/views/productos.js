/*=============================================
    ACTIVAR O DESACTIVA INPUT STOCK
=============================================*/
$('.opcion').on('change', function(){
    var selected = $('select[name=opcion]').val();
    if(selected == '1'){
        $('.stock-hidden').removeAttr('hidden');
    }else{
        $('.stock-hidden').attr('hidden',true);
        $('.stock-hidden').removeAttr('required');
    }
});
/*=============================================
    ACTIVAR O DESACTIVA INPUT STOCK
=============================================*/
$(document).ready(function(){
    var selected = $('select[name=opcion]').val();
    if(selected == '1'){
        $('.stock-hidden').removeAttr('hidden');
    }else{
        $('.stock-hidden').removeAttr('required');
        $('.stock-hidden').attr('hidden',true);
    }
})

$(document).ready(function () {

    // Select: GRUPO -> CATEGORIAS
    $('#grupo_id').on('change', function () {
        let grupoId = $(this).val();

        $('#categoria_id').empty().append('<option value="">Seleccione una categoría</option>');
        $('#subcategoria_id').empty().append('<option value="">Seleccione una subcategoría</option>');
        $('#subreferencia_id').empty().append('<option value="">Seleccione una subreferencia</option>');

        if (!grupoId) return;

        $.get('/grupos/' + grupoId + '/categorias', function (res) {
            $.each(res, function (id, nombre) {
                $('#categoria_id').append(`<option value="${id}">${nombre}</option>`);
            });
        });
    });

    // Select: CATEGORIA -> SUBCATEGORIAS
    $('#categoria_id').on('change', function () {
        let categoriaId = $(this).val();

        $('#subcategoria_id').empty().append('<option value="">Seleccione una subcategoría</option>');
        $('#subreferencia_id').empty().append('<option value="">Seleccione una subreferencia</option>');

        if (!categoriaId) return;

        $.get('/categorias/' + categoriaId + '/subcategorias', function (res) {
            $.each(res, function (id, nombre) {
                $('#subcategoria_id').append(`<option value="${id}">${nombre}</option>`);
            });
        });
    });

    // Select: SUBCATEGORIA -> SUBREFERENCIAS
    $('#subcategoria_id').on('change', function () {
        let subcatId = $(this).val();

        $('#subreferencia_id').empty().append('<option value="">Seleccione una subreferencia</option>');

        if (!subcatId) return;

        $.get('/subcategorias/' + subcatId + '/subreferencias', function (res) {
            $.each(res, function (id, nombre) {
                $('#subreferencia_id').append(`<option value="${id}">${nombre}</option>`);
            });
        });
    });

});

document.getElementById('imagen_input')?.addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('preview_img').src = e.target.result;
    }
    reader.readAsDataURL(file);
});