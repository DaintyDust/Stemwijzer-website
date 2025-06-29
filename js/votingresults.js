document.addEventListener('DOMContentLoaded', function () {
    const printButton = document.getElementById('print');

    if (printButton) {
        printButton.addEventListener('click', function () {
            const header = document.querySelector('header');
            const nav = document.querySelector('nav');
            const footer = document.querySelector('footer');
            const body = document.querySelector('body');
            const printButton = document.querySelector('#print');

            if (header) header.style.display = 'none';
            if (nav) nav.style.display = 'none';
            if (footer) footer.style.display = 'none';
            if (body) body.style.gridTemplate = 'none';
            if (printButton) printButton.style.display = 'none';

            window.print();

            if (header) header.style.display = '';
            if (nav) nav.style.display = '';
            if (footer) footer.style.display = '';
            if (body) body.style.gridTemplate = '';
            if (printButton) printButton.style.display = '';
        });
    }
});