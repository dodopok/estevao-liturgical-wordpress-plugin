# Roadmap - Estevão Liturgical Calendar

Este documento descreve as funcionalidades planejadas e sugestões de melhorias para o plugin.

## Legenda

- 🟢 **Pronto** - Implementado
- 🟡 **Em desenvolvimento** - Sendo trabalhado
- 🔵 **Planejado** - Próximas versões
- ⚪ **Sugestão** - Ideias para o futuro

---

## Versão 1.0 🟢

- [x] Shortcode `[liturgical_calendar]` com suporte a data e campos customizáveis
- [x] Shortcode `[liturgical_banner]` com 4 estilos visuais
- [x] Página de configurações no admin
- [x] Seleção de Livro de Oração
- [x] Seleção de Versão da Bíblia
- [x] Cache com WordPress Transients (1 hora)
- [x] Preview em tempo real no painel admin
- [x] CSS responsivo básico
- [x] Suporte a datas: hoje, domingo anterior, próximo domingo, data específica

---

## Versão 1.1 🔵

### Widgets Gutenberg (Blocos)

- [ ] **Bloco Calendário Litúrgico** - Editor visual para configurar o calendário
- [ ] **Bloco Banner Litúrgico** - Editor visual com preview de todos os estilos
- [ ] Suporte a cores personalizadas do tema
- [ ] Integração com Full Site Editing (FSE)

### Melhorias no Admin

- [ ] Preview lado a lado de todos os 4 estilos de banner
- [ ] Botão de copiar shortcode com atributos customizados
- [ ] Estatísticas de uso do cache (hits/misses)
- [ ] Log de erros da API para debug

### Performance

- [ ] Cache persistente opcional (banco de dados)
- [ ] Prefetch de dados do próximo domingo
- [ ] Lazy loading para leituras completas

---

## Versão 1.2 🔵

### Widget de Sidebar

- [ ] Widget clássico para sidebars
- [ ] Configurações visuais no Customizer
- [ ] Tamanho compacto para sidebars estreitas

### Elementor Nativo

- [ ] Widget Elementor dedicado (não apenas shortcode)
- [ ] Controles de estilo no painel do Elementor
- [ ] Suporte a Dynamic Tags
- [ ] Templates prontos para Elementor

### Novos Estilos de Banner

- [ ] **Minimalista** - Apenas texto, sem background
- [ ] **Gradiente** - Gradientes mais elaborados
- [ ] **Card** - Estilo de cartão com sombra
- [ ] **Full-width** - Banner que ocupa 100% da largura
- [ ] **Sticky** - Banner fixo no topo da página

---

## Versão 1.3 🔵

### Funcionalidades Avançadas

- [ ] **Calendário Mensal** - Visualização de todo o mês
- [ ] **Calendário Semanal** - Visualização da semana
- [ ] **Próximos Eventos** - Lista das próximas celebrações importantes
- [ ] **Santos do Dia** - Destaque para santos e mártires

### Notificações

- [ ] Email semanal com leituras do domingo
- [ ] Integração com WP Cron para agendamentos
- [ ] Webhook para integrações externas

### Internacionalização

- [ ] Tradução completa para inglês
- [ ] Tradução para espanhol
- [ ] Suporte a RTL (idiomas da direita para esquerda)
- [ ] Formatação de data por locale

---

## Versão 2.0 ⚪

### App/PWA

- [ ] Progressive Web App para acesso offline
- [ ] Sincronização de dados em background
- [ ] Push notifications para celebrações importantes

### API REST

- [ ] Endpoints REST para desenvolvedores
- [ ] Autenticação via API keys
- [ ] Rate limiting configurável
- [ ] Documentação Swagger/OpenAPI

### Multisite

- [ ] Suporte completo a WordPress Multisite
- [ ] Configurações por site ou globais
- [ ] Painel de administração de rede

### Temas Litúrgicos

- [ ] Tema WordPress completo com design litúrgico
- [ ] Cores do site mudam automaticamente conforme estação
- [ ] Templates de página para igrejas

---

## Sugestões da Comunidade ⚪

### Integrações

- [ ] **WooCommerce** - Produtos litúrgicos relacionados
- [ ] **LearnDash/LifterLMS** - Cursos sobre liturgia
- [ ] **BuddyPress** - Grupos de estudo bíblico
- [ ] **Events Calendar** - Sincronização de celebrações
- [ ] **Mailchimp/Newsletter** - Envio automático de leituras

### Conteúdo Adicional

- [ ] **Hinos sugeridos** - Hinário relacionado às leituras
- [ ] **Meditações** - Reflexões diárias
- [ ] **Orações extras** - Orações matutinas e vespertinas
- [ ] **Lectio Divina** - Guia para leitura orante

### Acessibilidade

- [ ] Modo alto contraste
- [ ] Suporte completo a leitores de tela
- [ ] Tamanho de fonte ajustável
- [ ] Versão para impressão

### Redes Sociais

- [ ] Compartilhamento automático no Facebook
- [ ] Cards do Twitter/X otimizados
- [ ] Stories do Instagram com leitura do dia
- [ ] Integração com WhatsApp

---

## Como Contribuir

### Sugerir Funcionalidades

1. Abra uma [Issue](https://github.com/douglas/estevao-liturgical-wordpress-plugin/issues) no GitHub
2. Use o template de "Feature Request"
3. Descreva a funcionalidade desejada
4. Explique o caso de uso

### Reportar Bugs

1. Abra uma [Issue](https://github.com/douglas/estevao-liturgical-wordpress-plugin/issues) no GitHub
2. Use o template de "Bug Report"
3. Inclua passos para reproduzir
4. Informe versão do WordPress e PHP

### Contribuir com Código

1. Escolha uma issue com label `good first issue` ou `help wanted`
2. Comente na issue que você vai trabalhar nela
3. Faça um fork e crie uma branch
4. Envie um Pull Request

---

## Priorização

As funcionalidades são priorizadas com base em:

1. **Impacto** - Quantos usuários serão beneficiados
2. **Esforço** - Complexidade de implementação
3. **Alinhamento** - Fit com a visão do plugin
4. **Demanda** - Quantidade de pedidos da comunidade

---

## Histórico de Decisões

### Por que não usar React no admin?

O painel de configurações usa JavaScript vanilla/jQuery para manter compatibilidade máxima com diferentes versões do WordPress e evitar conflitos com outros plugins.

### Por que cache de 1 hora?

Balanceamento entre performance e atualização dos dados. O calendário litúrgico não muda com frequência, então 1 hora é suficiente para a maioria dos casos.

### Por que não Gutenberg na v1.0?

Shortcodes são mais universais e funcionam em qualquer tema/page builder. Blocos Gutenberg serão adicionados na v1.1 como complemento.

---

*Última atualização: Janeiro 2026*
