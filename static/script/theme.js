let theme = localStorage.getItem('theme');

function applyTheme(theme) {
    document.querySelector('html').id = theme;
}

applyTheme(theme);

let themeCheckbox = document.querySelector('.themCheckbox');

if(themeCheckbox) {
    if(theme == 'black' || !theme) {
        themeCheckbox.setAttribute('checked', true);
    }

    themeCheckbox.addEventListener('change', () => {
        if(themeCheckbox.checked) {
            localStorage.setItem('theme', 'black');
            applyTheme('black');
        } else {
            localStorage.setItem('theme', 'light');
            applyTheme('light');
        }
    })
}