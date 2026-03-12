<script language="JavaScript">
    document.addEventListener('DOMContentLoaded', function() {
        // Cascada: país -> departamento
        $('#pais_id').change(function() {
            const paisId = $(this).val();
            const depSelect = $('#departamento_id').empty().append(
                '<option value="">Cargando...</option>');
            $('#ciudad_id').empty().append('<option value="">Seleccione una ciudad</option>');

            if (paisId) {
                $.getJSON(`/departamentos/${paisId}`, function(data) {
                    depSelect.empty().append(
                        '<option value="">Seleccione un departamento</option>');
                    data.forEach(dep => depSelect.append(
                        `<option value="${dep.id}">${dep.nombre}</option>`));
                });
            }
        });

        // Cascada: departamento -> ciudad
        $('#departamento_id').change(function() {
            const depId = $(this).val();
            const citySelect = $('#ciudad_id').empty().append('<option value="">Cargando...</option>');
            if (depId) {
                $.getJSON(`/ciudades/${depId}`, function(data) {
                    citySelect.empty().append(
                        '<option value="">Seleccione una ciudad</option>');
                    data.forEach(city => citySelect.append(
                        `<option value="${city.id}">${city.nombre}</option>`));
                });
            }
        });

        // Máscara para teléfono
        if ($.fn.inputmask) $('.phone').inputmask('(999) 999-9999');
    });
</script>
