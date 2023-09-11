### Onfly API Expenses

Esse projeto foi desenvolvido para participar da etapa técnica do processo seletivo da Onfly (<https://www.onfly.com.br/>).

A API conta principalmente com duas funcinalidades: Autenticação e Gerenciamento de despesas.

Outras particularidades são encontradas neste projeto, como:

- Autorização (Policies).
- Notificações por e-mail.
- Testes unitários e de integração.
- Documentação Swagger.

### Pré-requisitos
Antes de começar, verifique se você atende aos seguintes requisitos:

- **Docker** instalado e configurado em sua máquina.
- **Provedor de e-mails** para testes.
- Conhecimentos básicos de Laravel 10.
- Conhecimentos básicos de Docker.

### Configuração do Ambiente
Siga estas etapas para configurar o ambiente de desenvolvimento:

Clone Repositório

```sh
git clone -b https://github.com/raphaeldcout/onfly-api.git onfly-test-api
```

```sh
cd onfly-test-api
```

Crie o Arquivo .env

```sh
cp .env.example .env
```

Atualize as variáveis de ambiente do arquivo .env

```dosini
APP_NAME=Onfly
APP_URL=http://localhost:9001

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="nao-responda@example.com.br"
MAIL_FROM_NAME="${APP_NAME}"
```

Suba os containers do projeto

```sh
docker-compose up -d
```

Acesse o container app

```sh
docker-compose exec app bash
```

Instale as dependências do projeto

```sh
composer install
```

Gere a key do projeto Laravel

```sh
php artisan key:generate
```

Seu ambiente de desenvolvimento agora está configurado e em execução.

### Testes

Para executar testes PHPUnit, use o seguinte comando:

```sh
php artisan test
```

### Documentação API

A documentação da API é gerada automaticamente com o L5 Swagger PHP. Você pode acessar a documentação em:

```sh
http://localhost:9001/api/documentation
```

Acesse o projeto
[http://localhost:9001](http://localhost:9001)
