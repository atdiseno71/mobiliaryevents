document.addEventListener('DOMContentLoaded', function () {
    const buscarTexto = document.getElementById('buscarProducto');
    const buscarGrupo = document.getElementById('buscarPorGrupo');

    // Logs seguros
    console.log('buscarTexto element exists?', !!buscarTexto);
    console.log('buscarGrupo element exists?', !!buscarGrupo);

    // Debounce helper (evita ejecuciones excesivas al tipear)
    function debounce(fn, wait = 150) {
        let t;
        return function (...args) {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), wait);
        };
    }

    function aplicarFiltros() {
        try {
            const q = buscarTexto ? (buscarTexto.value || "").trim().toLowerCase() : "";
            const g = buscarGrupo ? ((buscarGrupo.value || "").toLowerCase()) : "";

            const rows = document.querySelectorAll('.product-row');
            const groups = document.querySelectorAll('.product-group');

            // Reset
            rows.forEach(row => row.classList.remove('hidden-row'));

            // Filtro por texto
            if (q !== "") {
                rows.forEach(row => {
                    const name = (row.dataset.name || "").toLowerCase();
                    row.classList.toggle('hidden-row', !name.includes(q));
                });
            }

            // Filtro por grupo (usa data-group en HTML)
            // groups.forEach(group => {
            //     const groupName = (group.dataset.grupo || "").toLowerCase();
            //     const children = group.querySelectorAll('.product-row:not(.hidden-row)');
            //     const matchGroup = g === "" || groupName === g;

            //     if (matchGroup && children.length > 0) {
            //         group.classList.remove('hidden-group');

            //         // abrir collapse si existe
            //         // const collapse = group.querySelector('.collapse');
            //         // if (collapse && !collapse.classList.contains('show')) {
            //         //     // usa jQuery/bootstrap collapse si está disponible
            //         //     if (typeof $ === 'function' && typeof $(collapse).collapse === 'function') {
            //         //         $(collapse).collapse('show');
            //         //     } else {
            //         //         // fallback: añadir clase show si usas BS5 sin jQuery
            //         //         collapse.classList.add('show');
            //         //     }
            //         // }
            //     } else {
            //         group.classList.add('hidden-group');
            //     }
            // });

            // Filtro por grupo (usa data-group en HTML)
            groups.forEach(group => {
                const groupName = (group.dataset.grupo || "").toLowerCase();
                const matchGroup = g === "" || groupName === g;

                // detectar si es COMBO
                const isComboGroup =
                    group.dataset.tipo === 'combo' ||
                    group.classList.contains('combo-group') ||
                    !!group.querySelector('.agregar-producto[data-inventario="combo"]');

                if (isComboGroup) {
                    const comboSearch = (group.dataset.name || "").toLowerCase(); // nombre + qr (ya lo armaste en Blade)
                    const matchText = (q === "" || comboSearch.includes(q));

                    // combo visible SOLO si coincide con grupo (si aplica) Y con texto (si aplica)
                    if (matchGroup && matchText) {
                        group.classList.remove('hidden-group');
                    } else {
                        group.classList.add('hidden-group');
                    }

                    return; // no seguir con lógica normal
                }

                // ====== lógica normal para productos/subrefs ======
                const children = group.querySelectorAll('.product-row:not(.hidden-row)');

                if (matchGroup && children.length > 0) {
                    group.classList.remove('hidden-group');
                } else {
                    group.classList.add('hidden-group');
                }
            });

            // OCULTAR PRODUCTOS SUELTOS (sin-serie y sin-subreferencia)
            rows.forEach(row => {
                const rowGroup = (row.dataset.grupo || "").toLowerCase();

                // Si se filtra por grupo, ocultar filas que no coinciden
                if (g !== "" && rowGroup !== g) {
                    row.classList.add('hidden-row');
                }
            });

        } catch (err) {
            console.error('Error en aplicarFiltros:', err);
        }
    }

    const aplicarFiltrosDebounced = debounce(aplicarFiltros, 120);

    // Eventos para el input de texto
    if (buscarTexto) {
        buscarTexto.addEventListener('keydown', e => {
            if (e.key === 'Enter') e.preventDefault();
        });
        buscarTexto.addEventListener('input', aplicarFiltrosDebounced);
    }

    // Evento para el select (nativo)
    if (buscarGrupo) {
        buscarGrupo.addEventListener('change', aplicarFiltros);
    }

    // También enganchar cambio si estás usando Select2 (Select2 dispara 'change.select2' en algunas versiones)
    if (typeof $ === 'function' && buscarGrupo) {
        try {
            // si select2 está inicializado en el select, este listener cubre ambos casos
            $(document).on('change', '#buscarPorGrupo', aplicarFiltros);
            // adicionalmente, en caso de eventos específicos de select2:
            $(document).on('select2:select select2:unselect', '#buscarPorGrupo', aplicarFiltros);
        } catch (e) {
            // silently ignore
        }
    }

    // Ejecutar al inicio para que el estado inicial se muestre correctamente
    aplicarFiltros();

    // debug: ver el valor del select SIN lanzar error si no existe
    console.log('valor inicial buscarGrupo:', buscarGrupo ? buscarGrupo.value : '(no existe)');

    async function showCantPrompt(defaultValue = 1) {
        const {
            value: cant
        } = await Swal.fire({
            title: 'Cantidad',
            input: 'number',
            inputValue: defaultValue,
            inputAttributes: {
                min: 1
            },
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        });

        if (!cant) return null;

        const parsed = parseInt(cant, 10);
        if (isNaN(parsed) || parsed <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Cantidad inválida'
            });
            return null;
        }

        return parsed;
    }

    const selectedTbody = document.querySelector('#productos-seleccionados tbody');

    function removeEmptyPlaceholder() {
        const ph = selectedTbody.querySelector('.empty-placeholder');
        if (ph) ph.remove();
    }

    function invKeyForDedup(inv, is_combo) {
        inv = String(inv || '');

        if (is_combo === '1' || inv === 'combo') return 'combo';
        if (inv === 'subreferencia') return 'subreferencia';
        if (inv === '1') return '1';

        // todo lo "producto normal / sin serie / insumo con stock numérico" dedupea como '0'
        if (inv === 'producto_sin_serie') return '0';
        if (/^\d+$/.test(inv)) return '0'; // stock numérico
        if (inv === '' || inv === '0') return '0';

        return inv;
    }

    document.querySelectorAll('.agregar-producto').forEach(btn => {
        btn.addEventListener('click', async function () {

            const rawId = this.dataset.id;
            const nombre = this.dataset.nombre;
            const codigo_qr = this.dataset.codigo_qr || '';
            const inventario = String(this.dataset.inventario);
            const is_insumo = String(this.dataset.is_insumo);
            const is_combo = String(this.dataset.is_combo);
            const max = parseInt(this.dataset.cantidad || "1");

            const keyInv = invKeyForDedup(inventario, is_combo);

            // const key = `${rawId}__inv${inventario}`;
            const key = `${rawId}__inv${keyInv}`;

            if (selectedTbody.querySelector(`tr[data-key="${key}"]`)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Este producto ya está agregado',
                    text: 'Ajusta la cantidad si deseas más unidades.'
                });
                return;
            }

            const cantidad = await showCantPrompt(1);
            if (!cantidad) return;

            if (cantidad > max) {
                Swal.fire({
                    icon: 'error',
                    title: 'Cantidad excedida',
                    text: `Solo hay ${max} unidades disponibles.`
                });
                return;
            }

            removeEmptyPlaceholder();

            const tr = document.createElement('tr');
            tr.setAttribute('data-key', key);
            tr.setAttribute('data-id', rawId);
            tr.setAttribute('data-inv', inventario);
            tr.dataset.max = max;

            tr.innerHTML = `
                        <td>
                            <div class="fw-semibold">${escapeHtml(nombre)}</div>
                            <div><small class="text-muted">${escapeHtml(codigo_qr)}</small></div>
                        </td>

                        <td class="align-middle">
                            <input type="hidden" name="productos[]" value="${rawId}">
                            <input type="hidden" name="inventarios[]" value="${inventario}">
                            <input type="hidden" name="is_insumos[]" value="${is_insumo}">
                            <input type="hidden" name="is_combos[]" value="${is_combo}">
                            <input type="number" name="cantidades[]" value="${cantidad}" min="1"
                                class="form-control form-control-sm cantidad-input">
                        </td>

                        <td class="align-middle text-center">
                            <button type="button" class="btn btn-danger btn-sm quitar-producto">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;

            selectedTbody.appendChild(tr);
        });
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.quitar-producto');
        if (!btn) return;

        const tr = btn.closest('tr');

        Swal.fire({
            title: 'Quitar producto',
            text: '¿Seguro que deseas eliminar este producto?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'No'
        }).then(result => {
            if (result.isConfirmed) {
                tr.remove();

                if (!selectedTbody.querySelector('tr')) {
                    selectedTbody.innerHTML =
                        `<tr class="empty-placeholder"><td colspan="3" class="text-center text-muted py-4">No hay productos seleccionados.</td></tr>`;
                }
            }
        });
    });

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function (e) {

            const hasProducts = selectedTbody.querySelector('input[name="productos[]"]');
            if (!hasProducts) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Faltan productos',
                    text: 'Agrega al menos un producto a la remisión.'
                });
                return false;
            }
        });
    }

    $(document).on('click', '.toggle-subref', function () {
        let icon = $(this).find('i');
        let target = $($(this).data('target'));

        setTimeout(() => {
            if (target.hasClass('show')) {
                icon.removeClass('fa-caret-right').addClass('fa-caret-down');
            } else {
                icon.removeClass('fa-caret-down').addClass('fa-caret-right');
            }
        }, 200);
    });

    /* ============================================================
        VALIDAR CAMBIOS MANUALES DE CANTIDAD
    ============================================================ */
    document.addEventListener('input', function (e) {
        const input = e.target.closest('.cantidad-input');
        if (!input) return;

        const tr = input.closest('tr');
        const key = tr.dataset.key;

        // buscar el botón original con el stock
        const btn = document.querySelector(`.agregar-producto[data-key="${key}"]`);

        let max = 999999;

        if (btn) {
            max = parseInt(btn.dataset.cantidad || "999999");
        } else {
            // modo edición: usa el data-max que agregamos en preload
            max = parseInt(tr.dataset.max || "999999");
        }

        let val = parseInt(input.value || 0);

        if (val > max) {
            input.value = max;
            Swal.fire({
                icon: 'warning',
                title: 'Cantidad excedida',
                text: `Solo hay ${max} unidades disponibles.`
            });
        }

        if (val <= 0) input.value = 1;
    });

    const btnClonar = document.getElementById('btnClonarRemision');
    const selClonar = document.getElementById('clonar_remision_id');

    if (btnClonar && selClonar) {
        btnClonar.addEventListener('click', function () {
            const id = selClonar.value;

            if (!id) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selecciona una remisión para clonar'
                });
                return;
            }

            // recarga el mismo create con el parámetro
            const baseUrl = "{{ route('remisiones.create') }}";
            window.location.href = `?clone_id=${encodeURIComponent(id)}`;
        });
    }


});