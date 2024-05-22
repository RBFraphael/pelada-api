![Pelada Logo](https://api.pelada.rbfstudio.net/logo.png)

# API do projeto Pelada.com

## Sobre o projeto
O Pelada.com consiste em um sistema de pequeno porte para organização de jogos de futebol entre amigos, normalmente em campos Society, popularmente conhecidos como "peladas".

## Inicializando o projeto
1. Faça o clone do projeto `git clone https://github.com/rbfraphael/pelada-api.git`
2. Renomeie o arquivo `.env.example` para `.env`
3. Utilizando o Composer, instale as dependências do projeto `composer install`
4. Caso deseje utilizar um banco de dados diferente do SQLite, é necessário atualizar as informações de conexão do banco no arquivo `.env`. Por padrão, o banco configurado é o SQLite, útil para fins de teste e validação.
5. Inicialize o banco de dados executando as migrations `php artisan migrate:fresh`
    - Pode utilizar, posteriormente, o comando `php artisan db:seed --class="AdminsSeeder"`, que fará o cadastro apenas do usuário administrador padrão
    - Se desejar, pode adicionar a flag `--seed` ao comando principal (`php artisan migrate:fresh --seed`) para carregar o banco de dados com usuários, jogadores e jogos aleatoriamente gerados, além do usuário administrador padrão
6. Gere a chave de segurança da aplicação `php artisan key:generate`
7. Para a aplicação funcionar corretamente, é necessário gerar uma chave secreta para a autenticação via JWT `php artisan jwt:secret`
8. Se você for executar a aplicação utilizando um servidor web (Apache, NGINX ou outros), altere a propriedade `APP_URL` no arquivo `.env`
9. Para que sejam feitos corretamente os mapeamentos de URL para o front-end, é necessário definir o valor da propriedade `FRONTEND_URL` no arquivo `.env`
10. Para o envio de e-mails, é necessário configurar corretamente os parâmetros de SMTP no arquivo `.env`, através das propriedades `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD` e `MAIL_ENCRYPTION`

## Executando o projeto
Feita toda a configuração necessária para o projeto, basta acessar o endereço correspondente à aplicação de front-end ([Repositório do Front-end](https://github.com/rbfraphael/pelada-frontend)) devidamente configurada para consumir a API desta instância.

## Utilizando o Postman
Incluso neste repositório, há um arquivo JSON correspondente à uma collection para a aplicação [Postman](https://www.postman.com/), que pode ser utilizada para testes da API sem a aplicação front-end. Para isso, basta importar a collection na interface do Postman.

**Nota 1:** Os endpoint necessitam de autenticação através do cabeçalho `Authorization: Bearer <token>`. Foi feita uma configuração nessa collection para auxiliar nos testes, onde, ao realizar a autenticação do usuário, as chamadas subsequentes aos endpoints terão o cabeçalho automaticamente inserido.

**Nota 2:** Os tokens de autenticação possuem uma vida útil de 60 minutos (1 hora). Após esse período, o token será invalidado e não será possível consumir endpoints. Para continuar, você pode executar a chamada ao endpoint `/auth/refresh`, que gera um novo token de autenticação baseado no token anterior (método utilizado automaticamente pelo front-end através de interceptors) ou realizar uma nova autenticação através do endpoint `/auth/login`.

**Nota 3:** Os endpoint `/invites/<invite_id>/confirm` e `/invites/<invite_id>/reject` não necessitam de autenticação, pois o intuito deles é a utilização através do e-mail enviado aos jogadores quando um novo convite para participação em um jogo é enviado.

## Informações complementares

### Design patterns
Foi utilizado, além da própria estrutura do framework Laravel, o design pattern de Repository, onde todos os repositórios estão localizados em `/app/Repositories`. Além disso, alguns recursos auxiliares foram criados, como Enums (`/app/Enums`) e Interfaces (`/app/Interfaces`).

### Algoritmo de geração e balanceamento dos times
O algoritmo de geração e balanceamento dos times pode ser encontrado no arquivo `/app/Repositories/TeamsRepository`, no método `generateTeams()`. O algoritmo consiste em listar todos os jogadores confirmados, ordenados de forma decrescente pelo seu nível de habilidade; determinar a quantidade de times a serem criados, com base no número de jogadores por time definido no jogo e na quantidade de jogadores confirmados; distribuir os jogadores de forma balanceada em cada time, calculando o nível de cada time para realizar o balanceamento durante a distribuição dos jogadores; colocar os jogadores "restantes" (cuja contagem não compreende um time completo, conforme número de jogadores especificado no jogo) em um time parcial; e, por fim, persistir todos os times completos e o time parcial, quando houver, em banco de dados.