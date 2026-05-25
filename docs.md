# Joga Facil - Documentacao do Projeto

## Visao geral

O Joga Facil e uma aplicacao web em PHP para encontrar, cadastrar, moderar e reservar quadras esportivas. A plataforma funciona como um marketplace: locatarios procuram arenas e reservam horarios, locadores cadastram e gerenciam suas quadras, gerentes podem administrar quadras vinculadas e administradores moderam cadastros e usuarios.

O projeto roda em Docker com PHP 8.2, Apache e MySQL 8.0. O codigo da aplicacao fica dentro de `src`, que e montado como raiz web do Apache no container.

## Funcionalidades principais

- Listagem publica de arenas ativas na pagina inicial.
- Filtro visual por modalidade na home.
- Cadastro e login de locatarios.
- Cadastro e login de locadores.
- Login de gerentes.
- Login de administradores.
- Painel do locador para cadastrar, visualizar, editar e excluir arenas.
- Cadastro de gerente vinculado a uma ou mais quadras do locador.
- Painel do gerente com acesso somente as quadras vinculadas.
- Painel administrativo com listagem de usuarios e moderacao de novas arenas.
- Aprovacao e rejeicao de arenas por administrador.
- Pagina publica de detalhes da arena com informacoes, contato, facilidades e horarios.
- Criacao automatica de horarios do dia conforme funcionamento da quadra.
- Reserva de horarios por locatarios autenticados.
- Modo lobby na reserva (público ou privado com código definido pelo organizador).
- Lista de lobbies e entrada em lobby público ou privado por locatário autenticado.
- Perfil do usuario com edicao de nome, e-mail, CPF e senha.
- Protecao CSRF nos formularios sensiveis.
- Senhas armazenadas com `password_hash` e validacao com `password_verify`.

## Stack

- PHP 8.2 com Apache.
- MySQL 8.0.
- PDO para conexao com banco.
- Docker e Docker Compose.
- Bootstrap 5.3.2.
- Bootstrap Icons 1.11.3.
- Fonte Inter via Google Fonts.
- CSS modular proprio em `src/assets/css/components`.
- JavaScript puro em `src/assets/js`.

## Estrutura de pastas

```text
.
├── Dockerfile
├── docker-compose.yml
├── .env
├── LICENSE
├── toDo.md
├── docs.md
└── src
    ├── index.php
    ├── checkDb.php
    ├── testDb.php
    ├── testCreate.php
    ├── assets
    │   ├── css
    │   │   ├── customStyles.css
    │   │   └── components
    │   └── js
    ├── config
    │   ├── csrf.php
    │   ├── database.php
    │   └── schema.sql
    ├── crud
    ├── includes
    ├── middleware
    ├── pages
    │   └── partials
    └── utils
```

## Arquivos de ambiente e infraestrutura

### `Dockerfile`

Define a imagem da aplicacao:

- Base: `php:8.2-apache`.
- Extensoes instaladas: `pdo`, `pdo_mysql` e `mysqli`.
- Modulo Apache habilitado: `rewrite`.

### `docker-compose.yml`

Cria dois servicos:

- `web`: build local, container `my_apache_php`, porta `8080:80`, monta `./src` em `/var/www/html`.
- `db`: imagem `mysql:8.0`, container `my_mysql`, porta `3306:3306`, volume persistente `db_data` e inicializacao por `src/config/schema.sql`.

### `.env`

Variaveis usadas pela conexao PDO:

```env
DB_HOST=db
DB_PORT=3306
DB_NAME=jogafacil
DB_USER=app_user
DB_PASSWORD=app_password
APP_ENV=development
```

## Como rodar o projeto

### Pre-requisitos

- Docker instalado.
- Docker Compose instalado.
- Porta `8080` livre para a aplicacao.
- Porta `3306` livre para o MySQL, caso queira acessar o banco pelo host.

### Subir os containers

```bash
docker compose up --build
```

Depois acesse:

```text
http://localhost:8080
```

### Rodar em segundo plano

```bash
docker compose up -d --build
```

### Parar os containers

```bash
docker compose down
```

### Resetar banco local

O banco usa o volume Docker `db_data`. Para apagar os dados e recriar tudo a partir de `src/config/schema.sql`, rode:

```bash
docker compose down -v
docker compose up --build
```

Use esse comando com cuidado, pois ele remove os dados persistidos do MySQL local.

### Atualizar banco sem apagar dados

Se o container MySQL ja existia antes de uma alteracao em `schema.sql`, aplique as migracoes em `src/config/migrations/`:

```bash
docker compose exec -T db mysql -u app_user -papp_password jogafacil < src/config/migrations/001_lobby.sql
```

## Banco de dados

O schema inicial esta em `src/config/schema.sql`. O arquivo cria o banco `jogafacil`, as tabelas principais e dados mockados para testes.

### Tabela `usuarios`

Guarda contas da plataforma.

Campos principais:

- `id`: chave primaria.
- `nome`: nome do usuario.
- `email`: unico.
- `senha`: hash da senha.
- `tipo`: `locador`, `locatario`, `gerente` ou `admin`.
- `cpf`: unico e opcional.
- `status`: `ativo` ou `inativo`.
- `created_at`: data de criacao.

### Tabela `quadras`

Guarda arenas/quadras cadastradas por locadores.

Campos principais:

- `id`: chave primaria.
- `nome`: nome da arena.
- `endereco`: endereco completo.
- `imagem`: URL de imagem.
- `locador_id`: dono da arena.
- `cnpj`: CNPJ da arena.
- `status`: `pendente`, `ativo`, `inativo`, `manutencao` ou `rejeitado`.
- `descricao`: descricao textual.
- `facilidades`: JSON com comodidades.
- `modalidades`: modalidades esportivas.
- `funcionamento`: faixa de horario no formato `HH:MM - HH:MM`.
- `cancelamento_horas`: politica de cancelamento em horas.
- `telefone`: telefone de contato.
- `created_at`: data de cadastro.

Quando uma quadra e criada por um locador, o status inicial padrao e `pendente`. Ela so aparece publicamente depois de ser aprovada pelo administrador e ficar como `ativo`.

### Tabela `horarios`

Guarda horarios disponiveis por quadra e data.

Campos principais:

- `id`: chave primaria.
- `quadra_id`: quadra relacionada.
- `data`: data do horario.
- `hora_inicio`: inicio do horario.
- `hora_fim`: fim do horario.
- `preco`: preco do horario.

Existe uma chave unica em `(quadra_id, data, hora_inicio)` para impedir duplicidade.

### Tabela `reservas`

Guarda reservas feitas pelos locatarios.

Campos principais:

- `id`: chave primaria.
- `horario_id`: horario reservado.
- `quadra_id`: quadra reservada.
- `usuario_id`: locatario responsavel.
- `status`: `pendente`, `confirmada` ou `cancelada`.
- `modo_lobby`: indica se a reserva foi aberta como lobby.
- `visibilidade_lobby`: `publico` ou `privado` quando `modo_lobby` está ativo.
- `codigo_acesso`: código definido pelo organizador para lobby privado (normalizado em maiúsculas).
- `created_at`: data da reserva.

### Tabela `lobby_participantes`

Registra locatários que entraram em um lobby (organizador fica em `reservas.usuario_id`).

Campos principais:

- `id`: chave primaria.
- `reserva_id`: lobby (reserva com `modo_lobby = 1`).
- `usuario_id`: locatário participante.
- `joined_at`: data da entrada.

Chave unica em `(reserva_id, usuario_id)`.

### Tabela `gerente_quadras`

Relaciona gerentes com as quadras que eles podem administrar.

Campos principais:

- `gerente_id`: usuario do tipo gerente.
- `quadra_id`: quadra vinculada.
- `created_at`: data do vinculo.

A chave primaria composta e `(gerente_id, quadra_id)`.

## Dados iniciais

O schema cria usuarios de teste. A senha de todos e:

```text
password
```

Contas iniciais:

| Perfil | E-mail | Senha |
| --- | --- | --- |
| Locador | `locador@email.com` | `password` |
| Locatario | `locatario@email.com` | `password` |
| Admin | `admin@email.com` | `password` |

Tambem e criada uma arena mockada:

- Nome: `Arena Gol de Placa`.
- Status: `ativo`.
- Locador: usuario locador inicial.

## Arquitetura da aplicacao

O projeto segue uma estrutura simples em PHP procedural:

- `pages`: telas acessadas pelo navegador.
- `crud`: acoes de escrita/leitura no banco e handlers POST.
- `config`: conexao, schema e utilitarios de seguranca.
- `middleware`: controle de sessao e autorizacao.
- `utils`: validacoes, mensagens flash e geracao de horarios.
- `includes`: componentes globais de HTML, como head, header e footer.
- `assets`: CSS e JavaScript do frontend.

As paginas montam a interface e chamam funcoes dos arquivos em `crud`. Alguns arquivos de `crud` tambem atuam como endpoints de formulario quando acessados via POST.

## Configuracao e seguranca

### Conexao com banco

Arquivo: `src/config/database.php`

Funcao principal:

- `getDbConnection(): PDO`

A conexao usa variaveis de ambiente:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`
- `DB_PORT`

O PDO e configurado com:

- `PDO::ERRMODE_EXCEPTION`
- `PDO::FETCH_ASSOC`
- `PDO::ATTR_EMULATE_PREPARES = false`

### CSRF

Arquivo: `src/config/csrf.php`

Funcoes:

- `generateCsrfToken()`: cria e retorna um token por sessao.
- `validateCsrfToken($submittedToken)`: compara o token enviado com o token salvo na sessao usando `hash_equals`.

Os principais formularios incluem um campo oculto `csrfToken`.

### Sessoes e autorizacao

Arquivo: `src/middleware/authGuard.php`

Funcoes:

- `initSession()`: inicia a sessao se ela ainda nao existir.
- `requireAuth($userType, $loginPage)`: exige usuario autenticado com um tipo especifico.
- `requireAnyAuth($userTypes, $loginPage)`: exige usuario autenticado com um dos tipos permitidos.
- `requireGuest($redirectPage)`: redireciona usuarios ja logados.

Variaveis de sessao usadas:

- `$_SESSION['usuarioLogado']`: ID do usuario.
- `$_SESSION['usuarioNome']`: nome do usuario.
- `$_SESSION['usuarioTipo']`: tipo do usuario.
- `$_SESSION['csrfToken']`: token CSRF.

### Mensagens flash

Arquivo: `src/utils/flashMessage.php`

Funcoes:

- `setFlash($message, $type)`.
- `setFlashFromResponse($responseData)`.
- `getFlash()`.
- `renderFlash()`.

As mensagens sao salvas em sessao e removidas apos a leitura.

## Perfis de usuario

### Locatario

Pode:

- Criar conta com nome, CPF, e-mail e senha.
- Fazer login como locatario.
- Ver arenas ativas.
- Abrir a pagina de detalhes de uma arena.
- Selecionar horario disponivel.
- Confirmar reserva.
- Usar modo lobby.
- Editar o proprio perfil.

Nao pode:

- Criar, editar ou excluir quadras.
- Acessar painel de locador, gerente ou administrador.

### Locador

Pode:

- Criar conta com nome, e-mail e senha.
- Fazer login como locador.
- Acessar painel de locador.
- Cadastrar nova arena.
- Editar dados da arena.
- Excluir arena.
- Cadastrar gerentes.
- Vincular gerentes a quadras proprias.
- Ver status de suas arenas.
- Editar o proprio perfil.

Nao pode:

- Aprovar ou rejeitar arenas.
- Excluir administradores.

### Gerente

Pode:

- Fazer login como gerente.
- Acessar o painel compartilhado com o locador.
- Visualizar apenas as quadras vinculadas a ele.
- Acessar detalhes de gerenciamento das quadras vinculadas.
- Editar o proprio perfil.

Nao pode:

- Criar novas quadras.
- Editar perfil da arena.
- Excluir arenas.
- Cadastrar gerentes.

### Administrador

Pode:

- Fazer login como administrador.
- Acessar painel administrativo.
- Ver todos os usuarios.
- Remover usuarios nao administradores.
- Ver arenas pendentes.
- Abrir pre-visualizacao de uma arena pendente.
- Aprovar ou rejeitar arena.
- Editar o proprio perfil.

Nao pode:

- Remover administradores pelo fluxo atual.

## Rotas e paginas

### Publicas

| Caminho | Arquivo | Descricao |
| --- | --- | --- |
| `/index.php` | `src/index.php` | Home com arenas ativas, busca visual e filtros por modalidade. |
| `/pages/arenaDetalhe.php?id={id}` | `src/pages/arenaDetalhe.php` | Detalhes publicos da arena ativa e widget de reserva. |
| `/pages/escolherLogin.php` | `src/pages/escolherLogin.php` | Escolha do tipo de login. |
| `/pages/escolherCadastro.php` | `src/pages/escolherCadastro.php` | Escolha do tipo de cadastro. |
| `/pages/cadastroLocatario.php` | `src/pages/cadastroLocatario.php` | Cadastro de locatario. |
| `/pages/cadastroLocador.php` | `src/pages/cadastroLocador.php` | Cadastro de locador. |
| `/pages/loginLocatario.php` | `src/pages/loginLocatario.php` | Login de locatario. |
| `/pages/loginLocador.php` | `src/pages/loginLocador.php` | Login de locador. |
| `/pages/loginGerente.php` | `src/pages/loginGerente.php` | Login de gerente. |
| `/pages/loginAdmin.php` | `src/pages/loginAdmin.php` | Login de administrador. |
| `/pages/logout.php` | `src/pages/logout.php` | Encerra a sessao e redireciona para a home. |

### Autenticadas

| Caminho | Arquivo | Perfis | Descricao |
| --- | --- | --- | --- |
| `/pages/perfil.php` | `src/pages/perfil.php` | Qualquer usuario logado | Perfil do usuario logado. |
| `/pages/minhasReservas.php` | `src/pages/minhasReservas.php` | Locatario | Lista reservas do locatario com filtros por status. |
| `/pages/listaLobbies.php` | `src/pages/listaLobbies.php` | Locatario | Meus lobbies, lobbies de terceiros e entrada na partida. |
| `/pages/dashboardLocador.php` | `src/pages/dashboardLocador.php` | Locador, gerente | Lista ou detalhe de quadras. |
| `/pages/dashboardLocador.php?arena_id={id}` | `src/pages/dashboardLocador.php` | Locador, gerente | Gerenciamento de uma quadra especifica. |
| `/pages/cadastrarGerente.php` | `src/pages/cadastrarGerente.php` | Locador | Cadastro de gerente vinculado a quadras. |
| `/pages/dashboardAdmin.php` | `src/pages/dashboardAdmin.php` | Admin | Painel administrativo. |
| `/pages/adminPreviewArena.php?id={id}` | `src/pages/adminPreviewArena.php` | Admin | Pre-visualizacao e moderacao da arena. |

### Endpoints de formulario

| Caminho | Arquivo | Metodo | Descricao |
| --- | --- | --- | --- |
| `/crud/createUsuario.php` | `src/crud/createUsuario.php` | POST | Cria locatario, locador ou gerente. |
| `/crud/updateUsuario.php` | `src/crud/updateUsuario.php` | POST | Atualiza usuario, usado em fluxo administrativo/locador. |
| `/crud/updatePerfil.php` | `src/crud/updatePerfil.php` | POST | Atualiza perfil do usuario logado. |
| `/crud/deleteUsuario.php` | `src/crud/deleteUsuario.php` | POST | Remove usuario, com bloqueio para admins. |
| `/crud/createQuadra.php` | `src/crud/createQuadra.php` | POST | Cria arena do locador logado. |
| `/crud/updateQuadra.php` | `src/crud/updateQuadra.php` | POST | Atualiza arena do locador logado. |
| `/crud/deleteQuadra.php` | `src/crud/deleteQuadra.php` | POST | Exclui arena do locador logado. |
| `/crud/updateQuadraStatus.php` | `src/crud/updateQuadraStatus.php` | POST | Aprova, rejeita ou volta arena para pendente. |
| `/crud/createReserva.php` | `src/crud/createReserva.php` | POST | Cria reserva para locatario logado. |
| `/crud/joinLobby.php` | `src/crud/joinLobby.php` | POST | Adiciona locatario a um lobby. |

## Fluxos principais

### Cadastro de locatario

1. Usuario acessa `pages/cadastroLocatario.php`.
2. Preenche nome, CPF, e-mail e senha.
3. O formulario envia POST para `crud/createUsuario.php`.
4. O endpoint valida CSRF.
5. `createUsuario()` valida tipo, campos obrigatorios, e-mail e CPF.
6. A senha e hasheada com `PASSWORD_BCRYPT`.
7. O usuario e inserido como `locatario`.
8. O usuario e redirecionado para `loginLocatario.php`.

### Cadastro de locador

1. Usuario acessa `pages/cadastroLocador.php`.
2. Preenche nome, e-mail e senha.
3. O formulario envia POST para `crud/createUsuario.php`.
4. O endpoint valida CSRF.
5. `createUsuario()` valida tipo, campos obrigatorios e e-mail.
6. O usuario e inserido como `locador`.
7. O usuario e redirecionado para `loginLocador.php`.

### Login

1. Usuario acessa a tela de login do seu perfil.
2. O formulario envia POST para a propria pagina.
3. A pagina valida CSRF.
4. `findUsuarioByEmailAndSenha()` busca o usuario por e-mail.
5. A senha e validada com `password_verify`.
6. A pagina confere se o tipo do usuario corresponde ao login escolhido.
7. A sessao e regenerada com `session_regenerate_id(true)`.
8. A sessao recebe `usuarioLogado`, `usuarioNome` e `usuarioTipo`.
9. O usuario e redirecionado para a area correspondente.

### Cadastro de arena

1. Locador acessa `dashboardLocador.php`.
2. Abre o modal de nova arena.
3. O formulario envia POST para `crud/createQuadra.php`.
4. O endpoint valida CSRF e exige usuario do tipo `locador`.
5. `validateQuadraData()` valida campos obrigatorios, CNPJ e formato de funcionamento.
6. A arena e inserida com `locador_id` do usuario logado.
7. O status inicial fica como `pendente`.
8. A arena aparece para o administrador aprovar ou rejeitar.

### Moderacao de arena

1. Admin acessa `dashboardAdmin.php`.
2. O painel lista quadras com status `pendente`.
3. Admin abre `adminPreviewArena.php?id={id}`.
4. A pagina carrega dados da quadra e do locador.
5. Admin envia formulario de aprovacao ou rejeicao.
6. `updateQuadraStatus()` valida status permitido.
7. A quadra passa para `ativo`, `rejeitado` ou `pendente`.

### Cadastro de gerente

1. Locador acessa `pages/cadastrarGerente.php`.
2. A pagina carrega as quadras do locador.
3. Locador informa nome, e-mail, CPF, senha e seleciona uma ou mais quadras.
4. O formulario envia POST para `crud/createUsuario.php` com `tipo=gerente` e `source=dashboard`.
5. `createUsuario()` confirma que o usuario logado e locador.
6. Valida CPF, e-mail e as quadras selecionadas.
7. Cria o usuario gerente.
8. Insere os vinculos em `gerente_quadras`.
9. O gerente passa a acessar apenas as quadras vinculadas.

### Reserva de horario

1. Locatario abre `arenaDetalhe.php?id={id}`.
2. A pagina busca apenas quadras com status `ativo`.
3. `getBookingSlotsByQuadraDate()` garante que horarios do dia existam.
4. Horarios sao agrupados por manha, tarde e noite.
5. Horarios com reserva `pendente` ou `confirmada` aparecem como indisponiveis.
6. O locatario seleciona um horario.
7. Opcionalmente ativa o modo lobby e escolhe publico ou privado (com codigo definido pelo usuario).
8. O formulario envia POST para `crud/createReserva.php`.
9. O endpoint exige usuario logado do tipo `locatario`.
10. `createReserva()` abre transacao, trava o horario com `FOR UPDATE`, confirma que o horario pertence a arena aberta, confere duplicidade e cria reserva `pendente` com dados de lobby quando aplicavel.
11. O usuario volta para a pagina da arena com mensagem de sucesso ou erro.

### Entrada em lobby (locatario)

1. Locatario autenticado acessa `pages/listaLobbies.php` (link **Lobbies** no header).
2. **Publico:** lista lobbies com `visibilidade_lobby = publico`; clica em **Solicitar entrada** (`tipo_entrada=publico`, `reserva_id`).
3. **Privado:** informa o codigo no formulario inferior (`tipo_entrada=privado`, `codigo_acesso`).
4. `joinLobbyPublico()` ou `joinLobbyPrivado()` insere em `lobby_participantes` com transacao.
5. Redirecionamento para `listaLobbies.php` com mensagem flash.

## Regras de negocio

- Apenas quadras `ativo` aparecem na home e podem ser reservadas pela pagina publica.
- Nova quadra cadastrada por locador inicia como `pendente`.
- Administrador pode mudar status de quadra para `ativo`, `rejeitado` ou `pendente`.
- Locador so gerencia quadras cujo `locador_id` e o seu ID.
- Gerente so visualiza quadras vinculadas em `gerente_quadras`.
- Gerente nao cria, edita nem exclui arenas pela interface atual.
- Locatario precisa estar autenticado como `locatario` para reservar.
- Um horario com reserva ativa nao pode ser reservado novamente.
- Reservas sao criadas como `pendente` (aguardando gestao da quadra).
- Lobby público: visível em `listaLobbies.php` para outros locatarios entrarem.
- Lobby privado: entrada apenas com `codigo_acesso` (4 a 20 caracteres, definido na reserva).
- Organizador ve seus lobbies em **Meus lobbies** em `listaLobbies.php`.
- Organizador do lobby (`reservas.usuario_id`) nao pode entrar como participante.
- Um locatario nao pode entrar duas vezes no mesmo lobby.
- CPF e obrigatorio para locatario cadastrado pela tela publica e para gerente.
- CPF e CNPJ sao armazenados somente com digitos.
- E-mail deve ser unico.
- CPF deve ser unico quando informado.
- Admins nao podem ser removidos pelo fluxo de exclusao de usuarios.

## Horarios e precos

Arquivo: `src/crud/readHorarios.php`

### Geracao de horarios

A funcao `generateRelativeTimeSlots()` recebe uma string de funcionamento no formato:

```text
08:00 - 22:00
```

Ela gera blocos de uma hora e agrupa em:

- `Manhã`: antes de 12h.
- `Tarde`: de 12h ate antes de 18h.
- `Noite`: a partir de 18h.

Na pagina de reserva, os grupos retornados para o frontend sao:

- `manha`
- `tarde`
- `noite`

### Precos por horario

A funcao `getSlotPriceByHour()` usa a hora de inicio:

| Periodo | Regra | Preco |
| --- | --- | --- |
| Manha | hora menor que 12 | R$ 150,00 |
| Tarde | hora menor que 18 | R$ 180,00 |
| Noite | hora igual ou maior que 18 | R$ 200,00 |

## Frontend

### CSS

Arquivo principal:

- `src/assets/css/customStyles.css`

Ele importa os componentes:

- `tokens.css`: variaveis de cor, raio, sombras e transicoes.
- `global.css`: estilos globais.
- `navbar.css`: cabecalho.
- `hero.css`: hero da home.
- `searchBar.css`: busca.
- `categoryFilter.css`: filtros de modalidade.
- `arenaCard.css`: cards de arena.
- `auth.css`: paginas de login/cadastro.
- `dashboard.css`: paineis.
- `arenaDetail.css`: detalhes de arena.
- `bookingWidget.css`: widget de horarios/reserva.
- `choiceCard.css`: cards de escolha.
- `footer.css`: rodape.

### JavaScript

#### `src/assets/js/appLogic.js`

Responsavel por:

- Botao e Enter da busca da home.
- Filtros de categoria na home.
- Mascara de CPF.
- Validacao visual dos formularios Bootstrap com `novalidate`.
- Confirmacao antes de remover usuario.
- Mascara de CNPJ.

Observacao: a busca da home atualmente apenas registra o termo no console. O filtro de categoria altera a exibicao dos cards ja renderizados.

#### `src/assets/js/arenaDetailLogic.js`

Responsavel pelo widget de reserva:

- Renderiza horarios por periodo.
- Mostra preco.
- Desabilita horarios ocupados.
- Seleciona horario disponivel.
- Atualiza o botao de confirmacao.
- Controla o modo lobby.
- Preenche campos ocultos `horario_id` e `modo_lobby`.

#### `src/assets/js/dashboardLocadorLogic.js`

Responsavel pelo widget visual de horarios no painel do locador/gerente:

- Renderiza horarios gerados a partir do funcionamento.
- Alterna abas `Manhã`, `Tarde` e `Noite`.
- Atualiza o botao de acao quando um horario e selecionado.

Observacao: no estado atual, esse painel mostra a interacao visual de bloquear/reservar horario, mas nao ha endpoint persistindo bloqueio manual de horarios.

## Includes compartilhados

### `src/includes/headTag.php`

Define:

- `charset`.
- `viewport`.
- `description` opcional.
- `title`.
- Google Fonts.
- Bootstrap CSS.
- Bootstrap Icons.
- CSS principal do projeto.

O arquivo calcula `assetUrl` para funcionar tanto em paginas da raiz quanto em paginas dentro de `pages` ou `crud`.

### `src/includes/header.php`

Renderiza:

- Logo com link para home.
- Links de login/cadastro para visitantes.
- Saudacao, perfil, painel e logout para usuarios logados.
- Painel correto para locador, gerente ou admin.

### `src/includes/footer.php`

Renderiza rodape simples com ano atual.

## Validadores e utilitarios

### `src/utils/validators.php`

Funcoes:

- `isValidCpf($cpfInput)`: valida CPF por formato e digitos verificadores.
- `isValidCnpj($cnpjInput)`: valida CNPJ por formato e digitos verificadores.
- `isValidOperatingHours($hours)`: valida formato `HH:MM - HH:MM`.
- `validateQuadraData($data)`: valida e sanitiza dados de quadra usados em create/update.

### `src/utils/timeSlotGenerator.php`

Funcao:

- `generateRelativeTimeSlots($operatingHours)`: gera horarios de uma hora agrupados por periodo.

Se o formato do horario de funcionamento nao for reconhecido, usa fallback de 08h ate 22h.

### `src/utils/flashMessage.php`

Centraliza mensagens de sucesso, erro e alerta usando sessao.

## CRUD e funcoes principais

### Usuarios

Arquivo: `src/crud/createUsuario.php`

- `createUsuario($inputData)`: cria usuarios dos tipos `locador`, `locatario` e `gerente`.
- Para gerente, valida locador logado e cria vinculos em `gerente_quadras`.
- O endpoint generico nao cria usuarios `admin`.
- Em POST, valida CSRF, define dados do usuario logado e redireciona conforme resultado.

Arquivo: `src/crud/readUsuarios.php`

- `readAllUsuarios()`: lista usuarios.
- `readUsuarioById($userId)`: busca usuario por ID.
- `findUsuarioByEmailAndSenha($inputEmail, $inputSenha)`: autentica por e-mail e senha.

Arquivo: `src/crud/updateUsuario.php`

- `updateUsuario($userId, $inputData)`: atualiza nome, e-mail, CPF e senha.

Arquivo: `src/crud/updatePerfil.php`

- Handler POST do formulario de perfil do usuario logado.

Arquivo: `src/crud/deleteUsuario.php`

- `deleteUsuario($userId)`: remove usuario por ID.
- O handler bloqueia remocao de administradores.

### Quadras

Arquivo: `src/crud/createQuadra.php`

- `createQuadra($data)`: valida dados e insere nova arena.

Arquivo: `src/crud/readQuadras.php`

- `getQuadrasByLocador($locadorId)`.
- `getQuadrasByGerente($gerenteId)`.
- `getQuadraByIdAndLocador($arenaId, $locadorId)`.
- `getQuadraByIdAndGerente($arenaId, $gerenteId)`.
- `getActiveQuadraById($arenaId)`.
- `getAllPendingQuadras()`.
- `getAllApprovedQuadras()`.

Arquivo: `src/crud/updateQuadra.php`

- `updateQuadra($data)`: atualiza dados da arena do locador.

Arquivo: `src/crud/deleteQuadra.php`

- `deleteQuadra($arenaId, $locadorId)`: remove arena do locador.
- O endpoint aceita apenas POST protegido por CSRF.

Arquivo: `src/crud/updateQuadraStatus.php`

- `updateArenaStatus($arenaId, $status)`: altera status da arena.

### Horarios

Arquivo: `src/crud/readHorarios.php`

- `getSlotPriceByHour($hour)`.
- `ensureHorariosForQuadraDate($quadraId, $date, $funcionamento)`.
- `getBookingSlotsByQuadraDate($quadraId, $date, $funcionamento)`.

### Reservas

Arquivo: `src/crud/createReserva.php`

- `createReserva($data)`: cria reserva com transacao, `modo_lobby`, `visibilidade_lobby` e `codigo_acesso` (privado).
- Usa `FOR UPDATE` para travar o horario durante a verificacao.
- Confirma que o horario enviado pertence a arena enviada no formulario.
- Impede reserva duplicada em horario com status `pendente` ou `confirmada`.

Arquivo: `src/crud/readReservasLocatario.php`

- `getReservasByLocatario($usuarioId)`: todas as reservas do locatario.
- `getLobbiesOrganizadosByLocatario($usuarioId)`: partidas em modo lobby criadas pelo usuario.
- `getLobbiesParticipandoByLocatario($usuarioId)`: lobbies em que entrou como participante.

Arquivo: `src/crud/readLobbies.php`

- `getPublicLobbies($usuarioId)`: lista lobbies publicos de outros jogadores.
- `getLobbyByCodigoAcesso($codigo)`: busca lobby privado pelo codigo.
- `usuarioParticipaLobby($reservaId, $usuarioId)`: verifica participacao existente.

Arquivo: `src/crud/joinLobby.php`

- `joinLobbyPublico($reservaId, $usuarioId)`: entrada em lobby publico.
- `joinLobbyPrivado($codigo, $usuarioId)`: entrada em lobby privado pelo codigo.
- Endpoint POST exige CSRF e sessao `locatario`.

## Testes manuais recomendados

### Ambiente

1. Rode `docker compose up --build`.
2. Acesse `http://localhost:8080`.
3. Confirme que a home abre e lista a arena mockada.

### Login por perfil

1. Acesse `pages/escolherLogin.php`.
2. Entre como locador com `locador@email.com` e `password`.
3. Saia.
4. Entre como locatario com `locatario@email.com` e `password`.
5. Saia.
6. Entre como admin com `admin@email.com` e `password`.

### Cadastro de arena e moderacao

1. Entre como locador.
2. Abra o painel.
3. Cadastre uma nova arena com CNPJ valido.
4. Saia.
5. Entre como admin.
6. Abra o painel administrativo.
7. Visualize a arena pendente.
8. Aprove a arena.
9. Volte para a home e confira se a arena aparece.

### Cadastro de gerente

1. Entre como locador.
2. Acesse `Cadastrar Gerente`.
3. Informe nome, e-mail, CPF valido, senha e selecione uma quadra.
4. Cadastre o gerente.
5. Saia.
6. Entre como gerente.
7. Confirme que ele visualiza apenas as quadras vinculadas.
8. Confirme que ele nao ve acoes de criar, editar ou excluir quadras.

### Reserva

1. Entre como locatario.
2. Abra uma arena ativa pela home.
3. Escolha um horario disponivel.
4. Opcionalmente ative modo lobby.
5. Confirme a reserva.
6. Reabra a pagina da arena.
7. Confirme que o horario aparece ocupado.

### Perfil

1. Entre com qualquer usuario.
2. Acesse `Meu Perfil`.
3. Clique em editar.
4. Altere nome ou e-mail.
5. Salve.
6. Confirme que a mensagem aparece e o nome da sessao e atualizado no cabecalho.

## Comandos uteis

### Ver logs dos containers

```bash
docker compose logs
```

### Ver logs apenas do PHP/Apache

```bash
docker compose logs web
```

### Ver logs apenas do MySQL

```bash
docker compose logs db
```

### Entrar no container web

```bash
docker exec -it my_apache_php bash
```

### Entrar no MySQL pelo container

```bash
docker exec -it my_mysql mysql -u app_user -papp_password jogafacil
```

### Validar sintaxe PHP dentro do container

```bash
docker exec my_apache_php php -l /var/www/html/index.php
```

Para validar outro arquivo, troque o caminho.

## Arquivos de diagnostico

### `src/checkDb.php`

Busca usuarios e retorna JSON. Tambem testa o hash padrao da senha `password`.

### `src/testDb.php`

Tenta abrir conexao com banco e imprime informacoes basicas do ambiente.

Observacao: o arquivo imprime `DB_PASS`, mas a aplicacao usa `DB_PASSWORD`. Se for usado para diagnostico, ajuste essa diferenca ou interprete a saida com cuidado.

### `src/testCreate.php`

Cria um usuario locador de teste chamando `createUsuario()`.

Use estes arquivos apenas em ambiente local/desenvolvimento. Em producao, remova ou bloqueie acesso publico a eles.

## Cuidados conhecidos

- O arquivo `.env` esta no repositorio local e contem credenciais de desenvolvimento. Para producao, use segredos reais fora do versionamento.
- `src/checkDb.php`, `src/testDb.php` e `src/testCreate.php` sao utilitarios de desenvolvimento e nao devem ficar publicos em producao.
- A busca da home ainda nao consulta o banco; ela apenas registra a busca no console.
- O widget de horarios do painel do locador e visual e ainda nao persiste bloqueios manuais.
- `testDb.php` referencia `DB_PASS`, enquanto a configuracao real usa `DB_PASSWORD`.
- As imagens das arenas usam URLs externas. Se a rede estiver indisponivel, as imagens externas podem nao carregar.

## Boas praticas para manutencao

- Sempre proteger novos formularios com `generateCsrfToken()` e `validateCsrfToken()`.
- Sempre usar prepared statements no PDO.
- Nunca salvar senha em texto puro.
- Conferir o tipo do usuario antes de executar acoes sensiveis.
- Para novas rotas privadas, usar `requireAuth()` ou `requireAnyAuth()`.
- Para novas mensagens de sucesso/erro, usar `setFlashFromResponse()` ou `setFlash()`.
- Para novos campos de quadra, atualizar juntos:
  - `src/config/schema.sql`
  - `src/utils/validators.php`
  - `src/pages/partials/modalCreateArena.php`
  - `src/pages/partials/modalEditArena.php`
  - `src/crud/createQuadra.php`
  - `src/crud/updateQuadra.php`
- Para novas regras de reserva, revisar `readHorarios.php`, `createReserva.php` e `arenaDetailLogic.js`.
- Para lobby e participantes, revisar `readLobbies.php`, `joinLobby.php`, `listaLobbies.php` e `schema.sql` (`lobby_participantes`, colunas de lobby em `reservas`).

## Licenca

O repositorio inclui um arquivo `LICENSE`. Consulte esse arquivo para os termos completos de licenciamento.
