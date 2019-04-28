# CEU (Casa do Estudante Universitário) Management - Federal University of Santa Maria

> System Administration for Student's Houses


### Descrição do projeto
[Acesse o Documento Visão](https://docs.google.com/document/d/13_heEjWbXY1FvPEGUubJIBRElNpaBujd8inD89FsEvQ/edit?usp=sharing) 


### Ferramentas de desenvolvimento

#### Back End: PHP 7 + Laravel 5.5
- Instruções de instalação das dependências de desenvolvimento (Ubuntu 18.04)

    [PHP + Composer](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-composer-on-ubuntu-18-04) 

- Instalar extensões php-xml php-pdo php-mysql (Ubuntu 18.04)
    
    ```
    sudo apt install php-xml php-pdo php-mysql
    ```

- Instalação MYSQL (Ubuntu 18.04)

    [MYSQL](https://www.digitalocean.com/community/tutorials/how-to-install-mysql-on-ubuntu-18-04)

- Instalação das dependencias do projeto backend

    ```
    cd backend && composer install
    ```
    
- Criando arquivo `.env` de configuração do projeto
    
    - O arquivo `.env` deve ser criado na pasta backend (raiz do projeto) e deve seguir essa sintaxe:
    
    ```dotenv
    APP_NAME=CEU,Management,-,Sistema,de,Gestão,da,Casa,do,Estudante,Universitário
    APP_ENV=local
    APP_KEY=base64:m6AjdVIi1fPbSmtDuWPxTJtGnA5/d1TQ1eKXNV68JC0=
    APP_DEBUG=true
    APP_LOG_LEVEL=debug
    APP_URL=http://localhost
    
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=ceu_management
    DB_USERNAME=usuario do seu banco
    DB_PASSWORD=senha do usuario do seu banco
    
    BROADCAST_DRIVER=pusher
    CACHE_DRIVER=file
    SESSION_DRIVER=file
    QUEUE_DRIVER=database
    
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379
    
  # PARA O ENVIO DE EMAILS FOI UTILIZADO O MAIL TRAP PARA DESENVOLVIMENTO
  # Tutorial em: https://laravel.com/docs/5.5/mail

    MAIL_DRIVER=smtp
    MAIL_HOST=smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=USUARIO
    MAIL_PASSWORD=SENHA
    MAIL_ENCRYPTION=tls
    
    PUSHER_APP_ID=441649
    PUSHER_APP_KEY=PUSHER ID
    PUSHER_APP_SECRET=PUSHER SECRET

    ```
    
- Criando o banco de dados (rode o comando abaixo dentro da pasta backend)

    ```
    sudo mysql -uroot -p
    ```
    
    No console do MYSQL crie um banco de dados
    ```
    create database ceu_management;
    ``` 
    Saia do console do MYSQL e rode: 
    
    ```
    php artisan migrate
    ```

#### Front End: Vue JS

- Instruções de instalação das dependências de desenvolvimento (Ubuntu 18.04)

    [NPM + Node JS](https://linuxize.com/post/how-to-install-node-js-on-ubuntu-18.04/)

- Instalação das dependencias do projeto

    ```
    cd frontend && npm install
    ```


### Execução do Projeto

#### Back end

- Rodar projeto (tenha certeza de estar na pasta backend)
    
    ```
    php artisan serve    
    ```

- Rodar listener de envio de emails e notificações (em um terminal separado)

    ```
    php artisan queue:work    
    ```
    
- Seedar banco de dados com usuários Fakes
    ```
    php artisan db:seed    
    ```
    Todos usuários criados possuem a senha: `segredo123`
    
#### Front end

- Rodar projeto (tenha certeza de estar na pasta frontend)

     ```
     npm start    
     ```

O site irá abrir automaticamente