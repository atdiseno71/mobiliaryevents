document.addEventListener("DOMContentLoaded", function () {

    /* ============================
        BUSCADOR POR QR
    ============================ */
    const buscar = document.getElementById("buscarProducto");

    let rows = [];
    let groups = [];
    let index = [];

    function rebuildSearchIndex() {
        rows = Array.from(document.querySelectorAll(".product-row"));
        groups = Array.from(document.querySelectorAll(".product-group"));

        index = rows.map(row => ({
            el: row,
            group: row.closest(".product-group"),
            text: (
                (row.dataset.code || "") + " " +
                (row.dataset.name || "")
            ).toLowerCase(),
        }));
    }

    rebuildSearchIndex();

    if (buscar) {
        let timer = null;

        buscar.addEventListener("input", function () {
            clearTimeout(timer);

            timer = setTimeout(() => {
                const q = this.value.replace(/\s+/g, "").trim().toLowerCase();

                if (q === "") {
                    rows.forEach(r => r.classList.remove("product-movimiento-hidden"));
                    groups.forEach(g => g.style.display = "");

                    document
                        .querySelectorAll(".collapse.show")
                        .forEach(c => c.classList.remove("show"));

                    return;
                }

                const visibles = [];
                const gruposVisibles = new Set();

                index.forEach(item => {
                    const match = item.text.includes(q);

                    item.el.classList.toggle("product-movimiento-hidden", !match);

                    if (match) {
                        visibles.push(item.el);
                        if (item.group) {
                            gruposVisibles.add(item.group);
                        }
                    }
                });

                groups.forEach(g => {
                    const mostrar = gruposVisibles.has(g);
                    g.style.display = mostrar ? "" : "none";

                    const collapse = g.querySelector(".collapse");
                    if (collapse) {
                        if (mostrar) collapse.classList.add("show");
                        else collapse.classList.remove("show");
                    }
                });

                if (visibles.length === 1) {
                    const btn = visibles[0].querySelector(".agregar-producto");
                    if (btn) btn.click();
                    buscar.value = "";
                }

            }, 100);
        });
    }

    /* ============================
        SWEETALERT: CANTIDAD
    ============================ */
    async function showCantPrompt(defaultValue = 1) {
        Swal.fire({
            title: "Agregando...",
            didOpen: () => {
                Swal.showLoading();
            },
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false,
            timer: 500
        });
        return defaultValue;
    }

    /* ============================
        UTILIDAD: STOCK REAL
    ============================ */
    function getStockReal(btn) {
        const almacenId = document.querySelector('[name="almacen_id"]').value;
        if (!almacenId) return 0;

        let stocks = {};
        try { stocks = JSON.parse(btn.dataset.stocks || "{}"); } catch (_) { }

        return stocks[almacenId] ?? 0;
    }

    /* ============================
        TABLA DERECHA: AGREGAR PRODUCTO
    ============================ */
    const selectedTbody = document.querySelector("#productos-seleccionados tbody");

    function removeEmptyPlaceholder() {
        const ph = selectedTbody.querySelector(".empty-placeholder");
        if (ph) ph.remove();
    }
    async function agregarProductoClickHandler(event) {
        const btn = event.currentTarget || this;

        const almacenId = document.querySelector('[name="almacen_id"]').value;
        if (!almacenId)
            return Swal.fire({ icon: "warning", title: "Selecciona un almacén" });

        const remisionId = document.querySelector('[name="remision_id"]').value;
        if (!remisionId)
            return Swal.fire({ icon: "warning", title: "Selecciona una remisión" });

        const tipoMov = document.querySelector('select[name="tipo"]').value;
        if (!tipoMov)
            return Swal.fire({ icon: "warning", title: "Selecciona tipo" });

        const stock = getStockReal(btn);

        const rawId = btn.dataset.id;
        const nombre = btn.dataset.nombre;
        const referencia = btn.dataset.referencia || btn.dataset.codigo_qr || "";
        const inventario = String(btn.dataset.inventario);

        const key = `${rawId}__inv${inventario}`;

        if (selectedTbody.querySelector(`tr[data-key="${key}"]`)) {
            return Swal.fire({
                icon: "warning",
                title: "Ya agregado",
                text: "Ese producto ya está en la lista."
            });
        }

        let cantidad = 1;

        if (!btn.dataset.autoAdd) {
            cantidad = await showCantPrompt(1);
            if (!cantidad) return;
        }

        delete btn.dataset.autoAdd;

        if (tipoMov === "salida" && cantidad > stock) {
            return Swal.fire({
                icon: "error",
                title: "Stock insuficiente",
                text: `Solo hay ${stock} unidades.`
            });
        }

        removeEmptyPlaceholder();

        const tr = document.createElement("tr");
        tr.dataset.key = key;
        tr.dataset.id = rawId;
        tr.dataset.inv = inventario;
        const disabled = btn.dataset.is_insumo !== "true";

        tr.innerHTML = `
            <td>
                <div class="fw-semibold">${escapeHtml(nombre)}</div>
                <div><small class="text-muted">${escapeHtml(referencia)}</small></div>

                <input type="hidden" name="productos[]" value="${rawId}">
                <input type="hidden" name="inventarios[]" value="${inventario}">
                <input type="hidden" name="cantidades[]" value="${cantidad}" class="cantidad-hidden">
            </td>

            <td>
                <input type="number"
                    class="form-control form-control-sm mt-1 cantidad-visible"
                    value="${cantidad}"
                    min="1"
                    ${disabled ? "disabled" : ""}
                    style="max-width: 90px;">
            </td>

            <td class="align-middle text-center">
                <button type="button" class="btn btn-danger btn-sm quitar-producto">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        selectedTbody.appendChild(tr);
    }

    document.querySelectorAll(".agregar-producto")
        .forEach(btn => btn.addEventListener("click", agregarProductoClickHandler));

    /* ============================
        QUITAR PRODUCTO
    ============================ */
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".quitar-producto");
        if (!btn) return;

        const tr = btn.closest("tr");

        Swal.fire({
            title: "Quitar producto",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Sí"
        }).then(res => {
            if (res.isConfirmed) {
                tr.remove();

                if (!selectedTbody.querySelector("tr")) {
                    selectedTbody.innerHTML =
                        `<tr class="empty-placeholder">
                            <td colspan="3" class="text-center text-muted py-4">
                                No hay productos seleccionados.
                            </td>
                        </tr>`;
                }
            }
        });
    });

    /* ============================
        SYNC: INPUT VISIBLE -> HIDDEN
    ============================ */
    document.addEventListener("input", function (e) {
        const input = e.target.closest(".cantidad-visible");
        if (!input) return;

        const tr = input.closest("tr");
        if (!tr) return;

        // normaliza
        let val = parseInt(input.value, 10);
        if (!Number.isFinite(val) || val < 1) val = 1;
        input.value = val;

        // actualiza hidden
        const hidden = tr.querySelector('input[name="cantidades[]"].cantidad-hidden');
        if (hidden) hidden.value = val;
    });

    /* ============================
        ESCAPAR HTML
    ============================ */
    function escapeHtml(text = "") {
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    /* ============================
        VALIDAR FORMULARIO
    ============================ */
    const form = document.querySelector("form");
    if (form) {
        form.addEventListener("submit", function (e) {
            if (!selectedTbody.querySelector('input[name="productos[]"]')) {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Faltan productos",
                    text: "Agrega al menos uno."
                });
            }
        });
    }

    /* ============================
        ICONOS DE COLAPSE
    ============================ */
    $(document).on("click", ".toggle-subref", function () {
        let icon = $(this).find("i");
        let target = $($(this).data("target"));

        setTimeout(() => {
            icon.toggleClass("fa-caret-right", !target.hasClass("show"));
            icon.toggleClass("fa-caret-down", target.hasClass("show"));
        }, 200);
    });

    /* ============================
        CAMBIAR REMISIÓN → CARGAR PRODUCTOS
    ============================ */
    $("#remision_id").on("change", async function () {
        const id = this.value;
        const tipo = $("#tipo").val();

        if (!tipo) {
            Swal.fire({ icon: "warning", title: "Falta seleccionar un tipo", text: "Por favor, selecciona un tipo." });
            return;
        }

        if (!id || !tipo) return;

        try {
            const resp = await fetch(`/api/remisiones/${id}?tipo=${tipo}`);
            const remision = await resp.json();
            cargarProductosDeRemision(remision.detalles);
            cargarProductosDisponiblesDesdeRemision(remision.detalles);
        } catch (err) {
            console.error(err);
            Swal.fire({ icon: "error", title: "Error", text: "No se pudo cargar la remisión." });
        }
    });

    /* ============================
        AUTO-CARGA EN CREATE:
        si ya hay remisión seleccionada, carga tabla + disponibles
    ============================ */
    (function autoLoadIfRemisionSelected() {
        const remSel = document.getElementById("remision_id");
        if (!remSel) return;

        const id = remSel.value;
        if (!id) return;

        // Dispara el mismo flujo del change (cargar tabla remisión + disponibles)
        $("#remision_id").trigger("change");
    })();

    /* ============================
        TABLA SUPERIOR: DETALLES AGRUPADOS
    ============================ */
    function cargarProductosDeRemision(detalles) {
        const tbodyRem = document.querySelector("#tabla-remision tbody");

        if (!detalles || detalles.length === 0) {
            tbodyRem.innerHTML = `
            <tr class="empty-remision">
                <td colspan="2" class="text-center text-muted py-3">
                    No se ha cargado ninguna remisión.
                </td>
            </tr>`;
            return;
        }

        const grupos = {};

        detalles.forEach(det => {
            let grupoId = 9999;
            let grupoNombre = "SIN GRUPO";

            /* =====================
                CASO COMBO
            ===================== */
            if (det.combinacion) {
                const combo = det.combinacion;
                const comboCantidad = det.cantidad ?? 1;

                // Grupo especial por combo (usa su id para evitar colisiones)
                grupoId = `combo_${combo.id}`;
                grupoNombre = `COMBO: ${combo.nombre}`;

                if (!grupos[grupoId]) {
                    grupos[grupoId] = {
                        nombre: grupoNombre,
                        items: []
                    };
                }

                // Cada producto interno del combo
                (combo.productos || []).forEach(p => {
                    grupos[grupoId].items.push({
                        nombre: p.nombre,
                        cantidad: comboCantidad // multiplicador del combo
                    });
                });

                return;
            }

            /* =====================
                PRODUCTO NORMAL
            ===================== */
            let nombre = "";
            let cantidad = det.cantidad ?? 1;

            if (det.producto) {
                grupoId = det.producto.grupo?.id ?? 9999;
                grupoNombre = det.producto.grupo?.nombre ?? "SIN GRUPO";
                nombre = det.producto.nombre;
            }
            else if (det.referencia) {
                const grupoProd = det.referencia.productos?.[0]?.grupo;
                grupoId = grupoProd?.id ?? 9999;
                grupoNombre = grupoProd?.nombre ?? "SIN GRUPO";
                nombre = det.referencia.nombre;
            }

            if (!grupos[grupoId]) {
                grupos[grupoId] = {
                    nombre: grupoNombre,
                    items: []
                };
            }

            grupos[grupoId].items.push({ nombre, cantidad });
        });

        /* =====================
            ORDENAR GRUPOS
        ===================== */
        const gruposOrdenados = Object
            .keys(grupos)
            .map(id => ({ id, ...grupos[id] }))
            .sort((a, b) => {
                // combos al final
                if (String(a.id).startsWith("combo_")) return 1;
                if (String(b.id).startsWith("combo_")) return -1;
                return Number(a.id) - Number(b.id);
            });

        tbodyRem.innerHTML = "";

        gruposOrdenados.forEach(grupo => {

            const header = document.createElement("tr");
            header.innerHTML = `
            <td colspan="2" style="
                background: #ffe800;
                font-weight: bold;
                text-transform: uppercase;
                padding: 6px;">
                ${escapeHtml(grupo.nombre)}
            </td>`;
            tbodyRem.appendChild(header);

            grupo.items.forEach(item => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                <td>${escapeHtml(item.nombre)}</td>
                <td class="text-center">${item.cantidad}</td>`;
                tbodyRem.appendChild(tr);
            });
        });
    }

    /* ============================
        IZQUIERDA: PRODUCTOS DISPONIBLES
    ============================ */
    function cargarProductosDisponiblesDesdeRemision(detalles) {
        const contenedor = document.querySelector(
            '.col-md-6 .card-body .table-responsive'
        );

        if (!contenedor) return;

        if (!detalles || detalles.length === 0) {
            contenedor.innerHTML = `<div class="text-center text-muted py-4">No hay productos.</div>`;
            rebuildSearchIndex();
            return;
        }

        let productosNorm = [];
        let combos = [];

        detalles.forEach(det => {

            /* =====================
                COMBOS
            ===================== */
            if (det.combinacion) {
                combos.push({
                    id: det.combinacion.id,
                    nombre: det.combinacion.nombre,
                    cantidad: det.cantidad ?? 1,
                    productos: det.combinacion.productos ?? [],
                    stocks: det.combinacion.stock_por_almacen ?? {},
                    is_insumo: "false",
                    codigo: det.combinacion.codigo_qr ?? "",
                });
                return;
            }

            /* =====================
                PRODUCTOS / REFERENCIAS
            ===================== */
            if (det.producto) {
                const p = det.producto;
                productosNorm.push({
                    id: p.id,
                    nombre: p.nombre,
                    codigo: p.codigo_qr ?? "",
                    inventario: p.inventario_por_serie ? 1 : 0,
                    subref: p.subreferencia?.nombre ?? null,
                    marca: p.marca?.nombre ?? "",
                    stocks: p.stock_por_almacen ?? {},
                    is_insumo: p.is_clase_insumo,
                });
            }
            else if (det.referencia && Array.isArray(det.referencia.productos)) {
                det.referencia.productos.forEach(p => {
                    productosNorm.push({
                        id: p.id,
                        nombre: p.nombre,
                        codigo: p.codigo_qr ?? "",
                        inventario: p.inventario_por_serie ? 1 : 0,
                        subref: p.subreferencia?.nombre ?? null,
                        marca: p.marca?.nombre ?? "",
                        refName: det.referencia.nombre,
                        stocks: p.stock_por_almacen ?? {},
                        is_insumo: "false",
                    });
                });
            }
        });

        /* ============================
            ELIMINAR DUPLICADOS
        ============================ */
        const mapById = new Map();
        productosNorm.forEach(p => {
            if (!mapById.has(p.id)) mapById.set(p.id, p);
        });
        const productos = Array.from(mapById.values());

        const porSerie = productos.filter(p => p.inventario === 1);
        const sinSerie = productos.filter(p => p.inventario === 0);

        /* ============================
            AGRUPAR POR SUBREF / REF
        ============================ */
        const grupos = {};

        porSerie.forEach(p => {
            const clave = p.refName || p.subref || "SIN SUBREF";
            if (!grupos[clave]) grupos[clave] = [];
            grupos[clave].push(p);
        });

        /* ============================
            CONSTRUIR HTML
        ============================ */
        let html = "";

        /* ========= COMBOS ========= */
        if (combos.length > 0) {
            html += `
            <div class="product-group combo-group mb-3" data-tipo="combo">
                <div class="p-2 border fw-bold">
                    COMBOS
                </div>

                <ul class="list-group list-group-flush border">
        `;

            combos.forEach(c => {
                html += `
                    <li class="list-group-item d-flex justify-content-between align-items-center product-row"
                        data-code="${escapeHtml(c.codigo).toLowerCase()}"
                        data-name="${escapeHtml((c.nombre + ' ' + c.codigo)).toLowerCase()}">
                    <div>
                        <div class="fw-semibold">${escapeHtml(c.nombre)}</div>
                        <small class="text-muted">
                        ${escapeHtml(c.codigo)} — Incluye ${c.productos.length} productos
                        </small>
                    </div>

                    <button type="button"
                        class="btn btn-sm btn-outline-primary agregar-producto"
                        data-id="${c.id}"
                        data-nombre="${escapeHtml(c.nombre)}"
                        data-codigo_qr="${escapeHtml(c.codigo)}"
                        data-inventario="combo"
                        data-is_combo="1"
                        data-is_insumo="${c.is_insumo}"
                        data-stocks='${JSON.stringify(c.stocks)}'
                        data-cantidad="${c.cantidad}">
                        <i class="fas fa-plus"></i>
                    </button>
                    </li>
                `;
            });

            html += `
                </ul>
            </div>
        `;
        }

        /* ========= PRODUCTOS POR SERIE ========= */
        Object.keys(grupos).forEach(subref => {
            const items = grupos[subref];
            if (!items.length) return;

            const padre = items[0];
            const collapseId = "grupo_" +
                subref.toLowerCase().replace(/[^a-z0-9]+/g, "_") +
                "_" + padre.id;

            html += `
            <div class="product-group mb-3">
                <div class="bg-light p-2 border">
                    <strong>${escapeHtml(subref)}</strong>
                </div>

                <ul class="list-group list-group-flush">
        `;

            items.forEach(p => {
                html += `
                <li class="list-group-item d-flex justify-content-between align-items-center product-row"
                    data-name="${escapeHtml(p.nombre).toLowerCase()}"
                    data-subref="${escapeHtml(subref).toLowerCase()}"
                    data-code="${escapeHtml(p.codigo).toLowerCase()}">

                    <div>
                        <div>${escapeHtml(p.nombre)}</div>
                        <small class="text-muted">${escapeHtml(p.codigo)}</small>
                    </div>

                    <button type="button"
                        class="btn btn-sm btn-outline-primary agregar-producto"
                        data-id="${p.id}"
                        data-nombre="${escapeHtml(p.nombre)}"
                        data-codigo_qr="${escapeHtml(p.codigo)}"
                        data-is_insumo="${p.is_insumo}"
                        data-stocks='${JSON.stringify(p.stocks)}'
                        data-inventario="${p.inventario}">
                        <i class="fas fa-plus"></i>
                    </button>
                </li>
            `;
            });

            html += `
                </ul>
            </div>
        `;
        });

        /* ========= PRODUCTOS GENERALES ========= */
        if (sinSerie.length > 0) {
            html += `
            <div class="product-group mb-3">
                <div class="bg-light p-2 border">
                    <strong>Generales</strong>
                </div>

                <ul class="list-group list-group-flush">
        `;

            sinSerie.forEach(p => {
                html += `
                <li class="list-group-item d-flex justify-content-between align-items-center product-row"
                    data-name="${escapeHtml(p.nombre).toLowerCase()}"
                    data-code="${escapeHtml(p.codigo).toLowerCase()}">

                    <div class="fw-semibold">${escapeHtml(p.nombre)}</div>
                    <small class="text-muted">${escapeHtml(p.codigo)}</small>

                    <button type="button"
                        class="btn btn-sm btn-outline-primary agregar-producto"
                        data-id="${p.id}"
                        data-nombre="${escapeHtml(p.nombre)}"
                        data-codigo_qr="${escapeHtml(p.codigo)}"
                        data-is_insumo="${p.is_insumo}"
                        data-stocks='${JSON.stringify(p.stocks)}'
                        data-inventario="0">
                        <i class="fas fa-plus"></i>
                    </button>
                </li>
            `;
            });

            html += `
                </ul>
            </div>
        `;
        }

        contenedor.innerHTML = html;

        document.querySelectorAll(".agregar-producto")
            .forEach(btn => btn.addEventListener("click", agregarProductoClickHandler));

        rebuildSearchIndex();
    }

});
