<!--
Título: Conventional Commits em inglês, como os commits: `feat(banner): …`,
`fix(shortcode): …`.
Escreva para quem vai revisar sem ter acompanhado a conversa. Seção que não se
aplica fica com "Nada." em vez de sumir: assim fica claro que ela foi pensada.
Estes comentários não aparecem no PR.
-->

## Por quê

<!-- O problema ou a necessidade, do ponto de vista de quem mantém o site da
paróquia ou de quem o visita. Link para a issue ou a conversa. -->

## O que muda

<!-- O que muda nos shortcodes (`[liturgical_calendar]`,
`[liturgical_banner]`), nos estilos de banner ou na página de configurações.
Lista curta; o diff já mostra o código. -->

## Como foi feito

<!-- O que o diff não explica: a abordagem, o que foi descartado e por quê, os
arquivos que valem ser lidos primeiro. -->

## Riscos

<!--
Uma linha por risco: o que pode dar errado, quem sente, e o que segura. Pense
em:
- sites que já usam o plugin: shortcodes e atributos (`date`, `show`, `style`)
  colados em páginas e no Elementor; atributo renomeado ou removido quebra a
  página sem aviso;
- as classes CSS documentadas no README, que temas sobrescrevem;
- opções gravadas (`estevao_liturgical_*`) e transients: formato novo lido
  pela versão anterior, e o contrário;
- a API key: só no servidor, enviada em `X-API-Key`, nunca no shortcode nem
  no HTML; sem ela, tudo responde 401;
- o contrato com a API (calendário do dia, livros, versões da Bíblia) e o
  cache de uma hora, que segura a resposta velha depois de uma correção;
- segurança do WordPress: saída escapada, entrada sanitizada, nonce e
  `current_user_can` no admin e no AJAX;
- compatibilidade: PHP 7.4 e WordPress 5.0 (os mínimos declarados), Elementor,
  nomes de função e classe que colidem com outros plugins;
- carga na API: vários shortcodes na mesma página, cache frio.
Se não há risco que valha nomear, diga por quê.
-->

## Publicação

<!--
O merge não chega a nenhum site: quem usa instala o zip da página de
Releases. Escreva o que a publicação pede, em ordem:
- versão subida no cabeçalho do plugin (`Version:`), em
  `ESTEVAO_LITURGICAL_VERSION`, no `readme.txt` (`Stable tag`, Changelog,
  Upgrade Notice) e no changelog do README;
- zip gerado por `./build.sh` (sai em `dist/`) e anexado a uma release no
  GitHub, com a tag da versão;
- o que quem atualiza precisa fazer (opção nova no admin, limpar o cache);
- PR da API que precisa estar em produção antes, com o link.
-->

- [ ] Nada além da release normal.

## Como desfazer

<!-- Reverter o merge não alcança os sites que já instalaram o zip: diga se
basta publicar uma versão de correção (com número maior) e o que não volta
(opção gravada, transient no formato novo, shortcode que alguém já mudou na
página). -->

## Como testar

<!-- O que foi rodado e como conferir à mão: versões do WordPress e do PHP, os
shortcodes e atributos tocados, cada estilo de banner, a página de
configurações com e sem chave, o Elementor se for o caso, o zip de
`./build.sh` instalado num site limpo. -->

## Telas

<!-- Antes e depois do shortcode ou do banner (cada estilo tocado) e da página
de configurações. "Nada." se não muda tela. -->

## Relacionados

<!-- PRs em outros repositórios (`dodopok/estevao-api#123`), a ordem em que
entram, e issues. -->

## Checklist

- [ ] Testado num WordPress com PHP 7.4 (o mínimo declarado), ou o PR diz em qual versão
- [ ] Shortcodes, atributos e classes CSS documentados continuam funcionando com o mesmo nome
- [ ] Saída escapada, entrada sanitizada; admin e AJAX conferem nonce e `current_user_can`
- [ ] API key só no servidor (`X-API-Key`), nunca no HTML, no shortcode ou em log
- [ ] Texto novo traduzível, com o text domain `estevao-liturgical-calendar`
- [ ] Opções e transients antigos continuam sendo lidos, ou são migrados ou limpos de propósito
- [ ] Versão nova: cabeçalho, `ESTEVAO_LITURGICAL_VERSION`, `readme.txt` e README batem
- [ ] Zip de `./build.sh` instalado e ativado num site limpo
- [ ] README e `readme.txt` atualizados se mudou shortcode, atributo ou configuração
