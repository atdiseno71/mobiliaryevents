$('.form-delete').submit(function (e) {
    e.preventDefault();
    Swal.fire({
        title: '¿Está seguro de eliminar esto?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, elimina esto!'
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
    })
});

function confirmDelete(form) {
    Swal.fire({
        title: '¿Está seguro de eliminar esto?',
        text: "No podrás revertir esta acción.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
