# Backend PHP Clean Architecture

API REST construída com PHP 8.3 utilizando Clean Architecture, DDD, TDD, PostgreSQL, Nginx e Docker.

## Tecnologias

* PHP 8.3
* PostgreSQL
* Nginx
* Docker
* PHPUnit
* PHPStan
* PHP CS Fixer

## Arquitetura

O projeto segue os princípios de Clean Architecture e Domain-Driven Design (DDD).

```text
src/
├── Shared/
│   ├── Domain/
│   ├── Infrastructure/
│   └── Interface/
│
└── User/
    ├── Domain/
    ├── Application/
    ├── Infrastructure/
    └── Interface/
```

### Fluxo da aplicação

```text
HTTP Request
      ↓
Router
      ↓
Controller
      ↓
Use Case
      ↓
Repository
      ↓
PostgreSQL
```

### Fluxo completo

```text
Cliente
    ↓
Nginx
    ↓
PHP-FPM
    ↓
public/index.php
    ↓
Router
    ↓
Controller
    ↓
UseCase
    ↓
Repository
    ↓
PostgreSQL
```

## Estrutura do domínio

### User

Entidade principal do sistema.

Campos:

* id
* name
* email
* passwordHash
* createdAt
* updatedAt
* deletedAt

Funcionalidades:

* criação de usuário
* alteração de nome
* alteração de e-mail
* alteração de senha
* soft delete

## Casos de uso implementados

### Create User

```text
POST /users
```

### Get User By Id

```text
GET /users/{id}
```

### List Users

```text
GET /users
```

### Update User

```text
PUT /users/{id}
```

### Delete User

```text
DELETE /users/{id}
```

## Executando o projeto

### Build

```bash
make build
```

### Subir containers

```bash
make up
```

### Derrubar containers

```bash
make down
```

## Qualidade

### Executar todos os checks

```bash
make quality
```

### Testes unitários

```bash
make test-unit
```

### Testes de integração

```bash
make test-integration
```

## Banco de dados

Executar migrations:

```bash
docker compose exec database \
psql -U app -d app \
-f /database/migrations/001_create_user_table.sql
```

Verificar tabela:

```bash
docker compose exec database \
psql -U app -d app \
-c "\d users"
```

## Endpoints

### Listar usuários

```bash
curl http://localhost:8080/users
```

Resposta:

```json
{
  "data": []
}
```

---

### Criar usuário

```bash
curl -X POST http://localhost:8080/users \
-H "Content-Type: application/json" \
-d '{
  "name":"John Doe",
  "email":"john@example.com",
  "password":"StrongPassword123!"
}'
```

Resposta:

```json
{
  "id":"550e8400-e29b-41d4-a716-446655440000",
  "name":"John Doe",
  "email":"john@example.com"
}
```

---

### Buscar usuário por ID

```bash
curl http://localhost:8080/users/{id}
```

Resposta:

```json
{
  "id":"550e8400-e29b-41d4-a716-446655440000",
  "name":"John Doe",
  "email":"john@example.com"
}
```

---

### Atualizar usuário

```bash
curl -X PUT http://localhost:8080/users/{id} \
-H "Content-Type: application/json" \
-d '{
  "name":"Jane Doe",
  "email":"jane@example.com",
  "password":"NewStrongPassword123!"
}'
```

Resposta:

```json
{
  "id":"550e8400-e29b-41d4-a716-446655440000",
  "name":"Jane Doe",
  "email":"jane@example.com"
}
```

---

### Remover usuário

```bash
curl -X DELETE http://localhost:8080/users/{id}
```

Resposta:

```http
204 No Content
```

## Tratamento de erros

A API utiliza RFC 9457 (Problem Details).

Exemplo:

```json
{
  "type": "https://api.example.com/problems/email-already-exists",
  "title": "Email already exists",
  "status": 409,
  "detail": "Email already exists."
}
```

Possíveis erros:

| Status | Erro                  |
| ------ | --------------------- |
| 400    | Invalid Request       |
| 404    | User Not Found        |
| 409    | Email Already Exists  |
| 500    | Internal Server Error |

## Cobertura

O projeto possui:

* testes unitários
* testes de integração
* análise estática com PHPStan
* arquitetura desacoplada
* persistência PostgreSQL real
* soft delete
* RFC 9457 para erros

## Próximos passos

* OpenAPI / Swagger
* Paginação
* Filtros
* Autenticação JWT
* Refresh Token
* Observabilidade
* CI/CD
* Kubernetes
* Event Driven Architecture
