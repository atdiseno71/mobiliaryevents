document.addEventListener('DOMContentLoaded', function () {

    const buscar = document.getElementById('buscarProducto');
    const buscarGrupo = document.getElementById('buscarPorGrupo');
    const tbodySel = document.querySelector('#tabla-seleccionados tbody');

    /* ============================
       CACHE DE PRODUCTOS
    ============================ */
    const productRows = Array.from(
        document.querySelectorAll('#tabla-productos tbody tr.prod-row')
    ).map(tr => ({
        el: tr,
        name: tr.dataset.name || '',
        code: tr.dataset.code || '',
        grupo: tr.dataset.grupo || ''
    }));

    /* ============================
       HELPERS
    ============================ */
    function escapeHtml(text = '') {
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function removeEmptySelected() {
        const ph = tbodySel.querySelector('.empty-sel');
        if (ph) ph.remove();
    }

    function isSelected(id) {
        return !!tbodySel.querySelector(`tr[data-id="${id}"]`);
    }

    /* ============================
       SELECCIONADOS
    ============================ */
    function addSelected(id, nombre, codigo) {
        if (isSelected(id)) return;

        removeEmptySelected();

        const tr = document.createElement('tr');
        tr.dataset.id = id;

        tr.innerHTML = `
            <td>
                <div class="fw-semibold">${escapeHtml(nombre)}</div>
                <small class="text-muted">${escapeHtml(codigo || '')}</small>
                <input type="hidden" name="productos[]" value="${id}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-prod">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tbodySel.appendChild(tr);
    }

    document.querySelectorAll('.add-prod').forEach(btn => {
        btn.addEventListener('click', function () {
            addSelected(this.dataset.id, this.dataset.nombre, this.dataset.codigo);
        });
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-prod');
        if (!btn) return;

        btn.closest('tr').remove();

        if (!tbodySel.querySelector('tr')) {
            tbodySel.innerHTML = `
                <tr class="empty-sel">
                    <td colspan="2" class="text-center text-muted py-3">
                        No hay productos seleccionados.
                    </td>
                </tr>
            `;
        }
    });

    /* ============================
       FILTROS OPTIMIZADOS
    ============================ */
    function aplicarFiltros() {
        const q = buscar ? buscar.value.trim().toLowerCase() : '';
        const grupoId = buscarGrupo ? buscarGrupo.value : '';

        for (let i = 0; i < productRows.length; i++) {
            const p = productRows[i];

            const matchTexto =
                !q || p.name.includes(q) || p.code.includes(q);

            const matchGrupo =
                !grupoId || p.grupo === grupoId;

            p.el.style.display =
                (matchTexto && matchGrupo) ? '' : 'none';
        }
    }

    /* ============================
       DEBOUNCE (CRUCIAL)
    ============================ */
    function debounce(fn, delay = 250) {
        let t;
        return function () {
            clearTimeout(t);
            t = setTimeout(fn, delay);
        };
    }

    const aplicarFiltrosDebounced = debounce(aplicarFiltros, 200);

    if (buscar) {
        buscar.addEventListener('input', aplicarFiltrosDebounced);
    }

    if (buscarGrupo) {
        buscarGrupo.addEventListener('change', aplicarFiltros);
        $(buscarGrupo).on('select2:select select2:clear', aplicarFiltros);
    }

});