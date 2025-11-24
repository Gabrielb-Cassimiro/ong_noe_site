📦 ONG de Noé — Sistema de Loja, Autenticação e Gerenciamento

Sistema web desenvolvido para a ONG de Noé, com objetivo de auxiliar na captação de recursos através da venda de produtos.
Inclui autenticação com níveis de acesso, gerenciamento administrativo e sistema completo de pedidos.

🚀 Funcionalidades
👤 Autenticação

Login e senha

Perfis:

Master (admin)

Comum (usuário final)

Verificação 2FA baseada em:

Nome materno

Data de nascimento

CEP

🛒 Loja e Produtos

Listagem de produtos na página principal

Adicionar ao carrinho

Atualização de quantidades no carrinho

Remoção de itens

Controle automático de estoque no pedido concluído

Página administrativa para CRUD completo de produtos

📦 Sistema de Pedidos
Usuário Comum:

Ver produtos

Adicionar ao carrinho

Finalizar pedido

Acompanhar seus pedidos em "Meus Pedidos"

Ver detalhes do pedido

Usuário Master:

Visualizar todos os pedidos

Detalhamento completo

Atualizar status:

pendente

pago

enviado

concluído

cancelado

Painel administrativo dedicado

📝 Gerenciamento

CRUD completo de usuários (apenas master)

CRUD de produtos

Logs de autenticação

Modelo do Banco de Dados integrado (modelo_bd.php)

Modo de acessibilidade com contraste alto

🗂 Estrutura de Diretórios
/ong_noe_site
|--- index.php
|--- config/
|      |--- config.php
|      |--- database.php
|      |--- processa_cadastro.php
|
|--- includes/
|      |--- functions.php
|
|--- pages/
|      |--- login.php
|      |--- cadastro.php
|      |--- 2fa.php
|      |--- principal.php
|      |--- carrinho.php
|      |--- finalizar_pedido.php
|      |--- meus_pedidos.php
|      |--- ver_pedido.php
|      |--- gerenciar_pedidos.php
|      |--- ver_pedido_admin.php
|      |--- update_status_pedido.php
|      |--- crud_produtos.php
|      |--- consulta_usuarios.php
|      |--- logs.php
|      |--- modelo_bd.php
|
|--- css/
|      |--- style.css
|
|--- js/
       |--- main.js

🛠 Tecnologias Utilizadas

PHP 8+

MySQL (MariaDB compatível)

JavaScript (Vanilla)

HTML5 / CSS3

PDO (Prepared Statements)

Sessions

XAMPP/LAMP/WAMP compatível

🗄 Banco de Dados

As principais tabelas são:

usuarios

dados_pessoais

enderecos

logs_autenticacao

produtos

pedidos

pedidos_itens

Trecho do SQL oficial está disponível no arquivo:
📄 /pages/modelo_bd.php

🧠 Fluxo de Funcionamento
1. Autenticação

→ usuário faz login
→ valida 2FA
→ redirecionado para principal.php

2. Loja e Carrinho

→ usuário adiciona produtos ao carrinho
→ ajusta quantidades
→ finaliza pedido
→ estoque é atualizado automaticamente

3. Pedidos

Usuário comum → vê apenas seus pedidos

Master → vê todos e atualiza status

👨‍💻 Como Rodar o Projeto Localmente
1. Clone o repositório
git clone https://github.com/SEU_USUARIO/ong_noe_site.git

2. Importe o banco de dados

Abra phpMyAdmin

Crie o banco ong_noe_db

Importe o script SQL incluído

3. Configure a conexão

Arquivo: /config/database.php

$host = 'localhost';
$db   = 'ong_noe_db';
$user = 'root';
$pass = '';

4. Execute no navegador
http://localhost/ong_noe_site/
