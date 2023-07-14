pandoc rapport.md --pdf-engine=lualatex `
    -V "mainfont:CMU Bright Roman" `
    -V "sansfont:CMU Bright Roman" `
    -V "monofont:Ubuntu Mono" `
    -f markdown+implicit_figures `
    --verbose `
    -o rapport.pdf