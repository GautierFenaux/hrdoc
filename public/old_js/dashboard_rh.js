const tabs = document.querySelectorAll('.tab');

Array.prototype.slice.call(tabs).map(tab => {
    tab.children[0] instanceof HTMLSpanElement ? tab.style.marginRight = '1rem' : '';
})