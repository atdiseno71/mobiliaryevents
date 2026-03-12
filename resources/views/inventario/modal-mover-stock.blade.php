<!-- resources/views/inventario/modal-mover-stock.blade.php -->
<div class="modal fade" id="moverStockModal" tabindex="-1" role="dialog" aria-labelledby="moverStockLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formMoverStock" method="POST" action="{{ route('inventarios.mover') }}">
            @csrf
            <input type="hidden" name="producto_id" id="modalProductoId">
            <input type="hidden" name="almacen_id" id="modalAlmacenId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="moverStockLabel">Mover Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de movimiento</label>
                        <select name="tipo" id="tipo" class="form-control select2" required>
                            <option value="">Seleccione...</option>
                            <option value="ingreso">Ingreso</option>
                            <option value="salida">Salida</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo (opcional)</label>
                        <textarea name="motivo" id="motivo" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Aplicar Movimiento</button>
                </div>
            </div>
        </form>
    </div>
</div>
