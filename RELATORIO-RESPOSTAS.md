# ComputaÃ§Ã£o em Nuvem - Trabalho Avaliativo: Docker e GitHub

**AplicaÃ§Ã£o:** BarberBot  
**Integrante 1:** Jacy Cordeiro  
**Integrante 2:** [preencher nome do segundo integrante]

## 1. O que o arquivo `.dockerignore` faz? Citem dois itens que vocÃªs incluÃ­ram nele e expliquem o motivo de cada um.

O `.dockerignore` define quais arquivos e diretÃ³rios nÃ£o entram no contexto enviado ao Docker durante a construÃ§Ã£o da imagem. No BarberBot incluÃ­mos `.git`, porque o histÃ³rico do Git nÃ£o Ã© necessÃ¡rio para executar a aplicaÃ§Ã£o e aumentaria o tamanho do contexto e da imagem. TambÃ©m incluÃ­mos `.env`, porque esse arquivo pode conter configuraÃ§Ãµes e credenciais locais e nÃ£o deve ser copiado para dentro da imagem. O projeto ainda ignora logs e diretÃ³rios de uploads, que sÃ£o arquivos gerados em execuÃ§Ã£o e nÃ£o fazem parte do cÃ³digo necessÃ¡rio para o build.

## 2. O que poderia acontecer se a pasta `.git` fosse copiada para dentro da imagem?

A pasta `.git` contÃ©m o histÃ³rico do repositÃ³rio, referÃªncias de branches e outras informaÃ§Ãµes de versionamento que nÃ£o sÃ£o necessÃ¡rias para executar o BarberBot. Se ela fosse copiada para a imagem, aumentaria desnecessariamente o contexto de build e o tamanho final da imagem. AlÃ©m disso, o histÃ³rico poderia preservar informaÃ§Ãµes removidas de commits atuais, o que representa risco de exposiÃ§Ã£o de dados. Por isso, `.git` estÃ¡ explicitamente listado no `app/.dockerignore`.

## 3. Por que o arquivo `.env` nÃ£o deve ser enviado para o repositÃ³rio?

O `.env` Ã© destinado Ã  configuraÃ§Ã£o de cada ambiente e pode conter senhas do banco, chaves de APIs e outros valores sensÃ­veis. No projeto, por exemplo, `DB_PASSWORD` e `DB_ROOT_PASSWORD` sÃ£o fornecidos ao Docker Compose pelo ambiente. Se um `.env` real fosse publicado em um repositÃ³rio pÃºblico, essas credenciais poderiam ser lidas e utilizadas por terceiros. Por isso, o `.gitignore` do BarberBot bloqueia `.env` e o arquivo nÃ£o Ã© versionado.

## 4. Se o `.env` nÃ£o vai para o repositÃ³rio, para que serve o arquivo `.env.example`?

O `.env.example` funciona como um modelo documentado das variÃ¡veis necessÃ¡rias para executar o projeto. Ele informa quais nomes precisam existir, como `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `APP_PORT` e outras configuraÃ§Ãµes, mas utiliza somente valores fictÃ­cios. Depois de clonar o repositÃ³rio, a pessoa copia `.env.example` para `.env` e pode ajustar os valores do seu prÃ³prio ambiente. Assim, o projeto continua reproduzÃ­vel sem publicar segredos reais.

## 5. Uma senha real foi enviada ao GitHub por engano. ApagÃ¡-la em um commit seguinte resolve o problema? Justifiquem a resposta.

NÃ£o. Apagar a senha apenas em um commit posterior nÃ£o elimina o valor do histÃ³rico anterior do Git. Quem tiver acesso ao repositÃ³rio ou ao histÃ³rico ainda poderÃ¡ localizar o commit que continha a credencial. Nesse caso, a senha deve ser considerada comprometida e precisa ser revogada ou alterada imediatamente. Depois disso, tambÃ©m Ã© necessÃ¡rio remover o segredo do histÃ³rico do Git com uma ferramenta apropriada e verificar se nÃ£o existem outras cÃ³pias ou chaves expostas.

