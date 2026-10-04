# Fotografo portfelis

Individualios WordPress temos pagrindas minimalistiniam, redakciniam fotografo portfeliui. Pirmasis etapas paruošia švarią struktūrą, tipografiją, spalvų sistemą ir laikiną pradžios puslapio demonstraciją SADA vizualinei krypčiai.

## Diegimas

1. Nukopijuokite aplanką `photographer-portfolio` į `wp-content/themes/`.
2. WordPress administravime atidarykite **Išvaizda > Temos**.
3. Aktyvuokite **Fotografo portfelis**.
4. Atidarykite **Išvaizda > Meniu** ir priskirkite meniu vietoms **Pagrindinis meniu** bei **Poraštės meniu**.
5. Pasirinktinai: nustatykite individualų logotipą skiltyje **Išvaizda > Tinkinti > Svetainės tapatybė**.

## Failų Struktūra

```text
photographer-portfolio/
├── assets/
│   ├── css/
│   │   ├── main.css
│   │   └── editor-style.css
│   ├── js/
│   │   └── main.js
│   └── images/
├── template-parts/
│   ├── header/
│   │   └── site-branding.php
│   ├── footer/
│   │   └── site-info.php
│   └── content/
│       └── content.php
├── functions.php
├── style.css
├── index.php
├── front-page.php
├── page.php
├── single.php
├── archive.php
├── header.php
├── footer.php
├── 404.php
├── screenshot.png
└── README.md
```

## Paletė

- Fonas ir paviršiai: `#000000`
- Pagrindinis tekstas: `#F2EBDD`
- Akcentas: `#FF2A1A`

Visos temos spalvos sutelktos CSS kintamuosiuose failuose `assets/css/main.css` ir `assets/css/editor-style.css`.

## Tipografija

- Antraštės: Anton
- Ranka rašyto stiliaus akcentas: Anton
- Pagrindinis tekstas: sistemos UI šriftų rinkinys

Anton įkeliamas per `wp_enqueue_style()` iš Google Fonts. CSS nenaudoja `@import`.

## Dabartinė Apimtis

Šiame etape yra temos karkasas, WordPress funkcijų palaikymas, prisitaikanti antraštė, laikinas pradžios puslapis, baziniai šablonai, Gutenberg redaktoriaus stiliai, minimalus JavaScript ir poraštės struktūra.

## Kiti Etapai

- Galutinė viršelio kryptis su fotografija.
- Portfelio archyvo ir galerijos išdėstymai.
- Apie ir kontaktų puslapių dizainas.
- Daugiakalbystės integracija.
- Pasirinktiniai įrašų tipai arba Gutenberg blokai, jei jų reikės turinio modeliui.
