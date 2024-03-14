# Auth API
API de autenticação simples, baseada em Laravel

## 1. Introdução
Essa é uma API simples e básica, demontrando as funcionalidades de autenticação de usuários presentes no Laravel. Utiliza a última versão do framework, o Laravel 11.

## 2. Instalação
**2.1** No terminal (ou GitBash), faça um clone do repositório:
```
git clone https://github.com/lantonello/auth-api.git
```

**2.2** Ainda pelo terminal (ou GitBash), acessa a pasta e execute a instalação dos pacotes:

```
composer update
```

**2.3** Em seu servidor MySQL, crie um banco de dados. Configure a conexão no arquivo `.env` do projeto.

**2.4** De volta ao terminal, crie as tabelas necessárias através do comando:
```
php artisan migrate
```

**2.5** Utilize o método que achar mais conveniente para servir a aplicação, como Apache, nginx ou o próprio PHP.

## 2. Estrutura
### 2.1 Validadores de Entrada de Dados
`App\Validators\*`

Cada `Controller` da aplicação possui um objeto responsável pela validação dos dados de entrada, que contém as regras de validação de cada endpoint que é relacionado.

Essas classes foram criadas porque não faz muito sentido o objeto `Request` do Laravel ser responsável pela validação de dados de entrada.

### 2.2 Repositório de Dados
`App\Repositories\*`

Essa API possui um padrão simples de repositório de dados. Cada entidade do banco de dados, que é representada por uma classe `Model`, deve possuir um objeto `Repository`.

Como essa aplicação só trabalha com a tabela de usuários, então só possui a classe `UserRepository`.

### 2.3 Helpers
`App\Helpers\*`

A aplicação possui duas classes que facilitam o desenvolvimento:
- `JsonWebToken`: Responsável por gerar um token válido, no padrão **JWT**
- `JsonResponse`: Padronização dos dados de resposta da API

### 2.4 Localização
A aplicação responde com mensagens em Inglês ou em Português, sendo que o padrão é o Inglês.

Para ver as mensagens em Português, altere o arquivo `.env`:

```
APP_LOCALE=pt_BR
```

## 3. Endpoints
### 3.1 Públicos

- **POST** `/api/signup`

    Esse endpoint é responsável pelo registro de um novo usuário na base de dados.

    **Corpo da requisição:**
    - `name`: Nome do usuário
    - `email`: Endereço de e-mail do usuário
    - `password`: Senha do usuário
---
- **POST** `/api/signin`
    
    Endpoint responsável pela autenticação do usuário.

    **Corpo da requisição:**
    - `email`: Endereço de e-mail do usuário
    - `password`: Senha do usuário
    
    > Esse endpoint retorna um `token` no padrão JWT que deve ser utilizado em todos os endpoints protegidos.
---
- **POST** `/api/forgot-password`
    
    Envia um e-mail para o usuário com link para redefinição de senha (_não funcional_)

    **Corpo da requisição:**
    - `email`: Endereço de e-mail do usuário
---
- **POST** `/api/reset-password`
    
    Endpoint para redefinição de senha do usuário

    **Corpo da requisição:**
    - `token`: Código recebido por e-mail
    - `email`: Endereço de e-mail do usuário
    - `password`: Nova senha
    - `password_confirmation`: Confirmação da nova senha

### 3.2 Protegidos
Todos os endpoints protegidos são verificados através de um `Middleware` que confere a validade do `token`, que deve ser passado pelo cabeçalho `Authorization Bearer {TOKEN_AQUI}`

- **GET** `/api/users`
    
    Retorna uma lista de usuários

    **Parâmetros de Query String:**
    - `with_deleted`: Indica se registros excluídos devem ser retornados na lista (`true`) ou não.
---
- **GET** `/api/users/{id}`
    
    Retorna um único registro de usuário

    **Parâmetros de Path:**
    - `id`: ID do usuário na base de dados
---
- **POST** `/api/users`
    
    Adiciona um novo registro de usuário. O corpo da requisição são os mesmos do endpoint `/api/signup`.
---
- **PATCH** `/api/users/{id}`
    
    Atualiza os dados de um registro de usuário

    **Parâmetros de Path:**
    - `id`: ID do usuário na base de dados

    **Corpo da requisição:**
    - `name`: Nome do usuário
    - `email`: Endereço de e-mail do usuário
---
- **DELETE** `/api/users/{id}`
    
    Exclui um usuário utilizando a funcionalidade de `softDelete` do ORM do Laravel, o Eloquent.

    **Parâmetros de Path:**
    - `id`: ID do usuário na base de dados