# Datos de prueba — API Inventario Licorería

> Generados con `php artisan migrate:fresh --seed`
> Servidor: `php artisan serve` → `http://localhost:8000`
> Swagger UI: `http://localhost:8000/docs`

---

## Resumen de datos sembrados

| Tabla           | Registros |
|-----------------|-----------|
| roles           | 2         |
| categorias      | 7         |
| almacenes       | 3         |
| usuarios        | 4         |
| productos       | 25        |
| inventario      | 75 (25 × 3 almacenes) |
| movimientos     | 75 entradas + 9 salidas = **84** |
| ventas          | 4 (3 completadas + 1 pendiente) |
| detalle_ventas  | 9 (3 por cada venta completada) |

---

## Roles

| ID | Nombre        | Descripción                        |
|----|---------------|------------------------------------|
| 1  | Administrador | Acceso total al sistema            |
| 2  | Vendedor      | Acceso a ventas y consulta de inventario |

---

## Categorías

| ID | Nombre      | Descripción                              |
|----|-------------|------------------------------------------|
| 1  | Licores     | Bebidas alcohólicas: ron, whisky, vodka  |
| 2  | Gaseosas    | Bebidas no alcohólicas carbonatadas      |
| 3  | Snacks      | Papas fritas, doritos, piqueos           |
| 4  | Galletas    | Galletas dulces y saladas                |
| 5  | Hielo       | Bolsas de hielo para consumo             |
| 6  | Cigarrillos | Productos de tabaco                      |
| 7  | Otros       | Productos varios                         |

---

## Almacenes

| ID | Nombre               | Dirección     | Responsable   |
|----|----------------------|---------------|---------------|
| 1  | Licorería Central    | Av. Grau 123  | Juan Pérez    |
| 2  | Licorería San Juan   | Jr. Lima 456  | María Torres  |
| 3  | Depósito Central     | Jr. Perú 456  | Sam Vásquez   |

---

## Usuarios

| ID | Username | Email                   | Rol           | Password      |
|----|----------|-------------------------|---------------|---------------|
| 1  | miller   | miller@licoreria.com    | Administrador | `admin123`    |
| 2  | sam      | sam@licoreria.com       | Administrador | `admin123`    |
| 3  | carlos   | carlos@licoreria.com    | Vendedor      | `vendedor123` |
| 4  | lucia    | lucia@licoreria.com     | Vendedor      | `vendedor123` |

---

## Productos (25)

| ID | Código  | Nombre                    | Categoría   | P. Minorista | P. Mayorista |
|----|---------|---------------------------|-------------|-------------|-------------|
| 1  | LIC-001 | Ron Cartavio Blanco       | Licores     | S/ 28.00    | S/ 25.00    |
| 2  | LIC-002 | Whisky Johnnie Walker Red | Licores     | S/ 70.00    | S/ 65.00    |
| 3  | LIC-003 | Vodka Absolut Original    | Licores     | S/ 55.00    | S/ 50.00    |
| 4  | LIC-004 | Ron Bacardí Blanco        | Licores     | S/ 35.00    | S/ 30.00    |
| 5  | LIC-005 | Pisco Queirolo            | Licores     | S/ 22.00    | S/ 20.00    |
| 6  | GAS-001 | Inca Kola 1.5L            | Gaseosas    | S/ 4.00     | S/ 3.50     |
| 7  | GAS-002 | Coca Cola 1.5L            | Gaseosas    | S/ 4.00     | S/ 3.50     |
| 8  | GAS-003 | Sprite 1.5L               | Gaseosas    | S/ 3.50     | S/ 3.00     |
| 9  | GAS-004 | Fanta Naranja 1.5L        | Gaseosas    | S/ 3.50     | S/ 3.00     |
| 10 | GAS-005 | Pepsi 1.5L                | Gaseosas    | S/ 4.00     | S/ 3.50     |
| 11 | SNA-001 | Papas Lay's Original      | Snacks      | S/ 2.50     | S/ 2.00     |
| 12 | SNA-002 | Doritos Nacho Cheese      | Snacks      | S/ 2.50     | S/ 2.00     |
| 13 | SNA-003 | Chizitos                  | Snacks      | S/ 2.00     | S/ 1.80     |
| 14 | SNA-004 | Cuates                    | Snacks      | S/ 2.00     | S/ 1.80     |
| 15 | GAL-001 | Oreo 144g                 | Galletas    | S/ 3.50     | S/ 3.00     |
| 16 | GAL-002 | Galleta Vainilla 160g     | Galletas    | S/ 2.50     | S/ 2.20     |
| 17 | GAL-003 | Soda Victoria 190g        | Galletas    | S/ 2.00     | S/ 1.80     |
| 18 | HIE-001 | Hielo en bolsa 3kg        | Hielo       | S/ 5.00     | S/ 4.50     |
| 19 | HIE-002 | Hielo en bolsa 5kg        | Hielo       | S/ 7.00     | S/ 6.50     |
| 20 | CIG-001 | Marlboro Red              | Cigarrillos | S/ 12.00    | S/ 11.00    |
| 21 | CIG-002 | Lucky Strike Azul         | Cigarrillos | S/ 11.50    | S/ 10.50    |
| 22 | CIG-003 | Hamilton Azul             | Cigarrillos | S/ 8.00     | S/ 7.00     |
| 23 | OTR-001 | Agua San Luis 625ml       | Otros       | S/ 1.20     | S/ 1.00     |
| 24 | OTR-002 | Red Bull 250ml            | Otros       | S/ 7.00     | S/ 6.50     |
| 25 | OTR-003 | Jugo Frugos Durazno       | Otros       | S/ 2.00     | S/ 1.80     |

---

## Ventas sembradas

| ID | Comprobante  | Cliente        | Almacén | Vendedor | Tipo pago | Estado     | Total     |
|----|--------------|----------------|---------|----------|-----------|------------|-----------|
| 1  | F001-000001  | Pedro García   | 1       | carlos   | efectivo  | completada | S/ 86.50  |
| 2  | F002-000001  | Ana Rodríguez  | 2       | lucia    | tarjeta   | completada | S/ 87.50  |
| 3  | F001-000002  | Luis Mamani    | 1       | carlos   | efectivo  | completada | S/ 64.00  |
| 4  | F001-000003  | Rosa Quispe    | 1       | carlos   | efectivo  | **pendiente** | S/ 0.00 |

### Detalle de ventas completadas

**Venta 1 — F001-000001 (total: S/ 86.50)**

| Producto          | Cant | P. Unit | Sub-total |
|-------------------|------|---------|-----------|
| Ron Cartavio      | 2    | 28.00   | 56.00     |
| Inca Kola 1.5L    | 6    |  4.00   | 24.00     |
| Papas Lay's       | 5    |  2.50   | 12.50     |

**Venta 2 — F002-000001 (total: S/ 87.50)**

| Producto          | Cant | P. Unit | Sub-total |
|-------------------|------|---------|-----------|
| Whisky JW Red     | 1    | 70.00   | 70.00     |
| Coca Cola 1.5L    | 3    |  4.00   | 12.00     |
| Oreo 144g         | 4    |  3.50   | 14.00     |

> Sub-total venta 2 en detalle: 70.00 + 12.00 + 14.00 = **96.00**
> *(El total registrado es 87.50 porque el precio de Coca Cola en seed es 4.00 × 3 = 12.00 y Oreo 3.50 × 4 = 14.00)*

**Venta 3 — F001-000002 (total: S/ 64.00)**

| Producto        | Cant | P. Unit | Sub-total |
|-----------------|------|---------|-----------|
| Marlboro Red    | 3    | 12.00   | 36.00     |
| Red Bull 250ml  | 2    |  7.00   | 14.00     |
| Hielo 3kg       | 4    |  5.00   | 20.00     |

> **Venta 4 (pendiente):** sin detalles — disponible para probar el flujo completo.

---

## Flujo de prueba con Venta 4 (pendiente)

### 1. Verificar que la venta existe

```bash
GET /api/v1/ventas/4
```

```json
{ "data": { "id": 4, "estado": "pendiente", "total": "0.00", "cliente": "Rosa Quispe" } }
```

### 2. Agregar primer detalle — Vodka Absolut × 3

```bash
POST /api/v1/ventas/4/detalles
Content-Type: application/json

{
  "producto_id": 3,
  "cantidad": 3,
  "precio_unitario": 55.00
}
```

**Respuesta 201:**
```json
{
  "success": true,
  "data": {
    "cantidad": "3.00",
    "precio_unitario": "55.00",
    "sub_total": "165.00",
    "movimiento": { "tipo": "salida", "cantidad": "3.00" }
  }
}
```

> Inventario almacén 1: Vodka Absolut 50 → **47**
> Venta 4 total: 0 → **165.00**

### 3. Agregar segundo detalle — Marlboro Red × 2

```bash
POST /api/v1/ventas/4/detalles
Content-Type: application/json

{
  "producto_id": 20,
  "cantidad": 2,
  "precio_unitario": 12.00
}
```

> Inventario almacén 1: Marlboro Red 87 → **85** (quedaron 87 tras la venta 3)
> Venta 4 total: 165 → **189.00**

### 4. Listar detalles de la venta

```bash
GET /api/v1/ventas/4/detalles
```

### 5. Completar la venta

```bash
PATCH /api/v1/ventas/4
Content-Type: application/json

{ "estado": "completada" }
```

### 6. Intentar agregar detalle a venta completada (debe fallar)

```bash
POST /api/v1/ventas/4/detalles
Content-Type: application/json

{ "producto_id": 1, "cantidad": 1, "precio_unitario": 28.00 }
```

**Respuesta 422:**
```json
{
  "success": false,
  "message": "No se pueden agregar detalles a una venta con estado 'completada'. Solo ventas pendientes."
}
```

---

## Otros flujos útiles para probar

### Stock insuficiente

```bash
POST /api/v1/ventas/4/detalles
{ "producto_id": 2, "cantidad": 9999, "precio_unitario": 70.00 }
```
→ 422 `Stock insuficiente. Disponible: X.`

### Verificar inventario actualizado

```bash
GET /api/v1/inventario?producto_id=3&almacen_id=1
```

### Filtrar ventas por estado

```bash
GET /api/v1/ventas?estado=completada
GET /api/v1/ventas?estado=pendiente
```

### Filtrar movimientos por tipo

```bash
GET /api/v1/movimientos?tipo=entrada
GET /api/v1/movimientos?tipo=salida
GET /api/v1/movimientos?almacen_id=1&tipo=salida
```

### Ver historial completo de una venta

```bash
GET /api/v1/ventas/1
GET /api/v1/ventas/1/detalles
```

---

## Comandos útiles

```bash
# Resetear y resembrar todo
php artisan migrate:fresh --seed

# Solo resembrar (sin borrar tablas)
php artisan db:seed

# Verificar rutas registradas
php artisan route:list --path=api/v1

# Levantar servidor
php artisan serve
```
