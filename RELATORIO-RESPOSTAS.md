# Computação em Nuvem - Trabalho Avaliativo: Docker e GitHub

**Aplicação:** BarberBot  
**Integrante 1:** Jacy Cordeiro  
**Integrante 2:** [preencher nome do segundo integrante]

## 1. O que o arquivo `.dockerignore` faz? Citem dois itens que vocês incluíram nele e expliquem o motivo de cada um.

O `.dockerignore` define quais arquivos e diretórios não entram no contexto enviado ao Docker durante a construção da imagem. No BarberBot incluímos `.git`, porque o histórico do Git não é necessário para executar a aplicação e aumentaria o tamanho do contexto e da imagem. Também incluímos `.env`, porque esse arquivo pode conter configurações e credenciais locais e não deve ser copiado para dentro da imagem. O projeto ainda ignora logs e diretórios de uploads, que são arquivos gerados em execução e não fazem parte do código necessário para o build.

## 2. O que poderia acontecer se a pasta `.git` fosse copiada para dentro da imagem?

A pasta `.git` contém o histórico do repositório, referências de branches e outras informações de versionamento que não são necessárias para executar o BarberBot. Se ela fosse copiada para a imagem, aumentaria desnecessariamente o contexto de build e o tamanho final da imagem. Além disso, o histórico poderia preservar informações removidas de commits atuais, o que representa risco de exposição de dados. Por isso, `.git` está explicitamente listado no `app/.dockerignore`.

## 3. Por que o arquivo `.env` não deve ser enviado para o repositório?

O `.env` é destinado à configuração de cada ambiente e pode conter senhas do banco, chaves de APIs e outros valores sensíveis. No projeto, por exemplo, `DB_PASSWORD` e `DB_ROOT_PASSWORD` são fornecidos ao Docker Compose pelo ambiente. Se um `.env` real fosse publicado em um repositório público, essas credenciais poderiam ser lidas e utilizadas por terceiros. Por isso, o `.gitignore` do BarberBot bloqueia `.env` e o arquivo não é versionado.

## 4. Se o `.env` não vai para o repositório, para que serve o arquivo `.env.example`?

O `.env.example` funciona como um modelo documentado das variáveis necessárias para executar o projeto. Ele informa quais nomes precisam existir, como `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `APP_PORT` e outras configurações, mas utiliza somente valores fictícios. Depois de clonar o repositório, a pessoa copia `.env.example` para `.env` e pode ajustar os valores do seu próprio ambiente. Assim, o projeto continua reproduzível sem publicar segredos reais.

## 5. Uma senha real foi enviada ao GitHub por engano. Apagá-la em um commit seguinte resolve o problema? Justifiquem a resposta.

Não. Apagar a senha apenas em um commit posterior não elimina o valor do histórico anterior do Git. Quem tiver acesso ao repositório ou ao histórico ainda poderá localizar o commit que continha a credencial. Nesse caso, a senha deve ser considerada comprometida e precisa ser revogada ou alterada imediatamente. Depois disso, também é necessário remover o segredo do histórico do Git com uma ferramenta apropriada e verificar se não existem outras cópias ou chaves expostas.
