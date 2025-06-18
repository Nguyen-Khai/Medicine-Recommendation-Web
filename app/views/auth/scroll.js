let sections = document.querySelectorAll('.section');
let current = 0;
let isScrolling = false;

function scrollToSection(index) {
  if (index >= 0 && index < sections.length) {
    isScrolling = true;
    window.scrollTo({
      top: sections[index].offsetTop,
      behavior: 'smooth'
    });
    setTimeout(() => isScrolling = false, 700); // khóa cuộn trong 0.7s
  }
}

window.addEventListener('wheel', (e) => {
  if (isScrolling) return;
  if (e.deltaY > 0 && current < sections.length - 1) {
    current++;
    scrollToSection(current);
  } else if (e.deltaY < 0 && current > 0) {
    current--;
    scrollToSection(current);
  }
});
