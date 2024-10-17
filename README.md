# WhoisFinder - Plugin WordPress

**WhoisFinder** é um plugin WordPress que permite exibir informações detalhadas sobre o IP do usuário e realizar consultas de domínios via Whois.

## Funcionalidades

- **Consulta de IP**: Exibe o endereço IP do visitante com informações detalhadas sobre a geolocalização, ISP e outras informações relevantes.
- **Consulta de Domínios (Whois)**: Permite que os usuários busquem informações de qualquer domínio, como data de criação, status e servidor Whois.

## Requisitos

- WordPress 5.0 ou superior
- PHP 7.0 ou superior
- Chave de API da [WhoisXML API](https://www.whoisxmlapi.com/)

## Instalação

1. Faça o download do plugin ou clone este repositório.
2. No painel de controle do WordPress, vá até **Plugins** -> **Adicionar Novo** -> **Enviar Plugin**.
3. Envie o arquivo ZIP do plugin ou extraia o conteúdo na pasta `wp-content/plugins/whoisfinder`.
4. Ative o plugin na lista de plugins instalados.
5. Para mostrar o plugin no seu site, adicione o shortcode `[whoisfinder]` em qualquer página ou post.

## Configuração

### API Whois

Este plugin utiliza a **WhoisXML API** para consultas de domínio Whois. Para configurar:

1. Crie uma conta em [WhoisXML API](https://www.whoisxmlapi.com/).
2. Obtenha sua chave de API.
3. No arquivo `whoisfinder-functions.php`, adicione a chave de API na linha:
   ```php
   $whois_api_key = 'SUA_CHAVE_DE_API_AQUI';


Este arquivo `README.md` fornece todas as informações necessárias para os usuários e desenvolvedores utilizarem e contribuírem com o plugin **WhoisFinder**.
