# Estevão Liturgical Calendar

Plugin WordPress para exibir informações do calendário litúrgico anglicano usando a API [Caminho Anglicano](https://caminhoanglicano.com.br).

![WordPress Version](https://img.shields.io/badge/WordPress-5.0%2B-blue)
![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple)
![License](https://img.shields.io/badge/License-GPLv2-green)

## Funcionalidades

- Exibe informações do calendário litúrgico anglicano
- Nome do domingo ou celebração
- Cor litúrgica com indicador visual
- Estação litúrgica (Advento, Natal, Epifania, Quaresma, Páscoa, Pentecostes, etc.)
- Ano litúrgico (A, B ou C)
- Coleta (oração) do dia
- Leituras bíblicas (referências ou texto completo)
- Celebrações e santos
- **4 estilos de banner**: Simples, Elegante, Moderno e Compacto
- **Compatível com Elementor** via widget de shortcode
- Cache automático (1 hora) para melhor performance

## Instalação

### Via Upload (Recomendado)

1. Baixe o arquivo `estevao-liturgical-calendar.zip` da [página de releases](https://github.com/dodopok/estevao-liturgical-wordpress-plugin/releases)
2. No WordPress, vá em **Plugins > Adicionar Novo > Fazer upload do plugin**
3. Selecione o arquivo zip e clique em **Instalar agora**
4. Ative o plugin

### Via FTP

1. Extraia o conteúdo do zip
2. Faça upload da pasta `estevao-liturgical-calendar` para `/wp-content/plugins/`
3. Ative o plugin no menu **Plugins** do WordPress

## Configuração

Após ativar o plugin, vá em **Configurações > Calendário Litúrgico** para:

- Selecionar o **Livro de Oração** (IEAB 2015, LOCb 2008, ACNA 2019, etc.)
- Selecionar a **Versão da Bíblia** (NVI, ARA, ACF, ESV, etc.)
- Escolher o **Estilo do Banner** padrão
- Configurar os **Elementos do Banner** exibidos por padrão

## Uso

### Shortcode Principal: `[liturgical_calendar]`

Exibe informações detalhadas do calendário litúrgico.

```
[liturgical_calendar]
```

#### Atributos

| Atributo | Valores | Padrão | Descrição |
|----------|---------|--------|-----------|
| `date` | `today`, `last_sunday`, `next_sunday`, `YYYY-MM-DD` | `today` | Data a exibir |
| `show` | Lista separada por vírgula | todos | Campos a exibir |

#### Campos disponíveis para `show`

- `date` - Data formatada
- `day_name` - Nome do dia (ex: "2º Domingo da Epifania")
- `season` - Estação litúrgica
- `color` - Cor litúrgica
- `year` - Ano litúrgico (A/B/C)
- `collect` - Oração(ões) do dia
- `readings` - Referências das leituras
- `readings_full` - Leituras com texto completo
- `celebration` - Celebração/santo do dia

#### Exemplos

```html
<!-- Todas as informações do próximo domingo -->
[liturgical_calendar date="next_sunday"]

<!-- Apenas nome e cor do domingo anterior -->
[liturgical_calendar date="last_sunday" show="day_name,color"]

<!-- Leituras completas de hoje -->
[liturgical_calendar date="today" show="day_name,readings_full"]

<!-- Data específica -->
[liturgical_calendar date="2024-12-25" show="day_name,collect"]
```

### Shortcode de Banner: `[liturgical_banner]`

Exibe um banner centralizado com a cor litúrgica como fundo.

```
[liturgical_banner]
```

#### Atributos

| Atributo | Valores | Padrão | Descrição |
|----------|---------|--------|-----------|
| `date` | `today`, `last_sunday`, `next_sunday`, `YYYY-MM-DD` | `today` | Data a exibir |
| `style` | `simple`, `elegant`, `modern`, `compact` | Config. admin | Estilo visual |
| `show` | Lista separada por vírgula | Config. admin | Elementos a exibir |

#### Estilos de Banner

| Estilo | Descrição |
|--------|-----------|
| `simple` | Limpo e minimalista, gradiente suave |
| `elegant` | Formal/litúrgico, fonte serif, bordas decorativas douradas |
| `modern` | Efeito glassmorphism, cantos arredondados, visual contemporâneo |
| `compact` | Barra horizontal compacta, ideal para headers |

#### Elementos disponíveis para `show`

- `date` - Data
- `title` - Título (estação/celebração)
- `year` - Ano litúrgico
- `readings` - Referências das leituras

#### Exemplos

```html
<!-- Banner elegante do próximo domingo -->
[liturgical_banner date="next_sunday" style="elegant"]

<!-- Banner moderno só com título e leituras -->
[liturgical_banner style="modern" show="title,readings"]

<!-- Banner compacto para header -->
[liturgical_banner style="compact" show="title,year"]
```

## Uso com Elementor

1. Adicione um widget **Shortcode** na página
2. Cole o shortcode desejado (ex: `[liturgical_banner style="elegant"]`)
3. Publique ou visualize a página

## Personalização CSS

O plugin inclui classes CSS bem definidas para customização:

### Calendário Principal

```css
.estevao-liturgical-calendar { }
.liturgical-day-name { }
.liturgical-season { }
.liturgical-color { }
.liturgical-year { }
.liturgical-readings { }
.liturgical-collect { }
```

### Banner

```css
.estevao-liturgical-banner { }
.liturgical-banner-title { }
.liturgical-banner-year { }
.liturgical-banner-readings { }

/* Classes de cor */
.liturgical-banner-verde { }
.liturgical-banner-roxo { }
.liturgical-banner-branco { }
.liturgical-banner-vermelho { }
.liturgical-banner-rosa { }
.liturgical-banner-azul { }
.liturgical-banner-preto { }

/* Classes de estilo */
.liturgical-banner-style-simple { }
.liturgical-banner-style-elegant { }
.liturgical-banner-style-modern { }
.liturgical-banner-style-compact { }
```

## Livros de Oração Suportados

- IEAB 2015 (Igreja Episcopal Anglicana do Brasil)
- LOCb 2008 (Diocese do Recife)
- IECB 1987
- ACNA 2019 (Anglican Church in North America)
- E outros disponíveis na API

## Versões da Bíblia Suportadas

### Português
- NVI (Nova Versão Internacional)
- ARA (Almeida Revista e Atualizada)
- ACF (Almeida Corrigida Fiel)
- NAA (Nova Almeida Atualizada)

### Inglês
- ESV (English Standard Version)
- NIV (New International Version)
- KJV (King James Version)
- E outras

## FAQ

### O plugin funciona offline?

Não. O plugin requer conexão com a internet para buscar dados da API Caminho Anglicano. Os dados são cacheados por 1 hora para melhor performance.

### Posso usar múltiplos shortcodes na mesma página?

Sim! Você pode usar quantos shortcodes quiser, cada um com configurações diferentes.

### Como limpar o cache?

Vá em **Configurações > Calendário Litúrgico** e clique no botão **Limpar Cache**.

### O plugin afeta a performance do site?

O impacto é mínimo graças ao cache de 1 hora. A primeira requisição do dia pode demorar alguns segundos, mas as seguintes são instantâneas.

## Changelog

### 1.0.1
- Correção de especificidade CSS nos estilos de banner
- Estilos Elegante, Moderno e Compacto agora são exibidos corretamente

### 1.0.0
- Versão inicial
- Shortcode `[liturgical_calendar]` com atributos date e show
- Shortcode `[liturgical_banner]` para banner com cor de fundo
- 4 estilos de banner: simples, elegante, moderno e compacto
- Preview em tempo real no painel de administração
- Página de configurações para Livro de Oração e Versão da Bíblia
- Cache com transients (1 hora)

## Contribuindo

Contribuições são bem-vindas! Por favor:

1. Faça um fork do repositório
2. Crie uma branch para sua feature (`git checkout -b feature/nova-feature`)
3. Commit suas mudanças (`git commit -m 'Adiciona nova feature'`)
4. Push para a branch (`git push origin feature/nova-feature`)
5. Abra um Pull Request

## Licença

Este plugin é licenciado sob a [GPLv2 ou posterior](https://www.gnu.org/licenses/gpl-2.0.html).

## Autor

**Douglas Araujo**
- Website: [caminhoanglicano.com.br](https://caminhoanglicano.com.br)
- GitHub: [@dodopok](https://github.com/dodopok)

## Agradecimentos

- API Caminho Anglicano por fornecer os dados litúrgicos
- Comunidade WordPress
