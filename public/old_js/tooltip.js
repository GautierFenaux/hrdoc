const tooltips = document.querySelectorAll('.tooltip');

let timeoutId;


Array.prototype.slice.call(tooltips).map((tooltip) => {

  tooltip.addEventListener('mouseenter', () => {
    timeoutId = setTimeout(() => {
      tooltip.classList.add('show-tooltip');
    }, 800);
  });

  tooltip.addEventListener('mouseleave', () => {
    clearTimeout(timeoutId);
    tooltip.classList.remove('show-tooltip');
  });

})