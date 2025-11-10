<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Logo Laravel"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Status da Build"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total de Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Última Versão Estável"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="Licença"></a>
</p>

# Projeto: Desafio Técnico - Sistem Test

Projeto desenvolvido como parte do processo seletivo para a vaga de **Analista Pleno**.
A aplicação consiste em um sistema CRUD (Create, Read, Update, Delete) completo, utilizando Laravel para o back-end (API RESTful) e Vue.js para o front-end (Interface de Consumo).

---

## 📋 Sobre o Projeto

Este sistema foi construído aproveitando o poder e a elegância do Laravel, um framework de aplicações web com sintaxe expressiva e elegante. Acreditamos que o desenvolvimento deve ser uma experiência agradável e criativa para ser verdadeiramente gratificante. Laravel facilita o desenvolvimento ao simplificar tarefas comuns utilizadas em muitos projetos web, tais como:

- [Motor de roteamento simples e rápido](https://laravel.com/docs/routing)
- [Poderoso container de injeção de dependências](https://laravel.com/docs/container)
- Múltiplos back-ends para armazenamento de [sessão](https://laravel.com/docs/session) e [cache](https://laravel.com/docs/cache)
- [ORM de banco de dados](https://laravel.com/docs/eloquent) expressivo e intuitivo
- [Migrações de schema](https://laravel.com/docs/migrations) agnósticas de banco de dados
- [Processamento robusto de jobs em background](https://laravel.com/docs/queues)
- [Transmissão de eventos em tempo real](https://laravel.com/docs/broadcasting)

Laravel é acessível, poderoso e fornece ferramentas necessárias para aplicações grandes e robustas.

---

## 🚀 Tecnologias Utilizadas

Este projeto foi construído com as seguintes tecnologias:

### Back-end:
* **Laravel Framework** [v10.x]
* **PHP** [v8.1+]
* **Banco de Dados:** MySQL ou PostgreSQL

### Front-end:
* **Vue.js** [v3.x+]
* **Vite** (ou Vue CLI)
* **Biblioteca de Estilo:** Tailwind CSS, Vuetify ou SASS

### Ferramentas de Desenvolvimento:
* Composer
* Node.js & NPM (ou Yarn)
* Git

---

## ✨ Funcionalidades Principais

O sistema permite o gerenciamento completo de recursos através de:

* **📋 Listar:** Visualização de todos os recursos cadastrados com paginação
* **➕ Criar:** Formulário para adição de novos recursos com validação
* **✏️ Editar:** Formulário para atualização dos dados de recursos existentes
* **🗑️ Excluir:** Opção para remover recursos do banco de dados com confirmação

---

## 🔧 Instalação e Execução

Siga os passos abaixo para configurar e executar o projeto em seu ambiente local.

### Pré-requisitos

Certifique-se de ter instalado em sua máquina:

* PHP >= 8.1
* Composer
* Node.js >= 16.x
* NPM ou Yarn
* Um SGBD (MySQL ou PostgreSQL)
* Git

### Passo 1: Clonar o Repositório

```bash
git clone [URL-DO-REPOSITORIO]
cd [NOME-DO-PROJETO]
```

### Passo 2: Configurar o Back-end (Laravel)

```bash
# Instalar dependências do PHP
composer install

# Copiar o arquivo de ambiente
cp .env.example .env

# Gerar a chave da aplicação
php artisan key:generate

# Configurar o banco de dados no arquivo .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=nome_do_banco
# DB_USERNAME=usuario
# DB_PASSWORD=senha

# Comando com base no env.example
CREATE DATABASE crud_interview
WITH OWNER = postgres
ENCODING = 'UTF8';

# Executar as migrações
php artisan migrate

# Executar seeders para dados de teste
php artisan db:seed

# Limpar cache e otimizar
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan optimize

# Iniciar o servidor de desenvolvimento
php artisan serve
```

O back-end estará disponível em: `http://localhost:8000`

### Passo 3: Configurar o Front-end (Vue.js)

```bash
# Navegar para o diretório do front-end (se separado)
cd frontend

# Instalar dependências do Node
npm install
# ou
yarn install

# Iniciar o servidor de desenvolvimento
npm run dev
# ou
yarn dev
```

O front-end estará disponível em: `http://localhost:5173` (porta padrão do Vite)

---

## 🤝 Contribuindo

Obrigado por considerar contribuir para este projeto! Se você encontrar algum problema ou tiver sugestões de melhorias, sinta-se à vontade para abrir uma issue ou enviar um pull request.

Para o framework Laravel, o guia de contribuição pode ser encontrado na [documentação do Laravel](https://laravel.com/docs/contributions).

---

## 📝 Código de Conduta

Para garantir que a comunidade Laravel seja acolhedora para todos, por favor revise e respeite o [Código de Conduta](https://laravel.com/docs/contributions#code-of-conduct).

---

## 🔒 Vulnerabilidades de Segurança

Se você descobrir uma vulnerabilidade de segurança no Laravel, envie um e-mail para Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). Todas as vulnerabilidades de segurança serão prontamente tratadas.

---

## 📄 Licença

O framework Laravel é um software de código aberto licenciado sob a [licença MIT](https://opensource.org/licenses/MIT).

---

## 🙏 Patrocinadores do Laravel

Gostaríamos de agradecer aos seguintes patrocinadores por financiar o desenvolvimento do Laravel:

### Parceiros Premium

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

---

## 📧 Contato

Para questões sobre este projeto específico, entre em contato através do email : munhosga@gmail.com