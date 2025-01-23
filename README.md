# API - Billetera Virtual

Este proyecto es una API de billetera virtual que permite realizar varias operaciones, como el registro de clientes, recarga de billetera, pago de compras, confirmación de pagos y consulta de saldo. A continuación se detallan las rutas disponibles, los parámetros que reciben y lo que retornan.

## Rutas

### 1. Registro de Cliente
- **Método**: `POST`
- **Ruta**: `/api/register-client`
- **Descripción**: Registra un nuevo cliente en el sistema.
- **Parámetros**:
  - `document` (required): Documento de identificación del cliente (solo números).
  - `name` (required): Nombre completo del cliente.
  - `email` (required): Correo electrónico del cliente.
  - `phone` (required): Número de teléfono del cliente (formato de 10 a 15 dígitos).
- **Respuesta**:
  ```json
  {
    "success": true,
    "cod_error": "00",
    "message_error": "Cliente registrado con éxito",
    "data": {
      "id": 1,
      "document": "12345678",
      "name": "Juan Perez",
      "email": "juan.perez@example.com",
      "phone": "1234567890"
    }
  }
  ```
### 2. Recarga de Billetera
- **Método**: `POST`
- **Ruta**: `/api/load-wallet`
- **Descripción**: Permite recargar la billetera de un cliente. El valor especificado se agrega al saldo actual del cliente. 
- **Parámetros**:
  - `document` (required): Documento de identificación del cliente (solo números).
  - `phone` (required): Número de teléfono del cliente.
  - `value` (required): Monto a recargar (número positivo). Este es el valor que se añadirá al saldo actual de la billetera.
  
#### Ejemplo de Petición:

```bash
POST /api/load-wallet
Content-Type: application/json
{
  "document": "12345678",
  "phone": "1234567890",
  "value": 500
}
  ```
```bash
{
  "success": true,
  "cod_error": "00",
  "message_error": "Billetera recargada con éxito",
  "data": {
    "document": "12345678",
    "phone": "1234567890",
    "new_balance": 2000
  }
}
  ```

### 3. Pagar
- **Método**: `POST`
- **Ruta**: `/api/pay`
- **Descripción**: Permite realizar el pago con el saldo de la billetera. Un token de 6 dígitos es enviado al correo del usuario para confirmación.
- **Parámetros**:
  - `document` (required): Documento de identificación del cliente (solo números).
  - `phone` (required): Número de teléfono del cliente.
  - `amount` (required): Monto de la compra a pagar (número positivo).
  
#### Ejemplo de Petición:

```bash
POST /api/pay
Content-Type: application/json
{
  "document": "12345678",
  "phone": "1234567890",
  "amount": 100
}
```
### 4. Confirmar Pago
- **Método**: `POST`
- **Ruta**: `/api/confirm-payment`
- **Descripción**: Confirma el pago de una compra utilizando el `session_id` y el token enviado al correo del cliente. Si la validación es exitosa, se descuenta el monto de la billetera.
- **Parámetros**:
  - `session_id` (required): ID de la sesión generado al momento de realizar el pago.
  - `token` (required): Token enviado al correo del cliente para confirmar la compra.

#### Ejemplo de Petición:

```bash
POST /api/confirm-payment
Content-Type: application/json
{
  "session_id": "df9f221e-260f-453d-b2d7-0c7daf2ad25a",
  "token": "123456"
}
```
### 5. Consultar Saldo
- **Método**: `POST`
- **Ruta**: `/api/consult-balance`
- **Descripción**: Consulta el saldo de la billetera de un cliente usando su `documento` y `número de celular`. Los dos valores deben coincidir para obtener el saldo.
- **Parámetros**:
  - `document` (required): Documento del cliente.
  - `phone` (required): Número de celular del cliente.

#### Ejemplo de Petición:

```bash
POST /api/consult-balance
Content-Type: application/json
{
  "document": "12345678",
  "phone": "9876543210"
}

