<?php
session_start();
?>
<link href="css/tablas_creditos.css" rel="stylesheet" type="text/css" />
<style>
/* Estilos de scrollbar personalizados */
::-webkit-scrollbar {
    width: 5px;
    height: 8px;
}

::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    border-radius: 10px;
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
    background: #47478D;
}

/* Estilos responsive para botones */
@media (max-width: 768px) {
    .btn {
        font-size: 0.8rem;
        padding: 0.375rem 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .btn i {
        font-size: 0.7rem;
    }
    
    .mr-2 {
        margin-right: 0.25rem !important;
    }
    
    .mb-3 {
        margin-bottom: 1rem !important;
    }
}

@media (max-width: 576px) {
    .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.4rem;
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .btn i {
        font-size: 0.65rem;
    }
    
    .container {
        padding-left: 10px;
        padding-right: 10px;
    }
}

/* Estilos para la tabla responsive */
.table-responsive {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.table-responsive .table {
    margin-bottom: 0;
}

.table-responsive .table th {
    background: #47478D;
    color: white;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.table-responsive .table td {
    vertical-align: middle;
    border-color: #e9ecef;
}

/* Estilos para botones de acción en la tabla */
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.btn-sm:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Responsive para botones de acción */
@media (max-width: 768px) {
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
    }
    
    .btn-sm i {
        font-size: 0.65rem;
    }
}
</style>
<div class="container mt-4">
    <h2 class="mb-4">Gestión de Gastos</h2>
    
    <!-- Totalizador de Saldos -->
    <div class="totales-container">
        <div class="total-item">
            <div class="total-label">Total de Saldos</div>
            <div class="total-valor" id="total_saldos">$0</div>
        </div>
    </div>
    
    <div class="mb-3">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                <button class="btn btn-success btn-block" id="btn_nueva_cuenta_gasto">
                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Nueva Cuenta/Fuente</span>
                    <span class="d-inline d-sm-none">Nueva Cuenta</span>
                </button>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                <button class="btn btn-primary btn-block" id="btn_movimientos_gasto">
                    <i class="fas fa-exchange-alt"></i> <span class="d-none d-sm-inline">Movimientos</span>
                    <span class="d-inline d-sm-none">Movimientos</span>
                </button>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                <button class="btn btn-warning btn-block" id="btn_transferir_gasto">
                    <i class="fas fa-exchange-alt"></i> <span class="d-none d-sm-inline">Transferir entre Cuentas</span>
                    <span class="d-inline d-sm-none">Transferir</span>
                </button>
            </div>
        </div>
    </div>
    <div id="div_listado_cuentas_gasto" class="mt-4">
        <!-- Aquí se mostrará el listado de cuentas/fuentes y sus saldos -->
    </div>
</div>
<!-- Modal Nueva Cuenta/Fuente de Gasto -->
<div class="modal fade" id="modal_nueva_cuenta_gasto" tabindex="-1" role="dialog" aria-labelledby="modalNuevaCuentaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNuevaCuentaLabel">Nueva Cuenta/Fuente de Gasto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form_nueva_cuenta_gasto">
          <div class="form-group">
            <label for="nombre_cuenta_gasto">Nombre de la cuenta/fuente</label>
            <input type="text" class="form-control" id="nombre_cuenta_gasto" name="nombre_cuenta_gasto" required>
          </div>
          <div class="form-group">
            <label for="color_cuenta_gasto">Color (opcional)</label>
            <input type="color" class="form-control" id="color_cuenta_gasto" name="color_cuenta_gasto" value="#47478D">
          </div>
          <div class="form-group">
            <label for="tipo_cuenta_gasto">Tipo de cuenta</label>
            <select class="form-control" id="tipo_cuenta_gasto" name="tipo_cuenta_gasto" required>
              <option value="">Seleccione...</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="btn_guardar_cuenta_gasto">Guardar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Movimientos -->
<div class="modal fade" id="modal_movimiento_gasto" tabindex="-1" role="dialog" aria-labelledby="modalMovimientoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalMovimientoLabel">Registrar Movimiento</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form_movimiento_gasto">
          <div class="form-group">
            <label for="cuenta_movimiento_gasto">Cuenta</label>
            <select class="form-control" id="cuenta_movimiento_gasto" name="cuenta_movimiento_gasto" required></select>
          </div>
          <div class="form-group">
            <label for="tipo_movimiento_gasto">Tipo de movimiento</label>
            <select class="form-control" id="tipo_movimiento_gasto" name="tipo_movimiento_gasto" required></select>
          </div>
          <div class="form-group">
            <label for="valor_movimiento_gasto">Valor</label>
            <input type="number" class="form-control" id="valor_movimiento_gasto" name="valor_movimiento_gasto" min="1" required>
          </div>
          <div class="form-group">
            <label for="fecha_movimiento_gasto">Fecha</label>
            <input type="date" class="form-control" id="fecha_movimiento_gasto" name="fecha_movimiento_gasto" required value="<?php echo date('Y-m-d'); ?>">
          </div>
          <div class="form-group">
            <label for="descripcion_movimiento_gasto">Descripción</label>
            <textarea class="form-control" id="descripcion_movimiento_gasto" name="descripcion_movimiento_gasto" rows="2"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="btn_guardar_movimiento_gasto">Guardar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Ajustar Saldo -->
<div class="modal fade" id="modal_ajustar_saldo" tabindex="-1" role="dialog" aria-labelledby="modalAjustarSaldoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAjustarSaldoLabel">Ajustar Saldo de Cuenta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form_ajustar_saldo">
          <input type="hidden" id="ajustar_codigo_cuenta" name="codigo_cuenta">
          <div class="form-group">
            <label>Cuenta</label>
            <input type="text" class="form-control" id="ajustar_nombre_cuenta" readonly>
          </div>
          <div class="form-group">
            <label>Saldo Actual</label>
            <input type="text" class="form-control" id="ajustar_saldo_actual" readonly>
            <input type="hidden" id="saldo_actual" name="saldo_actual">
          </div>
          <div class="form-group">
            <label for="ajustar_nuevo_saldo">Nuevo Saldo</label>
            <input type="number" class="form-control" id="ajustar_nuevo_saldo" name="nuevo_saldo" min="0" step="100" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="btn_guardar_ajuste_saldo">Guardar Ajuste</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Transferir entre Cuentas -->
<div class="modal fade" id="modal_transferir_gasto" tabindex="-1" role="dialog" aria-labelledby="modalTransferirLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTransferirLabel">Transferir entre Cuentas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form_transferir_gasto">
          <div class="form-group">
            <label for="cuenta_origen_transferir">Cuenta Origen</label>
            <select class="form-control" id="cuenta_origen_transferir" name="cuenta_origen" required>
              <option value="">Seleccione cuenta origen...</option>
            </select>
          </div>
          <div class="form-group">
            <label for="cuenta_destino_transferir">Cuenta Destino</label>
            <select class="form-control" id="cuenta_destino_transferir" name="cuenta_destino" required>
              <option value="">Seleccione cuenta destino...</option>
            </select>
          </div>
          <div class="form-group">
            <label for="valor_transferir">Valor a Transferir</label>
            <input type="number" class="form-control" id="valor_transferir" name="valor" min="1" step="100" required>
          </div>
          <div class="form-group">
            <label for="fecha_transferir">Fecha de Transferencia</label>
            <input type="date" class="form-control" id="fecha_transferir" name="fecha" required value="<?php echo date('Y-m-d'); ?>">
          </div>
          <div class="form-group">
            <label for="descripcion_transferir">Descripción (opcional)</label>
            <textarea class="form-control" id="descripcion_transferir" name="descripcion" rows="2" placeholder="Ej: Transferencia para gastos de oficina"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="btn_guardar_transferir">Realizar Transferencia</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Editar/Detalle Cuenta -->
<div class="modal fade" id="modal_editar_cuenta_gasto" tabindex="-1" role="dialog" aria-labelledby="modalEditarCuentaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarCuentaLabel">Editar Cuenta/Fuente</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" id="tabCuentaGasto" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="tab-editar-cuenta" data-toggle="tab" href="#editar-cuenta" role="tab">Editar</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="tab-movimientos-cuenta" data-toggle="tab" href="#movimientos-cuenta" role="tab">Movimientos</a>
          </li>
        </ul>
        <div class="tab-content mt-3">
          <div class="tab-pane fade show active" id="editar-cuenta" role="tabpanel">
            <form id="form_editar_cuenta_gasto">
              <input type="hidden" id="editar_codigo_cuenta_gasto" name="codigo">
              <div class="form-group">
                <label for="editar_nombre_cuenta_gasto">Nombre</label>
                <input type="text" class="form-control" id="editar_nombre_cuenta_gasto" name="nombre" required>
              </div>
              <div class="form-group">
                <label for="editar_color_cuenta_gasto">Color</label>
                <input type="color" class="form-control" id="editar_color_cuenta_gasto" name="color">
              </div>
              <div class="form-group">
                <label for="editar_tipo_cuenta_gasto">Tipo</label>
                <select class="form-control" id="editar_tipo_cuenta_gasto" name="tipo" required></select>
              </div>
              <div class="form-group">
                <label for="editar_estado_cuenta_gasto">Estado</label>
                <select class="form-control" id="editar_estado_cuenta_gasto" name="estado">
                  <option value="1">Activa</option>
                  <option value="0">Inactiva</option>
                </select>
              </div>
            </form>
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-danger" id="btn_eliminar_cuenta_gasto"><i class="fas fa-trash"></i> Eliminar (Desactivar)</button>
              <button type="button" class="btn btn-success" id="btn_guardar_editar_cuenta_gasto">Guardar Cambios</button>
            </div>
          </div>
          <div class="tab-pane fade" id="movimientos-cuenta" role="tabpanel">
            <div id="div_tabla_movimientos_cuenta"></div>
            <nav>
              <ul class="pagination justify-content-center" id="paginacion_movimientos_cuenta"></ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
// Mostrar el modal al hacer clic en el botón
$('#btn_nueva_cuenta_gasto').on('click', function() {
    rellenar_select('tbl_cuentas_tipo', 'codigo', 'nombre', 'codigo=2 or codigo=3', 'tipo_cuenta_gasto', '');
    $('#modal_nueva_cuenta_gasto').modal('show');
});
function cargarListadoCuentasGasto() {
    $.ajax({
        url: 'ajax/listar_cuentas_gasto.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            var html = '<div class="table-responsive"><table class="table table-striped table-hover table-bordered" style="background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px #0001;">';
            html += '<thead><tr><th>Nombre</th><th>Tipo</th><th>Color</th><th>Saldo Actual</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>';
            
            var totalSaldos = 0;
            
            if (data.length === 0) {
                html += '<tr><td colspan="6" class="text-center">No hay cuentas registradas.</td></tr>';
            } else {
                data.forEach(function(cuenta) {
                    html += '<tr>';
                    html += '<td>' + cuenta.nombre + '</td>';
                    html += '<td>' + (cuenta.tipo_nombre || '-') + '</td>';
                    html += '<td><span style="display:inline-block;width:24px;height:24px;background:' + (cuenta.color || '#ccc') + ';border-radius:4px;"></span></td>';
                    html += '<td>' + (cuenta.saldo_actual !== null ? parseFloat(cuenta.saldo_actual).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }) : '-') + '</td>';
                    html += '<td>' + (cuenta.estado == 1 ? 'Activa' : 'Inactiva') + '</td>';
                    html += '<td>';
                    html += '<button class="btn btn-sm btn-info mr-1 btn-editar-cuenta" data-codigo="' + cuenta.codigo + '"><i class="fas fa-edit"></i></button>';
                    html += '<button class="btn btn-sm btn-warning mr-1 btn-ajustar-saldo" data-codigo="' + cuenta.codigo + '" data-nombre="' + cuenta.nombre + '" data-saldo="' + cuenta.saldo_actual + '"><i class="fas fa-balance-scale"></i></button>';
                    html += '</td>';
                    html += '</tr>';
                    
                    // Sumar saldos de cuentas activas
                    if (cuenta.estado == 1 && cuenta.saldo_actual !== null) {
                        totalSaldos += parseFloat(cuenta.saldo_actual);
                    }
                });
            }
            html += '</tbody></table></div>';
            $('#div_listado_cuentas_gasto').html(html);
            
            // Actualizar totalizador
            $('#total_saldos').text(totalSaldos.toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }));
        }
    });
}
// Cargar el listado al iniciar y al crear nueva cuenta
$(document).ready(function () {
    cargarListadoCuentasGasto();
});
// Aquí irá la lógica para guardar la cuenta/fuente
$('#btn_guardar_cuenta_gasto').on('click', function() {
    var nombre = $('#nombre_cuenta_gasto').val().trim();
    var color = $('#color_cuenta_gasto').val();
    var tipo_cuenta = $('#tipo_cuenta_gasto').val();
    if (!nombre || !tipo_cuenta) {
        alert('Por favor, complete todos los campos obligatorios.');
        return;
    }
    $.ajax({
        url: 'ajax/guardar_cuenta_gasto.php',
        method: 'POST',
        data: {
            nombre: nombre,
            color: color,
            tipo_cuenta: tipo_cuenta
        },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                alert(res.msg);
                $('#modal_nueva_cuenta_gasto').modal('hide');
                $('#form_nueva_cuenta_gasto')[0].reset();
                cargarListadoCuentasGasto(); // Recargar listado
            } else {
                alert(res.msg);
            }
        },
        error: function() {
            alert('Error al guardar la cuenta/fuente.');
        }
    });
});
$('#btn_movimientos_gasto').on('click', function() {
    rellenar_select('tbl_cuentas', 'codigo', 'nombre', '', 'cuenta_movimiento_gasto', '');
    rellenar_select('tbl_cuentas_tipo', 'codigo', 'nombre', 'codigo=2 or codigo=3', 'tipo_movimiento_gasto', '');
    $('#modal_movimiento_gasto').modal('show');
});
// Aquí irá la lógica para guardar el movimiento
$('#btn_guardar_movimiento_gasto').on('click', function() {
    var cuenta_codigo = $('#cuenta_movimiento_gasto').val();
    var tipo_movimiento = $('#tipo_movimiento_gasto').val();
    var valor = $('#valor_movimiento_gasto').val();
    var fecha = $('#fecha_movimiento_gasto').val();
    var descripcion = $('#descripcion_movimiento_gasto').val().trim();

    if (!cuenta_codigo || !tipo_movimiento || valor === '' || valor < 1 || !fecha) {
        alert('Por favor, complete todos los campos obligatorios.');
        return;
    }

    $.ajax({
        url: 'ajax/guardar_movimiento_gasto.php',
        method: 'POST',
        data: {
            cuenta_codigo: cuenta_codigo,
            tipo_movimiento: tipo_movimiento,
            valor: valor,
            fecha: fecha,
            descripcion: descripcion
        },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                alert(res.msg);
                $('#modal_movimiento_gasto').modal('hide');
                $('#form_movimiento_gasto')[0].reset();
                // Recargar saldo de la cuenta seleccionada
                cargarListadoCuentasGasto();
            } else {
                alert(res.msg);
            }
        },
        error: function() {
            alert('Error al registrar el movimiento.');
        }
    });
});
// Abrir modal de ajuste de saldo
$(document).on('click', '.btn-ajustar-saldo', function() {
    var codigo = $(this).data('codigo');
    var nombre = $(this).data('nombre');
    var saldo = $(this).data('saldo');
    
    $('#ajustar_codigo_cuenta').val(codigo);
    $('#ajustar_nombre_cuenta').val(nombre);
    $('#ajustar_saldo_actual').val(parseFloat(saldo).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }));
    $('#saldo_actual').val(saldo);
    $('#ajustar_nuevo_saldo').val('');
    $('#modal_ajustar_saldo').modal('show');
});
// Guardar ajuste de saldo
$('#btn_guardar_ajuste_saldo').on('click', function() {
    var codigo_cuenta = $('#ajustar_codigo_cuenta').val();
    var nuevo_saldo = parseFloat($('#ajustar_nuevo_saldo').val());
    var saldo_actual = parseFloat($('#saldo_actual').val());
    
    if (isNaN(nuevo_saldo) || nuevo_saldo < 0) {
        alert('Por favor, ingrese un valor válido para el nuevo saldo.');
        return;
    }
    
    $.ajax({
        url: 'ajax/ajustar_saldo_cuenta.php',
        method: 'POST',
        data: {
            codigo_cuenta: codigo_cuenta,
            nuevo_saldo: nuevo_saldo,
            saldo_actual: saldo_actual
        },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                alert(res.msg);
                $('#modal_ajustar_saldo').modal('hide');
                cargarListadoCuentasGasto();
            } else {
                alert(res.msg);
            }
        },
        error: function() {
            alert('Error al ajustar el saldo.');
        }
    });
});
// Abrir modal de transferencia
$('#btn_transferir_gasto').on('click', function() {
    rellenar_select('tbl_cuentas', 'codigo', 'nombre', '', 'cuenta_origen_transferir', '');
    rellenar_select('tbl_cuentas', 'codigo', 'nombre', '', 'cuenta_destino_transferir', '');
    $('#modal_transferir_gasto').modal('show');
});
// Guardar transferencia
$('#btn_guardar_transferir').on('click', function() {
    var cuenta_origen = $('#cuenta_origen_transferir').val();
    var cuenta_destino = $('#cuenta_destino_transferir').val();
    var valor = $('#valor_transferir').val();
    var fecha = $('#fecha_transferir').val();
    var descripcion = $('#descripcion_transferir').val().trim();

    if (!cuenta_origen || !cuenta_destino || valor === '' || valor < 1 || !fecha) {
        alert('Por favor, complete todos los campos obligatorios.');
        return;
    }

    if (cuenta_origen === cuenta_destino) {
        alert('No puede transferir a la misma cuenta. Seleccione cuentas diferentes.');
        return;
    }

    $.ajax({
        url: 'ajax/transferir_gasto.php',
        method: 'POST',
        data: {
            cuenta_origen: cuenta_origen,
            cuenta_destino: cuenta_destino,
            valor: valor,
            fecha: fecha,
            descripcion: descripcion
        },
        dataType: 'json',
        success: function(res) {
          console.log(res);
            if (res.success) {
                alert(res.msg);
                $('#modal_transferir_gasto').modal('hide');
                $('#form_transferir_gasto')[0].reset();
                cargarListadoCuentasGasto(); // Recargar listado
            } else {
                alert(res.msg);
            }
        },
        error: function() {
            alert('Error al realizar la transferencia.');
        }
    });
});
// Abrir modal de edición al hacer clic en editar
$(document).on('click', '.btn-editar-cuenta', function() {
    var codigo = $(this).data('codigo');
    // Cargar datos de la cuenta
    $.ajax({
        url: 'ajax/obtener_cuenta_gasto.php',
        method: 'GET',
        data: {codigo: codigo},
        dataType: 'json',
        success: function(cuenta) {
            $('#editar_codigo_cuenta_gasto').val(cuenta.codigo);
            $('#editar_nombre_cuenta_gasto').val(cuenta.nombre);
            $('#editar_color_cuenta_gasto').val(cuenta.color || '#47478D');
            rellenar_select('tbl_cuentas_tipo', 'codigo', 'nombre', '', 'editar_tipo_cuenta_gasto', cuenta.tipo);
            $('#editar_estado_cuenta_gasto').val(cuenta.estado);
            $('#modal_editar_cuenta_gasto').modal('show');
            cargarMovimientosCuenta(cuenta.codigo, 1);
        }
    });
});
// Guardar cambios de la cuenta
$('#btn_guardar_editar_cuenta_gasto').on('click', function() {
    var datos = $('#form_editar_cuenta_gasto').serialize();
    $.ajax({
        url: 'ajax/editar_cuenta_gasto.php',
        method: 'POST',
        data: datos,
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                alert(res.msg);
                $('#modal_editar_cuenta_gasto').modal('hide');
                cargarListadoCuentasGasto();
            } else {
                alert(res.msg);
            }
        }
    });
});
// Eliminar (desactivar) cuenta
$('#btn_eliminar_cuenta_gasto').on('click', function() {
    if (!confirm('¿Seguro que deseas desactivar esta cuenta?')) return;
    var codigo = $('#editar_codigo_cuenta_gasto').val();
    $.ajax({
        url: 'ajax/eliminar_cuenta_gasto.php',
        method: 'POST',
        data: {codigo: codigo},
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                alert(res.msg);
                $('#modal_editar_cuenta_gasto').modal('hide');
                cargarListadoCuentasGasto();
            } else {
                alert(res.msg);
            }
        }
    });
});
// Cargar movimientos paginados
function cargarMovimientosCuenta(codigo_cuenta, pagina) {
    $.ajax({
        url: 'ajax/listar_movimientos_cuenta.php',
        method: 'GET',
        data: {codigo_cuenta: codigo_cuenta, pagina: pagina},
        dataType: 'json',
        success: function(res) {
            var html = '<div class="table-responsive"><table class="table table-sm table-striped table-hover w-100">';
            html += '<thead><tr><th>Fecha</th><th>Tipo</th><th>Valor</th><th>Descripción</th></tr></thead><tbody>';
            if (res.movimientos.length === 0) {
                html += '<tr><td colspan="4" class="text-center">Sin movimientos</td></tr>';
            } else {
                res.movimientos.forEach(function(mov) {
                    html += '<tr>';
                    html += '<td>' + mov.fecha + '</td>';
                    html += '<td>' + mov.tipo_movimiento + '</td>';
                    html += '<td>' + parseFloat(mov.valor).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }) + '</td>';
                    html += '<td>' + (mov.descripcion || '-') + '</td>';
                    html += '</tr>';
                });
            }
            html += '</tbody></table></div>';
            $('#div_tabla_movimientos_cuenta').html(html);
            // Paginación
            var pagHtml = '';
            for (var i = 1; i <= res.total_paginas; i++) {
                pagHtml += '<li class="page-item' + (i === res.pagina_actual ? ' active' : '') + '"><a class="page-link" href="#" onclick="cargarMovimientosCuenta(' + codigo_cuenta + ',' + i + ');return false;">' + i + '</a></li>';
            }
            $('#paginacion_movimientos_cuenta').html(pagHtml);
        }
    });
}
// Cambiar de tab recarga movimientos
$('a[data-toggle="tab"][href="#movimientos-cuenta"]').on('shown.bs.tab', function (e) {
    var codigo = $('#editar_codigo_cuenta_gasto').val();
    if (codigo) cargarMovimientosCuenta(codigo, 1);
});
</script> 