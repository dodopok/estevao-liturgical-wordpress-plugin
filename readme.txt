=== Estevão Liturgical Calendar ===
Contributors: douglasaraujo
Tags: liturgical, calendar, anglican, church, liturgy, readings
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Exibe informações do calendário litúrgico anglicano usando a API Caminho Anglicano.

== Description ==

O plugin Estevão Liturgical Calendar permite exibir informações do calendário litúrgico anglicano em seu site WordPress. Ele se conecta com a API Caminho Anglicano para fornecer:

* Nome do domingo ou celebração
* Cor litúrgica
* Estação litúrgica
* Ano litúrgico (A, B ou C)
* Coleta (oração) do dia
* Leituras bíblicas (referências ou texto completo)
* Celebrações e santos

**Funciona com Elementor!** Use o widget de shortcode do Elementor para adicionar o calendário litúrgico em qualquer lugar do seu site.

== Installation ==

1. Faça upload da pasta `estevao-liturgical-calendar` para o diretório `/wp-content/plugins/`
2. Ative o plugin no menu 'Plugins' do WordPress
3. Vá em Configurações > Calendário Litúrgico para escolher o Livro de Oração e Versão da Bíblia
4. Use o shortcode `[liturgical_calendar]` em suas páginas ou posts

== Usage ==

= Shortcode Básico =

`[liturgical_calendar]`

Mostra todas as informações do dia atual.

= Atributos Disponíveis =

**date** - Qual data exibir:
* `today` - Data atual (padrão)
* `last_sunday` - Domingo anterior
* `next_sunday` - Próximo domingo
* `2024-01-15` - Data específica no formato Y-m-d

**show** - Quais campos exibir (separados por vírgula):
* `date` - Data formatada
* `day_name` - Nome do dia
* `season` - Estação litúrgica
* `color` - Cor litúrgica
* `year` - Ano litúrgico
* `collect` - Oração(ões) do dia
* `readings` - Referências das leituras
* `readings_full` - Leituras com texto completo
* `celebration` - Celebração/santo

= Exemplos =

Próximo domingo com todas as informações:
`[liturgical_calendar date="next_sunday"]`

Domingo anterior, apenas nome e cor:
`[liturgical_calendar date="last_sunday" show="day_name,color"]`

Hoje com leituras completas:
`[liturgical_calendar date="today" show="day_name,readings_full"]`

Apenas as referências das leituras do próximo domingo:
`[liturgical_calendar date="next_sunday" show="day_name,readings"]`

= Banner Litúrgico =

Use `[liturgical_banner]` para exibir um banner centralizado com a cor litúrgica como fundo. Ideal para destacar informações na home do site.

`[liturgical_banner]`

Mostra: estação litúrgica (ou celebração + estação), ano litúrgico e referências das leituras.

**Atributos:**
* `date` - Mesmas opções do shortcode principal (today, last_sunday, next_sunday, ou data Y-m-d)
* `style` - Estilo do banner (simple, elegant, modern, compact)
* `show` - Elementos a exibir (date, title, year, readings)

**Exemplos:**

Banner do próximo domingo:
`[liturgical_banner date="next_sunday"]`

Banner elegante do domingo anterior:
`[liturgical_banner date="last_sunday" style="elegant"]`

Banner moderno só com título e leituras:
`[liturgical_banner style="modern" show="title,readings"]`

= Usando com Elementor =

1. Adicione um widget "Shortcode" na página
2. Cole o shortcode desejado
3. Publique ou visualize a página

== Frequently Asked Questions ==

= Quais Livros de Oração estão disponíveis? =

O plugin suporta vários Livros de Oração Comum, incluindo:
* IEAB 2015 (Igreja Episcopal Anglicana do Brasil)
* LOCb 2008 (Diocese do Recife)
* IECB 1987
* ACNA 2019 (Anglican Church in North America)
* E outros

= Quais versões da Bíblia estão disponíveis? =

O plugin suporta diversas versões em português e inglês, incluindo:
* NVI (Nova Versão Internacional)
* ARA (Almeida Revista e Atualizada)
* ACF (Almeida Corrigida Fiel)
* ESV (English Standard Version)
* E muitas outras

= O plugin funciona offline? =

Não. O plugin requer conexão com a internet para buscar dados da API Caminho Anglicano. Porém, os dados são cacheados por 1 hora para melhor performance.

= Posso personalizar o estilo? =

Sim! O plugin inclui CSS básico com classes bem definidas. Você pode sobrescrever os estilos no seu tema. As principais classes são:
* `.estevao-liturgical-calendar` - Container principal
* `.liturgical-day-name` - Nome do dia
* `.liturgical-color` - Cor litúrgica
* `.liturgical-readings` - Seção de leituras
* `.liturgical-collect` - Coleta/oração

== Screenshots ==

1. Exemplo do shortcode exibindo informações do domingo
2. Página de configurações no admin
3. Uso com Elementor

== Changelog ==

= 1.0.0 =
* Versão inicial
* Shortcode [liturgical_calendar] com atributos date e show
* Shortcode [liturgical_banner] para banner centralizado com cor de fundo
* 4 estilos de banner: simples, elegante, moderno e compacto
* Preview em tempo real no painel de administração
* Página de configurações para Livro de Oração e Versão da Bíblia
* Cache com transients (1 hora)
* Suporte a domingo anterior, hoje e próximo domingo
* CSS básico responsivo

== Upgrade Notice ==

= 1.0.0 =
Versão inicial do plugin.
