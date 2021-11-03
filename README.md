# PHP Juno

A classe foi criada para auxiliar a integração com API 2.0 da Juno,a mesma está em produção para empresas que presto serviço além de projetos pessoais.

 - [Documentação Juno (Oficial) ](https://dev.juno.com.br/api/v2)
 - [Repositório Juno (Oficial) ](https://github.com/tamojuno/integration-api-php-sdk)

## Projeto

Criando uma pasta com o projeto

```
git clone URL_DO_PROJETO
cd NOME_DO_PROJETO
```

## Branch de Desenvolvimento

Você deve criar uma branch para seu desenvolvimento, de preferência com o seu `nome.sobrenome` :)

```
git checkout -b NOME_BRANCH
git add .
git commit -m "Primeiro Commit"
git push -u origin NOME_BRANCH
```
> **Todas as alterações devem ser feitas em sua branch e enviar ao repositório remoto**

## Primeiros Passos

No arquivo `config.php` você deve informa o tokens de acesso gerado pelo painel da Juno.

No Painel Juno,vá em menu **Plugins & API > Criação de Credencial**.

> **Atenção: Os tokens devem ser gerados de acordo com o ambiente de uso, para desenvolvimento acesse [Juno Sandbox](https://sandbox.juno.com.br/#/)**.

Parâmetro | Tipo | Descrição
:-|:-|:-
Token | String ** | Token Privado
ClientID | String ** | Informe o ClientID gerado no passo anterior
ClientSecret | String ** |Informe o Secret gerado no passo anterior
Sandbox | Boolean | O último parâmetro defini qual ambiente você está trabalhando. Para desenvolvimento usar `TRUE`. Valor padrão `FALSE`

> __** Campos obrigátorios como parâmetros para a classe__

Código `config.php`: 

```
$token         = '';
$ClientID      = '';
$ClientSecrect = '';

$juno = new Juno($token, $ClientID, $ClientSecrect, false);
```

## Iniciar

Você pode iniciar o projeto com Docker ou servidor nativo do PHP:

* Docker
~~~docker
docker compose up -d
~~~

* PHP
~~~php
php -S localhost:8080
~~~
## QUA - Qualidade

É uma branch criada para receber o primeiro pull request de melhoria/correção, nossa equipe de colaboradores irá fazer a revisão e implementar em ambientes de homologação. Sendo avaliada com sucesso enviaremos para a master de projeto.

## License

- [MIT License](https://github.com/ylmenezes/php-juno-v2/blob/master/LICENSE)

## Versionamento do Software

Essa regra define que o software usa versionamento semântico e deve obrigatoriamente as versões no formato **x.y.z**, com x, y e z sendo inteiros não-negativos e sem conter zeros à esquerda.

- **x** é a versão principal (ou major version) ser incrementado se houver alterações incompatíveis com versões anteriores; <br>
- **y** é a versão secundária (ou minor version) com funcionalidades novas, mas ainda compatíveis com as versões passadas; <br>
- **z** é a versão de remendo (ou patch version) essa regra define o que é uma "correção de bug": <br>
